<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pack extends PS_Controller
{
	public $menu_code = 'PACKLIST';
	public $menu_sub_group_code = 'PACK';
	public $menu_group_code = 'IC';
	public $title = 'Pack List';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'pack';
		$this->load->model('pack_model');
		$this->load->model('pick_model');
		$this->load->model('pack_logs_model');
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'code' => get_filter('code', 'pack_code', ''),
			'orderCode' => get_filter('orderCode', 'pack_orderCode', ''),
			'pickCode' => get_filter('pickCode', 'pack_pickCode', ''),
			'CardName' => get_filter('CardName', 'pack_CardName', ''),
			'uname' => get_filter('uname', 'pack_uname', ''),
			'Status' => get_filter('Status', 'pack_Status', 'all'),
			'fromDate' => get_filter('fromDate', 'pack_fromDate', ''),
			'toDate' => get_filter('toDate', 'pack_toDate', ''),
			'order_by' => get_filter('order_by', 'pack_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'pack_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->pack_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->pack_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('pack/pack_list', $filter);
  }


	public function add_new()
	{
		$this->title = "Create Pack List";

		$ds = array(
			'so_list' => $this->pack_model->get_finish_so_list()
		);

		$this->load->view('pack/pack_add', $ds);
	}


	public function add()
	{
		$sc = TRUE;

		$orderCode = trim($this->input->post('orderCode'));
		$pickListNo = trim($this->input->post('pickListNo'));

		$ds = array();

		if(!empty($orderCode))
		{
			if(!empty($pickListNo))
			{

				$pick = $this->pick_model->get_by_code($pickListNo);

				if(!empty($pick))
				{
					if($pick->Canceled == 'N')
					{
						if($pick->Status == 'Y')
						{
							$details = $this->pack_model->get_pick_rows_by_so($pick->AbsEntry, $orderCode);

							if(!empty($details))
							{
								$CardName = $this->pack_model->get_card_name($pick->AbsEntry, $orderCode);
								$code = $this->get_new_code();
								$arr = array(
									'code' => $code,
									'orderCode' => $orderCode,
									'pickCode' => $pickListNo,
									'CardName' => $CardName,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								$this->db->trans_begin();

								$id = $this->pack_model->add($arr);

								if($id !== FALSE)
								{
									foreach($details as $rs)
									{
										if($sc === FALSE)
										{
											break;
										}

										$arr = array(
											'packCode' => $code,
											'orderCode' => $rs->OrderCode,
											'pickCode' => $pick->DocNum,
											'ItemCode' => $rs->ItemCode,
											'ItemName' => $rs->ItemName,
											'UomEntry' => $rs->UomEntry,
											'UomEntry2' => $rs->UomEntry2,
											'UomCode' => $rs->UomCode,
											'UomCode2' => $rs->UomCode2,
											'unitMsr' => $rs->unitMsr,
											'unitMsr2' => $rs->unitMsr2,
											'BaseQty' => $rs->BaseQty,
											'PickQtty' => $rs->PickQtty,
											'BasePickQty' => $rs->BasePickQty,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->pack_model->add_row($arr))
										{
											$sc = FALSE;
											$this->error = "Insert pack row failed @ {$rs->ItemCode}";

										}
										else
										{
											if(! $this->pick_model->set_row_status($rs->AbsEntry, $rs->PickEntry, 'C')) //--- loaded to pack
											{
												$sc = FALSE;
												$this->error = "Change Pick row Status failed";
											}
										}
									}


									if($sc === TRUE)
									{
										$this->pack_logs_model->add('add', $code);

										$this->db->trans_commit();
									}
									else
									{
										$this->db->trans_rollback();
									}

									if($sc === TRUE)
									{
										if($this->pick_model->is_all_closed($pick->AbsEntry))
										{
											$this->pick_model->update($pick->AbsEntry, array('Status' => 'C'));
										}

										$ds = array(
											'id' => $id,
											'code' => $code
										);
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "Create Pack Document failed";
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ไม่พบรายการจัดสินค้า";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Invalid Pick List State : current state = {$pick->state}";
						}

					}
					else
					{
						$sc = FALSE;
						$this->error = "Pick List already Canceled";
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Pick List No.";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : Pick List No.";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: SO No.";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function view_detail($id)
	{
		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			$rows = $this->pack_model->get_rows($doc->code);

			$ds = array(
				'doc' => $doc,
				'rows' => $rows,
				'logs' => $this->pack_logs_model->get($doc->code)
			);

			$this->load->view('pack/pack_detail', $ds);
		}
		else
		{
			$this->error_page();
		}
	}



	public function get_pick_list_by_so()
	{
		$sc = TRUE;
		$soNo = trim($this->input->get('orderCode'));
		$ds = array();

		if(!empty($soNo))
		{
			$so = $this->pack_model->get_pick_list_by_so($soNo);

			if(!empty($so))
			{
				foreach($so as $rs)
				{
					$arr = array(
						'docNum' => $rs->DocNum
					);

					array_push($ds, $arr);
				}
			}
		}

		echo json_encode($ds);
	}



	public function send_to_sap()
	{
		$this->load->model('warehouse_model');

		$sc = TRUE;
		$id = $this->input->post('id');
		$code = $this->input->post('code');
		$binCode = trim($this->input->post('BinCode'));

		//--- check BinCode
		$BufferWhsCode = getConfig('BUFFER_WAREHOUSE');

		$WhsCode = $this->warehouse_model->get_warehouse_code($binCode);

		if($WhsCode == $BufferWhsCode)
		{
			//--- Update warehouse and bin for transfer
			$arr = array(
				'TransWhsCode' => $WhsCode,
				'TransBinCode' => $binCode
			);

			if($this->pack_model->update($id, $arr))
			{
				if(! $this->doExport($id))
				{
					$sc = FALSE;
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Update Pack list Transfer Warehouse and Bin Location failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "คลังสินค้าไม่ถูกต้อง";
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function doExport($id)
	{
		$sc = TRUE;
		$this->load->model('warehouse_model');
		$this->load->model('transfer_model');
		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status == 'Y')
			{
				//---- check TR already in SAP
				$tr = $this->transfer_model->get_sap_transfer($doc->code);

				if(empty($tr))
				{
					//---- drop exists temp data
					$temp = $this->transfer_model->get_temp_transfer($doc->code);

					if(!empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if($this->transfer_model->drop_transfer_temp_data($rows->DocEntry) === FALSE)
		          {
		            $sc = FALSE;
		            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
		          }
		        }
		      }

					if($sc === TRUE)
					{
						$currency = getConfig('CURRENCY');
						$currency = empty($currency) ? 'THB' : $currency;

						$this->mc->trans_begin();

						//--- insert heade OWTR ก่อน แล้วได้ DocEntry มาเอาไปใส่ที่อื่นต่อ
						$header = array(
	            'DocDate' => sap_date(now(), TRUE),
	            'DocDueDate' => sap_date(now(), TRUE),
	            'CardCode' => NULL,
	            'CardName' => NULL,
	            'VatPercent' => 0.000000,
	            'VatSum' => 0.000000,
	            'VatSumFc' => 0.000000,
	            'DiscPrcnt' => 0.000000,
	            'DiscSum' => 0.000000,
	            'DiscSumFC' => 0.000000,
	            'DocCur' => $currency,
	            'DocRate' => 1,
	            'DocTotal' => 0.000000,
	            'DocTotalFC' => 0.000000,
							'U_WEBORDER' => $doc->code,
	            'F_Web' => 'A',
	            'F_WebDate' => sap_date(now(), TRUE)
						);

						$docEntry = $this->transfer_model->add_sap_transfer($header);

						if($docEntry !== FALSE)
						{
							$details = $this->pack_model->get_pack_results($doc->code);

							if(!empty($details))
							{
								$line = 0;
	              foreach($details as $rs)
	              {
									if($sc === FALSE)
									{
										break;
									}

									if($rs->Qty > 0)
									{
										$arr = array(
		                  'DocEntry' => $docEntry,
		                  'U_WEBORDER' => $rs->packCode,
		                  'LineNum' => $line,
		                  'ItemCode' => $rs->ItemCode,
		                  'Dscription' => limitText($rs->ItemName, 95),
		                  'Quantity' => $rs->Qty,
											'UomCode' => $rs->UomCode,
											'UomEntry' => $rs->UomEntry,
		                  'unitMsr' => $rs->unitMsr,
											'UomCode2' => $rs->UomCode2,
											'UomEntry2' => $rs->UomEntry2,
											'unitMsr2' => $rs->unitMsr2,
		                  'PriceBefDi' => 0.000000,
		                  'LineTotal' => 0.000000,
		                  'ShipDate' => sap_date(now(), TRUE),
		                  'Currency' => $currency,
		                  'Rate' => 1,
		                  'DiscPrcnt' => 0.000000,
		                  'Price' => 0.000000,
		                  'TotalFrgn' => 0.000000,
		                  'FromWhsCod' => $this->warehouse_model->get_warehouse_code($rs->BinCode),
		                  'WhsCode' => $doc->TransWhsCode,
		                  'F_FROM_BIN' => $rs->BinCode,
		                  'F_TO_BIN' => $doc->TransBinCode,
		                  'TaxStatus' => 'Y',
		                  'VatPrcnt' => 0.000000,
		                  'VatGroup' => NULL,
		                  'PriceAfVAT' => 0.000000,
		                  'VatSum' => 0.000000,
		                  'TaxType' => 'Y'
		                );

										if( ! $this->transfer_model->add_sap_transfer_detail($arr))
		                {
		                  $sc = FALSE;
		                  $this->error = 'เพิ่มรายการไม่สำเร็จ';
		                }

		                $line++;
									}
	              }
							} //-- end if empty details

						} //--- end if docEntry

						if($sc === TRUE)
						{
							$this->mc->trans_commit();
						}
						else
						{
							$this->mc->trans_rollback();
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "เอกสารถูกนำเข้า SAP แล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Status";
			}
		}

		if($sc === TRUE)
		{
			$arr = array(
				'tempStatus' => 'P',
				'tempDate' => now()
			);

			$this->pack_model->update($id, $arr);
		}


		return $sc;
	}



	public function find_bin_code($whsCode)
	{
		$txt = trim($_REQUEST['term']);
		$ds = array();

		$qr  = "SELECT BinCode FROM OBIN ";
		$qr .= "WHERE WhsCode LIKE '{$whsCode}' ";
		$qr .= "AND BinCode LIKE N'%{$txt}%' ";
		$qr .= "ORDER BY BinCode ASC ";
		$qr .= "OFFSET 0 ROW FETCH NEXT 50 ROWS ONLY";

		$rs = $this->ms->query($qr);

		if($rs->num_rows() > 0)
		{
			foreach($rs->result() as $rd)
			{
				$ds[] = $rd->BinCode;
			}
		}

		echo json_encode($ds);
	}


	public function check_bin_code()
	{
		$sc = FALSE;
		$whsCode = getConfig('BUFFER_WAREHOUSE');
		$binCode = trim($this->input->post('BinCode'));

		$rs = $this->ms
		->select('AbsEntry')
		->where('WhsCode', $whsCode)
		->where('BinCode', $binCode)
		->get('OBIN');

		if($rs->num_rows() == 1) {
			$sc = TRUE;
		}

		echo $sc === TRUE ? 'success' : 'Location ไม่ถูกต้อง';
	}



	public function get_temp_detail()
	{
		$this->load->model('transfer_model');
		$this->load->model('sales_order_model');

		$sc = TRUE;
		$code = trim($this->input->get('code'));

		$doc = $this->pack_model->get_by_code($code);

		$ds = array();

		if(!empty($doc))
		{
			if($doc->tempStatus != NULL && $doc->tempStatus != 'N')
			{
				$temp = $this->transfer_model->get_last_temp_transfer($code);

				if(!empty($temp))
				{
					$prefix = $this->sales_order_model->get_prefix_by_docNum($doc->orderCode);
					$ds = array(
						'id' => $doc->id,
						'DocEntry' => $temp->DocEntry,
						'U_WEBORDER' => $code,
						'OrderCode' => $prefix.'-'.$doc->orderCode,
						'PickCode' => $doc->pickCode,
						'TransWhsCode' => $doc->TransWhsCode,
						'TransBinCode' => $doc->TransBinCode,
						'F_WebDate' => thai_date($temp->F_WebDate, TRUE),
						'F_SapDate' => $temp->F_Sap == NULL ? '-' : thai_date($temp->F_SapDate, TRUE),
						'F_Sap' => $temp->F_Sap == 'Y' ? 'Success' :($temp->F_Sap == 'N' ? 'Failed' : 'Pending'),
						'Message' => $temp->F_Sap == 'Y' ? '' : $temp->Message,
					);

					if($temp->F_Sap != 'Y')
					{
						$ds['del_btn'] = 'Y';
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "No Temp data";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Temp Status OR Document not in Temp";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "{$code} not found";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function delete_temp()
	{
		$this->load->model('transfer_model');
		$sc = TRUE;
		$DocEntry = $this->input->post('DocEntry');
		$id = $this->input->post('id');

		if(! $this->transfer_model->drop_transfer_temp_data($DocEntry))
		{
			$sc = FALSE;
			$this->error = "Delete Temp Failed";
		}
		else
		{
			$arr = array(
				'tempStatus' => 'N',
				'tempDate' => NULL
			);

			$this->pack_model->update($id, $arr);
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PACK_LIST');
    $run_digit = getConfig('RUN_DIGIT_PACK_LIST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->pack_model->get_max_code($pre);
    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }





	public function clear_filter()
	{
		$filter = array(
			'pack_code',
			'pack_orderCode',
			'pack_pickCode',
			'pack_CardName',
			'transferCode',
			'pack_uname',
			'pack_Status',
			'pack_fromDate',
			'pack_toDate',
			'pack_order_by',
			'pack_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

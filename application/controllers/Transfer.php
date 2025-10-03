<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
	public $menu_code = 'TRANSFER';
	public $menu_sub_group_code = '';
	public $menu_group_code = 'IC';
	public $title = 'Inventory Transfer';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'transfer';
		$this->load->model('transfer_model');
		$this->load->model('transfer_bin_model');
		$this->load->model('pallet_model');
		$this->load->model('item_model');
  }


	public function index()
	{		
		$filter = array(
			'code' => get_filter('code', 'trCode', ''),
			'orderCode' => get_filter('orderCode', 'trOrderCode', ''),
			'pickCode' => get_filter('pickCode', 'trPickCode', ''),
			'packCode' => get_filter('packCode', 'trPackCode', ''),
			'palletCode' => get_filter('palletCode', 'trPalletCode'),
			'uname' => get_filter('uname', 'trUname', ''),
			'Status' => get_filter('Status', 'trStatus', 'all'),
			'fromDate' => get_filter('fromDate', 'trFromDate', ''),
			'toDate' => get_filter('toDate', 'trToDate', ''),
			'order_by' => get_filter('order_by', 'trOrder_by', 'code'),
			'sort_by' => get_filter('sort_by', 'trSort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->transfer_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->transfer_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);
		$this->load->view('transfer/transfer_list', $filter);
	}


	public function add_new()
	{
		$this->title = "New Transfer";
		$ds = array(
			'code' => $this->get_new_code(),
			'toWhsCode' => getConfig('BUFFER_WAREHOUSE')
		);

		$this->load->view('transfer/transfer_add', $ds);
	}


	public function add()
	{
		$sc = TRUE;
		$this->load->model('warehouse_model');
		$this->load->model('pack_model');

		$docDate = db_date($this->input->post('docDate'));
		$toBinCode = trim($this->input->post('toBinCode'));
		$remark = get_null(trim($this->input->post('remark')));
		$palletCode = trim($this->input->post('palletCode'));

		//--- check bin code
		$whsCode = getConfig('BUFFER_WAREHOUSE');

		$existsBin = $this->transfer_bin_model->is_exists_code($toBinCode);

		if($existsBin)
		{
			//--- check pallet status
			$pallet = $this->pallet_model->get_pallet_by_code($palletCode);

			if(!empty($pallet))
			{
				if($pallet->Status == 'O')
				{

					$details = $this->transfer_model->get_pallet_items($pallet->id);

					if(!empty($details))
					{
						//--- add transfer  header
						$code = $this->get_new_code($docDate);

						$arr = array(
							'code' => $code,
							'toWhsCode' => $whsCode,
							'transfer_bin_code' => $toBinCode,
							'palletCode' => $pallet->code,
							'DocDate' => $docDate,
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'remark' => $remark
						);

						$this->db->trans_begin();

						$id = $this->transfer_model->add($arr);

						if($id)
						{
							foreach($details as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}

								$Qty = $rs->BasePackQty > 0 ? $rs->BasePackQty/$rs->BaseQty : 0;

								$arr = array(
									'transfer_id' => $id,
									'ItemCode' => $rs->ItemCode,
									'ItemName' => $this->item_model->getName($rs->ItemCode),
									'fromWhsCode' => $this->warehouse_model->get_warehouse_code($rs->BinCode),
									'fromBinCode' => $rs->BinCode,
									'UomEntry' => $rs->UomEntry,
									'UomCode' => $rs->UomCode,
									'unitMsr' => $rs->unitMsr,
									'UomEntry2' => $rs->UomEntry2,
									'UomCode2' => $rs->UomCode2,
									'unitMsr2' => $rs->unitMsr2,
									'BaseQty' => $rs->BaseQty,
									'Qty' => round($Qty, 6),
									'InvQty' => $rs->BasePackQty,
									'pickCode' => $rs->pickCode,
									'packCode' => $rs->packCode,
									'orderCode' => $rs->OrderCode,
									'palletCode' => $pallet->code,
									'OrderDate' => $rs->OrderDate
								);

								if(! $this->transfer_model->add_detail($arr))
								{
									$sc = FALSE;
									$this->error = "Insert detail failed";
								}
							} //--- endforeach

							if($sc === TRUE)
							{
								$arr = array(
									'Status' => 'C',
									'transferCode' => $code
								);

								if(! $this->pallet_model->update($pallet->id, $arr))
								{
									$sc = FALSE;
									$this->error = "Update Pallet Status failed";
								}
							}

							if($sc === TRUE)
							{
								//--- close pack list
								$packlist = $this->pallet_model->get_pack_list_by_pallet($pallet->id);

								if(!empty($packlist))
								{
									foreach($packlist as $rs)
									{
										if($sc === FALSE)
										{
											break;
										}

										if(! $this->pack_model->set_rows_status($rs->PackCode, 'C'))
										{
											$sc = FALSE;
											$this->error = "Update Pack rows status failed";
										}

										if($sc === TRUE)
										{
											if(! $this->pack_model->update_by_code($rs->PackCode, array('Status' => 'C')))
											{
												$sc = FALSE;
												$this->error = "Update pack list status failed";
											}
										}
									}
								}
							}



							if($sc === TRUE)
							{
								$this->db->trans_commit();
							}
							else
							{
								$this->db->trans_rollback();
							}

						}
						else
						{
							$sc = FALSE;
							$this->error = "Insert Document failed";
						}

					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบรายการในพาเลท";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "พาเลท {$palletCode} ถูกโอนไปแล้วโดยเอกสาร {{$pallet->transferCode}}";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่พาเลทไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Location ปลายทางไม่ถูกต้อง";
		}


		echo $sc === TRUE ? $id : $this->error;
	}


	public function cancle_transfer()
	{
		$sc = TRUE;
		$this->load->model('pack_model');

		$id = $this->input->post('id');
		$code = $this->input->post('code');

		$doc = $this->transfer_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status === 'N')
			{
				$sap = $this->transfer_model->get_sap_transfer($doc->code);

				if(empty($sap))
				{
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
						$this->db->trans_begin();

						if(! $this->transfer_model->cancle_details($doc->id))
						{
							$sc = FALSE;
							$this->error = "ยกเลิกรายการโอนสินค้าไม่สำเร็จ";
						}
						else
						{
							$arr = array(
								'Status' => 'C'
							);

							if(! $this->transfer_model->update($doc->id, $arr))
							{
								$sc = FALSE;
								$this->error = "ยกเลิกเอกสารไม่สำเร็จ";
							}
						}


						if($sc === TRUE)
						{
							//--- release pallet packed
							$arr = array(
								'Status' => 'O',
								'transferCode' => NULL
							);

							if(! $this->pallet_model->update_by_code($doc->palletCode, $arr))
							{
								$sc = FALSE;
								$this->error = "Update pallet Status failed";
							}
						}


						//--- update packed status
						if($sc === TRUE)
						{
							$pallet = $this->pallet_model->get_pallet_by_code($doc->palletCode);

							if(!empty($pallet))
							{
								//--- Unclose pack list
								$packlist = $this->pallet_model->get_pack_list_by_pallet($pallet->id);

								if(!empty($packlist))
								{
									foreach($packlist as $rs)
									{
										if($sc === FALSE)
										{
											break;
										}

										if(! $this->pack_model->set_rows_status($rs->PackCode, 'Y'))
										{
											$sc = FALSE;
											$this->error = "Update Pack rows status failed";
										}

										if($sc === TRUE)
										{
											if(! $this->pack_model->update_by_code($rs->PackCode, array('Status' => 'Y')))
											{
												$sc = FALSE;
												$this->error = "Update pack list status failed";
											}
										}
									}
								}
							}
						}

						if($sc === TRUE)
						{
							$this->db->trans_commit();
							$this->update_order_line($id, -1);
						}
						else
						{
							$this->db->trans_rollback();
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่สามารถยกเลิกได้ เนื่องจากเอกสารเข้า SAP แล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่สามารถยกเลิกเอกสารได้ เนื่องจากสถานะเอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเอกสาร";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function view_detail($id)
	{
		$this->title = "Transfer Details";

		$doc = $this->transfer_model->get($id);
		$details = $this->transfer_model->get_details($id);

		if(!empty($details))
		{
			$ds = array(
				'doc' => $doc,
				'details' => $details
			);

			$this->load->view('transfer/transfer_detail', $ds);
		}
		else
		{
			$this->page_error();
		}
	}


	public function send_to_sap()
	{
		$sc = TRUE;

		$id = $this->input->post('id');

		if(! $this->doExport($id))
		{
			$sc = FALSE;
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function doExport($id)
	{
		$sc = TRUE;

		$this->load->model('warehouse_model');

		$doc = $this->transfer_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status !== 'Y')
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
	            'DocDate' => sap_date($doc->DocDate, TRUE),
	            'DocDueDate' => sap_date($doc->DocDate, TRUE),
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
	            'F_WebDate' => sap_date(now(), TRUE),
							'U_BookCode' => "TR"
						);

						$docEntry = $this->transfer_model->add_sap_transfer($header);

						if($docEntry !== FALSE)
						{
							$details = $this->transfer_model->get_details($id);

							if(!empty($details))
							{
								$line = 0;

	              foreach($details as $rs)
	              {
									if($sc === FALSE)
									{
										break;
									}

									if($rs->InvQty > 0)
									{
										$arr = array(
		                  'DocEntry' => $docEntry,
		                  'U_WEBORDER' => $doc->code,
		                  'LineNum' => $line,
		                  'ItemCode' => $rs->ItemCode,
		                  'Dscription' => $rs->ItemName,
		                  'Quantity' => $rs->InvQty,
											'InvQty' => $rs->InvQty,
											'UomCode' => $rs->UomCode2, //-- ต้องสลับ field uom
											'UomEntry' => $rs->UomEntry2,
		                  'unitMsr' => $rs->unitMsr2,
											'UomCode2' => $rs->UomCode2,
											'UomEntry2' => $rs->UomEntry2,
											'unitMsr2' => $rs->unitMsr2,
											'NumPerMsr' => 1.000000,
											'NumPerMsr2' => 1.000000,
		                  'PriceBefDi' => 0.000000,
		                  'LineTotal' => 0.000000,
		                  'ShipDate' => sap_date($doc->DocDate, TRUE),
		                  'Currency' => $currency,
		                  'Rate' => 1,
		                  'DiscPrcnt' => 0.000000,
		                  'Price' => 0.000000,
		                  'TotalFrgn' => 0.000000,
		                  'FromWhsCod' => $rs->fromWhsCode,
		                  'WhsCode' => $doc->toWhsCode,
		                  'F_FROM_BIN' => $rs->fromBinCode,
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
							$this->update_order_location($id);
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
				'Status' => 'P',
				'tempDate' => now()
			);

			$this->transfer_model->update($id, $arr);
		}


		if($sc === TRUE)
		{
			$this->update_order_line($id);
			$this->update_order_transfer_code($doc->code);
		}


		return $sc;
	}


	public function get_open_pallet()
	{
		$sc = array();

		$txt = trim($_REQUEST['term']);

		$this->db->where('Status', 'O');

		if($txt != '*')
		{
			$this->db->like('code', $txt);
		}

		$rs = $this->db->order_by('code', 'ASC')->limit(50)->get('pallet');

		if($rs->num_rows() > 0)
		{
			foreach($rs->result() as $rd)
			{
				$sc[] = $rd->code;
			}
		}
		else
		{
			$sc[] = "not found";
		}

		echo json_encode($sc);
	}


	public function get_buffer_bin_code()
	{
		$sc = array();

		$txt = trim($_REQUEST['term']);

		$this->db->select('code, name');

		if($txt != '*')
		{
			$this->db
			->group_start()
			->like('code', $txt)
			->or_like('name', $txt);
		}

		$qs = $this->db->order_by('code', 'ASC')->limit(50)->get('transfer_bin');

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$sc[] = $rs->code. ' | '.$rs->name;
			}
		}
		else
		{
			$sc[] = "not found";
		}


		echo json_encode($sc);
	}



	public function get_item_in_pallet()
	{
		$sc = TRUE;
		$ds = array();

		$palletCode = trim($this->input->get('palletCode'));

		$pallet = $this->pallet_model->get_pallet_by_code($palletCode);

		if( ! empty($pallet))
		{
			if($pallet->Status == 'O')
			{
				//--- check packlist is all finished
				$unFinish = $this->pallet_model->get_not_finished_pack_code($pallet->id);

				if(empty($unFinish))
				{
					$details = $this->transfer_model->get_pallet_items($pallet->id);

					if(!empty($details))
					{
						$toBin = getConfig('BUFFER_WAREHOUSE');

						foreach($details as $rs)
						{
							$arr = array(
								'id' => $rs->id,
								'ItemCode' => $rs->ItemCode,
								'ItemName' => $this->item_model->getName($rs->ItemCode),
								'palletCode' => $pallet->code,
								'orderCode' => $rs->OrderCode,
								'pickCode' => $rs->pickCode,
								'packCode' => $rs->packCode,
								'fromBin' => $rs->BinCode,
								'toBin' => $toBin,
								'qty' => number($rs->BasePackQty, 2),
								'unitMsr' => $rs->unitMsr
							);

							array_push($ds, $arr);
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "No Item Found";
					}

				}
				else
				{
					$sc = FALSE;

					$packlist = "";

					$i = 1;

					foreach($unFinish as $rs)
					{
						$packlist .= $i == 1 ? $rs->PackCode : ", {$rs->PackCode}";
						$i++;
					}

					$this->error = "Operation can not be done. {$packlist} Still packing.";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Pallet already Closed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Pallet No.";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


	public function get_temp_data()
	{
		$code = $this->input->get('code'); //--- U_WEBORDER

		$data = $this->transfer_model->get_temp_data($code);

		if(!empty($data))
		{

			$status = 'Pending';

			if($data->F_Sap === NULL)
			{
				$status = "Pending";
			}
			else if($data->F_Sap === 'N')
			{
				$status = "Failed";
			}
			else if($data->F_Sap === 'Y')
			{
				$status = "Success";
			}


			$arr = array(
				'U_WEBORDER' => $data->U_WEBORDER,
				'F_WebDate' => thai_date($data->F_WebDate, TRUE),
				'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
				'F_Sap' => $status,
				'Message' => empty($data->Message) ? '' : $data->Message,
				'del_btn' => ($status === "Pending" OR $status === "Failed") ? 'ok' : ''
			);

			echo json_encode($arr);
		}
		else
		{
			echo 'No data found';
		}
	}


	public function remove_temp()
	{
		$sc = TRUE;
		$code = $this->input->post('U_WEBORDER');
		$temp = $this->transfer_model->get_temp_status($code);

		if(empty($temp))
		{
			$sc = FALSE;
			$this->error = "Temp data not exists";
		}
		else if($temp->F_Sap === 'Y')
		{
			$sc = FALSE;
			$this->error = "Delete Failed : Temp Data already in SAP";
		}

		if($sc === TRUE)
		{
			if(! $this->transfer_model->drop_transfer_temp_data($temp->DocEntry))
			{
				$sc = FALSE;
				$this->error = "Delete Failed : Delete Temp Failed";
			}
			else
			{
				$arr = array(
					'Status' => 'N',
					'DocNum' => NULL,
					'message' => NULL,
					'SapDate' => NULL,
					'tempDate' => NULL
				);

				$this->transfer_model->update_by_code($code, $arr);
			}
		}


		$this->response($sc);
	}


	public function update_order_line($transfer_id, $method = 1)
	{
		$sc= TRUE;

		$doc = $this->transfer_model->get($transfer_id);

		if(!empty($doc))
		{
			$details = $this->transfer_model->get_sum_order_item_details($transfer_id);

			if(!empty($details))
			{
				foreach($details as $detail)
				{
					if($sc === FALSE)
					{
						break;
					}

					if($detail->InvQty > 0)
					{
						$invQty = $detail->InvQty;

						$pick_id = $this->transfer_model->get_pick_id_by_code($detail->pickCode);

						if(!empty($pick_id))
						{
							$rows = $this->transfer_model->get_pick_rows_by_item_uom($pick_id, $detail->orderCode, $detail->ItemCode, $detail->UomEntry);

							if(!empty($rows))
							{
								foreach($rows as $row)
								{
									if($sc === FALSE)
									{
										break;
									}

									if($invQty > 0)
									{
										//--- get RDR1 data
										$line = $this->transfer_model->get_order_line($row->OrderEntry, $row->OrderLine);

										if(!empty($line))
										{
											if($method < 0 OR $line->U_TransferNo != $doc->code)
											{
												$packed = $invQty >= $row->BasePickQty ? $row->BasePickQty : $invQty;

												$packed = $row->BasePickQty > 0 ? round(($row->BasePickQty/$row->BaseQty), 6) : $packed;
												$packed = $packed * $method;
												$prevPacked = $line->U_PrevPacked + $packed;

												$arr = array(
													'U_Packed' => ($packed + $line->U_Packed),
													'U_PrevPacked' => $prevPacked,
													'U_TransferNo' => $method > 0 ? $doc->code : NULL
												);

												if( ! $this->transfer_model->update_order_line($row->OrderEntry, $row->OrderLine, $arr) )
												{
													$sc = FALSE;
													$this->error = "Update Order Packed failed";
												}

												$invQty -= ($packed * $method);
											}
										}
									}
								}
							}
						}
					}
				}
			}

			if($sc === TRUE)
			{
				$this->transfer_model->update($transfer_id, array('SO_Updated' => 'Y'));
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document";
		}

		return $sc;

	}


	public function manual_update_order_line($transfer_id, $method = 1)
	{
		$sc= TRUE;

		$doc = $this->transfer_model->get($transfer_id);

		if(!empty($doc))
		{
			$details = $this->transfer_model->get_sum_order_item_details($transfer_id);

			if(!empty($details))
			{
				foreach($details as $detail)
				{
					if($sc === FALSE)
					{
						break;
					}

					if($detail->InvQty > 0)
					{
						$invQty = $detail->InvQty;

						$pick_id = $this->transfer_model->get_pick_id_by_code($detail->pickCode);

						if(!empty($pick_id))
						{
							$rows = $this->transfer_model->get_pick_rows_by_item_uom($pick_id, $detail->orderCode, $detail->ItemCode, $detail->UomEntry);

							if(!empty($rows))
							{
								foreach($rows as $row)
								{
									if($sc === FALSE)
									{
										break;
									}

									if($invQty > 0)
									{
										//--- get RDR1 data
										$line = $this->transfer_model->get_order_line($row->OrderEntry, $row->OrderLine);

										if(!empty($line))
										{
											if($method < 0 OR $line->U_TransferNo != $doc->code)
											{
												$packed = $invQty >= $row->BasePickQty ? $row->BasePickQty : $invQty;

												$packed = $row->BasePickQty > 0 ? round(($row->BasePickQty/$row->BaseQty), 6) : $packed;
												$packed = $packed * $method;
												$prevPacked = $line->U_PrevPacked + $packed;

												$arr = array(
													'U_Packed' => ($packed + $line->U_Packed),
													'U_PrevPacked' => $prevPacked,
													'U_TransferNo' => $method > 0 ? $doc->code : NULL
												);

												if( ! $this->transfer_model->update_order_line($row->OrderEntry, $row->OrderLine, $arr) )
												{
													$sc = FALSE;
													$this->error = "Update Order Packed failed";
												}

												$invQty -= ($packed * $method);
											}
										}
									}
								}
							}
						}
					}
				}
			}

			if($sc === TRUE)
			{
				$this->transfer_model->update($transfer_id, array('SO_Updated' => 'Y'));
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document";
		}

		$this->response($sc);
	}


	public function update_order_location($id)
	{
		$sc = TRUE;
		$doc = $this->transfer_model->get($id);

		if(!empty($doc))
		{
			$orders = $this->transfer_model->get_order_codes($id);

			if(!empty($orders))
			{
				foreach($orders as $rs)
				{
					$this->transfer_model->update_order_location($rs->orderCode, $doc->transfer_bin_code);
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document";
		}
	}


	public function update_order_transfer_code($code)
	{
		$this->load->model('sales_order_model');

		$orders = $this->transfer_model->get_order_by_transfer_code($code);

		if( ! empty($orders))
		{
			foreach($orders as $rs)
			{
				$this->transfer_model->update_order_transfer_code($rs->orderCode, $code);
				$this->sales_order_model->update_by_doc_num($rs->orderCode, ['transfer_code' => $code]);
			}
		}
	}



	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
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
			'trCode',
			'trOrderCode',
			'trPickCode',
			'trPackCode',
			'trPalletCode',
			'trUname',
			'trStatus',
			'trFromDate',
			'trToDate',
			'trOrder_by',
			'trSort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

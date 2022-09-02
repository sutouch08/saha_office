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

		$ds = $this->pack_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($ds))
		{
			foreach($ds as $rs)
			{
				$rs->palletNo = ($rs->Status == 'Y' OR $rs->Status == 'C' OR $rs->Status == 'P') ? add_commas($this->pack_model->get_pallet_code($rs->code)) : NULL;
			}
		}

    $filter['data'] = $ds;

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
					if($pick->Status != 'N')
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
											if(! $this->pick_model->set_row_status($rs->AbsEntry, $rs->OrderCode, $rs->ItemCode, $rs->UomEntry, 'C')) //--- loaded to pack
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



	public function cancle_pack()
	{
		$sc = TRUE;

		$id = $this->input->post('id');
		$code = $this->input->post('code');

		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status != 'C')
			{
				if($doc->Status != 'D')
				{
					$this->load->model('pallet_model');
					$this->load->model('picking_model');

					$this->db->trans_begin();

					//--- remove pallet row
					if(! $this->pallet_model->delete_pack_rows($doc->code))
					{
						$sc = FALSE;
						$this->error = "ลบการเชื่อมโยงพาเลทไม่สำเร็จ";
					}
					else
					{
						//--- remove pack box
						if(! $this->pack_model->delete_pack_boxes($doc->code))
						{
							$sc = FALSE;
							$this->error = "ลบกล่องไม่สำเร็จ";
						}
						else
						{
							//--- set pack row status
							if(! $this->pack_model->set_rows_status($doc->code, 'D'))
							{
								$sc = FALSE;
								$this->error = "ยกเลิกรายการแพ็คไม่สำเร็จ";
							}
							else
							{
								//--- set pack list status
								if(! $this->pack_model->update($doc->id, array('Status' => 'D')))
								{
									$sc = FALSE;
									$this->error = "ยกเลิกเอกสารแพ็คไม่สำเร็จ";
								}
								else
								{
									$pick = $this->pick_model->get_by_code($doc->pickCode);

									if(!empty($pick))
									{
										//---- restore buffer
										$details = $this->pack_model->get_pack_results($code);

										if(!empty($details))
										{
											foreach($details as $detail)
											{
												if($sc === FALSE)
												{
													break;
												}


												$arr = array(
													'AbsEntry' => $pick->AbsEntry,
													'DocNum' => $detail->pickCode,
													'OrderCode' => $detail->OrderCode,
													'ItemCode' => $detail->ItemCode,
													'ItemName' => $detail->ItemName,
													'UomEntry' => $detail->UomEntry2,
													'UomCode' => $detail->UomCode2,
													'unitMsr' => $detail->unitMsr2,
													'BasePickQty' => $detail->BasePackQty,
													'BinCode' => $detail->BinCode,
													'user_id' => $detail->user_id,
													'uname' => $this->user->uname,
													'date_upd' => now()
												);

												if(! $this->picking_model->restore_buffer($arr))
												{
													$sc = FALSE;
													$this->error = "Restore buffer failed";
												}
											}
										}


										if($sc === TRUE)
										{
											//--- change pick status
											if(! $this->pick_model->un_close_rows($pick->AbsEntry, $doc->orderCode))
											{
												$sc = FALSE;
												$this->error = "เปลียนสถานะ Pick details ไม่สำเร็จ";
											}
											else
											{
												if(! $this->pick_model->is_all_closed($pick->AbsEntry))
												{
													if(! $this->pick_model->update($pick->AbsEntry, array('Status' => 'Y')))
													{
														$sc = FALSE;
														$this->error = "เปลี่ยนสถานะ Pick list ไม่สำเร็จ";
													}
												}
											}
										}

										//--- add logs
										if($sc === TRUE)
										{
											$this->pack_logs_model->add('cancle', $doc->code);
										}
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
			}
			else
			{
				$sc = FALSE;
				$this->error = "กรุณายกเลิกใบโอนสินค้าก่อนยกเลิก Pack list";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document";
		}


		echo $sc === TRUE ? 'success' : $this->error;
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

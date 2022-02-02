<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Packing extends PS_Controller
{
	public $menu_code = 'PACKING';
	public $menu_sub_group_code = 'PACK';
	public $menu_group_code = 'IC';
	public $title = 'รอแพ็ค';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'packing';
		$this->load->model('pack_model');
		$this->load->model('packing_model');
		$this->load->model('pick_model');
		$this->load->model('item_model');
		$this->load->model('pack_logs_model');
  }



  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'pack_code', ''),
			'orderCode' => get_filter('orderCode', 'pack_orderCode', ''),
			'pickCode' => get_filter('pickCode', 'pack_pickCode', ''),
			'CardName' => get_filter('CardName', 'pack_CardName', ''),
			'uname' => get_filter('uname', 'pack_uname', ''),
			'fromDate' => get_filter('fromDate', 'pack_fromDate', ''),
			'toDate' => get_filter('toDate', 'pack_toDate', ''),
			'order_by' => get_filter('order_by', 'pack_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'pack_sort_by', 'DESC')
		);

		$status = 'N'; //--- รอแพ็ค

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->packing_model->count_rows($filter, $status);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->packing_model->get_list($filter, $perpage, $this->uri->segment($segment), $status);

    $filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('packing/packing_list', $filter);
  }



	public function view_process()
  {

		$filter = array(
			'code' => get_filter('code', 'pack_code', ''),
			'orderCode' => get_filter('orderCode', 'pack_orderCode', ''),
			'pickCode' => get_filter('pickCode', 'pack_pickCode', ''),
			'CardName' => get_filter('CardName', 'pack_CardName', ''),
			'uname' => get_filter('uname', 'pack_uname', ''),
			'fromDate' => get_filter('fromDate', 'pack_fromDate', ''),
			'toDate' => get_filter('toDate', 'pack_toDate', ''),
			'order_by' => get_filter('order_by', 'pack_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'pack_sort_by', 'DESC')
		);

		$status = 'P'; //--- รอแพ็ค

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->packing_model->count_rows($filter, $status);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->packing_model->get_list($filter, $perpage, $this->uri->segment($segment), $status);

    $filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('packing/packing_process_list', $filter);
  }



	public function process($id)
	{
		$this->title = "แพ็คสินค้า";

		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status == 'N' OR $doc->Status == 'P')
			{
				if($doc->Status == 'N')
				{
					$arr = array(
						'Status' => 'P',
						'StartPack' => now()
					);

					$this->pack_model->update($id, $arr);
					$this->pack_model->set_rows_status($doc->code, 'P');
					$this->pack_logs_model->add('pack', $doc->code);
				}

				$rows = $this->pack_model->get_rows($doc->code);
				$pack_qty = 0;
				$all_qty = 0;

				if(!empty($rows))
				{
					foreach($rows as $rs)
					{
						$rs->barcode = $this->item_model->get_barcode_uom($rs->ItemCode, $rs->UomEntry);
						$all_qty += round($rs->PickQtty, 2);
						$pack_qty += round($rs->PackQtty, 2);
					}
				}

				$box_list = $this->packing_model->get_box_list($doc->code);

				if(empty($box_list))
				{
					if($this->packing_model->add_new_box($doc->code, 1, 1))
					{
						$box_list = $this->packing_model->get_box_list($doc->code);
					}
				}


				$ds = array(
					'doc' => $doc,
					'rows' => $rows,
					'box_list' => $box_list,
					'pack_qty' => $pack_qty,
					'all_qty' => $all_qty
				);

				$this->load->view('packing/packing_process', $ds);
			}
			else
			{
				$this->load->view('packing/invalid_state');
			}
		}
		else
		{
			$this->page_error();
		}
	}



	public function get_details_table()
	{
		$sc = TRUE;
		$ds = array();

		$id = $this->input->get('id');

		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			$rows = $this->pack_model->get_rows($doc->code);
			$pack_qty = 0;
			$all_qty = 0;

			if(!empty($rows))
			{
				foreach($rows as $rs)
				{
					$balance = $rs->PickQtty - $rs->PackQtty;

					$arr = array(
						'id' => $rs->id,
						'barcode' => $this->item_model->get_barcode_uom($rs->ItemCode, $rs->UomEntry),
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'UomEntry' => $rs->UomEntry,
						'unitMsr' => $rs->unitMsr,
						'PickQtty' => round($rs->PickQtty, 2),
						'PackQtty' => round($rs->PackQtty, 2),
						'balance' => $balance < 0 ? 0 : round($balance , 2),
						'color' => $balance <= 0 ? 'background-color:#ebf1e2;' : ''
					);

					array_push($ds, $arr);
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document id";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function do_packing()
	{
		$sc = TRUE;
		$ds = array();

		$code = $this->input->post('code');
		$barcode = trim($this->input->post('barcode'));
		$box_id = $this->input->post('box_id');
		$qty = $this->input->post('qty');


		//--- get itemcode by barcode
		$item = $this->item_model->get_item_code_uom_by_barcode($barcode);

		if(!empty($item))
		{
			$row = $this->pack_model->get_detail_by_item_uom($code, $item->ItemCode, $item->UomEntry);

			//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
			if(!empty($row))
			{
				$baseQty = $this->item_model->get_base_qty($item->ItemCode, $item->UomEntry);
				//--- แปลงเป็น หน่วยนับย่อย
				$bcQty = $qty * $baseQty;

				//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
				$remain = $row->BasePickQty - $row->BasePackQty;

				if($remain < $bcQty)
				{
					$sc = FALSE;
					$this->error = "สินค้าเกิน กรุณาตรวจสอบ";
				}
				else
				{
					$packQty = $bcQty/$row->BaseQty;

					$packed = $row->PackQtty + $packQty;
					$balance = $row->PickQtty - $packed;

					$this->db->trans_begin();

					$arr = array(
						'packCode' => $code,
						'ItemCode' => $row->ItemCode,
						'UomEntry' => $row->UomEntry,
						'UomCode' => $row->UomCode,
						'unitMsr' => $row->unitMsr,
						'BaseQty' => $baseQty,
						'BasePackQty' => $bcQty,
						'qty' => $packQty,
						'box_id' => $box_id,
						'user_id' => $this->user->id
					);


					if(! $this->packing_model->update_pack_details($arr))
					{
						$sc = FALSE;
						$this->error = "Update pack details failed";
					}
					else
					{
						if(! $this->packing_model->update_pack_row($row->id, $packQty, $bcQty))
						{
							$sc = FALSE;
							$this->error = "Update pack row failed";
						}
						else
						{
							$arr = array(
								'id' => $row->id,
								'packed' => round($packed, 2),
								'pack_qty' => round($packQty, 2),
								'balance' => round($balance, 2),
								'valid' => ($balance <= 0) ? TRUE : FALSE
							);

							array_push($ds, $arr);
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
				$rows = $this->pack_model->get_details_by_item_other_uom($code, $item->ItemCode, $item->UomEntry);

				if(!empty($rows))
				{
					//---- ตัวคูณ หน่วยนับที่ยิงมา
					$baseQty = $this->item_model->get_base_qty($item->ItemCode, $item->UomEntry);

					//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
					$bcQty = $qty * $baseQty;

					$testQty = $bcQty;

					//---- ทดสอบว่ายอดที่ยิงมามัันเกินที่เหลือมั้ย
					foreach($rows as $row)
					{
						//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
						$remain = $row->BasePickQty - $row->BasePackQty;

						$packQty = $testQty <= $remain ? $testQty : $remain;
						$testQty -= $packQty;
					}

					if($testQty > 0)
					{
						$sc = FALSE;
						$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วแพ็คสินค้าใหม่อีกครั้ง";
					}
					else
					{
						$this->db->trans_begin();

						foreach($rows as $row)
						{
							if($sc === FALSE)
							{
								break;
							}

							if($bcQty > 0)
							{
								//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
								$remain = $row->BasePickQty - $row->BasePackQty;

								$invQty = $bcQty <= $remain ? $bcQty : $remain;

								$packQty = $invQty / $row->BaseQty;

								$packed = $row->PackQtty + $packQty;

								$balance = $row->PickQtty - $packed;

								$arr = array(
									'packCode' => $code,
									'ItemCode' => $row->ItemCode,
									'UomEntry' => $row->UomEntry,
									'UomCode' => $row->UomCode,
									'unitMsr' => $row->unitMsr,
									'BaseQty' => $baseQty,
									'BasePackQty' => $invQty,
									'qty' => $packQty,
									'box_id' => $box_id,
									'user_id' => $this->user->id
								);


								if(! $this->packing_model->update_pack_details($arr))
								{
									$sc = FALSE;
									$this->error = "Update pack details failed";
								}
								else
								{
									if(! $this->packing_model->update_pack_row($row->id, $packQty, $invQty))
									{
										$sc = FALSE;
										$this->error = "Update pack row failed";
									}
									else
									{
										$arr = array(
											'id' => $row->id,
											'packed' => round($packed, 2),
											'pack_qty' => round($packQty, 2),
											'balance' => round($balance, 2),
											'valid' => ($balance <= 0) ? TRUE : FALSE
										);

										array_push($ds, $arr);
									}
								}

								$bcQty -= $invQty;
							} //-- endif
						} //--- end foreach

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
					$this->error = "สินค้าไม่ถูกต้อง";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "บาร์โค้ดไม่ถูกต้อง";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function pack_with_option()
	{
		$sc = TRUE;
		$ds = array();

		$id = $this->input->post('id');
		$code = $this->input->post('code');
		$ItemCode = trim($this->input->post('ItemCode'));
		$UomEntry = $this->input->post('UomEntry');
		$box_id = $this->input->post('box_id');
		$qty = $this->input->post('qty');


		//--- get itemcode by barcode
		$item = $this->item_model->get($ItemCode);

		if(! empty($item))
		{
			$row = $this->pack_model->get_detail_by_item_uom($code, $ItemCode, $UomEntry);

			//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
			if(!empty($row))
			{
				$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);
				//--- แปลงเป็น หน่วยนับย่อย
				$bcQty = $qty * $baseQty;

				//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
				$remain = $row->BasePickQty - $row->BasePackQty;

				if($remain < $bcQty)
				{
					$sc = FALSE;
					$this->error = "สินค้าเกิน กรุณาตรวจสอบ";
				}
				else
				{
					$packQty = $bcQty/$row->BaseQty;

					$packed = $row->PackQtty + $packQty;
					$balance = $row->PickQtty - $packed;

					$this->db->trans_begin();

					$arr = array(
						'packCode' => $code,
						'ItemCode' => $row->ItemCode,
						'UomEntry' => $row->UomEntry,
						'UomCode' => $row->UomCode,
						'unitMsr' => $row->unitMsr,
						'BaseQty' => $baseQty,
						'BasePackQty' => $bcQty,
						'qty' => $packQty,
						'box_id' => $box_id,
						'user_id' => $this->user->id
					);


					if(! $this->packing_model->update_pack_details($arr))
					{
						$sc = FALSE;
						$this->error = "Update pack details failed";
					}
					else
					{
						if(! $this->packing_model->update_pack_row($row->id, $packQty, $bcQty))
						{
							$sc = FALSE;
							$this->error = "Update pack row failed";
						}
						else
						{
							$arr = array(
								'id' => $row->id,
								'packed' => round($packed, 2),
								'pack_qty' => round($packQty, 2),
								'balance' => round($balance, 2),
								'valid' => ($balance <= 0) ? TRUE : FALSE
							);

							array_push($ds, $arr);
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
				$rows = $this->pack_model->get_details_by_item_other_uom($code, $ItemCode, $UomEntry);

				if(!empty($rows))
				{
					//---- ตัวคูณ หน่วยนับที่ยิงมา
					$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);

					//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
					$bcQty = $qty * $baseQty;

					$testQty = $bcQty;

					//---- ทดสอบว่ายอดที่ยิงมามัันเกินที่เหลือมั้ย
					foreach($rows as $row)
					{
						//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
						$remain = $row->BasePickQty - $row->BasePackQty;

						$packQty = $testQty <= $remain ? $testQty : $remain;
						$testQty -= $packQty;
					}

					if($testQty > 0)
					{
						$sc = FALSE;
						$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วแพ็คสินค้าใหม่อีกครั้ง";
					}
					else
					{
						$this->db->trans_begin();

						foreach($rows as $row)
						{
							if($sc === FALSE)
							{
								break;
							}

							if($bcQty > 0)
							{
								//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
								$remain = $row->BasePickQty - $row->BasePackQty;

								$invQty = $bcQty <= $remain ? $bcQty : $remain;

								$packQty = $invQty / $row->BaseQty;

								$packed = $row->PackQtty + $packQty;

								$balance = $row->PickQtty - $packed;

								$arr = array(
									'packCode' => $code,
									'ItemCode' => $row->ItemCode,
									'UomEntry' => $row->UomEntry,
									'UomCode' => $row->UomCode,
									'unitMsr' => $row->unitMsr,
									'BaseQty' => $baseQty,
									'BasePackQty' => $invQty,
									'qty' => $packQty,
									'box_id' => $box_id,
									'user_id' => $this->user->id
								);


								if(! $this->packing_model->update_pack_details($arr))
								{
									$sc = FALSE;
									$this->error = "Update pack details failed";
								}
								else
								{
									if(! $this->packing_model->update_pack_row($row->id, $packQty, $invQty))
									{
										$sc = FALSE;
										$this->error = "Update pack row failed";
									}
									else
									{
										$arr = array(
											'id' => $row->id,
											'packed' => round($packed, 2),
											'pack_qty' => round($packQty, 2),
											'balance' => round($balance, 2),
											'valid' => ($balance <= 0) ? TRUE : FALSE
										);

										array_push($ds, $arr);
									}
								}

								$bcQty -= $invQty;
							} //-- endif
						} //--- end foreach

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
					$this->error = "สินค้าไม่ถูกต้อง";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "สินค้าไม่ถูกต้อง";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}

	public function get_box()
  {
    $code = $this->input->get('code');
    $barcode = $this->input->get('barcode');

    $box = $this->packing_model->get_box($code, $barcode);

    if(!empty($box))
    {
      echo $box->id;
    }
    else
    {
      //--- insert new box
      $box_no = $this->packing_model->get_last_box_no($code) + 1;
      $id_box = $this->packing_model->add_new_box($code, $barcode, $box_no);
      echo $id_box === FALSE ? 'เพิมกล่องไม่สำเร็จ' : $id_box;
    }
  }


	public function add_box() {
		$sc = TRUE;
		$box_id = "";

		$code = $this->input->post('code');
		if(!empty($code))
		{
			$box_no = $this->packing_model->get_last_box_no($code) + 1;
			$box_id = $this->packing_model->add_new_box($code, $box_no, $box_no);
			if(! $box_id)
			{
				$sc = FALSE;
				$this->error = "เพิ่มกล่องไม่สำเร็จ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		echo $sc === TRUE ? $box_id : $this->error;
	}





	public function get_box_list()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $id = $this->input->get('box_id');
    $box_list = $this->packing_model->get_box_list($code);

    if(!empty($box_list))
    {
      $ds = array();

      foreach($box_list as $box)
      {
        $arr = array(
          'no' => $box->box_no,
          'box_id' => $box->id,
          'qty' => number($box->qty),
          'class' => $box->id == $id ? 'btn-success' : 'btn-default'
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'no box';

  }



	public function finish_pack()
	{
		$sc = TRUE;

		$id = $this->input->post('id');
		$code = $this->input->post('code');

		$doc = $this->pack_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status == 'P')
			{
				$details = $this->pack_model->get_rows($code);

				if(!empty($details))
				{
					$this->db->trans_begin();

					foreach($details as $row)
					{
						if($sc === FALSE)
						{
							break;
						}

						$PackQtty = $row->PackQtty;

						$buffer = $this->packing_model->get_buffer_uom($row->pickCode, $row->orderCode, $row->ItemCode, $row->UomEntry);

						if(! empty($buffer))
						{
							foreach($buffer as $bf)
							{
								if($PackQtty > 0)
								{
									if($sc === FALSE)
									{
										break;
									}

									$bufferQty = $bf->Qty;
									$packQty = $bufferQty >= $PackQtty ? $PackQtty : $bufferQty;
									$BasePackQty = $packQty * $row->BaseQty;

									$bufferQty -= $packQty;

									$arr = array(
										'packCode' => $doc->code,
										'OrderCode' => $row->orderCode,
										'ItemCode' => $row->ItemCode,
										'UomEntry' => $row->UomEntry,
										'UomCode' => $row->UomCode,
										'unitMsr' => $row->unitMsr,
										'BaseQty' => $row->BaseQty,
										'Qty' => $packQty,
										'BasePackQty' => $BasePackQty,
										'BinCode' => $bf->BinCode,
										'user_id' => $this->user->id
									);

									//--- Create pack result
									if(! $this->packing_model->add_result($arr))
									{
										$sc = FALSE;
										$this->error = "สร้างรายการสรุปยอดแพ็คไม่สำเร็จ : {$row->ItemCode}";
									}

									//-- Update buffer
									if($sc === TRUE)
									{
										if($bufferQty == 0)
										{
											if(! $this->packing_model->drop_buffer($bf->id))
											{
												$sc = FALSE;
												$this->error = "Delete Buffer failed : {$row->ItemCode}";
											}
										}
										else
										{
											if(! $this->packing_model->update_buffer($bf->id, $packQty, $BasePackQty))
											{
												$sc = FALSE;
												$this->error = "Update Buffer failed : $row->ItemCode;";
											}
										}
									}

									$PackQtty -= $packQty;
								}
								else
								{
									break;
								}
							} //--- end foreach
						}
						else
						{
							$sc = FALSE;
							$this->error = "ไม่พบรายการจัดสินค้าที่ตรงกัน";
						}
					} //--- end foreach

					if($sc === TRUE)
					{
						$arr = array(
							'Status' => 'Y',
							'FinishPack' => now()
						);

						if(! $this->pack_model->update($id, $arr))
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
						}

						if(! $this->pack_model->set_rows_status($code, 'Y'))
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะรายการแพ็คไม่สำเร็จ";
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


					//--- add logs
					if($sc === TRUE)
					{
						$this->pack_logs_model->add('packed', $code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการแพ็คสินค้า";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "สถานะเอกสารไม่ถูกต้อง";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function get_pack_box_details()
	{
		$sc = TRUE;
		$ds = array();
		$code = $this->input->get('code');
		$box_id = $this->input->get('box_id');
		$box = $this->packing_model->get_box_by_id($box_id);

		if(!empty($box))
		{
			$ds['box_no'] = $box->box_no;

			$details = $this->packing_model->get_pack_box_details($code, $box_id);

			if(!empty($details))
			{
				$rows = array();

				foreach($details as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $this->item_model->getName($rs->ItemCode),
						'qty' => round($rs->qty, 2),
						'unitMsr' => $rs->unitMsr
					);

					array_push($rows, $arr);
				}

				$ds['rows'] = $rows;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "The specified box not found";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	public function delete_pack_detail()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$code = trim($this->input->post('code'));

		if(!empty($code))
		{
			$doc = $this->pack_model->get_by_code($code);

			if(!empty($doc))
			{
				if($doc->Status == 'P')
				{
					$detail = $this->packing_model->get_pack_detail($id);

					if(!empty($detail))
					{
						$row = $this->pack_model->get_detail_by_item_uom($code, $detail->ItemCode, $detail->UomEntry);

						if(!empty($row))
						{
							$packDif = $row->PackQtty - $detail->qty;
							$baseDif = $row->BasePackQty - $detail->BasePackQty;

							if($packDif < 0 OR $baseDif < 0)
							{
								$sc = FALSE;
								$this->error = "จำนวนที่ต้องการลบ มากกว่าจำนวนแพ็คแล้ว";
							}

							//---- Update pack row
							if($sc === TRUE)
							{

								$this->db->trans_begin();

								//--- delete pack detail
								if(! $this->packing_model->delete_pack_detail($id))
								{
									$sc = FALSE;
									$this->error = "Delete pack detail failed";
								}

								if($sc === TRUE)
								{
									$packQty = $detail->qty * -1;
									$basePackQty = $detail->BasePackQty * -1;

									if(! $this->packing_model->update_pack_row($row->id, $packQty, $basePackQty))
									{
										$sc = FALSE;
										$this->error = "Update pack row failed";
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
							$this->error = "Pack row not found";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid pack detail id";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid document status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function delete_pack_box()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$code = trim($this->input->post('code'));
		$box_id = $this->input->post('box_id');

		if(!empty($code))
		{
			$doc = $this->pack_model->get_by_code($code);

			if(!empty($doc))
			{
				if($doc->Status == 'P')
				{
					$this->db->trans_begin();

					if(! $this->delete_packed_box($code, $box_id))
					{
						$sc = FALSE;
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
					$this->error = "Invalid document status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	private function delete_packed_box($code, $box_id)
	{
		$sc = TRUE;

		$details = $this->packing_model->get_pack_box_details($code, $box_id);

		if(!empty($details))
		{
			foreach($details as $detail)
			{
				if($sc === FALSE)
				{
					break;
				}

				$row = $this->pack_model->get_detail_by_item_uom($code, $detail->ItemCode, $detail->UomEntry);

				if(!empty($row))
				{
					$packDif = $row->PackQtty - $detail->qty;
					$baseDif = $row->BasePackQty - $detail->BasePackQty;

					if($packDif < 0 OR $baseDif < 0)
					{
						$sc = FALSE;
						$this->error = "จำนวนที่ต้องการลบ มากกว่าจำนวนแพ็คแล้ว";
					}

					//---- Update pack row
					if($sc === TRUE)
					{
						//--- delete pack detail
						if(! $this->packing_model->delete_pack_detail($detail->id))
						{
							$sc = FALSE;
							$this->error = "Delete pack detail failed";
						}

						if($sc === TRUE)
						{
							$packQty = $detail->qty * -1;
							$basePackQty = $detail->BasePackQty * -1;

							if(! $this->packing_model->update_pack_row($row->id, $packQty, $basePackQty))
							{
								$sc = FALSE;
								$this->error = "Update pack row failed";
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Pack row not found";
				}
			}
		}


		if($sc === TRUE)
		{
			//--- delete box
			if(! $this->packing_model->delete_box($box_id))
			{
				$sc = FALSE;
				$this->error = "Delete box failed";
			}

			if($sc === TRUE)
			{
				//--- rearange box
				$boxes = $this->packing_model->get_pack_boxes($code);

				if(!empty($boxes))
				{
					$box_no = 1;
					foreach($boxes as $box)
					{
						$arr = array(
							'code' => $box_no,
							'box_no' => $box_no
						);

						if(! $this->packing_model->update_box($box->id, $arr))
						{
							$sc = FALSE;
							$this->error = "Update box failed";
						}

						$box_no++;
					}
				}
			}
		}

		return $sc;
	}



	public function delete_select_box()
	{
		$sc = TRUE;

		$code = $this->input->post('code');
		$boxes = json_decode($this->input->post('boxes'));

		if(!empty($code))
		{
			$doc = $this->pack_model->get_by_code($code);

			if(!empty($doc))
			{
				if($doc->Status == 'P')
				{
					if(!empty($boxes))
					{
						$this->db->trans_begin();

						foreach($boxes as $box)
						{
							if($sc === FALSE)
							{
								break;
							}

							if(! $this->delete_packed_box($code, $box->box_id))
							{
								$sc = FALSE;
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
					$this->error = "Invalid document status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function print_box($code, $box_id)
	{
		$this->title = "Print Label";
		$this->load->model('sales_order_model');
		$this->load->library('printer');

		$arr = array($box_id);
		$box = $this->packing_model->get_selected_boxes($arr);

		$doc = $this->pack_model->get_by_code($code);
		$order = $this->getOrder($doc->orderCode);
		$order->BeginStr = $this->sales_order_model->get_prefix($order->Series);


		$ds = array(
			'doc' => $doc,
			'boxes' => $box,
			'last_box_no' => $this->packing_model->get_last_box_no($doc->code),
			'order' => $order
		);

		$this->load->view('print/print_pack_label', $ds);
	}



	public function print_selected_boxes($code, $box_ids)
	{
		$this->title = "Print Label";
		$this->load->model('sales_order_model');
		$this->load->library('printer');

		$arr = explode("-", $box_ids);

		$box = $this->packing_model->get_selected_boxes($arr);

		$doc = $this->pack_model->get_by_code($code);
		$order = $this->getOrder($doc->orderCode);
		$order->BeginStr = $this->sales_order_model->get_prefix($order->Series);


		$ds = array(
			'doc' => $doc,
			'boxes' => $box,
			'last_box_no' => $this->packing_model->get_last_box_no($doc->code),
			'order' => $order
		);

		$this->load->view('print/print_pack_label', $ds);
	}


	public function getOrder($soNo)
	{
		$rs = $this->ms->where('DocNum', $soNo)->get('ORDR');
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function clear_filter()
	{
		$filter = array(
			'pack_code',
			'pack_orderCode',
			'pack_pickCode',
			'pack_CardName',
			'pack_uname',
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

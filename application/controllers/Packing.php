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
		$this->load->model('pallet_model');
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
		$this->title = "กำลังแพ็ค";

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
						$rs->ItemName = $this->item_model->getName($rs->ItemCode);
						$all_qty += round($rs->BasePickQty, 2);
						$pack_qty += round($rs->BasePackQty, 2);
					}
				}

				$box_list = $this->packing_model->get_box_list($doc->code);

				$ds = array(
					'doc' => $doc,
					'rows' => $rows,
					'box_list' => $box_list,
					'pack_qty' => $pack_qty,
					'all_qty' => $all_qty,
					'pallet_list' => $this->pallet_model->get_pallet_list($doc->code)
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
					$balance = $rs->BasePickQty - $rs->BasePackQty;
					$barcode = $this->item_model->get_barcode_uom($rs->ItemCode, $rs->UomEntry);

					$arr = array(
						'id' => $rs->id,
						'barcode' => $barcode,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $this->item_model->getName($rs->ItemCode),
						'UomEntry' => $rs->UomEntry2,
						'unitMsr' => $rs->unitMsr2,
						'PickQtty' => round($rs->BasePickQty, 2),
						'PackQtty' => round($rs->BasePackQty, 2),
						'balance' => $balance < 0 ? 0 : round($balance , 2),
						'color' => $balance <= 0 ? 'background-color:#ebf1e2;' : '',
						'bcolor' => is_null($barcode) ? '#0032e7' : '#000000'
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
		$pallet_id = $this->input->post('pallet_id');
		$qty = $this->input->post('qty');

		$doc = $this->pack_model->get_by_code($code);

		if(!empty($doc))
		{
			//--- get itemcode by barcode
			$item = $this->item_model->get_item_code_uom_by_barcode($barcode);

			if(!empty($item))
			{
				$row = $this->pack_model->get_detail_by_item($code, $item->ItemCode);

				//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
				if(!empty($row))
				{
					$baseQty = $this->item_model->get_base_qty($item->ItemCode, $item->UomEntry);
					//--- แปลงเป็น หน่วยนับย่อย
					$invQty = $qty * $baseQty;

					//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
					$remain = $row->BasePickQty - $row->BasePackQty;

					if($remain < $invQty)
					{
						$sc = FALSE;
						$this->error = "สินค้าเกิน กรุณาตรวจสอบ";
					}
					else
					{
						$packed = $row->BasePackQty + $invQty;
						$balance = $row->BasePickQty - $packed;

						$this->db->trans_begin();

						$arr = array(
							'packCode' => $code,
							'orderCode' => $doc->orderCode,
							'pickCode' => $doc->pickCode,
							'ItemCode' => $row->ItemCode,
							'UomEntry' => $row->UomEntry2,
							'UomCode' => $row->UomCode2,
							'unitMsr' => $row->unitMsr2,
							'BaseQty' => $baseQty,
							'BasePackQty' => $invQty,
							'box_id' => $box_id,
							'pallet_id' => $pallet_id,
							'user_id' => $this->user->id
						);


						if(! $this->packing_model->update_pack_details($arr))
						{
							$sc = FALSE;
							$this->error = "Update pack details failed";
						}
						else
						{
							if(! $this->packing_model->update_pack_row($row->id, $invQty))
							{
								$sc = FALSE;
								$this->error = "Update pack row failed";
							}
							else
							{
								$arr = array(
									'id' => $row->id,
									'packed' => round($packed, 2),
									'pack_qty' => round($invQty, 2),
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
			}
			else
			{
				$sc = FALSE;
				$this->error = "บาร์โค้ดไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Pack List No";
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
		$pallet_id = $this->input->post('pallet_id');
		$qty = $this->input->post('qty');

		$doc = $this->pack_model->get_by_code($code);

		if(!empty($doc))
		{
			//--- get itemcode by barcode
			$item = $this->item_model->get($ItemCode);

			if(! empty($item))
			{
				$row = $this->pack_model->get_detail_by_item($code, $ItemCode);

				//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
				if(!empty($row))
				{
					$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);
					//--- แปลงเป็น หน่วยนับย่อย
					$invQty = $qty * $baseQty;

					//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
					$remain = $row->BasePickQty - $row->BasePackQty;

					if($remain < $invQty)
					{
						$sc = FALSE;
						$this->error = "สินค้าเกิน กรุณาตรวจสอบ";
					}
					else
					{
						$packed = $row->BasePackQty + $invQty;
						$balance = $row->BasePickQty - $packed;

						$this->db->trans_begin();

						$arr = array(
							'packCode' => $code,
							'orderCode' => $doc->orderCode,
							'pickCode' => $doc->pickCode,
							'ItemCode' => $row->ItemCode,
							'UomEntry' => $row->UomEntry2,
							'UomCode' => $row->UomCode2,
							'unitMsr' => $row->unitMsr2,
							'BaseQty' => $baseQty,
							'BasePackQty' => $invQty,
							'box_id' => $box_id,
							'pallet_id' => $pallet_id,
							'user_id' => $this->user->id
						);


						if(! $this->packing_model->update_pack_details($arr))
						{
							$sc = FALSE;
							$this->error = "Update pack details failed";
						}
						else
						{
							if(! $this->packing_model->update_pack_row($row->id, $invQty))
							{
								$sc = FALSE;
								$this->error = "Update pack row failed";
							}
							else
							{
								$arr = array(
									'id' => $row->id,
									'packed' => round($packed, 2),
									'pack_qty' => round($invQty, 2),
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
			}
			else
			{
				$sc = FALSE;
				$this->error = "สินค้าไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Pack List No";
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
		$pallet_id = $this->input->post('pallet_id');

		if(!empty($code) && !empty($pallet_id))
		{
			$box_no = $this->packing_model->get_last_box_no($code) + 1;
			$box_id = $this->packing_model->add_new_box($code, $box_no, $box_no, $pallet_id);
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



	public function add_pallet() {
		$sc = TRUE;
		$id = "";

		$code = $this->input->post('packCode');

		if(!empty($code))
		{
			$pallet_code = $this->new_pallet_code();

			if(!empty($pallet_code))
			{
				$id = $this->pallet_model->add($pallet_code);

				if($id)
				{
					$arr = array(
						'pallet_id' => $id,
						'PackCode' => $code
					);

					$this->pallet_model->add_row($arr);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Add pallet failed";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: PackCode";
		}

		echo $sc === TRUE ? $id : $this->error;
	}




	public function get_pallet_by_code()
	{
		$sc = TRUE;
		$id = NULL;
		$packCode = $this->input->get('packCode');
		$palletCode = $this->input->get('palletCode');

		$pallet = $this->pallet_model->get_pallet_by_code($palletCode);

		if(!empty($pallet) && $pallet->Status == 'O')
		{
			$id = $pallet->id;

			$row = $this->pallet_model->get_pallet_row($id, $packCode);

			if(empty($row))
			{
				$arr = array(
					'pallet_id' => $id,
					'PackCode' => $packCode
				);

				if(! $this->pallet_model->add_row($arr))
				{
					$sc = FALSE;
					$this->error = "Update pallet row failed";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Pallet code";
		}


		echo $sc === TRUE ? $id : $this->error;
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
					'pallet_id' => $box->pallet_id,
					'pallet_code' => $box->palletCode,
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



	public function get_pallet_list()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $id = $this->input->get('pallet_id');
    $pallet_list = $this->pallet_model->get_pallet_list($code);

    if(!empty($pallet_list))
    {
      $ds = array();

      foreach($pallet_list as $rs)
      {
        $arr = array(
          'id' => $rs->id,
          'code' => $rs->code,
					'qty' => $this->pallet_model->count_box($rs->id),
          'class' => $rs->id == $id ? 'btn-primary' : ''
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'no pallet';

  }



	public function get_pallet_detail()
	{
		$sc = TRUE;
		$ds = array();
		$row = array();

		$pallet_id = $this->input->get('pallet_id');
		$packCode = $this->input->get('code');

		$pallet = $this->pallet_model->get($pallet_id);

		if(!empty($pallet))
		{

			$detail = $this->packing_model->get_boxes_by_pallet_id($packCode, $pallet_id);

			if(!empty($detail))
			{
				foreach($detail as $rs)
				{
					$arr = array(
						"box_id" => $rs->box_id,
						"box_no" => $rs->box_no,
						"pallet_id" => $rs->pallet_id,
						"qty" => round($rs->qty,2)
					);

					array_push($row, $arr);
				}

				$ds = array(
					"code" => $pallet->code,
					"rows" => $row
				);
			}
			else
			{
				$ds = array(
					"code" => $pallet->code,
					"nodata" => "nodata"
				);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Pallet id";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function get_no_pallet_box()
	{
		$sc = TRUE;
		$ds = array();

		$packCode = $this->input->get('code');

		$boxes = $this->packing_model->get_no_pallet_box($packCode);

		if(!empty($boxes))
		{
			foreach($boxes as $box)
			{
				$arr = array(
					'box_id' => $box->box_id,
					'box_no' => $box->box_no,
					'qty' => round($box->qty, 2)
				);

				array_push($ds, $arr);
			}
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	function add_box_to_pallet()
	{
		$sc = TRUE;
		$pallet_id = $this->input->post('pallet_id');
		$box_list = explode('-', $this->input->post('box_list'));

		if(!empty($box_list))
		{
			if(! $this->packing_model->update_pallet_box($pallet_id, $box_list))
			{
				$sc = FALSE;
				$this->error = "add pallet box failed";
			}
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function remove_pallet_box()
	{
		$sc = TRUE;
		$box_no = $this->input->post('box_no');
		$box_id = $this->input->post('box_id');

		$arr = array(
			'pallet_id' => NULL
		);

		if(! $this->packing_model->update_box($box_id, $arr))
		{
			$sc = FALSE;
			$this->error = "Pull out Box no {{$box_no}} failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function remove_pallet_row()
	{
		$sc = TRUE;
		$pallet_id = $this->input->post('pallet_id');
		$packCode = $this->input->post('packCode');

		if(! $this->pallet_model->delete_row($pallet_id, $packCode))
		{
			$sc = FALSE;
			$this->error = "Remove Pallet row failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
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
				//--- check pallet and box
				$noPalletBox = $this->packing_model->get_no_pallet_box($code);

				if(empty($noPalletBox))
				{
					$details = $this->pack_model->get_pack_details($code);

					if(!empty($details))
					{
						$this->db->trans_begin();

						foreach($details as $row)
						{
							if($sc === FALSE)
							{
								break;
							}

							$PackQtty = $row->BasePackQty;

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

										$bufferQty = $bf->BasePickQty;
										$packQty = $bufferQty >= $PackQtty ? $PackQtty : $bufferQty;
										$BasePackQty = $packQty;
										$bufferQty -= $packQty;


										$arr = array(
											'packCode' => $row->packCode,
											'pickCode' => $row->pickCode,
											'OrderCode' => $row->orderCode,
											'ItemCode' => $row->ItemCode,
											'UomEntry' => $row->UomEntry,
											'UomCode' => $row->UomCode,
											'unitMsr' => $row->unitMsr,
											'BaseQty' => $row->BaseQty,
											'BasePackQty' => $BasePackQty,
											'BinCode' => $bf->BinCode,
											'box_id' => $row->box_id,
											'pallet_id' => $row->pallet_id,
											'user_id' => $this->user->id
										);


										if(! $this->packing_model->add_pack_result($arr))
										{
											$sc = FALSE;
											$this->error = "Add result buffer failed";
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
												if(! $this->packing_model->update_buffer($bf->id, $BasePackQty))
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
							$this->pack_model->update_sap_pack_code($doc->orderCode, $doc->code);

							if(getConfig('CLOSE_PICK_LINE_STATUS') === 'pack')
							{
								$pick_id = $this->pick_model->get_pick_id_by_code($doc->pickCode);
								$this->pick_model->close_pick_line_status($pick_id, $doc->orderCode);								
							}

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
					$box_no = "";
					$i = 1;

					foreach($noPalletBox as $rs)
					{
						$box_no .= $i === 1 ? $rs->box_no : ", {$rs->box_no}";
						$i++;
					}

					$sc = FALSE;
					$this->error = "กล่องที่ {$box_no} ไม่อยู่ใน พาเลท กรุณาตรวจสอบ";
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
						'qty' => round($rs->BasePackQty, 2),
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
						$row = $this->pack_model->get_detail_by_item($code, $detail->ItemCode);

						if(!empty($row))
						{
							$baseDif = $row->BasePackQty - $detail->BasePackQty;

							if($baseDif < 0)
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
									$basePackQty = $detail->BasePackQty * -1;

									if(! $this->packing_model->update_pack_row($row->id, $basePackQty))
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

				$row = $this->pack_model->get_detail_by_item($code, $detail->ItemCode);

				if(!empty($row))
				{
					$baseDif = $row->BasePackQty - $detail->BasePackQty;

					if($baseDif < 0)
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
							$basePackQty = $detail->BasePackQty * -1;

							if(! $this->packing_model->update_pack_row($row->id, $basePackQty))
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



	public function print_pallet($pallet_id)
	{
		$this->title = "Print Label";
		$this->load->library('printer');

		$arr = array($pallet_id);
		$pallets = $this->pallet_model->get_selected_pallet($arr);

		$ds = array(
			'pallets' => $pallets
		);

		$this->load->view('print/print_pallet_label', $ds);
	}



	public function print_selected_pallet($pallet_ids)
	{
		$this->title = "Print Label";
		$this->load->library('printer');
		$arr = explode("-", $pallet_ids);

		$pallets = $this->pallet_model->get_selected_pallet($arr);

		$ds = array(
			'pallets' => $pallets
		);

		$this->load->view('print/print_pallet_label', $ds);
	}







	public function print_box($code, $box_id, $copies = 0)
	{
		$this->title = "Print Label";
		$this->load->model('sales_order_model');
		$this->load->library('printer');

		$arr = array($box_id);

		$boxes = array();

		if($copies > 0)
		{
			$i = 1;

			while($i <= $copies)
			{
				$box = new stdClass();
				$box->box_no = $i;
				$boxes[] = $box;
				$i++;
			}

			$box = $boxes;
		}
		else
		{
			$box = $this->packing_model->get_selected_boxes($arr);
		}


		$doc = $this->pack_model->get_by_code($code);
		$order = $this->getOrder($doc->orderCode);
		$order->BeginStr = $this->sales_order_model->get_prefix($order->Series);


		$ds = array(
			'doc' => $doc,
			'boxes' => $box,
			'last_box_no' => $copies > 0 ? $copies : $this->packing_model->get_last_box_no($doc->code),
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



	public function new_pallet_code()
	{
		$prefix = date('ym')."-";
		$run_digit = 4;

		$code = $this->pallet_model->get_max_code($prefix);

		if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
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

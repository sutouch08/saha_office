<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Picking extends PS_Controller
{
	public $menu_code = 'PICKING';
	public $menu_sub_group_code = 'PICK';
	public $menu_group_code = 'IC';
	public $title = 'Pick List';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'picking';
		$this->load->model('picking_model');
		$this->load->model('pick_model');
		$this->load->model('stock_model');
		$this->load->model('item_model');
		$this->load->model('pick_list_logs_model');
  }



  public function index()
  {
		$this->title = "รอจัด";

		$filter = array(
			'WebCode' => get_filter('WebCode', 'pick_WebCode', ''),
			'SoNo' => get_filter('SoNo', 'pick_SoNo', ''),
			'Uname' => get_filter('Uname', 'pick_Uname', ''),
			'fromDate' => get_filter('fromDate', 'pick_fromDate', ''),
			'toDate' => get_filter('toDate', 'pick_toDate', ''),
			'order_by' => get_filter('order_by', 'pick_order_by', 'DocNum'),
			'sort_by' => get_filter('sort_by', 'pick_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->picking_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->picking_model->get_list($filter, $perpage, $this->uri->segment($segment), 'R');

		if(!empty($rs))
		{
			foreach($rs as $rd)
			{
				$rd->sum_so = $this->pick_model->count_so($rd->AbsEntry);
				$rd->sum_item_line = $this->pick_model->count_item_line($rd->AbsEntry);
			}
		}

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('picking/picking_list', $filter);
  }


	public function process_list()
	{
		$this->title = "กำลังจัด";

		$filter = array(
			'WebCode' => get_filter('WebCode', 'pick_WebCode', ''),
			'SoNo' => get_filter('SoNo', 'pick_SoNo', ''),
			'Uname' => get_filter('Uname', 'pick_Uname', ''),
			'fromDate' => get_filter('fromDate', 'pick_fromDate', ''),
			'toDate' => get_filter('toDate', 'pick_toDate', ''),
			'order_by' => get_filter('order_by', 'pick_order_by', 'DocNum'),
			'sort_by' => get_filter('sort_by', 'pick_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->picking_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->picking_model->get_list($filter, $perpage, $this->uri->segment($segment), 'P');

		if(!empty($rs))
		{
			foreach($rs as $rd)
			{
				$rd->sum_so = $this->pick_model->count_so($rd->AbsEntry);
				$rd->sum_item_line = $this->pick_model->count_item_line($rd->AbsEntry);
			}
		}

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('picking/process_list', $filter);
	}



	public function is_document_avalible()
  {
    $absEntry = $this->input->get('AbsEntry');
    $uuid = $this->input->get('uuid');
    if( ! $this->pick_model->is_document_avalible($absEntry, $uuid))
    {
      echo "not_available";
    }
    else
    {
      echo "available";
    }
  }


	public function update_uuid()
  {
    $sc = TRUE;
    $absEntry = trim($this->input->post('AbsEntry'));
    $uuid = trim($this->input->post('uuid'));

    if( ! empty($uuid))
    {
      return $this->pick_model->update_uuid($absEntry, $uuid);
    }
  }

	public function process($absEntry, $uuid)
	{
		$this->title = "จัดสินค้า";
		$doc = $this->pick_model->get($absEntry);
		if(!empty($doc) && ($doc->Status == 'R' OR $doc->Status == 'P'))
		{
			if($doc->Status == 'R')
			{
				$arr = array(
					'StartPick' => now(),
					'Status' => 'P', //--- pick
					'state' => 'pick'
				);

				$this->pick_model->update($absEntry, $arr);

				$this->pick_model->set_rows_status($absEntry, 'P');

				$this->pick_list_logs_model->add('pick', $doc->DocNum);
			}

			$details = $this->picking_model->get_details($absEntry);

			if(!empty($details))
			{
				foreach($details as $rs)
				{
					$rs->barcode = $this->item_model->get_barcode_uom($rs->ItemCode, $rs->UomEntry);
					$rs->stock_in_zone = $this->get_stock_in_zone($rs->ItemCode);
				}
			}

			$data = array(
				'doc' => $doc,
				'details' => $details,
				'orderList' => $this->pick_model->get_order_list($absEntry)
			);

			$this->pick_model->update_uuid($absEntry, $uuid);

			$this->load->view('picking/picking_process', $data);
		}
		else
		{
			$this->error_page();
		}
	}


	public function get_stock_in_zone($ItemCode)
	{
		$sc = "ไม่มีสินค้า";
		$stock = $this->stock_model->get_stock_in_zone($ItemCode);

		if(!empty($stock))
		{
			$sc = "";

			foreach($stock as $rs)
			{
				$prepared = $this->picking_model->get_buffer_zone($rs->BinCode, $ItemCode);
				$qty = $rs->qty - $prepared;
				if($qty > 0)
				{
					$sc .= $rs->code.' : '.($rs->qty - $prepared).'<br/>';
				}
			}
		}

		return empty($sc) ? 'ไม่พบสินค้า' : $sc;
	}


	//---- สินค้าคงเหลือในโซน ลบด้วย สินค้าที่จัดไปแล้ว
  public function get_stock_zone($ItemCode, $BinCode)
  {
    //---- สินค้าคงเหลือในโซน
    $stock = $this->stock_model->getStockZone($ItemCode, $BinCode);

    //--- ยอดจัดสินค้าที่จัดออกจากโซนนี้ไปแล้ว แต่ยังไม่ได้ตัด
    $prepared = $this->picking_model->get_sku_buffer_zone($BinCode, $ItemCode);


    return $stock - $prepared;

  }



	public function pick_item()
	{
		$sc = TRUE;
		$ds = array();
		$absEntry = $this->input->post('AbsEntry');
		$docNum = trim($this->input->post('DocNum'));
		$binCode = $this->input->post('BinCode');
		$orderCode = $this->input->post('orderCode');
		$barcode = trim($this->input->post('barcode'));
		$qty = $this->input->post('qty');

		if($barcode != "" && ! is_null($barcode))
		{
			//--- get itemcode by barcode
			$item = $this->item_model->get_item_code_uom_by_barcode($barcode);
			///----- end new

			if(! empty($item))
			{
				$orderCode = empty($orderCode) ? $this->picking_model->get_order_code($absEntry, $item->ItemCode) : $orderCode;

				if(! empty($orderCode))
				{
					$detail = $this->picking_model->get_detail_by_item($absEntry, $orderCode, $item->ItemCode);

					//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
					if(! empty($detail))
					{
						//---- ตัวคูณ หน่วยนับที่ยิงมา
						$baseQty = $this->item_model->get_base_qty($item->ItemCode, $item->UomEntry);

						//--- แปลงเป็น หน่วยนับย่อย
						$invQty = $qty * $baseQty;

						//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
						$remain = $detail->BaseRelQty - $detail->BasePickQty;

						if($remain >= $invQty)
						{
							//--- ตรวจสอบสต็อกในโซน พอจัดมั้ย
							$stock = $this->get_stock_zone($item->ItemCode, $binCode);

							//--- ถ้ามีสต็อกพอ จัดได้
							if($stock >= $invQty)
							{
								//----- แปลงจำนวนจากหน่วยย่อยไปเป็นหน่วยที่สั่งจัด
								$picked = $detail->BasePickQty + $invQty;
								$balance = $detail->BaseRelQty - $picked;

								$this->db->trans_begin();

								if(! $this->picking_model->update_picked_qty($detail->id, $invQty))
								{
									$sc = FALSE;
									$this->error = "Update Pick Detail failed";
								}

								if($sc === TRUE)
								{
									$buffer = $this->picking_model->get_unique_buffer($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($buffer))
									{
										if(! $this->picking_model->update_buffer_qty($buffer->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update Buffer failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_buffer($arr))
										{
											$sc = FALSE;
											$this->error = "Add Buffer failed";
										}
									}
								}


								if($sc === TRUE)
								{
									$prepare = $this->picking_model->get_unique_prepare($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($prepare))
									{
										if(! $this->picking_model->update_prepare_qty($prepare->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update picking detail failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_prepare($arr))
										{
											$sc = FALSE;
											$this->error = "Add Picking detail failed";
										}
									}
								}


								if($sc === TRUE)
								{
									$this->db->trans_commit();

									$arr = array(
										'id' => $detail->id,
										'picked' => round($picked, 2),
										'balance' => round($balance, 2)
									);

									array_push($ds, $arr);
								}
								else
								{
									$this->db->trans_rollback();
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "สินค้าใน Location ที่กำหนดไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
						}
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "กรุณาระบุ SO";
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
			$this->error = "Missing required parameter : barcode";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	public function pick_with_option()
	{
		$sc = TRUE;
		$ds = array();
		$absEntry = $this->input->post('AbsEntry');
		$docNum = trim($this->input->post('DocNum'));
		$binCode = $this->input->post('BinCode');
		$orderCode = $this->input->post('orderCode');
		$ItemCode = trim($this->input->post('ItemCode'));
		$UomEntry = $this->input->post('UomEntry');
		$qty = $this->input->post('qty');

		if($this->input->post())
		{
			//--- get item
			$item = $this->item_model->get($ItemCode);

			if(! empty($item))
			{
				$orderCode = empty($orderCode) ? $this->picking_model->get_order_code($absEntry, $ItemCode) : $orderCode;

				if(!empty($orderCode))
				{
					$detail = $this->picking_model->get_detail_by_item($absEntry, $orderCode, $ItemCode);

					//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
					if(! empty($detail))
					{
						//---- ตัวคูณ หน่วยนับที่ยิงมา
						$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);

						//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
						$invQty = $qty * $baseQty;

						//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
						$remain = $detail->BaseRelQty - $detail->BasePickQty;

						if($remain >= $invQty)
						{
							//--- ตรวจสอบสต็อกในโซน พอจัดมั้ย
							$stock = $this->get_stock_zone($ItemCode, $binCode);

							//--- ถ้ามีสต็อกพอ จัดได้
							if($stock >= $invQty)
							{
								$picked = $detail->BasePickQty + $invQty;
								$balance = $detail->BaseRelQty - $picked;

								$this->db->trans_begin();

								if(! $this->picking_model->update_picked_qty($detail->id, $invQty))
								{
									$sc = FALSE;
									$this->error = "Update Pick Detail failed";
								}

								if($sc === TRUE)
								{

									$buffer = $this->picking_model->get_unique_buffer($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($buffer))
									{
										if(! $this->picking_model->update_buffer_qty($buffer->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update Buffer failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_buffer($arr))
										{
											$sc = FALSE;
											$this->error = "Add Buffer failed";
										}
									}

								}


								if($sc === TRUE)
								{
									$prepare = $this->picking_model->get_unique_prepare($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($prepare))
									{
										if(! $this->picking_model->update_prepare_qty($prepare->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update picking detail failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_prepare($arr))
										{
											$sc = FALSE;
											$this->error = "Add Picking detail failed";
										}
									}
								}


								if($sc === TRUE)
								{
									$this->db->trans_commit();

									$arr = array(
										'id' => $detail->id,
										'picked' => round($picked, 2),
										'balance' => round($balance, 2)
									);

									array_push($ds, $arr);
								}
								else
								{
									$this->db->trans_rollback();
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "สินค้าใน Location ที่กำหนดไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "กรุณาระบุ SO";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "รหัสสินค้าไม่ถูกต้อง";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	public function pick_from_cancle()
	{
		$this->load->model('cancle_model');
		$sc = TRUE;
		$ds = array();
		$absEntry = $this->input->post('AbsEntry');
		$docNum = trim($this->input->post('DocNum'));
		$pick_detail_id = $this->input->post('pick_detail_id');
		$cancle_id = $this->input->post('cancle_id');
		$qty = $this->input->post('qty');

		if($this->input->post())
		{

			//--- get cancle data
			$cancle = $this->cancle_model->get($cancle_id);

			if(!empty($cancle))
			{
				$binCode = $cancle->BinCode;
				$ItemCode = $cancle->ItemCode;
				$UomEntry = $cancle->UomEntry;

				if($qty <= $cancle->BasePickQty)
				{
					$detail = $this->picking_model->get_detail($pick_detail_id);

					//--- ถ้ามีแสดงว่า หน่วยนับตรงกัน
					if(! empty($detail))
					{
						$orderCode = $detail->OrderCode;

						//---- ตัวคูณ หน่วยนับที่ยิงมา
						$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);

						//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
						$invQty = $qty * $baseQty;

						//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
						$remain = $detail->BaseRelQty - $detail->BasePickQty;

						if($remain >= $invQty)
						{
							//--- ตรวจสอบสต็อกในโซน พอจัดมั้ย
							$stock = $this->get_stock_zone($ItemCode, $binCode);

							//--- ถ้ามีสต็อกพอ จัดได้
							if($stock >= $invQty)
							{
								$picked = $detail->BasePickQty + $invQty;
								$balance = $detail->BaseRelQty - $picked;

								$this->db->trans_begin();

								if(! $this->picking_model->update_picked_qty($detail->id, $invQty))
								{
									$sc = FALSE;
									$this->error = "Update Pick Detail failed";
								}

								if($sc === TRUE)
								{

									$buffer = $this->picking_model->get_unique_buffer($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($buffer))
									{
										if(! $this->picking_model->update_buffer_qty($buffer->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update Buffer failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_buffer($arr))
										{
											$sc = FALSE;
											$this->error = "Add Buffer failed";
										}
									}

								}


								if($sc === TRUE)
								{
									$prepare = $this->picking_model->get_unique_prepare($absEntry, $orderCode, $detail->ItemCode, $binCode, $detail->UomEntry2);

									if(!empty($prepare))
									{
										if(! $this->picking_model->update_prepare_qty($prepare->id, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update picking detail failed";
										}
									}
									else
									{
										$arr = array(
											'AbsEntry' => $absEntry,
											'DocNum' => $docNum,
											'OrderCode' => $orderCode,
											'ItemCode' => $detail->ItemCode,
											'ItemName' => $detail->ItemName,
											'UomEntry' => $detail->UomEntry2,
											'UomCode' => $detail->UomCode2,
											'unitMsr' => $detail->unitMsr2,
											'BasePickQty' => $invQty,
											'BinCode' => $binCode,
											'user_id' => $this->user->id,
											'uname' => $this->user->uname
										);

										if(! $this->picking_model->add_prepare($arr))
										{
											$sc = FALSE;
											$this->error = "Add Picking detail failed";
										}
									}
								}

								//--- update cancle
								if($sc === TRUE)
								{
									$cancle_balance = $cancle->BasePickQty - $qty;

									if($cancle_balance == 0) {
										if(! $this->cancle_model->delete($cancle->id))
										{
											$sc = FALSE;
											$this->error = "Remove Cancle failed";
										}
									}
									else
									{
										$arr = array(
											'BasePickQty' => $cancle_balance
										);

										if(! $this->cancle_model->update($cancle->id, $arr))
										{
											$sc = FALSE;
											$this->error = "Update Cancle failed";
										}
									}
								}


								if($sc === TRUE)
								{
									$this->db->trans_commit();

									$arr = array(
										'id' => $detail->id,
										'picked' => round($picked, 2),
										'balance' => round($balance, 2)
									);

									array_push($ds, $arr);
								}
								else
								{
									$this->db->trans_rollback();
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "สินค้าใน Location ที่กำหนดไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Pick detail id";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "จำนวนที่ต้องการ เกินกว่าจำนวนที่มีใน Canceled";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบรายการในโซนยกเลิก";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	//----- ดึงรายการที่ pick ไปแล้ว เพื่อไปแก้ไข
	public function get_picking_details()
	{
		$sc = TRUE;
		$data = array();

		$id = $this->input->post('pick_detail_id');

		$ds = $this->picking_model->get_detail($id);

		if(!empty($ds))
		{
			//--- get picking details
			$details = $this->picking_model->get_prepare($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry2);

			if(!empty($details))
			{
				$this->load->model('zone_model');

				foreach($details as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'OrderCode' => $rs->OrderCode,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'unitMsr' => $rs->unitMsr,
						'Qty' => round($rs->BasePickQty, 2),
						'QtyLabel' => number($rs->BasePickQty, 2),
						'BinCode' => $this->zone_model->getName($rs->BinCode)
					);

					array_push($data, $arr);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "No item found";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบรายการ";
		}

		echo $sc === TRUE ? json_encode($data) : $this->error;
	}



	public function update_picking_qty()
	{
		$sc = TRUE;

		$pick_detail_id = $this->input->post('pick_detail_id');
		$picking_id = $this->input->post('picking_id');
		$qty = $this->input->post('qty');

		//--- picking detail
		$ds = $this->picking_model->get_prepare_by_id($picking_id);

		if(!empty($ds))
		{
			$limit = $ds->BasePickQty;

			if( $qty <= 0 OR $qty > $limit )
			{
				$sc = FALSE;
				$this->error = "จำนวนต้องมากกว่า 0 หรือต้องไม่มากกว่ายอดที่จัดไปแล้ว";
			}

			if($sc === TRUE)
			{
				$this->load->model('buffer_model');
				$buffer = $this->picking_model->get_unique_buffer($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->BinCode, $ds->UomEntry);

				$InvQty = $qty * -1;

				$this->db->trans_begin();
				//--- ถ้าจำนวนเท่ากับที่เคยจัดไป ลบรายการจัดออกได้เลย
				if($qty == $limit)
				{
					//--- delete buffer
					if(! empty($buffer))
					{
						if(! $this->buffer_model->delete($buffer->id))
						{
							$sc = FALSE;
							$this->error = "ลบ Buffer ไม่สำเร็จ";
						}
					}

					//--- delete picking detail
					if(! $this->picking_model->delete_prepare($ds->id))
					{
						$sc = FALSE;
						$this->error = "ลบรายการจัดไม่สำเร็จ";
					}
				}
				else
				{
					//--- update buffer
					if(! $this->picking_model->update_buffer_qty($buffer->id, $InvQty))
					{
						$sc = FALSE;
						$this->error = "แก้ไขจำนวนใน Buffer ไม่สำเร็จ";
					}

					if($sc === TRUE)
					{
						//---- update picking detail
						if(! $this->picking_model->update_prepare_qty($ds->id, $InvQty))
						{
							$sc = FALSE;
							$this->error = "แก้ไขยอดจัดไม่สำเร็จ";
						}
					}
				}


				if($sc === TRUE)
				{
					//--- update pick detail
					if(! $this->picking_model->update_picked_qty($pick_detail_id, $InvQty))
					{
						$sc = FALSE;
						$this->error = "แก้ไขยอดจัดรวมไม่สำเร็จ";
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
			$this->error = "ไม่พบรายการ";
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function get_item_uom_list()
	{
		$sc = TRUE;
		$uom = "";
		$itemCode = trim($this->input->get('ItemCode'));
		$UomEntry = $this->input->get('UomEntry');

		if(!empty($itemCode))
		{
			$item = $this->item_model->get($itemCode);
			if(!empty($item))
			{
				$UomList = $this->item_model->get_uom_list($item->UgpEntry);

				if(!empty($UomList))
				{
					foreach($UomList as $ls)
					{
						$uom .= '<option data-code="'.$ls->UomCode.'" value="'.$ls->UomEntry.'" '.is_selected($ls->UomEntry, $UomEntry).'>'.$ls->UomName.'</option>';
					}

					$uom = array('option' => $uom);

					$uom = json_encode($uom);
				}
			}
		}

		echo $uom;
	}



	public function finish_pick()
	{
		$sc = TRUE;
		$absEntry = $this->input->post('AbsEntry');
		$docNum = trim($this->input->post('DocNum'));

		//--- update picked qty
		$details = $this->picking_model->get_details($absEntry);

		if(!empty($details))
		{
			foreach($details as $rs)
			{
				$rows = $this->pick_model->get_pick_rows_by_item($absEntry, $rs->OrderCode, $rs->ItemCode);

				if(!empty($rows))
				{
					$basePick = $rs->BasePickQty;

					foreach($rows as $row)
					{
						if($basePick > 0)
						{
							$baseDiff = $row->BaseRelQty - $row->BasePickQty;

							if($baseDiff > 0)
							{
								$baseQty = $basePick > $baseDiff ? $baseDiff : $basePick;
								$this->pick_model->update_pick_qtty($row->AbsEntry, $row->PickEntry, $baseQty);
								$basePick -= $baseQty;
							}
						}
					}
				}
			}
		}

		$arr = array(
			'Status' => 'Y',
			'state' => 'picked',
			'FinishPick' => now()
		);

		$this->db->trans_begin();

		if(! $this->pick_model->update($absEntry, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Pick List Status failed";
		}

		if($sc === TRUE)
		{
			if(! $this->pick_model->set_rows_status($absEntry, 'Y'))
			{
				$sc = FALSE;
				$this->error = "Update Pick Rows Status Failed";
			}
		}

		if($sc === TRUE)
		{
			$this->pick_model->set_picked_user($absEntry, $this->user->id, $this->user->uname);
		}

		if($sc === TRUE)
		{
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		if($sc === TRUE)
		{
			$this->pick_list_logs_model->add('picked', $docNum);

			$orderList = $this->pick_model->get_order_list($absEntry);

			if( ! empty($orderList))
			{
				foreach($orderList as $rs)
				{
					$this->pick_model->update_sap_pick_code($rs->OrderCode, $docNum);
				}
			}
		}

		$this->response($sc);
	}


	public function find_bin_code()
	{
		$txt = trim($_REQUEST['term']);
		$ds = array();

		$qr  = "SELECT BinCode FROM OBIN ";
		$qr .= "WHERE SysBin = 'N' ";
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
		$sc = TRUE;

		$code = $this->input->get('binCode');

		if(! $this->picking_model->check_bin_code($code))
		{
			$sc = FALSE;
			$this->error = "Invalid BinCode";
		}

		$this->response($sc);
	}



	public function get_state()
	{
		$absEntry = $this->input->get('AbsEntry');
		$doc = $this->pick_model->get_state($absEntry);

		if(!empty($doc))
		{
			if($doc->Status == 'D')
			{
				$sc = "Canceled";
			}
			else
			{
				if($doc->Status == 'P' OR $doc->Status == 'Y')
				{
					$sc = "ok";
				}
				else
				{
					$sc = $doc->Status;
				}
			}
		}
		else
		{
			$sc = "notfound";
		}

		echo $sc;
	}




	public function remove_pick_row()
	{
		$sc = TRUE;
		$id = $this->input->post('pick_detail_id');

		$ds = $this->picking_model->get_detail($id);

		if(! empty($ds))
		{
			$this->load->model('cancle_model');
			$this->load->model('buffer_model');

			//--- get_buffer and add to cancle
			$buffer = $this->picking_model->get_buffer_by_pick_detail($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry);

			$this->db->trans_begin();

			if(!empty($buffer))
			{
				foreach($buffer as $rs)
				{
					if($sc === FALSE)
					{
						break;
					}

					$arr = array(
						'AbsEntry' => $rs->AbsEntry,
						'DocNum' => $rs->DocNum,
						'OrderCode' => $rs->OrderCode,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'UomEntry' => $rs->UomEntry,
						'UomCode' => $rs->UomCode,
						'unitMsr' => $rs->unitMsr,
						'BasePickQty' => $rs->BasePickQty,
						'BinCode' => $rs->BinCode,
						'user_id' => $this->user->id,
						'uname' => $this->user->uname
					);

					if($this->cancle_model->add($arr))
					{
						//-- remove buffer
						if( ! $this->buffer_model->delete($rs->id))
						{
							$sc = FALSE;
							$this->error = "Delete Bufer failed";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Insert Cancle failed";
					}
				}
			}

			//--- remove picking detail
			if($sc === TRUE)
			{

				if( ! $this->picking_model->delete_prepares($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry))
				{
					$sc = FALSE;
					$this->error = "Delete prepared details failed";
				}
			}

			//--- remove pick detail
			if($sc === TRUE)
			{
				if( ! $this->picking_model->delete_pick_detail($id))
				{
					$sc = FALSE;
					$this->error = "Delete Pick detail failed";
				}
			}

			//--- remove pick row
			if($sc === TRUE)
			{
				if( ! $this->picking_model->delete_pick_row($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry))
				{
					$sc = FALSE;
					$this->error = "Delete pick row failed";
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
			$this->error = "Missing required parameter : row_id";
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function remove_pick_order()
	{
		$this->load->model('cancle_model');
		$this->load->model('buffer_model');
		$sc = TRUE;
		$absEntry = $this->input->post('absEntry');
		$orderCode = $this->input->post('orderCode');

		if( ! empty($absEntry) && ! empty($orderCode))
		{
			$doc = $this->pick_model->get($absEntry);

			if( ! empty($doc))
			{
				$rows = $this->picking_model->get_details_by_order($absEntry, $orderCode);

				if( ! empty($rows))
				{
					$this->db->trans_begin();

					foreach($rows as $ds)
					{
						if($sc === FALSE)
						{
							break;
						}
						//--- get_buffer and add to cancle
						$buffer = $this->picking_model->get_buffer_by_pick_detail($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry);

						if(!empty($buffer))
						{
							foreach($buffer as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}

								$arr = array(
									'AbsEntry' => $rs->AbsEntry,
									'DocNum' => $rs->DocNum,
									'OrderCode' => $rs->OrderCode,
									'ItemCode' => $rs->ItemCode,
									'ItemName' => $rs->ItemName,
									'UomEntry' => $rs->UomEntry,
									'UomCode' => $rs->UomCode,
									'unitMsr' => $rs->unitMsr,
									'BasePickQty' => $rs->BasePickQty,
									'BinCode' => $rs->BinCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if($this->cancle_model->add($arr))
								{
									//-- remove buffer
									if( ! $this->buffer_model->delete($rs->id))
									{
										$sc = FALSE;
										$this->error = "Delete Bufer failed";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "Insert Cancle failed";
								}
							}
						}

						//--- remove picking detail
						if($sc === TRUE)
						{

							if( ! $this->picking_model->delete_prepares($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry))
							{
								$sc = FALSE;
								$this->error = "Delete prepared details failed";
							}
						}

						//--- remove pick detail
						if($sc === TRUE)
						{
							if( ! $this->picking_model->delete_pick_detail($ds->id))
							{
								$sc = FALSE;
								$this->error = "Delete Pick detail failed";
							}
						}

						//--- remove pick row
						if($sc === TRUE)
						{
							if( ! $this->picking_model->delete_pick_row($ds->AbsEntry, $ds->OrderCode, $ds->ItemCode, $ds->UomEntry))
							{
								$sc = FALSE;
								$this->error = "Delete pick row failed";
							}
						}
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
				$this->error = "Invalid Document Entry";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : AbsEntry or OrderCode";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function clear_filter()
	{
		$filter = array(
			'pick_WebCode',
			'pick_SoNo',
			'pick_Uname',
			'pick_Status',
			'pick_fromDate',
			'pick_toDate',
			'pick_order_by',
			'pick_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

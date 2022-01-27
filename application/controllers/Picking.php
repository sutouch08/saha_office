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

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('picking/process_list', $filter);
	}




	public function process($absEntry)
	{
		$this->title = "จัดสินค้า";
		$doc = $this->pick_model->get($absEntry);
		if(!empty($doc) && $doc->Canceled == 'N' && ($doc->Status == 'R' OR $doc->Status == 'P'))
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
				'details' => $details
			);

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
		$barcode = trim($this->input->post('barcode'));
		$qty = $this->input->post('qty');

		if($barcode != "" && ! is_null($barcode))
		{
			//--- get itemcode by barcode
			$item = $this->item_model->get_item_code_uom_by_barcode($barcode);
			///----- end new

			if(! empty($item))
			{
				$detail = $this->picking_model->get_detail_by_item_uom($absEntry, $item->ItemCode, $item->UomEntry);

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
							$pickQty = $invQty/$detail->BaseQty;

							$picked = $detail->PickQtty + $pickQty;
							$balance = $detail->RelQtty - $picked;

							$this->db->trans_begin();

							if(! $this->picking_model->update_picked_qty($detail->id, $pickQty, $invQty))
							{
								$sc = FALSE;
								$this->error = "Update Pick Detail failed";
							}

							if($sc === TRUE)
							{
								$arr = array(
									'AbsEntry' => $absEntry,
									'DocNum' => $docNum,
									'ItemCode' => $detail->ItemCode,
									'UomEntry' => $detail->UomEntry,
									'UomCode' => $detail->UomCode,
									'unitMsr' => $detail->unitMsr,
									'BaseQty' => $detail->BaseQty,
									'Qty' => $pickQty,
									'BasePickQty' => $invQty,
									'BinCode' => $binCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if(! $this->picking_model->update_buffer($arr))
								{
									$sc = FALSE;
									$this->error = "Update Buffer failed";
								}
							}


							if($sc === TRUE)
							{
								$arr = array(
									'AbsEntry' => $absEntry,
									'DocNum' => $docNum,
									'ItemCode' => $detail->ItemCode,
									'UomEntry' => $detail->UomEntry,
									'UomCode' => $detail->UomCode,
									'unitMsr' => $detail->unitMsr,
									'BaseQty' => $detail->BaseQty,
									'Qty' => $pickQty,
									'BasePickQty' => $invQty,
									'BinCode' => $binCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if(! $this->picking_model->update_prepare($arr))
								{
									$sc = FALSE;
									$this->error = "Update Picking detail failed";
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
					//---- กรณีหน่วนนับไม่ตรงกับในรายการ
					//---- ดึงรายการที่หน่วยนับ ไม่ตรงกับที่ยิงมา
					$details = $this->picking_model->get_details_by_item_other_uom($absEntry, $item->ItemCode, $item->UomEntry);

					if(!empty($details))
					{
						//---- ตัวคูณ หน่วยนับที่ยิงมา
						$baseQty = $this->item_model->get_base_qty($item->ItemCode, $item->UomEntry);

						//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
						$bcQty = $qty * $baseQty;

						$testQty = $bcQty;

						//---- ทดสอบว่ายอดที่ยิงมามัันเกินที่เหลือมั้ย
						foreach($details as $detail)
						{
							//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
							$remain = $detail->BaseRelQty - $detail->BasePickQty;

							$pickQty = $testQty <= $remain ? $testQty : $remain;
							$testQty -= $pickQty;
						}

						if($testQty > 0)
						{
							$sc = FALSE;
							$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
						}
						else
						{
							//--- ตรวจสอบสต็อกในโซน พอจัดมั้ย
							$stock = $this->get_stock_zone($item->ItemCode, $binCode);

							//--- ถ้ามีสต็อกพอ จัดได้
							if($stock >= $bcQty)
							{

								$this->db->trans_begin();

								//--- วนเพื่อจัดจริง
								foreach($details as $detail)
								{
									if($sc === FALSE)
									{
										break;
									}

									if($bcQty > 0)
									{
										//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
										$remain = $detail->BaseRelQty - $detail->BasePickQty;

										$invQty = $bcQty <= $remain ? $bcQty : $remain;

										$pickQty = $invQty / $detail->BaseQty;

										$picked = $detail->PickQtty + $pickQty;
										$balance = $detail->RelQtty - $picked;

										if(! $this->picking_model->update_picked_qty($detail->id, $pickQty, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update Pick Detail failed";
										}

										if($sc === TRUE)
										{
											$arr = array(
												'AbsEntry' => $absEntry,
												'DocNum' => $docNum,
												'ItemCode' => $detail->ItemCode,
												'UomEntry' => $detail->UomEntry,
												'UomCode' => $detail->UomCode,
												'unitMsr' => $detail->unitMsr,
												'BaseQty' => $detail->BaseQty,
												'Qty' => $pickQty,
												'BasePickQty' => $invQty,
												'BinCode' => $binCode,
												'user_id' => $this->user->id,
												'uname' => $this->user->uname
											);

											if(! $this->picking_model->update_buffer($arr))
											{
												$sc = FALSE;
												$this->error = "Update Buffer failed";
											}
										}


										if($sc === TRUE)
										{
											$arr = array(
												'AbsEntry' => $absEntry,
												'DocNum' => $docNum,
												'ItemCode' => $detail->ItemCode,
												'UomEntry' => $detail->UomEntry,
												'UomCode' => $detail->UomCode,
												'unitMsr' => $detail->unitMsr,
												'BaseQty' => $detail->BaseQty,
												'Qty' => $pickQty,
												'BasePickQty' => $invQty,
												'BinCode' => $binCode,
												'user_id' => $this->user->id,
												'uname' => $this->user->uname
											);

											if(! $this->picking_model->update_prepare($arr))
											{
												$sc = FALSE;
												$this->error = "Update Picking detail failed";
											}
										}

										if($sc === TRUE)
										{
											$arr = array(
												'id' => $detail->id,
												'picked' => round($picked,2),
												'balance' => round($balance, 2)
											);

											array_push($ds, $arr);
										}

										$bcQty -= $invQty;

									} //--- end if
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
							else
							{
								$sc = FALSE;
								$this->error = "สินค้าใน Location ไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
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
		$ItemCode = trim($this->input->post('ItemCode'));
		$UomEntry = $this->input->post('UomEntry');
		$qty = $this->input->post('qty');

		if($this->input->post())
		{
			//--- get item
			$item = $this->item_model->get($ItemCode);

			if(! empty($item))
			{
				$detail = $this->picking_model->get_detail_by_item_uom($absEntry, $ItemCode, $UomEntry);

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
							//----- แปลงจำนวนจากหน่วยย่อยไปเป็นหน่วยที่สั่งจัด
							$pickQty = $invQty/$detail->BaseQty;

							$picked = $detail->PickQtty + $pickQty;
							$balance = $detail->RelQtty - $picked;

							$this->db->trans_begin();

							if(! $this->picking_model->update_picked_qty($detail->id, $pickQty, $invQty))
							{
								$sc = FALSE;
								$this->error = "Update Pick Detail failed";
							}

							if($sc === TRUE)
							{
								$arr = array(
									'AbsEntry' => $absEntry,
									'DocNum' => $docNum,
									'ItemCode' => $detail->ItemCode,
									'UomEntry' => $detail->UomEntry,
									'UomCode' => $detail->UomCode,
									'unitMsr' => $detail->unitMsr,
									'BaseQty' => $detail->BaseQty,
									'Qty' => $pickQty,
									'BasePickQty' => $invQty,
									'BinCode' => $binCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if(! $this->picking_model->update_buffer($arr))
								{
									$sc = FALSE;
									$this->error = "Update Buffer failed";
								}
							}


							if($sc === TRUE)
							{
								$arr = array(
									'AbsEntry' => $absEntry,
									'DocNum' => $docNum,
									'ItemCode' => $detail->ItemCode,
									'UomEntry' => $detail->UomEntry,
									'UomCode' => $detail->UomCode,
									'unitMsr' => $detail->unitMsr,
									'BaseQty' => $detail->BaseQty,
									'Qty' => $pickQty,
									'BasePickQty' => $invQty,
									'BinCode' => $binCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if(! $this->picking_model->update_prepare($arr))
								{
									$sc = FALSE;
									$this->error = "Update Picking detail failed";
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
					//---- กรณีหน่วนนับไม่ตรงกับในรายการ
					//---- ดึงรายการที่หน่วยนับ ไม่ตรงกับที่ยิงมา
					$details = $this->picking_model->get_details_by_item_other_uom($absEntry, $ItemCode, $UomEntry);

					if(!empty($details))
					{
						//---- ตัวคูณ หน่วยนับที่ยิงมา
						$baseQty = $this->item_model->get_base_qty($ItemCode, $UomEntry);

						//--- ถ้าไม่มี แปลงเป็น หน่วยนับย่อย
						$bcQty = $qty * $baseQty;

						$testQty = $bcQty;

						//---- ทดสอบว่ายอดที่ยิงมามัันเกินที่เหลือมั้ย
						foreach($details as $detail)
						{
							//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
							$remain = $detail->BaseRelQty - $detail->BasePickQty;

							$pickQty = $testQty <= $remain ? $testQty : $remain;
							$testQty -= $pickQty;
						}

						if($testQty > 0)
						{
							$sc = FALSE;
							$this->error = "จำนวนสินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
						}
						else
						{
							//--- ตรวจสอบสต็อกในโซน พอจัดมั้ย
							$stock = $this->get_stock_zone($ItemCode, $binCode);

							//--- ถ้ามีสต็อกพอ จัดได้
							if($stock >= $bcQty)
							{

								$this->db->trans_begin();

								//--- วนเพื่อจัดจริง
								foreach($details as $detail)
								{
									if($sc === FALSE)
									{
										break;
									}

									if($bcQty > 0)
									{
										//--- ตรวจสอบว่า ยอดที่ยิงมา มากกว่า ยอดคงเหลือในรายการจัดหรือไม่
										$remain = $detail->BaseRelQty - $detail->BasePickQty;

										$invQty = $bcQty <= $remain ? $bcQty : $remain;

										$pickQty = $invQty / $detail->BaseQty;

										$picked = $detail->PickQtty + $pickQty;
										$balance = $detail->RelQtty - $picked;

										if(! $this->picking_model->update_picked_qty($detail->id, $pickQty, $invQty))
										{
											$sc = FALSE;
											$this->error = "Update Pick Detail failed";
										}

										if($sc === TRUE)
										{
											$arr = array(
												'AbsEntry' => $absEntry,
												'DocNum' => $docNum,
												'ItemCode' => $detail->ItemCode,
												'UomEntry' => $detail->UomEntry,
												'UomCode' => $detail->UomCode,
												'unitMsr' => $detail->unitMsr,
												'BaseQty' => $detail->BaseQty,
												'Qty' => $pickQty,
												'BasePickQty' => $invQty,
												'BinCode' => $binCode,
												'user_id' => $this->user->id,
												'uname' => $this->user->uname
											);

											if(! $this->picking_model->update_buffer($arr))
											{
												$sc = FALSE;
												$this->error = "Update Buffer failed";
											}
										}


										if($sc === TRUE)
										{
											$arr = array(
												'AbsEntry' => $absEntry,
												'DocNum' => $docNum,
												'ItemCode' => $detail->ItemCode,
												'UomEntry' => $detail->UomEntry,
												'UomCode' => $detail->UomCode,
												'unitMsr' => $detail->unitMsr,
												'BaseQty' => $detail->BaseQty,
												'Qty' => $pickQty,
												'BasePickQty' => $invQty,
												'BinCode' => $binCode,
												'user_id' => $this->user->id,
												'uname' => $this->user->uname
											);

											if(! $this->picking_model->update_prepare($arr))
											{
												$sc = FALSE;
												$this->error = "Update Picking detail failed";
											}
										}

										if($sc === TRUE)
										{
											$arr = array(
												'id' => $detail->id,
												'picked' => round($picked,2),
												'balance' => round($balance, 2)
											);

											array_push($ds, $arr);
										}

										$bcQty -= $invQty;

									} //--- end if
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
							else
							{
								$sc = FALSE;
								$this->error = "สินค้าใน Location ไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
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
				$rows = $this->pick_model->get_pick_rows_by_item_uom($absEntry, $rs->ItemCode, $rs->UomEntry);

				if(!empty($rows))
				{
					$picked = $rs->PickQtty;
					$basePick = $rs->BasePickQty;

					foreach($rows as $row)
					{
						if($picked > 0)
						{
							$diff = $row->RelQtty - $row->PickQtty;
							$baseDiff = $row->BaseRelQty - $row->BasePickQty;

							if($diff > 0)
							{
								$qty = $picked > $diff ? $diff : $picked;
								$baseQty = $basePick > $baseDiff ? $baseDiff : $basePick;

								$this->pick_model->update_pick_qtty($row->AbsEntry, $row->PickEntry, $qty, $baseQty);
								$picked -= $qty;
								$basePick -= $baseQty;
							}
						}
						else
						{
							break;
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
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}

		if($sc === TRUE)
		{
			$this->pick_list_logs_model->add('picked', $docNum);
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
			if($doc->Canceled == 'Y')
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

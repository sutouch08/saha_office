<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick extends PS_Controller
{
	public $menu_code = 'PICKLIST';
	public $menu_sub_group_code = 'PICK';
	public $menu_group_code = 'IC';
	public $title = 'Pick List';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'pick';
		$this->load->model('pick_model');
		$this->load->model('stock_model');
		$this->load->model('item_model');
		$this->load->model('pick_list_logs_model');
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'WebCode' => get_filter('WebCode', 'pick_WebCode', ''),
			'SoNo' => get_filter('SoNo', 'pick_SoNo', ''),
			'Uname' => get_filter('Uname', 'pick_Uname', ''),
			'Status' => get_filter('Status', 'pick_Status', 'all'),
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
		$rows = $this->pick_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->pick_model->get_list($filter, $perpage, $this->uri->segment($segment));

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
    $this->load->view('pick/pick_list', $filter);
  }


	public function add_new()
	{
		$this->title = "Create Pick List";
		$this->load->view('pick/pick_add');
	}


	public function add()
	{
		$sc = TRUE;
		if($this->input->post())
		{
			$remark = get_null($this->input->post('remark'));

			$arr = array(
				'DocNum' => $this->get_new_code(),
				'user_id' => $this->user->id,
				'uname' => $this->user->uname,
				'remark' => $remark
			);

			$id = $this->pick_model->add($arr);

			if(! $id)
			{
				$sc = FALSE;
				$this->error = "Create Pick List failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No form data found!";
		}

		echo $sc === TRUE ? $id : $this->error;

	}


	public function edit($AbsEntry)
	{
		$this->title = "Edit Pick List";
		$doc = $this->pick_model->get($AbsEntry);
		if(!empty($doc))
		{
			$rows = $this->pick_model->get_pick_rows($AbsEntry);

			if(!empty($rows))
			{
				$onhand = array();

				foreach($rows as $rs)
				{
					$Line = $this->pick_model->getOrderRow($rs->OrderEntry, $rs->OrderLine);
					$rs->rowNum = $rs->OrderEntry.$rs->OrderLine;
					$rs->CardName = empty($Line) ? NULL : $Line->CardName;
					$rs->ItemCode = empty($Line) ? NULL : $Line->ItemCode;
					$rs->ItemName = empty($Line) ? NULL : $Line->ItemName;
					$rs->OrderQty = empty($Line) ? 0 : $Line->Quantity;
					$rs->OpenQty = empty($Line) ? 0 : $Line->OpenQty;
					$rs->AvailableQty = empty($Line) ? 0 : (($rs->OrderQty - $rs->PrevRelease) > 0 ? $rs->OrderQty - $rs->PrevRelease : 0);
					$rs->Qty = empty($rs->RelQtty) ? $rs->AvailableQty : round($rs->RelQtty, 2);

					if(! isset($onhand[$rs->ItemCode]))
					{
						$onHandStock = $this->stock_model->get_onhand_stock($rs->ItemCode);
						$committed = $this->get_committed_stock($rs->ItemCode);
						$thisPickList = $this->get_committed_stock_by_pick_list($AbsEntry, $rs->ItemCode);
						$onhand[$rs->ItemCode] = $onHandStock - ($committed - $thisPickList);
					}

					$rs->OnHand = $onhand[$rs->ItemCode];
					$onhand[$rs->ItemCode] -= $rs->BaseRelQty;
				}
			}

			$ds = array(
				'doc' => $doc,
				'details' => $rows
			);

			$this->load->view('pick/pick_edit', $ds);
		}
		else
		{
			$this->error_page();
		}
	}



	public function view_detail($AbsEntry)
	{
		$this->title = "Pick Details";
		$doc = $this->pick_model->get($AbsEntry);
		if(!empty($doc))
		{
			$rows = $this->pick_model->get_pick_rows($AbsEntry);

			if(!empty($rows) && $doc->Status == 'N')
			{
				$onhand = array();

				foreach($rows as $rs)
				{
					if(! isset($onhand[$rs->ItemCode]))
					{
						$onHandStock = $this->stock_model->get_onhand_stock($rs->ItemCode);
						$committed = $this->get_committed_stock($rs->ItemCode);
						$thisPickList = $this->get_committed_stock_by_pick_list($AbsEntry, $rs->ItemCode);
						$onhand[$rs->ItemCode] = $onHandStock - ($committed - $thisPickList);
					}

					$rs->OnHand = $onhand[$rs->ItemCode];
					$onhand[$rs->ItemCode] -= $rs->BaseRelQty;
				}
			}

			$ds = array(
				'doc' => $doc,
				'details' => $rows,
				'logs' => $this->pick_list_logs_model->get($doc->DocNum)
			);

			$this->load->view('pick/pick_detail', $ds);
		}
		else
		{
			$this->error_page();
		}
	}




	public function remove_pick_row()
	{
		$sc = TRUE;
		$AbsEntry = $this->input->post('AbsEntry');
		$PickEntry = $this->input->post('PickEntry');

		if(! $this->pick_model->remove_pick_row($AbsEntry, $PickEntry))
		{
			$sc = FALSE;
			$this->error = "ลบรายการไม่สำเร็จ";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function get_committed_stock($ItemCode)
	{
		return $this->pick_model->get_committed_stock($ItemCode);
	}



	public function get_committed_stock_by_pick_list($absEntry, $ItemCode)
	{
		return $this->pick_model->get_committed_stock_by_pick_list($absEntry, $ItemCode);
	}


	public function update_header()
	{
		$sc = TRUE;
		$AbsEntry = $this->input->post('AbsEntry');
		$remark = get_null($this->input->post('remark'));

		if(!empty($AbsEntry))
		{
			$arr = array(
				'remark' => $remark
			);

			if(! $this->pick_model->update($AbsEntry, $arr))
			{
				$sc = FALSE;
				$this->error = "Update failed";
			}
		}

		$this->response($sc);
	}



	public function add_order_to_list()
	{
		$sc = TRUE;
		$AbsEntry = $this->input->post('AbsEntry');
		$DocEntries = $this->input->post('DocEntry');

		$ds = array();

		if(!empty($DocEntries))
		{
			foreach($DocEntries as $DocEntry)
			{
				$details = $this->pick_model->getOpenRows($DocEntry);

				if(!empty($details))
				{
					foreach($details as $rs)
					{
						$PrevRelease = $this->pick_model->get_prev_release_qty($rs->DocEntry, $rs->LineNum);
						$AvailableQty = ($rs->Quantity - $PrevRelease) > 0 ? $rs->Quantity - $PrevRelease : 0;
						$onhand = $this->stock_model->get_onhand_stock($rs->ItemCode);
						$commit = $this->get_committed_stock($rs->ItemCode);
						$OnHand = $onhand - $commit;

						$arr = array(
							'rowNum' => $rs->DocEntry.$rs->LineNum,
							'OrderCode' => $rs->DocNum,
							'OrderEntry' => $rs->DocEntry,
							'OrderLine' => $rs->LineNum,
							'OrderDate' => $rs->DocDate,
							'CardName' => $rs->CardName,
							'ItemCode' => $rs->ItemCode,
							'ItemName' => $rs->ItemName,
							'UomEntry' => $rs->UomEntry,
							'UomEntry2' => $rs->UomEntry2,
							'UomCode' => $rs->UomCode,
							'UomCode2' => $rs->UomCode2,
							'unitMsr' => $rs->unitMsr,
							'unitMsr2' => $rs->unitMsr2,
							'Price' => $rs->Price,
							'PriceLabel' => number($rs->Price, 2),
							'BaseQty' => ($rs->UomEntry == $rs->UomEntry2) ? 1 : $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry),
							'OrderQty' => number($rs->Quantity, 2),
							'OpenQty' => number($rs->OpenQty, 2),
							'PrevRelease' => number($PrevRelease, 2),
							'AvailableQty' => number($AvailableQty, 2),
							'Qty' => $AvailableQty,
							'OnHand' => number($OnHand, 2),
							'red' => ($AvailableQty <= 0 OR $AvailableQty > $OnHand) ? 'red' : ''
						);

						array_push($ds, $arr);
					}
				}
			}


			if(empty($ds))
			{
				$sc = FALSE;
				$this->error = "ไม่พบรายการที่สามารถเพิ่มเข้าเอกสารได้";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Order Selected";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function add_items_to_list()
	{
		$sc = TRUE;
		$data = json_decode($this->input->post('data'));

		$ds = array();

		if(!empty($data))
		{
			foreach($data as $row)
			{
				$rs = $this->pick_model->getOrderRow($row->DocEntry, $row->LineNum);

				if(!empty($rs))
				{
					$PrevRelease = $this->pick_model->get_prev_release_qty($rs->DocEntry, $rs->LineNum);
					$baseQty = ($rs->UomEntry == $rs->UomEntry2) ? 1 : $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry);
					$invQty = $rs->InvQty;
					$AvailableQty = ($invQty - $PrevRelease) > 0 ? $invQty - $PrevRelease : 0;
					$onhand = $this->stock_model->get_onhand_stock($rs->ItemCode);
					$commit = $this->get_committed_stock($rs->ItemCode);
					$OnHand = $onhand - $commit;

					$AvailableQty = $AvailableQty > 0 ? $AvailableQty/$baseQty : 0;
					$OnHand = $OnHand > 0 ? $OnHand/$baseQty : 0;
					$PrevRelease = $PrevRelease > 0 ? $PrevRelease/$baseQty : 0;

					$arr = array(
						'rowNum' => $rs->DocEntry.$rs->LineNum,
						'OrderCode' => $rs->DocNum,
						'OrderEntry' => $rs->DocEntry,
						'OrderLine' => $rs->LineNum,
						'OrderDate' => $rs->DocDate,
						'CardName' => $rs->CardName,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'UomEntry' => $rs->UomEntry,
						'UomEntry2' => $rs->UomEntry2,
						'UomCode' => $rs->UomCode,
						'UomCode2' => $rs->UomCode2,
						'unitMsr' => $rs->unitMsr,
						'unitMsr2' => $rs->unitMsr2,
						'Price' => $rs->Price,
						'PriceLabel' => number($rs->Price, 2),
						'BaseQty' => $baseQty,
						'OrderQty' => number($rs->Quantity, 2),
						'OpenQty' => number($rs->OpenQty, 2),
						'PrevRelease' => number($PrevRelease, 2),
						'AvailableQty' => number($AvailableQty, 2),
						'Qty' => $AvailableQty,
						'OnHand' => number($OnHand, 2),
						'red' => $AvailableQty > $OnHand ? 'red' : ''
					);

					array_push($ds, $arr);
				}
			}

			if(empty($ds))
			{
				$sc = FALSE;
				$this->error = "ไม่พบรายการที่สามารถเพิ่มเข้าเอกสารได้";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Order Selected";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function get_open_order_details()
	{
		$sc = TRUE;
		$docEntries = $this->input->get('DocEntry');
		$ds = array();

		if(!empty($docEntries))
		{
			foreach($docEntries as $docEntry)
			{
				$details = $this->pick_model->getOpenRows($docEntry);

				if(!empty($details))
				{
					foreach($details as $rs)
					{
						$PrevRelease = $this->pick_model->get_prev_release_qty($rs->DocEntry, $rs->LineNum);
						$baseQty = $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry);
						$invQty = $rs->Quantity * $baseQty;
						$AvailableQty = ($invQty - $PrevRelease) > 0 ? $invQty - $PrevRelease : 0;
						$onhand = $this->stock_model->get_onhand_stock($rs->ItemCode);
						$commit = $this->get_committed_stock($rs->ItemCode);
						$OnHand = $onhand - $commit;

						$PrevRelease = $PrevRelease > 0 ? $PrevRelease/$baseQty : 0;
						$AvailableQty = $AvailableQty > 0 ? $AvailableQty/$baseQty : 0;
						$OnHand = $OnHand > 0 ? $OnHand/$baseQty : 0;

						$arr = array(
							'rowNum' => $rs->DocEntry.$rs->LineNum,
							'OrderCode' => $rs->DocNum,
							'OrderEntry' => $rs->DocEntry,
							'OrderLine' => $rs->LineNum,
							'OrderDate' => $rs->DocDate,
							'CardName' => $rs->CardName,
							'ItemCode' => $rs->ItemCode,
							'ItemName' => $rs->ItemName,
							'Price' => $rs->Price,
							'PriceLabel' => number($rs->Price, 2),
							'OrderQty' => number($rs->Quantity, 2),
							'OpenQty' => number($rs->OpenQty, 2),
							'PrevRelease' => number($PrevRelease, 2),
							'AvailableQty' => number($AvailableQty, 2),
							'OnHand' => number($OnHand, 2),
							'unitMsr' => $rs->unitMsr,
							'red' => ($AvailableQty <= 0 OR $AvailableQty > $OnHand) ? 'red' : '',
							'disabled' => ($OnHand <= 0 OR $AvailableQty <= 0) ? 'yes' : ''
						);

						array_push($ds, $arr);
					}
				}
			}

			if(empty($ds))
			{
				$sc = FALSE;
				$this->error = "ไม่พบรายการที่สามารถเพิ่มเข้าเอกสารได้";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Order Selected";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}




	public function validate_item()
	{
		$sc = TRUE;
		$data = json_decode($this->input->post('data'));
		$error = array();
		$onhand = array();

		if(!empty($data))
		{
			foreach($data as $rs)
			{
				if(empty($onhand[$rs->ItemCode]))
				{
					$onhand[$rs->ItemCode] = $this->stock_model->get_onhand_stock($rs->ItemCode);
					$committed = $this->get_committed_stock($rs->ItemCode);
					$onhand[$rs->ItemCode] -= $committed;
				}

				if($onhand[$rs->ItemCode] < $rs->RelQtty)
				{
					$error[] = $rs->DocEntry.$rs->LineNum;
				}

				$onhand[$rs->ItemCode] -= $rs->RelQtty;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing dataset";
		}

		echo $sc === TRUE ? (empty($error) ? 'success' : json_encode($error)) : $this->error;
	}





	public function save()
	{
		$sc = TRUE;
		$absEntry = $this->input->post('AbsEntry');
		$data = json_decode($this->input->post('data'));

		if(!empty($absEntry))
		{
			if(!empty($data))
			{
				$doc = $this->pick_model->get($absEntry);

				if(!empty($doc))
				{
					if($doc->Status != 'D')
					{
						if($doc->Status == 'N')
						{
							$this->db->trans_begin();

								//--- drop current rows
							if($this->pick_model->drop_current_rows($absEntry))
							{
								$lineNum = 0;

								foreach($data as $rs)
								{
									if($sc === FALSE)
									{
										break;
									}

									$baseQty = ($rs->UomEntry == $rs->UomEntry2) ? 1 : $this->item_model->get_base_qty($rs->ItemCode, $rs->UomEntry);

									//--- insert new row
									$arr = array(
										'AbsEntry' => $absEntry,
										'code' => $doc->DocNum,
										'PickEntry' => $lineNum,
										'OrderCode' => $rs->OrderCode,
										'OrderEntry' => $rs->DocEntry,
										'OrderLine' => $rs->LineNum,
										'OrderDate' => $rs->OrderDate,
										'CardName' => $rs->CardName,
										'ItemCode' => $rs->ItemCode,
										'ItemName' => $rs->ItemName,
										'UomEntry' => $rs->UomEntry,
										'UomEntry2' => $rs->UomEntry2,
										'UomCode' => $rs->UomCode,
										'UomCode2' => $rs->UomCode2,
										'unitMsr' => $rs->unitMsr,
										'unitMsr2' => $rs->unitMsr2,
										'price' => $rs->price,
										'BaseQty' => $baseQty,
										'OrderQty' => $rs->OrderQty,
										'OpenQty' => $rs->OpenQty,
										'RelQtty' => $rs->RelQtty,
										'BaseRelQty' => $rs->RelQtty * $baseQty,
										'PrevRelease' => $rs->PrevRelease
									);

									if(! $this->pick_model->add_row($arr))
									{
										$sc = FALSE;
										$this->error = "Insert failed at row {$lineNum}";
									}
									else
									{
										$lineNum++;
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Drop Current pick details failed";
							}

							if($sc === TRUE)
							{
								$arr = array(
									'state' => 'edit'
								);

								if(! $this->pick_model->update($absEntry, $arr))
								{
									$sc = FALSE;
									$this->error = "Change Pick List State failed";
								}
							}


							if($sc === TRUE)
							{
								//--- add logs
								$this->pick_list_logs_model->add($doc->state, $doc->DocNum);

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
						$this->error = "Document has already canceled";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Document AbsEntry";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Items Data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Document AbsEntry";
		}

		echo $this->response($sc);
	}


	//---- create pick details by item
	public function release_picklist()
	{
		$sc = TRUE;
		$absEntry = $this->input->post('AbsEntry');
		$onhand = array();
		$error = array();
		$details = array();

		if(!empty($absEntry))
		{
			$doc = $this->pick_model->get($absEntry);
			if(!empty($doc))
			{
				if($doc->Status != 'D')
				{
					if($doc->Status == 'N')
					{
						$rows = $this->pick_model->get_pick_rows($absEntry);

						if(!empty($rows))
						{
							foreach($rows as $rs)
							{
								$key = $rs->AbsEntry.$rs->OrderCode.$rs->ItemCode;

								if(! isset($onhand[$rs->ItemCode]))
								{
									$onHandStock = $this->stock_model->get_onhand_stock($rs->ItemCode);
									$committed = $this->get_committed_stock($rs->ItemCode);
									$thisPickList = $this->get_committed_stock_by_pick_list($absEntry, $rs->ItemCode);
									$onhand[$rs->ItemCode] = ($onHandStock - $committed) + $thisPickList;
								}

								if($onhand[$rs->ItemCode] < $rs->BaseRelQty)
								{
									$arr = array(
										'rowNum' => $rs->OrderEntry.$rs->OrderLine,
										'onHand' => round($onhand[$rs->ItemCode], 2),
										'unitMsr' => $rs->unitMsr2
									);

									array_push($error, $arr);
								}

								$onhand[$rs->ItemCode] -= $rs->BaseRelQty;

								if(! isset($details[$key]))
								{
									$row = new stdClass();
									$row->AbsEntry = $rs->AbsEntry;
									$row->DocNum = $doc->DocNum;
									$row->OrderCode = $rs->OrderCode;
									$row->OrderDate = $rs->OrderDate;
									$row->ItemCode = $rs->ItemCode;
									$row->ItemName = $rs->ItemName;
									$row->UomEntry = $rs->UomEntry2;
									$row->UomCode = $rs->UomCode2;
									$row->unitMsr = $rs->unitMsr2;
									$row->UomEntry2 = $rs->UomEntry2;
									$row->UomCode2 = $rs->UomCode2;
									$row->unitMsr2 = $rs->unitMsr2;
									$row->price = $rs->price;
									$row->BaseQty = 1;
									$row->RelQtty = $rs->BaseRelQty;
									$row->BaseRelQty = $rs->BaseRelQty;

									$details[$key] = $row;
								}
								else
								{
									$details[$key]->RelQtty += $rs->BaseRelQty;
									$details[$key]->BaseRelQty += $rs->BaseRelQty;
								}

							}

							//--- if not any error
							if(empty($error))
							{
								//--- create pick details
								$this->db->trans_begin();

								//--- check exists pick details
								$is_exists = $this->pick_model->is_picking_details_exists($absEntry);

								if(! $is_exists)
								{
									//--- create prick detail row
									foreach($details as $rs)
									{
										if($sc === FALSE)
										{
											break;
										}

										$arr = array(
											'AbsEntry' => $rs->AbsEntry,
											'DocNum' => $rs->DocNum,
											'OrderCode' => $rs->OrderCode,
											'OrderDate' => $rs->OrderDate,
											'ItemCode' => $rs->ItemCode,
											'ItemName' => $rs->ItemName,
											'UomEntry' => $rs->UomEntry,
											'UomCode' => $rs->UomCode,
											'unitMsr' => $rs->unitMsr,
											'UomEntry2' => $rs->UomEntry2,
											'UomCode2' => $rs->UomCode2,
											'unitMsr2' => $rs->unitMsr2,
											'price' => $rs->price,
											'BaseQty' => $rs->BaseQty,
											'RelQtty' => $rs->RelQtty,
											'BaseRelQty' => $rs->BaseRelQty
										);

										if(! $this->pick_model->add_pick_detail($arr))
										{
											$sc = FALSE;
											$this->error = "Create Pick details failed";
										}
									}
								}

								if($sc === TRUE)
								{
									if(! $this->pick_model->release($absEntry))
									{
										$sc = FALSE;
										$this->error = "Release Pick list failed";
									}
									else
									{
										//--- add logs
										$this->pick_list_logs_model->add('release', $doc->DocNum);
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

						if($doc->Status == 'R')
						{
							$this->error = "เอกสารถูก Release ไปแล้ว";
						}
						elseif($doc->Status == 'Y')
						{
							$this->error = "เอกสารถูกจัดสินค้าเรียบร้อยแล้ว";
						}
						elseif($doc->Status == 'P')
						{
							$this->error = "เอกสารอยู่ระหว่างการจัดสินค้า";
						}
						elseif($doc->Status == 'C')
						{
							$this->error = "เอกสารถูกปิดแล้ว";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "เอกสารถูกยกเลิกแล้ว";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document No";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: AbsEntry";
		}

		echo $sc === TRUE ? (empty($error) ? 'success' : json_encode($error)) : $this->error;
	}




	public function unrelease_picklist()
	{
		$sc = TRUE;
		$absEntry = $this->input->post('AbsEntry');

		if(!empty($absEntry))
		{
			$doc = $this->pick_model->get($absEntry);
			if(!empty($doc))
			{
				if($doc->Status != 'D')
				{
					if($doc->Status == 'R')
					{
						//--- drop pick details
						$this->db->trans_begin();

						if(! $this->pick_model->drop_pick_details($absEntry))
						{
							$sc = FALSE;
							$this->error = "Delete Pick details failed";
						}

						if($sc === TRUE)
						{
							if(! $this->pick_model->unrelease($absEntry))
							{
								$sc = FALSE;
								$this->error = "UnRelease Pick list failed";
							}
							else
							{
								//--- add logs
								$this->pick_list_logs_model->add('unrelease', $doc->DocNum);
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

						if($doc->Status == 'N')
						{
							$this->error = "เอกสารถูก UnRelease ไปแล้ว";
						}
						elseif($doc->Status == 'Y')
						{
							$this->error = "เอกสารถูกจัดสินค้าเรียบร้อยแล้ว";
						}
						elseif($doc->Status == 'P')
						{
							$this->error = "เอกสารอยู่ระหว่างการจัดสินค้า";
						}
						elseif($doc->Status == 'C')
						{
							$this->error = "เอกสารถูกปิดแล้ว";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "เอกสารถูกยกเลิกแล้ว";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document No";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: AbsEntry";
		}

		$this->response($sc);
	}


	public function find_open_so()
	{
		$sc = TRUE;
		$ds = array();
		$docNum = $this->input->get('DocNum');
		$customer = $this->input->get('customer');
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');

		$qr  = "SELECT DocEntry, DocNum, CardCode, CardName, DocDate, DocDueDate, Address2, Comments, U_Delivery_Urgency, U_Remark_Int ";
		$qr .= "FROM ORDR ";
		$qr .= "WHERE DocStatus = 'O' ";

		if($docNum != NULL && $docNum != '')
		{
			$qr .= "AND DocNum Like '%{$docNum}%' ";
		}

		if($customer != NULL && $customer != '')
		{
			$qr .= "AND (CardCode LIKE N'%{$customer}%' OR CardName LIKE N'%{$customer}%') ";
		}

		if(!empty($fromDate) && !empty($toDate))
		{
			$qr .= "AND DocDate >= '".from_date($fromDate)."' ";
			$qr .= "AND DocDate <= '".to_date($toDate)."' ";
		}

		$qr .= "ORDER BY DocNum ASC ";

		$rs = $this->ms->query($qr);

		if($rs->num_rows() > 0)
		{
			foreach($rs->result() as $rd)
			{
				$arr = array(
					'DocEntry' => $rd->DocEntry,
					'DocNum' => $rd->DocNum,
					'CardCode' => $rd->CardCode,
					'CardName' => $rd->CardName,
					'DocDate' => thai_date($rd->DocDate, FALSE, '.'),
					'DocDueDate' => thai_date($rd->DocDueDate, FALSE, '.'),
					'PostingDate' => $rd->DocDate,
					'Urgency' => $rd->U_Delivery_Urgency,
					'Remark_int' => $rd->U_Remark_Int,
					'ShipTo' => escape_quot($rd->Address2),
					'remark' => escape_quot($rd->Comments)
				);

				array_push($ds, $arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบผลการค้นหาตามเงื่อนไขที่กำหนด";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PICK_LIST');
    $run_digit = getConfig('RUN_DIGIT_PICK_LIST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->pick_model->get_max_code($pre);
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




	public function print_pick_order_slip($code)
	{
		$this->title = "Print Pick Slip";
		$this->load->model('sales_order_model');

		$orders = $this->pick_model->get_pick_orders($code);

		if(!empty($orders))
		{
			foreach($orders as $rs)
			{
				$order = $this->getOrder($rs->OrderCode);
				$rs->ItemRows = $this->pick_model->count_order_rows($rs->AbsEntry, $rs->OrderCode);
				$rs->prefix = $this->sales_order_model->get_prefix($order->Series);
				$rs->DocDate = $order->DocDate; //-- So date
				$rs->CardCode = $order->CardCode;
				$rs->CardName = $order->CardName;
				$rs->NumAtCard = $order->NumAtCard;
				$rs->shipTo = $order->Address2;
				$rs->remark = $this->pick_model->get_internal_remark($rs->OrderCode);
			}

			$this->load->view('print/print_pick_label', array("orders" => $orders));
		}
	}



	public function getOrder($SoNo)
	{
		$rs = $this->ms->where('DocNum', $SoNo)->get('ORDR');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}



	public function cancle_pick()
	{
		$sc = TRUE;

		$id = $this->input->post('id');
		$code = $this->input->post('code');

		$this->load->model('buffer_model');
		$this->load->model('cancle_model');

		$doc = $this->pick_model->get($id);

		if(!empty($doc))
		{
			if($doc->Status != 'C')
			{
				if($doc->Status != 'D')
				{
					//--- check ว่ามีรายการใน pick list ถูกดึงไปเปิด
					if($this->pick_model->is_all_open($id))
					{
						$this->db->trans_begin();

						//--- move buffer to cancle
						$buffer = $this->buffer_model->get_details($doc->DocNum);

						if(!empty($buffer))
						{
							foreach($buffer as $bf)
							{
								if($sc === FALSE)
								{
									break;
								}

								$arr = array(
									'AbsEntry' => $bf->AbsEntry,
									'DocNum' => $bf->DocNum,
									'OrderCode' => $bf->OrderCode,
									'ItemCode' => $bf->ItemCode,
									'ItemName' => $bf->ItemName,
									'UomEntry' => $bf->UomEntry,
									'UomCode' => $bf->UomCode,
									'unitMsr' => $bf->unitMsr,
									'BasePickQty' => $bf->BasePickQty,
									'BinCode' => $bf->BinCode,
									'user_id' => $this->user->id,
									'uname' => $this->user->uname
								);

								if(! $this->cancle_model->add($arr))
								{
									$sc = FALSE;
									$this->error = "บันทึกรายการยกเลิกไม่สำเร็จ";
								}
								else
								{
									if(! $this->buffer_model->delete($bf->id))
									{
										$sc = FALSE;
										$this->error = "ลบ Buffer ไม่สำเร็จ";
									}
								}
							}
						}


						//--- change rows status
						if($sc === TRUE)
						{
							if(! $this->pick_model->cancle_pick_rows($id))
							{
								$sc = FALSE;
								$this->error = "ยกเลิกสถานะรายการไม่สำเร็จ";
							}
							else
							{
								if(! $this->pick_model->update($id, array('Status' => 'D')))
								{
									$sc = FALSE;
									$this->error = "ยกเลิกสถานะเอกสารไม่สำเร็จ";
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
						$this->error = "บางรายการถูกดึงไปแพ็คแล้ว ไม่สามารถยกเลิกได้";
					}
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "เอกสารถูกดึงไปแพ็คแล้ว ไม่สามารถยกเลิกได้";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร";
		}


		$this->response($sc);
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

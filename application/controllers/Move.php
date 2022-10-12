<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Move extends PS_Controller
{
	public $menu_code = 'MOVE';
	public $menu_sub_group_code = '';
	public $menu_group_code = 'IC';
	public $title = 'ย้ายสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'move';
		$this->load->model('move_model');
		$this->load->model('item_model');
		$this->load->model('warehouse_model');
		$this->load->model('zone_model');
		$this->load->model('stock_model');
  }



  public function index()
  {

		$filter = array(
			'code' => get_filter('code', 'mvCode', ''),
			'fromWhsCode' => get_filter('fromWhsCode', 'mvFromWhsCode', ''),
			'toWhsCode' => get_filter('toWhsCode', 'mvToWhsCode', ''),
			'uname' => get_filter('uname', 'mvUname', ''),
			'status' => get_filter('status', 'mvStatus', 'all'),
			'fromDate' => get_filter('fromDate', 'mvFromDate', ''),
			'toDate' => get_filter('toDate', 'mvToDate', ''),
			'order_by' => get_filter('order_by', 'mvOrder_by', 'Code'),
			'sort_by' => get_filter('sort_by', 'mvSort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->move_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/', $rows, $perpage, $segment);

		$rs = $this->move_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('move/move_list', $filter);
  }



	public function save($move_id)
	{
		$sc = TRUE;

		$doc = $this->move_model->get($move_id);

		if( ! empty($doc))
		{
			if($doc->Status === 'O')
			{
				$this->db->trans_begin();

				if(! $this->move_model->valid_details($move_id, 1))
				{
					$sc = FALSE;
					$this->error = "Update move item failed";
				}

				if($sc === TRUE)
				{
					$arr = array('Status' => 'P');

					if( ! $this->move_model->update($move_id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update document status failed";
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
					if( ! $this->doExport($move_id))
					{
						$arr = array(
							'Status' => 'N'
						);

						$this->move_model->update($move_id, $arr);
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Status : '{$doc->Status}'";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document No.";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function export_move($id)
	{
		$sc = $this->doExport($id);

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function add_new()
	{
		$ds = array('code' => $this->get_new_code());
		$this->load->view('move/move_add', $ds);
	}


	public function add()
	{
		$sc = TRUE;

		$docDate = db_date($this->input->post('docDate'), FALSE);
		$fromWhsCode = trim($this->input->post('fromWhsCode'));
		$toWhsCode = trim($this->input->post('toWhsCode'));
		$remark = get_null(trim($this->input->post('remark')));


		if($sc === TRUE)
		{
			$arr = array(
				'DocDate' => $docDate,
				'code' => $this->get_new_code($docDate),
				'fromWhsCode' => $fromWhsCode,
				'toWhsCode' => $toWhsCode,
				'user_id' => $this->user->id,
				'uname' => $this->user->uname,
				'remark' => $remark
			);

			$id = $this->move_model->add($arr);

			if(! $id)
			{
				$sc = FALSE;
				$this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
			}
		}

		echo $sc === TRUE ? $id : $this->error;
	}




	public function edit($id, $method = 'barcode')
	{
		//--- method  'barcode', 'normal'
		$doc = $this->move_model->get($id);

		if(!empty($doc))
		{
			$details = $this->move_model->get_details($id);

			if(!empty($details))
			{
				foreach($details as $rs)
				{
					$rs->temp_qty = $this->move_model->get_temp_qty($id, $rs->ItemCode, $rs->fromBinCode);
				}
			}

			$temp = $this->move_model->get_temp_details($id);

			$ds = array(
				'doc' => $doc,
				'details' => $details,
				//'temp' => $temp,
				'method' => $method
			);

			$this->load->view('move/move_edit', $ds);
		}
		else
		{
			$this->error_page();
		}
	}



	public function update()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$docDate = db_date($this->input->post('docDate'), FALSE);
		$fromWhsCode = trim($this->input->post('fromWhsCode'));
		$toWhsCode = trim($this->input->post('toWhsCode'));
		$remark = get_null(trim($this->input->post('remark')));


		if($sc === TRUE)
		{
			$arr = array(
				'DocDate' => $docDate,
				'fromWhsCode' => $fromWhsCode,
				'toWhsCode' => $toWhsCode,
				'update_user' => $this->user->uname,
				'remark' => $remark
			);

			if(! $this->move_model->update($id, $arr))
			{
				$sc = FALSE;
				$this->error = "แก้ไขเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
			}
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function delete_detail()
	{
		$sc = TRUE;

		$move_id = $this->input->post('move_id');
		$id = $this->input->post('id');

		if( ! empty($move_id) && ! empty($id))
		{

			$doc = $this->move_model->get($move_id);

			if( ! empty($doc))
			{
				if($doc->Status === 'O')
				{
					if( ! $this->move_model->delete_detail($id))
					{
						$sc = FALSE;
						$this->error = "Delete failed";
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
				$this->error = "Invalid Document id";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function view_detail($id)
	{
		$doc = $this->move_model->get($id);

		if(!empty($doc))
		{
			$details = $this->move_model->get_details($id);

			$ds = array(
				'doc' => $doc,
				'details' => $details
			);

			$this->load->view('move/move_view_detail', $ds);
		}
		else
		{
			$this->error_page();
		}
	}

	public function delete_temp()
	{
		$sc = TRUE;

		$move_id = $this->input->post('move_id');
		$id = $this->input->post('id');

		if( ! empty($move_id) && ! empty($id))
		{
			if( ! $this->move_model->delete_temp($id))
			{
				$sc = FALSE;
				$this->error = "Delete failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function cancle_move($id)
	{
		$sc = TRUE;
		$doc = $this->move_model->get($id);

		if( ! empty($doc))
		{
			if( $doc->Status === 'O' OR $doc->Status === 'N' OR $doc->Status === 'P')
			{
				$this->db->trans_begin();

				if( ! $this->move_model->delete_all_temp($id))
				{
					$sc = FALSE;
					$this->error = "Delete move temp failed";
				}

				if($sc === TRUE)
				{
					$arr = array(
						'Status' => 'C'
					);

					if( ! $this->move_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update document failed";
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
				$this->error = "Invalid document status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document Id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}




	public function get_move_table($id)
  {
    $ds = array();
    $details = $this->move_model->get_details($id);

    if(!empty($details))
    {
      $no = 1;
      $total_qty = 0;
      foreach($details as $rs)
      {
        $arr = array(
          'id' => $rs->id,
          'no' => $no,
					'itemCode' => $rs->ItemCode,
					'itemName' => $rs->ItemName,
          'from_zone' => $rs->fromBinCode,
          'to_zone' => $rs->toBinCode,
          'qty' => number($rs->Qty),
					'unitMsr' => $rs->unitMsr,
					'valid' => $rs->valid
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->Qty;
      } //--- end foreach

      $arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



	public function get_temp_table($id)
  {
    $ds = array();

    $temp = $this->move_model->get_temp_details($id);

    if(!empty($temp))
    {
      $no = 1;
			$total_qty = 0;

      foreach($temp as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
					'itemCode' => $rs->ItemCode,
					'itemName' => $rs->ItemName,
          'binCode' => $rs->BinCode,
          'qty' => round($rs->Qty, 2),
					'label_qty' => number($rs->Qty),
					'unitMsr' => $rs->unitMsr,
					'defaultBin' => $this->move_model->get_default_bin_code($rs->ItemCode)
        );

        array_push($ds, $arr);
        $no++;
				$total_qty += round($rs->Qty, 2);
      }

			$arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



	public function get_move_zone()
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
		else
		{
			$sc[] = "not found";
		}

		echo json_encode($ds);
  }




	public function get_product_in_zone()
  {
    $sc = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('item_model');

      $zone_code = $this->input->get('zone_code');

			$itemCode = trim($this->input->get('itemCode'));

      $move_id = $this->input->get('move_id');

      $stock = $this->stock_model->get_filter_stock_in_zone($zone_code, $itemCode);

      if(!empty($stock))
      {
        $no = 1;

        foreach($stock as $rs)
        {
					if($rs->qty > 0)
					{
						//--- จำนวนที่อยู่ใน temp
						$temp_qty = $this->move_model->get_temp_qty($move_id, $rs->ItemCode, $zone_code);
						//--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
						$move_qty = get_zero($this->move_model->get_move_qty($move_id, $rs->ItemCode, $zone_code));
						//--- จำนวนที่โอนได้คงเหลือ
						$qty = $rs->qty - ($temp_qty + $move_qty);

						if($qty > 0)
						{
							$arr = array(
								'no' => $no,
								'barcode' => $this->item_model->get_barcode($rs->ItemCode),
								'itemCode' => $rs->ItemCode,
								'itemName' => $rs->ItemName,
								'qty' => $qty,
								'label_qty' => number($qty),
								'unitMsr' => $rs->unitMsr,
								'temp_qty' => $temp_qty,
								'move_qty' => $move_qty,
								'move_id' => $move_id
							);

							array_push($sc, $arr);
							$no++;
						}
					}
        }
      }
      else
      {
        array_push($sc, array("nodata" => "nodata"));
      }

      echo json_encode($sc);
    }
  }



	public function add_item_to_temp()
  {
    $sc = TRUE;
		$ds = array();

    if($this->input->post('move_id'))
    {
      $this->load->model('item_model');

      $move_id = $this->input->post('move_id');
			$move_code = $this->input->post('move_code');
      $binCode = $this->input->post('binCode');
      $itemCode = $this->input->post('itemCode');
			$itemName = trim($this->input->post('itemName'));
      $qty = $this->input->post('qty');

			$doc = $this->move_model->get($move_id);

			if(! empty($doc))
			{
				if($doc->Status == 'O')
				{
					$item = $this->item_model->getItemByCode($itemCode);

					if( ! empty($item))
					{
						$stockQty = $this->stock_model->getStockZone($itemCode, $binCode);
						//--- จำนวนที่อยู่ใน temp
						$tempQty = $this->move_model->get_temp_qty($move_id, $itemCode, $binCode);
						//--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
						$moveQty = $this->move_model->get_move_qty($move_id, $itemCode, $binCode);

		        //--- จำนวนที่โอนได้คงเหลือ
		        $cQty = $stockQty - ($tempQty + $moveQty);

						if($qty <= $cQty)
						{
							$id = $this->move_model->get_temp_id($move_id, $itemCode, $binCode);

							if( ! empty($id))
							{
								if( ! $this->move_model->update_temp_qty($id, $qty))
								{
									$sc = FALSE;
									$this->error = "Update temp qty failed";
								}
							}
							else
							{
								$arr = array(
									'move_id' => $move_id,
									'move_code' => $move_code,
									'ItemCode' => $item->ItemCode,
									'ItemName' => $item->ItemName,
									'BinCode' => $binCode,
									'UomEntry' => $item->UomEntry,
									'UomCode' => $item->UomCode,
									'unitMsr' => $item->UomName,
									'Qty' => $qty,
									'uname' => $this->user->uname
								);

								if(! $this->move_model->add_temp($arr))
								{
									$sc = FALSE;
									$this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
								}
							}

							if($sc === TRUE)
							{
								$ds['current_qty'] = $cQty - $qty;
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "จำนวนที่ย้ายเกินจำนวนที่มี";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบสินค้าหรือรหัสสินค้าไม่ถูกต้อง";
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
				$this->error = "ไม่พบเลขที่เอกสาร";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



	public function add_to_temp()
  {
    $sc = TRUE;
		$ds = array();

    if($this->input->post('move_id'))
    {
      $this->load->model('item_model');

      $move_id = $this->input->post('move_id');
			$move_code = $this->input->post('move_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->item_model->getItemByBarcode($barcode);

      if(! empty($item))
      {
				$qty = $qty * $item->BaseQty;

        $stock = $this->stock_model->getStockZone($item->ItemCode, $zone_code);

        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->move_model->get_temp_qty($move_id, $item->ItemCode, $zone_code);

        //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
        $move_qty = $this->move_model->get_move_qty($move_id, $item->ItemCode, $zone_code);

        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $move_qty);

        if($qty <= $cqty)
        {
					$temp_id = $this->move_model->get_temp_id($move_id, $item->ItemCode, $zone_code);

					if($temp_id)
					{
						if(! $this->move_model->update_temp_qty($temp_id, $qty))
						{
							$sc = FALSE;
							$this->error = "ย้ายสินค้าเข้า Temp ไม่สำเร็จ";
						}
					}
					else
					{
						$arr = array(
							'move_id' => $move_id,
	            'move_code' => $move_code,
	            'ItemCode' => $item->ItemCode,
							'ItemName' => $item->ItemName,
	            'BinCode' => $zone_code,
							'UomEntry' => $item->UomEntry,
							'UomCode' => $item->UomCode,
							'unitMsr' => $item->UomName,
	            'Qty' => $qty,
							'uname' => $this->user->uname
	          );

	          if(! $this->move_model->add_temp($arr))
	          {
	            $sc = FALSE;
	            $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
	          }
					}

					if($sc === TRUE)
					{
						$ds['itemCode'] = $item->ItemCode;
						$ds['current_qty'] = $cqty - $qty;
					}
        }
        else
        {
          $sc = FALSE;
          $this->error = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



	public function move_item_to_zone()
  {
    $sc = TRUE;
		$ds = array();

    if($this->input->post('move_id'))
    {
      $move_id = $this->input->post('move_id');
			$move_code = $this->input->post('move_code');
      $temp_id = $this->input->post('temp_id');
			$toBinCode = $this->input->post('binCode');
      $qty = $this->input->post('qty');

			$doc = $this->move_model->get($move_id);

			if( ! empty($doc))
			{
				if($doc->Status == 'O')
				{
					$temp = $this->move_model->get_temp_detail($temp_id);

					if( ! empty($temp))
					{
						if($qty <= $temp->Qty)
						{
							if($toBinCode != $temp->BinCode)
							{
								$detail = $this->move_model->get_move_detail($temp->move_id, $temp->ItemCode, $temp->BinCode, $toBinCode);

								$this->db->trans_begin();

								if( ! empty($detail))
								{
									if( ! $this->move_model->update_move_qty($detail->id, $qty))
									{
										$sc = FALSE;
										$this->error = "Update Move Qty Failed";
									}
								}
								else
								{
									$arr = array(
										'move_id' => $temp->move_id,
										'move_code' => $temp->move_code,
										'ItemCode' => $temp->ItemCode,
										'ItemName' => $temp->ItemName,
										'fromWhsCode' => $this->zone_model->getWhsCode($temp->BinCode),
										'fromBinCode' => $temp->BinCode,
										'toWhsCode' => $this->zone_model->getWhsCode($toBinCode),
										'toBinCode' => $toBinCode,
										'UomEntry' => $temp->UomEntry,
										'UomCode' => $temp->UomCode,
										'unitMsr' => $temp->unitMsr,
										'Qty' => $qty,
										'uname' => $this->user->uname
									);

									if( ! $this->move_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = 'เพิ่มรายการไม่สำเร็จ';
									}
								}

								if($sc === TRUE)
								{
									if($qty == $temp->Qty)
									{
										if( ! $this->move_model->delete_temp($temp_id))
										{
											$sc = FALSE;
											$this->error = "Remove Temp Failed";
										}
									}
									else
									{
										if( ! $this->move_model->update_temp_qty($temp_id, ($qty * -1)))
										{
											$sc = FALSE;
											$this->error = "Update Temp Qty Failed";
										}
									}
								}

								if($sc === TRUE)
								{
									$this->db->trans_commit();

									$ds['id'] = $temp_id;
									$ds['current_qty'] = $temp->Qty - $qty;
								}
								else
								{
									$this->db->trans_rollback();
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Location ต้นทาง - ปลายทาง ต้องเป็นคนละ Location กัน";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "จำนวนที่่ย้ายต้องไม่มากกว่า temp";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Temp id";
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
				$this->error = "ไม่พบเอกสาร";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



	public function move_to_zone()
  {
    $sc = TRUE;

    if($this->input->post('move_id'))
    {
      $move_id = $this->input->post('move_id');
			$move_code = $this->input->post('move_code');
      $barcode = trim($this->input->post('barcode'));
      $toBinCode = $this->input->post('zone_code');
      $qty = $this->input->post('qty');

			$doc = $this->move_model->get($move_id);

			if( ! empty($doc))
			{
				if($doc->Status == 'O')
				{
					$item = $this->item_model->getItemByBarcode($barcode);

					if( ! empty($item))
					{
						$qty = $qty * $item->BaseQty;

		        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า move detail
		        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
		        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
		        $temp = $this->move_model->get_temp_product($move_id, $item->ItemCode);

		        if(!empty($temp))
		        {
		          //--- เริ่มใช้งาน transction
		          $this->db->trans_begin();

		          foreach($temp as $rs)
		          {
		            if($sc === FALSE)
		            {
		              break;
		            }

		            if($rs->BinCode != $toBinCode)
		            {
		              if($qty > 0 && $rs->Qty > 0)
		              {
		                //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
		                //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
		                //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
		                $temp_qty = $qty <= $rs->Qty ? $qty : $rs->Qty;

		                $id = $this->move_model->get_detail_id($move_id, $item->ItemCode, $rs->BinCode, $toBinCode);

		                //--- ถ้าพบไอดีให้แก้ไขจำนวน
		                if(!empty($id))
		                {
		                  if($this->move_model->update_move_qty($id, $temp_qty) === FALSE)
		                  {
		                    $sc = FALSE;
		                    $this->error = 'แก้ไขยอดในรายการไม่สำเร็จ';
		                  }
		                }
		                else
		                {
		                  //--- ถ้ายังไม่มีรายการ ให้เพิ่มใหม่
		                  $ds = array(
		                    'move_id' => $move_id,
												'move_code' => $move_code,
		                    'ItemCode' => $item->ItemCode,
		                    'ItemName' => $item->ItemName,
		                    'fromWhsCode' => $this->zone_model->getWhsCode($rs->BinCode),
												'fromBinCode' => $rs->BinCode,
												'toWhsCode' => $this->zone_model->getWhsCode($toBinCode),
												'toBinCode' => $toBinCode,
												'UomEntry' => $item->UomEntry,
												'UomCode' => $item->UomCode,
												'unitMsr' => $item->UomName,
		                    'Qty' => $temp_qty,
												'uname' => $this->user->uname
		                  );

		                  if($this->move_model->add_detail($ds) === FALSE)
		                  {
		                    $sc = FALSE;
		                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
		                  }
		                }

										if($sc === TRUE)
										{
											//--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
											if($this->move_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
											{
												$sc = FALSE;
												$this->error = 'แก้ไขยอดใน temp ไม่สำเร็จ';
											}
										}

		                //--- ตัดยอดที่ต้องการย้ายออก เพื่อยกยอดไปรอบต่อไป
		                $qty -= $temp_qty;
		              }
		              else
		              {
		                break;
		              } //-- end if qty > 0
		            }
		            else
		            {
		              $sc = FALSE;
		              $this->error = 'โซนต้นทาง - ปลายทาง ต้องไม่ใช่โซนเดียวกัน';
		            }
		          } //--- end foreach

							//--- ลบ temp ที่ยอดเป็น 0
							if($sc === TRUE)
							{
								$this->move_model->drop_zero_temp();
							}

		          //--- เมื่อทำงานจนจบแล้ว ถ้ายังเหลือยอด แสดงว่ายอดที่ต้องการย้ายเข้า มากกว่ายอดที่ย้ายออกมา
		          //--- จะให้ทำกร roll back แล้วแจ้งกลับ
							if($sc === TRUE)
							{
								if($qty > 0)
								{
									$sc = FALSE;
									$this->error = 'ยอดที่ย้ายเข้า ต้องไม่มากกว่า ยอดที่ใน Temp';
								}
							}

		          if($sc === FALSE)
		          {
		            $this->db->trans_rollback();
		          }
		          else
		          {
		            $this->db->trans_commit();
		          }
		        }
		        else
		        {
		          $sc = FALSE;
		          $this->error = 'ไม่พบรายการใน temp';
		        }

					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบสินค้าหรือบาร์โค้ดไม่ถูกต้อง";
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
				$this->error = "ไม่พบเลขที่เอกสาร";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function is_exists_zone()
	{
		$zone_code = trim($this->input->get('zone_code'));

		if($this->zone_model->is_exists_bin_code($zone_code))
		{
			echo "ok";
		}
		else
		{
			echo "Bin Location ไม่ถูกต้อง";
		}
	}



	public function is_exists_temp($move_id)
	{
		$temp = $this->move_model->is_exists_temp($move_id);

		if( ! empty($temp))
		{
			echo "พบสินค้าค้างใน Temp กรุณาตรวจสอบ";
		}
		else
		{
			echo "success";
		}
	}



	public function doExport($id)
	{
		$sc = TRUE;

		$doc = $this->move_model->get($id);

		if(! empty($doc))
		{
			if($doc->Status !== 'Y' && $doc->Status !== 'C')
			{
				//---- check TR already in SAP
				$mv = $this->move_model->get_sap_transfer($doc->code);

				if(empty($mv))
				{
					//---- drop exists temp data
					$temp = $this->move_model->get_temp_transfer($doc->code);

					if(!empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if($this->move_model->drop_transfer_temp_data($rows->DocEntry) === FALSE)
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
							'U_BookCode' => 'MV'
						);

						$docEntry = $this->move_model->add_sap_transfer($header);

						if($docEntry !== FALSE)
						{
							$details = $this->move_model->get_details($id);

							if(!empty($details))
							{
								$line = 0;

	              foreach($details as $rs)
	              {
									if($sc === FALSE)
									{
										break;
									}

									if($rs->Qty > 0 && $rs->valid == 1)
									{
										$arr = array(
		                  'DocEntry' => $docEntry,
		                  'U_WEBORDER' => $doc->code,
		                  'LineNum' => $line,
		                  'ItemCode' => $rs->ItemCode,
		                  'Dscription' => $rs->ItemName,
		                  'Quantity' => $rs->Qty,
											'InvQty' => $rs->Qty,
											'UomCode' => $rs->UomCode,
											'UomEntry' => $rs->UomEntry,
		                  'unitMsr' => $rs->unitMsr,
											'NumPerMsr' => 1.000000,
											'UomCode2' => $rs->UomCode,
											'UomEntry2' => $rs->UomEntry,
											'unitMsr2' => $rs->unitMsr,
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
											'F_TO_BIN' => $rs->toBinCode,
		                  'TaxStatus' => 'Y',
		                  'VatPrcnt' => 0.000000,
		                  'VatGroup' => NULL,
		                  'PriceAfVAT' => 0.000000,
		                  'VatSum' => 0.000000,
		                  'TaxType' => 'Y'
		                );

										if( ! $this->move_model->add_sap_transfer_detail($arr))
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
				'Status' => 'P',
				'tempDate' => now()
			);

			$this->move_model->update($id, $arr);
		}

		return $sc;
	}



	public function get_sap_temp()
  {
    $code = $this->input->get('code'); //--- U_WEBORDER

    $data = $this->move_model->get_temp_data($code);

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


	public function remove_sap_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('U_WEBORDER');
    $temp = $this->move_model->get_temp_status($code);

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
      if(! $this->move_model->drop_transfer_temp_data($temp->DocEntry))
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

				$this->move_model->update_by_code($code, $arr);
				$this->move_model->valid_details_by_code($code, 0);
			}
    }


    $this->response($sc);
  }


	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_MOVE');
    $run_digit = getConfig('RUN_DIGIT_MOVE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->move_model->get_max_code($pre);
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
			'mvCode',
			'mvFromWhsCode',
			'mvToWhsCode',
			'mvUname',
			'mvStatus',
			'mvFromDate',
			'mvToDate',
			'mvOrder_by',
			'mvSort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

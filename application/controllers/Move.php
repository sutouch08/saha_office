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

		//--- check whs
		if($this->warehouse_model->is_exists_warehouse($fromWhsCode) === FALSE)
		{
			$sc = FALSE;
			$this->error = "รหัสคลังต้นทางไม่ถูกต้อง";
		}
		else
		{
			if($this->warehouse_model->is_exists_warehouse($toWhsCode) === FALSE)
			{
				$sc = FALSE;
				$this->error = "รหัสคลังปลายทางไม่ถูกต้อง";
			}
		}


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
		$sc = TRUE;
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

			$ds = array(
				'doc' => $doc,
				'details' => $details,
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

		//--- check whs
		if($this->warehouse_model->is_exists_warehouse($fromWhsCode) === FALSE)
		{
			$sc = FALSE;
			$this->error = "รหัสคลังต้นทางไม่ถูกต้อง";
		}
		else
		{
			if($this->warehouse_model->is_exists_warehouse($toWhsCode) === FALSE)
			{
				$sc = FALSE;
				$this->error = "รหัสคลังปลายทางไม่ถูกต้อง";
			}
		}

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
        $btn_delete = '';
        if($rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="deleteMoveItem('.$rs->id.', \''.$rs->ItemCode.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'products' => $rs->ItemCode,
          'from_zone' => $rs->fromBinCode,
          'to_zone' => $rs->toBinCode,
          'qty' => number($rs->Qty),
					'unitMsr' => $rs->unitMsr,
          'btn_delete' => $btn_delete
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

      foreach($temp as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->ItemCode,
          'from_zone' => $rs->BinCode,
          'qty' => round($rs->Qty, 2),
					'unitMsr' => $rs->unitMsr
        );

        array_push($ds, $arr);
        $no++;
      }
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
      $move_id = $this->input->get('move_id');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);

      if(!empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
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
              'products' => $rs->ItemCode .' | '.$rs->ItemName,
              'qty' => $qty,
							'unitMsr' => $rs->unitMsr
            );

            array_push($sc, $arr);
            $no++;
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

			$item = empty($item) ? $this->item_model->getItemByCode($barcode) : $item;

      if(! empty($item))
      {
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
							'barcode' => $barcode,
	            'ItemCode' => $item->ItemCode,
	            'BinCode' => $zone_code,
							'UomEntry' => $item->UomEntry,
							'UomCode' => $item->UomCode,
							'unitMsr' => $item->UomName,
	            'Qty' => round($qty * $item->BaseQty),
							'uname' => $this->user->uname
	          );

	          if(! $this->move_model->add_temp($arr))
	          {
	            $sc = FALSE;
	            $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
	          }
						else
						{
							$ds['current_qty'] = round($qty * $item->BaseQty);
						}
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

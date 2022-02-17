<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancle extends PS_Controller
{
	public $menu_code = 'CANCEL';
	public $menu_sub_group_code = 'CHECK';
	public $menu_group_code = 'IC';
	public $title = 'ตรวจสอบ CANCLE';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'cancle';
		$this->load->model('cancle_model');
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'DocNum' => get_filter('DocNum', 'cancle_DocNum', ''),
			'ItemCode' => get_filter('ItemCode', 'cancle_ItemCode', ''),
			'OrderCode' => get_filter('OrderCode', 'cancle_OrderCode', ''),
			'uname' => get_filter('uname', 'cancle_uname', ''),
			'BinCode' => get_filter('BinCode', 'cancle_BinCode', ''),
			'fromDate' => get_filter('fromDate', 'cancle_fromDate', ''),
			'toDate' => get_filter('toDate', 'cancle_toDate', ''),
			'order_by' => get_filter('order_by', 'cancle_order_by', 'DocNum'),
			'sort_by' => get_filter('sort_by', 'cancle_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->cancle_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$details = $this->cancle_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['details'] = $details;

		$this->pagination->initialize($init);
    $this->load->view('cancle/cancle_list', $filter);
  }


	public function delete_cancle()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if(! $this->cancle_model->delete($id))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		$this->response($sc);
	}


	public function delete_selected()
	{
		$sc = TRUE;
		$ids = $this->input->post('ids');

		$ds = explode(',', $ids);

		if(!empty($ds))
		{
			if(! $this->cancle_model->delete_selected($ds))
			{
				$sc = FALSE;
				$this->error = "Delete failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid parameter";
		}

		$this->response($sc);
	}


	public function get_items_list()
	{
		$sc = TRUE;
		$itemCode = $this->input->post('itemCode');
		$orderCode = $this->input->post('orderCode');

		$ds = array();

		if(!empty($itemCode))
		{
			$details = $this->cancle_model->get_items_list($itemCode);

			if(!empty($details))
			{
				foreach($details as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'DocNum' => $rs->DocNum,
						'OrderCode' => $rs->OrderCode,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'unitMsr' => $rs->unitMsr,
						'Qty' => round($rs->Qty, 2),
						'BinCode' => $rs->BinCode
					);

					array_push($ds, $arr);
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Item found";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function clear_filter()
	{
		$filter = array(
			'cancle_DocNum',
			'cancle_ItemCode',
			'cancle_OrderCode',
			'cancle_uname',
			'cancle_BinCode',
			'cancle_fromDate',
			'cancle_toDate',
			'cancle_order_by',
			'cancle_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

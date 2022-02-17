<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buffer extends PS_Controller
{
	public $menu_code = 'BUFFER';
	public $menu_sub_group_code = 'CHECK';
	public $menu_group_code = 'IC';
	public $title = 'ตรวจสอบ BUFFER';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'buffer';
		$this->load->model('buffer_model');
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'DocNum' => get_filter('DocNum', 'buffer_DocNum', ''),
			'ItemCode' => get_filter('ItemCode', 'buffer_ItemCode', ''),
			'OrderCode' => get_filter('OrderCode', 'buffer_OrderCode', ''),
			'uname' => get_filter('uname', 'buffer_uname', ''),
			'BinCode' => get_filter('BinCode', 'buffer_BinCode', ''),
			'fromDate' => get_filter('fromDate', 'buffer_fromDate', ''),
			'toDate' => get_filter('toDate', 'buffer_toDate', ''),
			'order_by' => get_filter('order_by', 'buffer_order_by', 'DocNum'),
			'sort_by' => get_filter('sort_by', 'buffer_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->buffer_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$details = $this->buffer_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['details'] = $details;

		$this->pagination->initialize($init);
    $this->load->view('buffer/buffer_list', $filter);
  }



	public function clear_filter()
	{
		$filter = array(
			'buffer_DocNum',
			'buffer_ItemCode',
			'buffer_OrderCode',
			'buffer_uname',
			'buffer_BinCode',
			'buffer_fromDate',
			'buffer_toDate',
			'buffer_order_by',
			'buffer_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

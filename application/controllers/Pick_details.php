<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_details extends PS_Controller
{
	public $menu_code = 'PICKLOG';
	public $menu_sub_group_code = 'CHECK';
	public $menu_group_code = 'IC';
	public $title = 'Pick Details';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'pick_details';
		$this->load->model('pick_details_model');	
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'DocNum' => get_filter('DocNum', 'pick_DocNum', ''),
			'ItemCode' => get_filter('ItemCode', 'pick_ItemCode', ''),
			'OrderCode' => get_filter('OrderCode', 'pick_OrderCode', ''),
			'uname' => get_filter('uname', 'pick_uname', ''),
			'BinCode' => get_filter('BinCode', 'pick_BinCode', ''),
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
		$rows = $this->pick_details_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$details = $this->pick_details_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['details'] = $details;

		$this->pagination->initialize($init);
    $this->load->view('pick_details/pick_details_list', $filter);
  }



	public function clear_filter()
	{
		$filter = array(
			'pick_DocNum',
			'pick_ItemCode',
			'pick_OrderCode',
			'pick_uname',
			'pick_BinCode',
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

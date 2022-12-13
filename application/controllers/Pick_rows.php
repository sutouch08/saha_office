<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_rows extends PS_Controller
{
	public $menu_code = 'PICKROWS';
	public $menu_sub_group_code = 'CHECK';
	public $menu_group_code = 'IC';
	public $title = 'ตรวจสอบ Pick List';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'pick_rows';
		$this->load->model('pick_rows_model');
  }



  public function index()
  {

		$filter = array(
			'DocNum' => get_filter('DocNum', 'pick_DocNum', ''),
			'OrderCode' => get_filter('OrderCode', 'pick_OrderCode', ''),
			'ItemCode' => get_filter('ItemCode', 'pick_ItemCode', ''),
			'Status' => get_filter('Status', 'pick_status','all'),
			'LineStatus' => get_filter('LineStatus', 'LineStatus', 'all'),
			'fromDate' => get_filter('fromDate', 'pick_fromDate', ''),
			'toDate' => get_filter('toDate', 'pick_toDate', ''),
			'uname' => get_filter('uname', 'pick_uname', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->pick_rows_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$details = $this->pick_rows_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['details'] = $details;

		$this->pagination->initialize($init);
    $this->load->view('pick_details/pick_row_details', $filter);
  }



	public function clear_filter()
	{
		$filter = array(
			'pick_DocNum',
			'pick_ItemCode',
			'pick_OrderCode',
			'pick_uname',
			'pick_status',
			'LineStatus',
			'pick_fromDate',
			'pick_toDate'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

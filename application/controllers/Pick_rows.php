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
		$this->load->helper('picklist');
  }



  public function index()
  {
		$filter = array(
			'DocNum' => get_filter('DocNum', 'pick_row_DocNum', ''),
			'ItemCode' => get_filter('ItemCode', 'pick_row_ItemCode', ''),
			'OrderCode' => get_filter('OrderCode', 'pick_row_OrderCode', ''),
			'PickStatus' => get_filter('PickStatus', 'pick_row_PickStatus', 'all'),
			'LineStatus' => get_filter('LineStatus', 'pick_row_LineStatus', 'all'),
			'order_by' => get_filter('order_by', 'pick_row_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'pick_row_sort_by', 'DESC')
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
    $this->load->view('pick_rows/pick_rows_list', $filter);
  }



	public function clear_filter()
	{
		$filter = array(
			'pick_row_DocNum',
			'pick_row_ItemCode',
			'pick_row_OrderCode',
			'pick_row_PickStatus',
			'pick_row_LineStatus',
			'pick_row_order_by',
			'pick_row_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


}//--- end class


 ?>

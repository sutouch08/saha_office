<?php
class Delivery_details extends PS_Controller
{
	public $menu_code = 'DELDETAIL';
	public $menu_group_code = 'TR';
	public $title = 'รายละเอียดการจัดส่ง';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'delivery_details';
		$this->load->model('delivery_details_model');
    $this->load->helper('transport');
  }



  public function index()
  {
		$filter = array(
			'delivery_code' => get_filter('delivery_code', 'delivery_code', ''),
      'driver_id' => get_filter('driver_id', 'driver_id', 'all'),
      'vehicle_id' => get_filter('vehicle_id', 'vehicle_id', 'all'),
      'route_id' => get_filter('route_id', 'route_id', 'all'),
			'CardCode' => get_filter('CardCode', 'CardCode', ''),
      'CardName' => get_filter('CardName', 'CardName', ''),
      'contact' => get_filter('contact', 'contact', ''),
      'type' => get_filter('type', 'type', 'all'),
      'DocType' => get_filter('DocType', 'DocType', 'all'),
      'DocNum' => get_filter('DocNum', 'DocNum', ''),
      'result_status' => get_filter('result_status', 'result_status', 'all'),
      'line_status' => get_filter('line_status', 'line_status', 'all'),
      'release_from' => get_filter('release_from', 'release_from', ''),
      'release_to' => get_filter('release_to', 'release_to', ''),
      'finish_from' => get_filter('finish_from', 'finish_from', ''),
      'finish_to' => get_filter('finish_to', 'finish_to', ''),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'uname' => get_filter('uname', 'uname', '')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->delivery_details_model->count_rows($filter);
    $rs = $this->delivery_details_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $filter['data'] = $rs;

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
    $this->pagination->initialize($init);

    $this->load->view('delivery_details/delivery_details_list', $filter);
  }


  public function clear_filter()
  {
    $filter = array(
			'delivery_code',
      'driver_id',
      'vehicle_id',
      'route_id',
			'CardCode',
      'CardName',
      'contact',
      'type',
      'DocType',
      'DocNum',
      'result_status',
      'line_status',
      'release_from',
      'release_to',
      'finish_from',
      'finish_to',
      'from_date',
      'to_date',
      'uname',
		);

    return clear_filter($filter);
  }



} //--- end class
?>

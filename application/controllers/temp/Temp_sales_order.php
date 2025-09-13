<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_sales_order extends PS_Controller
{
  public $menu_code = 'TMSOD';
	public $menu_group_code = 'Temp';
  public $menu_sub_group_code = '';
	public $title = 'Temp Sales Order';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'temp/temp_sales_order';
    $this->load->model('temp/temp_sales_order_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'temp_so_code', ''),
      'customer' => get_filter('customer', 'temp_so_customer', ''),
      'from_date' => get_filter('from_date', 'temp_so_from_date', ''),
      'to_date' => get_filter('to_date', 'temp_so_to_date', ''),
      'status' => get_filter('status', 'temp_so_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows = $this->temp_sales_order_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders = $this->temp_sales_order_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('temp_sales_order/temp_list', $filter);
  }


  public function get_detail($id)
  {
    $ds = array(
      'doc' => $this->temp_sales_order_model->get($id),
      'details' => $this->temp_sales_order_model->get_detail($id)
    );

    $this->load->view('temp_sales_order/temp_detail', $ds);
  }


	public function remove_temp($docEntry)
	{
		$sc = TRUE;

		if(! $this->temp_sales_order_model->removeTemp($docEntry))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function set_temp_to_success($docEntry)
  {
    $sc = TRUE;

    if( ! $this->temp_sales_order_model->setStatus($docEntry, 'Y'))
    {
      $sc = FALSE;
      $this->error = "Failed to change status";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'temp_so_code',
      'temp_so_supplier',
      'temp_so_from_date',
      'temp_so_to_date',
      'temp_so_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>

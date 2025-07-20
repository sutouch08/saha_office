<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_receive_po extends PS_Controller
{
  public $menu_code = 'TMGRPO';
	public $menu_group_code = 'Temp';
  public $menu_sub_group_code = '';
	public $title = 'Temp Goods Receipt PO';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'temp/temp_receive_po';
    $this->load->model('temp/temp_receive_po_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'temp_receive_code', ''),
      'supplier' => get_filter('supplier', 'temp_receive_supplier', ''),
      'from_date' => get_filter('from_date', 'temp_receive_from_date', ''),
      'to_date' => get_filter('to_date', 'temp_receive_to_date', ''),
      'status' => get_filter('status', 'temp_receive_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows = $this->temp_receive_po_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders = $this->temp_receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('temp_receive_po/temp_list', $filter);
  }


  public function get_detail($id)
  {
    $ds = array(
      'doc' => $this->temp_receive_po_model->get($id),
      'details' => $this->temp_receive_po_model->get_detail($id)
    );
        
    $this->load->view('temp_receive_po/temp_detail', $ds);
  }


	public function remove_temp($docEntry)
	{
		$sc = TRUE;

		if(! $this->temp_receive_po_model->removeTemp($docEntry))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function set_temp_to_success($docEntry)
  {
    $sc = TRUE;

    if( ! $this->temp_receive_po_model->setStatus($docEntry, 'Y'))
    {
      $sc = FALSE;
      $this->error = "Failed to change status";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'temp_receive_code',
      'temp_receive_supplier',
      'temp_receive_from_date',
      'temp_receive_to_date',
      'temp_receive_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gp_rule extends PS_Controller{
	public $menu_code = 'GPRULE';
	public $menu_group_code = 'SC'; //--- System security
	public $title = 'GP Rule';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'gp_rule';
		$this->load->model('gp_rule_model');
		$this->load->model('sales_team_model');
  }



  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'rule_name', ''),
			'sale_team' => get_filter('sale_team', 'rule_sale_team', 'all'),
			'min_gp' => get_filter('min_gp', 'rule_min', ''),
			'max_gp' => get_filter('max_gp', 'rule_max', ''),
			'active' => get_filter('active', 'rule_status', 'all'),
			'order_by' => get_filter('order_by', 'rule_order_by', 'id'),
			'sort_by' => get_filter('sort_by', 'rule_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->gp_rule_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->gp_rule_model->get_list($filter, $perpage, $this->uri->segment($segment));


		$filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('gp_rule/rule_list', $filter);
  }



	public function add_new()
	{
		$this->title = "Add New GP Rule";

		if($this->isAdmin)
		{
			$this->load->view('gp_rule/rule_add');
		}
		else
		{
			$this->deny_page();
		}

	}


	public function add()
	{
		$sc = TRUE;
		if($this->isAdmin)
		{
			if($this->input->post('name'))
			{
				$arr = array(
					'name' => trim($this->input->post('name')),
					'sale_team' => trim($this->input->post('sale_team')),
					'gp' => $this->input->post('gp'),
					'active' => $this->input->post('active')
				);

				if(! $this->gp_rule_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Insert Failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required Parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}



	public function edit($id)
	{
		$this->title = "Edit Discount Rule";
		$data = $this->gp_rule_model->get($id);
		$ds = array(
			'data' => $data
		);

		$this->load->view('gp_rule/rule_edit', $ds);
	}


	public function update()
	{
		$sc = TRUE;

		if($this->isAdmin)
		{
			if($this->input->post('id'))
			{
				$id = $this->input->post('id');
				$arr = array(
					'name' => trim($this->input->post('name')),
					'sale_team' => trim($this->input->post('sale_team')),
					'gp' => $this->input->post('gp'),
					'active' => $this->input->post('active')
				);

				if(! $this->gp_rule_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update Failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Required Parameter : id";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}


	public function delete()
	{
		$sc = TRUE;

		if($this->isAdmin)
		{
			$id = $this->input->post('id');
			if(!empty($id))
			{
				if( ! $this->gp_rule_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Delete Failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : id";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}


	public function clear_filter()
	{
		$filter = array(
			'rule_name',
			'rule_sale_team',
			'rule_min',
			'rule_max',
			'rule_status',
			'rule_order_by',
			'rule_sort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>

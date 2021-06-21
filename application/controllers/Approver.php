<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approver extends PS_Controller{
	public $menu_code = 'APPROVER'; //--- Add/Edit Users
	public $menu_group_code = 'AD'; //--- System security
	public $title = 'Approval';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'approver';
		$this->load->model('approver_model');
  }



  public function index()
  {
		$this->load->model('sales_team_model');

		$filter = array(
			'uname' => get_filter('uname', 'ap_username', ''),
			'name' => get_filter('name', 'ap_name', ''),
			'sale_team' => get_filter('sale_team', 'ap_sale_team', 'all'),
			'status' => get_filter('status', 'ap_user_status', 'all'),
			'order_by' => get_filter('order_by', 'ap_user_order_by', 'id'),
			'sort_by' => get_filter('sort_by', 'ap_user_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->approver_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->approver_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($rs))
		{
			foreach($rs as $rd)
			{
				$rd->sale_team_name = $rd->sale_team === 'all' ? 'All Team' : $this->sales_team_model->get_name($rd->sale_team);
			}
		}

		$filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('approver/approver_list', $filter);
  }



	public function add_new()
	{
		$this->title = "Add Approval";

		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('approver/approver_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if($this->input->post('uname'))
			{
				$discount = $this->input->post('discount');
				$arr = array(
					'uname' => trim($this->input->post('uname')),
					'name' => trim($this->input->post('emp_name')),
					'sale_team' => $this->input->post('sale_team'),
					'max_discount' => ($discount < 0 ? 0 : ($discount > 100 ? 100 : $discount)),
					'status' => $this->input->post('status')
				);

				if(! $this->approver_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Add new Approval failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Required Parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}

		$this->response($sc);
	}


	public function edit($id)
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$rs = $this->approver_model->get($id);
			if(!empty($rs))
			{
				$ds['data'] = $rs;
				$this->load->view('approver/approver_edit', $ds);
			}
			else
			{
				$this->load->view('page_error');
			}
		}
		else
		{
			$this->deny_page();
		}

	}



	public function update()
	{
		$sc = TRUE;
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if($this->input->post('id') && $this->input->post('uname'))
			{
				$id = $this->input->post('id');
				$gp = $this->input->post('discount');
				$arr = array(
					'uname' => trim($this->input->post('uname')),
					'name' => trim($this->input->post('emp_name')),
					'sale_team' => $this->input->post('sale_team'),
					'max_discount' => ($gp < 0 ? 0 : ($gp > 100 ? 100 : $gp)),
					'status' => $this->input->post('status')
				);

				if(! $this->approver_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
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
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if($this->input->post('id') && $this->input->post('uname'))
			{
				$id = $this->input->post('id');

				if(! $this->approver_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Delete failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
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
			'ap_username',
			'ap_name',
			'ap_sale_team',
			'ap_user_status',
			'ap_user_order_by',
			'ap_user_sort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>

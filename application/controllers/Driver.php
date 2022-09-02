<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver extends PS_Controller
{
	public $menu_code = 'DRIVER';
	public $menu_group_code = 'AD';
	public $title = 'Driver';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'driver';
		$this->load->model('driver_model');
  }



  public function index()
  {

		$filter = array(
			'emp_name' => get_filter('emp_name', 'driver_name', ''),
			'type' => get_filter('type', 'driver_type', 'all'),
			'active' => get_filter('active', 'driver_active', 'all')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$rows = get_rows();

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

		$rs = $this->driver_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('driver/driver_list', $filter);
  }



	public function add_new()
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('driver/driver_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;

		$emp_id = $this->input->post('emp_id');
		$type = $this->input->post('type');
		$active = $this->input->post('active');

		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if(!empty($emp_id))
			{
				if( ! $this->driver_model->is_exists($emp_id))
				{
					$arr = array(
						'emp_id' => $emp_id,
						'emp_name' => $this->user_model->get_emp_name($emp_id),
						'type' => $type == 'D' ? 'D' : 'E',
						'active' => $active == 1 ? 1 : 0
					);

					if( ! $this->driver_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "พนักงานซ้ำ";
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
			$this->error = "Missing permission";
		}

		$this->response($sc);
	}


	public function edit($id)
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$rs = $this->driver_model->get($id);

			if(!empty($rs))
			{
				$this->load->view('driver/driver_edit', $rs);
			}
			else
			{
				$this->error_page();
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
			$emp_id = $this->input->post('emp_id');
			$type = $this->input->post('type');
			$active = $this->input->post('active');

			$arr = array(
				'type' => $type == 'D' ? 'D' : 'E',
				'active' => $active == 1 ? 1 : 0
			);

			if( ! $this->driver_model->update($emp_id, $arr))
			{
				$sc = FALSE;
				$this->error = "Update failed";
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
			$emp_id = $this->input->post('emp_id');

			if(!empty($emp_id))
			{
				$has_transection = $this->driver_model->has_transection($emp_id);

				if(! $has_transection)
				{
					if(! $this->driver_model->delete($emp_id))
					{
						$sc = FALSE;
						$this->error = "Delete Failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Delete failed : This Driver already has transections";
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
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}




  public function clear_filter()
	{
		$filter = array(
			'driver_name',
			'driver_type',
			'driver_active'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

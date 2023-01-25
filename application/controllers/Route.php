<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route extends PS_Controller
{
	public $menu_code = 'ROUTE';
	public $menu_group_code = 'TR';
	public $title = 'เส้นทางขนส่ง';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'route';
		$this->load->model('route_model');
  }



  public function index()
  {

		$filter = array(
			'name' => get_filter('name', 'route_name', ''),
			'active' => get_filter('active', 'route_active', 'all')
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

		$rs = $this->route_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('route/route_list', $filter);
  }



	public function add_new()
	{
		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('route/route_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;

		$name = trim($this->input->post('name'));
		$level = $this->input->post('level');
		$active = $this->input->post('active');

		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			if(!empty($name))
			{
				if( ! $this->route_model->is_exists($name))
				{
					$arr = array(
						'name' => $name,
						'level' => $level,
						'active' => $active == 1 ? 1 : 0
					);

					if( ! $this->route_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "เส้นทางซ้ำ";
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
		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			$rs = $this->route_model->get($id);

			if(!empty($rs))
			{
				$this->load->view('route/route_edit', $rs);
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

		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			$id = $this->input->post('id');
			$name = trim($this->input->post('name'));
			$level = $this->input->post('level');
			$active = $this->input->post('active');

			if( ! $this->route_model->is_exists($name, $id))
			{
				$arr = array(
					'name' => $name,
					'level' => $level,
					'active' => $active == 1 ? 1 : 0
				);

				if( ! $this->route_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เส้นทางซ้ำ";
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

		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			$id = $this->input->post('id');

			if(!empty($id))
			{
				$has_transection = $this->route_model->has_transection($id);

				if(! $has_transection)
				{
					if(! $this->route_model->delete($id))
					{
						$sc = FALSE;
						$this->error = "Delete Failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Delete failed : This Route already has transections";
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
			'route_name',
			'route_active'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

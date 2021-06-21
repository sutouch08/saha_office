<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends PS_Controller{
	public $menu_code = 'USER'; //--- Add/Edit Users
	public $menu_group_code = 'AD'; //--- System security
	public $title = 'Users';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users';
  }



  public function index()
  {
		$filter = array(
			'uname' => get_filter('uname', 'username', ''),
			'emp_name' => get_filter('emp_name', 'emp_name', ''),
			'sale_team' => get_filter('sale_team', 'sale_team', 'all'),
			'sale_id' => get_filter('sale_id', 'sale_id', 'all'),
			'user_group' => get_filter('user_group', 'user_group', 'all'),
			'status' => get_filter('status', 'user_status', 'all'),
			'order_by' => get_filter('order_by', 'user_order_by', 'id'),
			'sort_by' => get_filter('sort_by', 'user_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->user_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->user_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('users/users_list', $filter);
  }



	public function add_new()
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('users/user_add');
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
				$arr = array(
					'uname' => trim($this->input->post('uname')),
					'emp_id' => get_null(trim($this->input->post('emp_id'))),
					'emp_name' => trim($this->input->post('emp_name')),
					'sale_id' => get_null(trim($this->input->post('sale_id'))),
					'pwd' => password_hash(trim($this->input->post('pwd')), PASSWORD_DEFAULT),
					'uid' => md5(uniqid()),
					'sale_team' => $this->input->post('sale_team'),
					'ugroup' => $this->input->post('ugroup'),
					'department_code' => $this->input->post('department'),
					'division_code' => $this->input->post('division'),
					'status' => $this->input->post('status')
				);

				if(! $this->user_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Add new user failed";
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


  public function is_exists_uname()
	{
		$sc = TRUE;
		$uname = trim($this->input->post('uname'));
		$old_uname = trim($this->input->post('old_uname'));

		if($this->user_model->is_exists_uname($uname, $old_uname))
		{
			$sc = FALSE;
			$this->error = "User Name ซ้ำ";
		}

		$this->response($sc);
	}




	public function edit($id)
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$rs = $this->user_model->get($id);
			if(!empty($rs))
			{
				$ds['data'] = $rs;
				$this->load->view('users/user_edit', $ds);
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

				$arr = array(
					'uname' => trim($this->input->post('uname')),
					'emp_id' => get_null(trim($this->input->post('emp_id'))),
					'emp_name' => trim($this->input->post('emp_name')),
					'sale_id' => get_null(trim($this->input->post('sale_id'))),
					'sale_team' => $this->input->post('sale_team'),
					'ugroup' => $this->input->post('ugroup'),
					'department_code' => $this->input->post('department'),
					'division_code' => $this->input->post('division'),
					'status' => $this->input->post('status')
				);

				if(! $this->user_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update user failed";
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
			$id = $this->input->post('id');

			$user = $this->user_model->get($id);
			if(!empty($user))
			{
				//--- check transection
				if($this->user_model->isApprover($user->uname))
				{
					$sc = FALSE;
					$this->error = "Delete Failed : User is approver";
				}

				if($sc === TRUE && $this->user_model->has_quotation_transection($user->id))
				{
					$sc = FALSE;
					$this->error = "Delete Failed : User has Quotation transections";
				}

				if($sc === TRUE && $this->user_model->has_customer_transection($user->id))
				{
					$sc = FALSE;
					$this->error = "Delete Failed : User has Customer transection";
				}

				if($sc === TRUE)
				{
					if(! $this->user_model->delete($user->id))
					{
						$sc = FALSE;
						$this->error = "Delete Failed";
					}
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid User ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}


	//---- Reset password by Administrator
	public function reset_password($id)
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$this->title = 'Reset Password';
			$rs = $this->user_model->get($id);
			if(!empty($rs))
			{
				$data['data'] = $rs;
				$this->load->view('users/user_reset_pwd', $data);
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



	public function change_password()
	{
		$sc = TRUE;

		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if(!empty($this->input->post('id')) && !empty($this->input->post('pwd')))
			{
				$pwd = trim($this->input->post('pwd'));

				if(!empty($pwd))
				{
					$id = $this->input->post('id');
					$pwd = password_hash($pwd, PASSWORD_DEFAULT);

					$arr = array(
						'pwd' => $pwd
					);

					if( ! $this->user_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update Failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Password Can not be empty";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter !";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}



	public function get_sale_name($id)
	{
		$name = $this->user_model->get_saleman_name($id);
		return $name;
	}

	public function clear_filter()
	{

		$filter = array(
			'username',
			'emp_name',
			'sale_id',
			'sale_team',
			'user_group',
			'user_status',
			'user_order_by',
			'user_sort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>

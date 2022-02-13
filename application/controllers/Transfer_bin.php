<?php
class Transfer_bin extends PS_Controller{
	public $menu_code = 'Location';
	public $menu_group_code = 'AD';
	public $title = 'Bin Location';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'transfer_bin';
    $this->load->model('transfer_bin_model');
  }



  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'tb_code', ''),
			'name' => get_filter('name', 'tb_name', ''),
      'fromDate' => get_filter('fromDate', 'tb_fromDate', ''),
      'toDate' => get_filter('toDate', 'tb_toDate', ''),
      'uname' => get_filter('uname', 'tb_uname', ''),
			'order_by' => get_filter('order_by', 'tb_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'tb_sort_by', 'DESC')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->transfer_bin_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->transfer_bin_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('transfer_bin/bin_list', $filter);
  }


	public function add_new()
	{
		if($this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('transfer_bin/transfer_bin_add');
		}
		else
		{
			$this->deny_page();
		}
	}



	public function add()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));

		if($this->isAdmin OR $this->isSuperAdmin)
		{
			if(!empty($code))
			{
				if(!empty($name))
				{
					$arr = array(
						'code' => $code,
						'name' => $name,
						'uname' => $this->user->uname,
						'user_id' => $this->user->id,
						'createDate' => now()
					);

					if(! $this->transfer_bin_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert data failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing 'Name' Parameter";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing 'Code' Parameter";
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
			$rs = $this->transfer_bin_model->get_by_id($id);

			if(!empty($rs))
			{
				$data['data'] = $rs;
				$this->load->view('transfer_bin/transfer_bin_edit', $data);
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
			$id = $this->input->post('id');
			$name = trim($this->input->post('name'));

			$arr = array(
				'name' => $name
			);

			if(! $this->transfer_bin_model->update($id, $arr))
			{
				$sc = FALSE;
				$this->error = "Update Failed";
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
		$id = $this->input->post('id');

		if(!empty($id))
		{
			if(! $this->transfer_bin_model->delete($id))
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

		$this->response($sc);
	}



	public function is_exists_code()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$old_code = $this->input->post('old_code');

		if($this->transfer_bin_model->is_exists_code($code, $old_code))
		{
			$sc = FALSE;
			$this->error = "รหัสซ้ำ กรุณาใช้รหัสอื่น";
		}

		$this->response($sc);
	}


	public function clear_filter()
	{
		$filter = array(
			'tb_code', 'tb_name', 'tb_fromDate', 'tb_toDate', 'tb_uname', 'tb_order_by', 'tb_sort_by'
		);

		clear_filter($filter);

		echo 'done';
	}


} //--- end class


 ?>

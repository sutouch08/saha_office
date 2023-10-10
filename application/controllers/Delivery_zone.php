<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_zone extends PS_Controller
{
	public $menu_code = 'DEZONE';
	public $menu_group_code = 'TR';
	public $title = 'สายการขนส่ง';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'delivery_zone';
		$this->load->model('delivery_zone_model');
  }



  public function index()
  {

		$filter = array(
			'zipCode' => get_filter('zipCode', 'zipCode', ''),
			'district' => get_filter('district', 'district', ''),
			'province' => get_filter('province', 'province', ''),
			'active' => get_filter('active', 'zone_active', 'all')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
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

			$rs = $this->delivery_zone_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

			$filter['data'] = $rs;

			$this->pagination->initialize($init);
			$this->load->view('delivery_zone/delivery_zone_list', $filter);
		}
  }



	public function add_new()
	{
		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			$this->load->view('delivery_zone/delivery_zone_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;
		$district = $this->input->post('district');
		$province = $this->input->post('province');
		$zipCode = $this->input->post('zipCode');
		$active = $this->input->post('active');

		if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin)
		{
			if(! empty($district) && ! empty($province) && ! empty($zipCode))
			{
				if( ! $this->delivery_zone_model->is_exists($district, $province))
				{
					$arr = array(
						'district' => $district,
						'province' => $province,
						'zipCode' => $zipCode,
						'active' => $active == 1 ? 1 : 0
					);

					if( ! $this->delivery_zone_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "{$district} >> {$province} >> {$zipCode} มีในระบบแล้ว";
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
			$rs = $this->delivery_zone_model->get($id);

			if(!empty($rs))
			{
				$this->load->view('delivery_zone/delivery_zone_edit', $rs);
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
			$district = $this->input->post('district');
			$province = $this->input->post('province');
			$zipCode = $this->input->post('zipCode');
			$active = $this->input->post('active');

			if( ! $this->delivery_zone_model->is_exists($district, $province, $id))
			{
				$arr = array(
					'district' => $district,
					'province' => $province,
					'zipCode' => $zipCode,
					'active' => $active == 1 ? 1 : 0
				);

				if( ! $this->delivery_zone_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "{$district} >> {$province} >> {$zipCode} มีในระบบแล้ว";
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

			if( ! empty($id))
			{
				if(! $this->delivery_zone_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Delete Failed";
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


  public function district()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('amphur', $_REQUEST['term'])
    ->group_by('amphur')
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


	public function province()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('province', $_REQUEST['term'])
		->group_by('amphur')
    ->group_by('province')
    ->limit(20)
		->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


	public function zipcode()
  {
    $sc = array();
    $adr = $this->db
		->like('zipcode', $_REQUEST['term'])
		->group_by('amphur')
		->group_by('province')
		->limit(20)
		->get('address_info');

    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }

  public function clear_filter()
	{
		$filter = array(
			'zipCode',
			'district',
			'province',
			'zone_active'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

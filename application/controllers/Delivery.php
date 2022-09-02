<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends PS_Controller
{
	public $menu_code = 'DELIVERY';
	public $menu_group_code = 'TR';
	public $title = 'การจัดส่ง';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'delivery';
		$this->load->model('delivery_model');
		$this->load->model('driver_model');
		$this->load->model('vehicle_model');
			$this->load->model('route_model');
		$this->load->helper('transport');
  }



  public function index()
  {

		$filter = array(
			'code' => get_filter('code', 'de_code', ''),
			'driver' => get_filter('driver', 'de_driver', 'all'),
			'vehicle' => get_filter('vecicle', 'de_vehicle', 'all'),
			'route' => get_filter('route', 'de_route', 'all'),
			'fromDate' => get_filter('fromDate', 'de_formDate', ''),
			'toDate' => get_filter('toDate', 'de_toDate', ''),
			'uname' => get_filter('uname', 'de_uname', 'all'),
			'status' => get_filter('status', 'de_status', 'all')
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

		$rs = $this->delivery_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('delivery/delivery_list', $filter);
  }



	public function add_new()
	{
		$supportList = $this->driver_model->get_all(array('E'), TRUE);

		$this->load->view('delivery/delivery_add', array('supportList' => $supportList));

	}


	public function add()
	{
		$sc = TRUE;

		$date_add = db_date($this->input->post('date'));
		$vehicle_id = $this->input->post('vehicle');
		$driver_id = $this->input->post('driver');
		$route_id = $this->input->post('route');
		$support = $this->input->post('support');

		$code = $this->get_new_code($date_add);

		if(!empty($code))
		{
			$car = $this->vehicle_model->get($vehicle_id);
			$driver = $this->driver_model->get($driver_id);
			$route = $this->route_model->get($route_id);

			if( ! empty($car))
			{
				if( ! empty($driver))
				{
					$arr = array(
						'date_add' => $date_add,
						'code' => $code,
						'driver_id' => $driver->emp_id,
						'driver_name' => $driver->emp_name,
						'vehicle_id' => $car->id,
						'vehicle_name' => $car->name,
						'route_id' => $route->id,
						'route_name' => $route->name,
						'status' => 'F',
						'uname' => $this->user->uname
					);

					if( ! $this->delivery_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Add Document failed";
					}
					else
					{
						$arr = array(
							'delivery_code' => $code,
							'emp_id' => $driver->emp_id,
							'emp_name' => $driver->emp_name,
							'type' => 'D',
							'vehicle_name' => $car->name,
							'date_add' => $date_add
						);

						$this->delivery_model->add_delivery_employee($arr);

						if( ! empty($support))
						{
							foreach($support as $emp_id)
							{
								$emp = $this->driver_model->get($emp_id);

								if( ! empty($emp))
								{
									$arr = array(
										'delivery_code' => $code,
										'emp_id' => $emp->emp_id,
										'emp_name' => $emp->emp_name,
										'type' => $emp->type,
										'vehicle_name' => $car->name,
										'date_add' => $date_add
									);

									$this->delivery_model->add_delivery_employee($arr);
								}
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบชื่อพนักงานขับรถ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบทะเบียนรถ";
			}
		}


		if($sc === TRUE)
		{
			$arr = array(
				'status' => 'success',
				'code' => $code
			);

			echo json_encode($arr);
		}
		else
		{
			echo $this->error;
		}
	}




	public function edit($code)
	{
		$order = $this->delivery_model->get($code);

		if( ! empty($order))
		{
			$empList = $this->delivery_model->get_delivery_employee('E', $code);
			$emp = array();

			if(!empty($empList))
			{
				foreach($empList as $rs)
				{
					$emp[$rs->emp_id] = $rs->emp_name;
				}
			}

			$ds = array(
				'doc' => $order,
				'details' => $this->delivery_model->get_details($code),
				'emp' => $emp,
				'supportList' => 	$this->driver_model->get_all(array('E'), TRUE)
			);

			$this->load->view('delivery/delivery_edit', $ds);
		}
		else
		{
			$this->error_page();
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

			if( ! $this->delivery_model->update($emp_id, $arr))
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
				$has_transection = $this->delivery_model->has_transection($emp_id);

				if(! $has_transection)
				{
					if(! $this->delivery_model->delete($emp_id))
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




	public function get_doc_num($doc_type = NULL)
	{
		if(!empty($doc_type))
		{
			$term = $_REQUEST['term'];

			if($doc_type === 'DO')
			{
				$query = $this->ms->like('DocNum', $term)->where()
			}
		}
	}


	function get_new_code($date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : $date;
		$Y = date('y', strtotime($date));
		$M = date('m', strtotime($date));
		$prefix = getConfig('PREFIX_DELIVERY');
		$run_digit = getConfig(('RUN_DIGIT_DELIVERY'));
		$pre = $prefix.'-'.$Y.$M;

		$code = $this->delivery_model->get_max_code($pre);

		if( ! empty($code))
		{
			$run_no = mb_substr($code, ($run_digit * (-1)), NULL, 'UTF-8') + 1;
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', $run_no);
		}
		else
		{
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', '001');
		}

		return $new_code;
	}



  public function clear_filter()
	{
		$filter = array(
			'de_code',
			'de_driver',
			'de_vehicle',
			'de_route',
			'de_fromDate',
			'de_toDate',
			'de_uname',
			'de_status'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

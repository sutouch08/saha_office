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
			'shipFromDate' => get_filter('shipFromDate', 'shipFromDate', ''),
			'shipToDate' => get_filter('shipToDate', 'shipToDate', ''),
			'uname' => get_filter('uname', 'de_uname', ''),
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


	public function save_add()
	{
		$sc = TRUE;
		$docDate = db_date($this->input->post('date'));
		$shipDate = db_date($this->input->post('shipDate'));
		$vehicle_id = $this->input->post('vehicle');
		$driver_id = $this->input->post('driver');
		$route_id = $this->input->post('route');
		$support = $this->input->post('support');
		$DocTotal = $this->input->post('DocTotal');
		$details = json_decode($this->input->post('details'));

		$code = $this->get_new_code($docDate);

		if(!empty($code))
		{
			$car = $this->vehicle_model->get($vehicle_id);
			$driver = $this->driver_model->get($driver_id);
			$route = $this->route_model->get($route_id);

			$this->db->trans_begin();

			if( ! empty($car))
			{
				if( ! empty($driver))
				{
					$arr = array(
						'DocDate' => $docDate,
						'date_add' => date('Y-m-d'),
						'ShipDate' => $shipDate,
						'code' => $code,
						'driver_id' => $driver->emp_id,
						'driver_name' => $driver->emp_name,
						'vehicle_id' => $car->id,
						'vehicle_name' => $car->name,
						'route_id' => $route->id,
						'route_name' => $route->name,
						'DocTotal' => $DocTotal,
						'uname' => $this->user->uname
					);

					if( ! $this->delivery_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Add Document failed";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'delivery_code' => $code,
							'emp_id' => $driver->emp_id,
							'emp_name' => $driver->emp_name,
							'type' => 'D',
							'vehicle_name' => $car->name,
							'date_add' => date('Y-m-d')
						);

						if(! $this->delivery_model->add_delivery_employee($arr))
						{
							$sc = FALSE;
							$this->error = "Create driver list failed";
						}
					}

					if($sc === TRUE)
					{
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
										'date_add' => date('Y-m-d')
									);

									$this->delivery_model->add_delivery_employee($arr);
								}
							}
						}
					}

					if($sc === TRUE)
					{
						if( ! empty($details))
						{
							foreach($details as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}

								$is_exists = FALSE;

								if($rs->shipType == 'P' && ($rs->docType == 'DO' OR $rs->docType == 'IV'))
								{
									$is_exists = $this->delivery_model->is_loaded($rs->docNum, $rs->docType, $code);
								}

								if( ! $is_exists)
								{
									$arr = array(
										'delivery_code' => $code,
										'CardCode' => $rs->cardCode,
										'CardName' => $rs->cardName,
										'Address' => $rs->address,
										'contact' => $rs->contact,
										'type' => $rs->shipType,
										'DocType' => empty($rs->docType) ? NULL : $rs->docType,
										'DocNum' => empty($rs->docNum) ? NULL : $rs->docNum,
										'DocTotal' => empty($rs->docTotal) ? 0.00 : $rs->docTotal,
										'remark' => get_null($rs->remark),
										'ShipDate' => $shipDate,
										'ShipToCode' => get_null(trim($rs->ShipToCode)),
										'Street' => get_null(trim($rs->Street)),
										'Block' => get_null(trim($rs->Block)),
										'City' => get_null(trim($rs->City)),
										'County' => get_null(trim($rs->County)),
										'Country' => get_null(trim($rs->Country)),
										'ZipCode' => get_null(trim($rs->ZipCode)),
										'Phone' => get_null(trim($rs->Phone)),
										'WorkDate' => get_null(trim($rs->WorkDate)),
										'WorkTime' => get_null(trim($rs->WorkTime))
									);

									if( ! $this->delivery_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert detail failed";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "{$rs->docType}-{$rs->docNum} ถูกโหลดเข้าเอกสารอื่นแล้ว";
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


			if($sc === TRUE)
			{
				$this->db->trans_commit();

				$arr = array(
					'code' => $code,
					'user_id' => $this->user->id,
					'uname' => $this->user->uname,
					'emp_name' => $this->user->emp_name,
					'action' => 'add'
				);

				$this->delivery_model->add_logs($arr);
			}
			else
			{
				$this->db->trans_rollback();
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



	public function save_update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$docDate = db_date($this->input->post('date'));
		$shipDate = db_date($this->input->post('shipDate'));
		$vehicle_id = $this->input->post('vehicle');
		$driver_id = $this->input->post('driver');
		$route_id = $this->input->post('route');
		$support = $this->input->post('support');
		$DocTotal = $this->input->post('DocTotal');
		$details = json_decode($this->input->post('details'));

		if(! empty($code) && ! empty($details))
		{
			$doc = $this->delivery_model->get($code);

			if(! empty($doc))
			{
				if($doc->status == 'O')
				{
					$car = $this->vehicle_model->get($vehicle_id);
					$driver = $this->driver_model->get($driver_id);
					$route = $this->route_model->get($route_id);

					$this->db->trans_begin();

					if( ! empty($car))
					{
						if(! empty($driver))
						{
							$arr = array(
								'DocDate' => $docDate,
								'ShipDate' => $shipDate,
								'driver_id' => $driver->emp_id,
								'driver_name' => $driver->emp_name,
								'vehicle_id' => $car->id,
								'vehicle_name' => $car->name,
								'route_id' => $route->id,
								'route_name' => $route->name,
								'DocTotal' => $DocTotal,
								'uname' => $this->user->uname
							);

							if( ! $this->delivery_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update header failed";
							}

							if($sc === TRUE)
							{
								if($this->delivery_model->drop_delivery_employee($code))
								{
									$arr = array(
										'delivery_code' => $code,
										'emp_id' => $driver->emp_id,
										'emp_name' => $driver->emp_name,
										'type' => 'D',
										'vehicle_name' => $car->name,
										'date_add' => date('Y-m-d')
									);

									if(! $this->delivery_model->add_delivery_employee($arr))
									{
										$sc = FALSE;
										$this->error = "Create driver list failed";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "Drop current driver and employee failed";
								}
							}

							if($sc === TRUE)
							{
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
												'date_add' => date('Y-m-d')
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


					if($sc === TRUE)
					{
						//--- drop current details
						if($this->delivery_model->drop_details($code))
						{
							foreach($details as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}

								$is_exists = FALSE;

								if($rs->shipType == 'P' && ($rs->docType == 'DO' OR $rs->docType == 'IV'))
								{
									$is_exists = $this->delivery_model->is_loaded($rs->docNum, $rs->docType, $code);
								}

								if( ! $is_exists)
								{
									$arr = array(
										'delivery_code' => $code,
										'CardCode' => $rs->cardCode,
										'CardName' => $rs->cardName,
										'Address' => $rs->address,
										'contact' => $rs->contact,
										'type' => $rs->shipType,
										'DocType' => empty($rs->docType) ? NULL : $rs->docType,
										'DocNum' => empty($rs->docNum) ? NULL : $rs->docNum,
										'DocTotal' => empty($rs->docTotal) ? 0.00 : $rs->docTotal,
										'remark' => get_null($rs->remark),
										'ShipDate' => $shipDate,
										'ShipToCode' => get_null(trim($rs->ShipToCode)),
										'Street' => get_null(trim($rs->Street)),
										'Block' => get_null(trim($rs->Block)),
										'City' => get_null(trim($rs->City)),
										'County' => get_null(trim($rs->County)),
										'Country' => get_null(trim($rs->Country)),
										'ZipCode' => get_null(trim($rs->ZipCode)),
										'Phone' => get_null(trim($rs->Phone)),
										'WorkDate' => get_null(trim($rs->WorkDate)),
										'WorkTime' => get_null(trim($rs->WorkTime))
									);

									if( ! $this->delivery_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert detail failed";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "{$rs->docType}-{$rs->docNum} ถูกโหลดเข้าเอกสารอื่นแล้ว";
								}
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Drop current details failed";
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();

						$arr = array(
							'code' => $code,
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'emp_name' => $this->user->emp_name,
							'action' => 'edit'
						);

						$this->delivery_model->add_logs($arr);
					}
					else
					{
						$this->db->trans_rollback();
					}
				}
				else
				{
					$sc = FALSE;

					if($doc->status == 'R')
					{
						$this->error = "ไม่สามารถบันทึกรายการได้เนื่องจากเอกสารถูก release แล้ว";
					}
					else if($doc->status == 'D')
					{
						$this->error = "ไม่สามารถบันทึกรายการได้เนื่องจากเอกสารถูกยกเลิกแล้ว";
					}
					else if($doc->status == 'C')
					{
						$this->error = "ไม่สามารถบันทึกรายการได้เนื่องจากเอกสารถูก close แล้ว";
					}
					else {
						$this->error = "Invalid Document Status";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Code";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Empty data or Invalid data format";
		}

		$this->response($sc);
	}



	public function edit($code)
	{
		$order = $this->delivery_model->get($code);

		if( ! empty($order))
		{
			if($order->status == 'O')
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
					'supportList' => 	$this->driver_model->get_all(array('E'), TRUE),
					'logs' => $this->delivery_model->get_logs($code)
				);

				$this->load->view('delivery/delivery_edit', $ds);
			}
			else
			{
				$this->view_detail($code);
			}
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



	public function view_detail($code)
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
				'logs' => $this->delivery_model->get_logs($code)
			);

			$this->load->view('delivery/delivery_view_detail', $ds);
		}
		else
		{
			$this->error_page();
		}
	}



	public function do_release()
	{
		$sc = TRUE;
		$message = "";
		$err = 0;

		$code = $this->input->post('code');

		$doc = $this->delivery_model->get($code);

		if(! empty($doc))
		{
			if( $doc->status == 'O')
			{
				$details = $this->delivery_model->get_details($code);

				if( ! empty($details))
				{
					foreach($details as $rs)
					{
						if($rs->type == 'P' && ($rs->DocType == 'DO' OR $rs->DocType == 'IV'))
						{
							if($this->delivery_model->is_loaded($rs->DocNum, $rs->DocType, $code))
							{
								$err++;
								$message .= "{$rs->DocType}-{$rs->DocNum} ถูกโหลดเข้าเอกสารอื่นแล้ว ".PHP_EOL;
							}
						}
					}

					if($err > 0)
					{
						$sc = FALSE;
						$this->error = $message;
					}
				}


				if($sc === TRUE)
				{
					$this->db->trans_begin();

					if($this->delivery_model->release_order($code))
					{
						if(! $this->delivery_model->release_details($code))
						{
							$sc = FALSE;
							$this->error = "Release delivery rows failed";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Release failed";
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();

						$arr = array(
							'code' => $code,
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'emp_name' => $this->user->emp_name,
							'action' => 'release'
						);

						$this->delivery_model->add_logs($arr);
					}
					else
					{
						$this->db->trans_rollback();
					}
				}
			}
			else
			{
				if($doc->status != 'R')
				{
					$sc = FALSE;
					$this->error = "Invalid Document Status";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document Number";
		}

		$this->response($sc);
	}



	public function un_release()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		$doc = $this->delivery_model->get($code);

		if(! empty($doc))
		{
			if( $doc->status == 'R')
			{
				$this->db->trans_begin();
				if($this->delivery_model->un_release_order($code))
				{
					if(! $this->delivery_model->un_release_details($code))
					{
						$sc = FALSE;
						$this->error = "Unrelease delivery rows failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Unrelease failed";
				}

				if($sc === TRUE)
				{
					$this->db->trans_commit();

					$arr = array(
						'code' => $code,
						'user_id' => $this->user->id,
						'uname' => $this->user->uname,
						'emp_name' => $this->user->emp_name,
						'action' => 'unrelease'
					);

					$this->delivery_model->add_logs($arr);
				}
				else
				{
					$this->db->trans_rollback();
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Document Number";
		}

		$this->response($sc);
	}


	public function update_and_close()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$details = json_decode($this->input->post('rows'));

		$doc = $this->delivery_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'R')
			{
				if( ! empty($details))
				{
					$this->db->trans_begin();
					foreach($details as $rs)
					{
						if($sc === FALSE)
						{
							break;
						}

						$finish_date = $rs->result_status == 4 ? date('Y-m-d') : NULL;
						$arr = array(
							'result_status' => $rs->result_status,
							'line_status' => 'C',
							'finish_date' => $finish_date
						);

						if(! $this->delivery_model->update_detail($rs->id, $arr))
						{
							$sc = FALSE;
							$this->error = "Update line status failed";
						}
					}

					if($sc === TRUE)
					{
						//--- close document
						$arr = array(
							'status' => 'C'
						);

						if( ! $this->delivery_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Close document failed";
						}
						else
						{
							$arr = array(
								'code' => $code,
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'emp_name' => $this->user->emp_name,
								'action' => 'close'
							);

							$this->delivery_model->add_logs($arr);
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
					}
					else
					{
						$this->db->trans_rollback();
					}

					if($sc === TRUE)
					{
						//--- update document on sap (จัดส่งแล้ว ไม่สารถจัดส่งได้อีก)
						$ds = $this->delivery_model->get_finish_details($code);

						if( ! empty($ds))
						{
							foreach($ds as $rs)
							{
								$arr = array(
						      'U_Deliver_doc' => $code,
						      'U_Deliver_status' => $rs->result_status
						    );

								switch ($rs->DocType) {
									case 'IV':
										$this->delivery_model->finish_iv_doc_num($rs->DocNum, $arr);
									break;
									case 'DO':
										$this->delivery_model->finish_do_doc_num($rs->DocNum, $arr);
									break;
									case 'CN':
										$this->delivery_model->finish_cn_doc_num($rs->DocNum, $arr);
									break;
									case 'PB':
										$this->delivery_model->finish_pb_doc_num($rs->DocNum, $arr);
									break;
								}
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Empty rows data or invalid format";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document number";
		}

		$this->response($sc);
	}



	public function un_close_delivery()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		$doc = $this->delivery_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'C')
			{
				$details = $this->delivery_model->get_details($code);

				if( ! empty($details))
				{
					$this->db->trans_begin();

					foreach($details as $rs)
					{
						if($sc === FALSE)
						{
							break;
						}


						$arr = array(
							'result_status' => 1,
							'line_status' => 'R',
							'finish_date' => NULL
						);

						if(! $this->delivery_model->update_detail($rs->id, $arr))
						{
							$sc = FALSE;
							$this->error = "Update line status failed";
						}
					}

					if($sc === TRUE)
					{
						//--- update document status to released
						$arr = array(
							'status' => 'R',
							'update_user' => $this->user->uname
						);

						if( ! $this->delivery_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Close document failed";
						}
						else
						{
							$arr = array(
								'code' => $code,
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'emp_name' => $this->user->emp_name,
								'action' => 'unclose'
							);

							$this->delivery_model->add_logs($arr);
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
					}
					else
					{
						$this->db->trans_rollback();
					}

					if($sc === TRUE)
					{
						//--- update document on sap (จัดส่งแล้ว ไม่สารถจัดส่งได้อีก)
						$ds = $details;

						if( ! empty($ds))
						{
							foreach($ds as $rs)
							{
								$arr = array(
						      'U_Deliver_doc' => NULL,
						      'U_Deliver_status' => NULL
						    );

								switch ($rs->DocType) {
									case 'IV':
										$this->delivery_model->finish_iv_doc_num($rs->DocNum, $arr);
									break;
									case 'DO':
										$this->delivery_model->finish_do_doc_num($rs->DocNum, $arr);
									break;
									case 'CN':
										$this->delivery_model->finish_cn_doc_num($rs->DocNum, $arr);
									break;
									case 'PB':
										$this->delivery_model->finish_pb_doc_num($rs->DocNum, $arr);
									break;
								}
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Empty rows data or invalid format";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document number";
		}

		$this->response($sc);
	}



	public function get_doc_num($doc_type, $ship_type)
	{
		$sc = array();
		$ds = NULL;

		if(! empty($doc_type) && ! empty($ship_type))
		{
			$term = $_REQUEST['term'];

			if($doc_type === 'DO')
			{
				$this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ODLN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->like('O.DocNum', $term);

				if($ship_type == 'P')
				{
					$this->ms
					->group_start()
					->where('O.U_Deliver_status IS NULL', NULL, FALSE)
					->or_where('O.U_Deliver_status !=',4)
					->group_end();
				}

				$rs = $this->ms->order_by('O.DocNum', 'DESC')->limit(50)->get();

				if($rs->num_rows() > 0)
				{
					$ds = $rs->result();
				}
			}


			if($doc_type === 'IV')
			{
				$this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('OINV AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->like('O.DocNum', $term);

				if($ship_type == 'P')
				{
					$this->ms
					->group_start()
					->where('O.U_Deliver_status IS NULL', NULL, FALSE)
					->or_where('O.U_Deliver_status !=',4)
					->group_end();
				}

				$rs = $this->ms->order_by('O.DocNum', 'DESC')->limit(50)->get();

				if($rs->num_rows() > 0)
				{
					$ds = $rs->result();
				}
			}

			if($doc_type === 'CN')
			{
				$this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ORDN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->like('O.DocNum', $term);

				if($ship_type == 'P')
				{
					$this->ms
					->group_start()
					->where('O.U_Deliver_status IS NULL', NULL, FALSE)
					->or_where('O.U_Deliver_status !=',4)
					->group_end();
				}

				$rs = $this->ms->order_by('O.DocNum', 'DESC')->limit(50)->get();

				if($rs->num_rows() > 0)
				{
					$ds = $rs->result();
				}
			}

			if($doc_type === 'PB')
			{
				$this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ODLN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdrsType = 'S'", 'left')
				->like('O.DocNum', $term);

				if($ship_type == 'P')
				{
					$this->ms
					->group_start()
					->where('O.U_Deliver_status IS NULL', NULL, FALSE)
					->or_where('O.U_Deliver_status !=',4)
					->group_end();
				}

				$rs = $this->ms->order_by('O.DocNum', 'DESC')->limit(50)->get();

				if($rs->num_rows() > 0)
				{
					$ds = $rs->result();
				}
			}

			if(! empty($ds))
			{
				foreach($ds as $rs)
				{
					$sc[] = array(
						'CardCode' => $rs->CardCode,
						'CardName' => $rs->CardName,
						'ContactName' => $rs->Contact.' '.$rs->Phone,
						'shipTo' => $rs->Street.' '.$rs->Block.' '.$rs->City.' '.$rs->County.' '.$rs->ZipCode,
						'ShipToCode' => $rs->ShipToCode,
						'Street' => $rs->Street,
						'Block' => $rs->Block,
						'City' => $rs->City,
						'County' => $rs->County,
						'Country' => $rs->Country,
						'ZipCode' => $rs->ZipCode,
						'Phone' => $rs->Phone,
						'Contact' => $rs->Contact,
						'WorkDate' => $rs->WorkDate,
						'WorkTime' => $rs->WorkTime,
						'label' => $rs->DocNum,
						'docTotal' => number($rs->DocTotal, 2)
					);
				}
			}
			else
			{
				$sc[] = "Not found";
			}
		}

		echo json_encode($sc);
	}



	public function get_document_data()
	{
		$sc = TRUE;
		$shipType = $this->input->get('shipType');
		$docType = $this->input->get('docType');
		$docNum = $this->input->get('docNum');
		$code = get_null($this->input->get('delivery_code'));

		$ds = array();

		if(! empty($shipType) && ! empty($docType) && ! empty($docNum))
		{

			if($docType === 'DO')
			{
				$rs = $this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal, O.U_Deliver_doc, O.U_Deliver_status')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ODLN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->where('O.DocNum', $docNum)
				->get();
			}

			if($docType === 'IV')
			{
				$rs = $this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal, O.U_Deliver_doc, O.U_Deliver_status')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('OINV AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->where('O.DocNum', $docNum)
				->get();
			}

			if($docType === 'CN')
			{
				$rs = $this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal, O.U_Deliver_doc, O.U_Deliver_status')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ORDN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->where('O.DocNum', $docNum)
				->get();
			}

			if($docType === 'PB')
			{
				$rs = $this->ms
				->select('O.DocNum, O.CardCode, O.CardName, O.Address2, O.DocTotal, O.U_Deliver_doc, O.U_Deliver_status')
				->select('C.Address AS ShipToCode, C.Street, C.Block, C.ZipCode, C.City, C.County, C.Country')
				->select('C.U_Contract AS Contact, C.U_Tel AS Phone, C.U_SP_DateWork AS WorkDate, C.U_SP_DateTime AS WorkTime')
				->from('ODLN AS O')
				->join('CRD1 AS C', "O.CardCode = C.CardCode AND O.ShipToCode = C.Address AND C.AdresType = 'S'", 'left')
				->where('O.DocNum', $docNum)
				->get();
			}

			if($rs->num_rows() === 1)
			{
				$rd = $rs->row();

				if(($docType == 'DO' OR $docType == 'IV') && $shipType == 'P' )
				{
					if($rd->U_Deliver_status == 4)
					{
						$sc = FALSE;
						$this->error = "{$docType}-{$docNum} เคยถูกจัดส่งสำเร็จแล้วโดย {$rd->U_Deliver_doc}";
					}
					else
					{
						//---- check document exists in another delivery_doc
						$is_exists = $this->delivery_model->is_loaded($docNum, $docType, $code);

						if($is_exists)
						{
							$sc = FALSE;
							$this->error = "{$docType}-{$docNum} ถูกโหลดเข้าเอกสารอื่นแล้ว";
						}
					}
				}


				if($sc === TRUE)
				{
					$ship_type = ($shipType == 'P' && ($docType == 'CN' OR $docType == 'PB')) ? 'D' : $shipType;

					$ds = array(
						'CardCode' => $rd->CardCode,
						'CardName' => $rd->CardName,
						'ContactName' => $rd->Contact.' '.$rd->Phone,
						'shipTo' => $rd->Street.' '.$rd->Block.' '.$rd->City.' '.$rd->County.' '.$rd->ZipCode,
						'ShipToCode' => $rd->ShipToCode,
						'Street' => $rd->Street,
						'Block' => $rd->Block,
						'City' => $rd->City,
						'County' => $rd->County,
						'Country' => $rd->Country,
						'ZipCode' => $rd->ZipCode,
						'Phone' => $rd->Phone,
						'Contact' => $rd->Contact,
						'WorkDate' => $rd->WorkDate,
						'WorkTime' => $rd->WorkTime,
						'docTotal' => number($rd->DocTotal, 2),
						'shipType' => $ship_type
					);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Document not found !";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required paramater";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


	public function cancle_delivery()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		if( ! empty($code))
		{
			$doc = $this->delivery_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status === 'O')
				{
					$this->db->trans_begin();

					//--- cancle details
					$arr = array(
						'line_status' => 'D'
					);

					if( ! $this->delivery_model->update_details($code, $arr))
					{
						$sc = FALSE;
						$this->error = "ยกเลิกรายการไม่สำเร็จ";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'status' => 'D',
							'update_user' => $this->user->uname
						);

						if( ! $this->delivery_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "ยกเลิกเอกสารไม่สำเร็จ";
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();

						$arr = array(
							'code' => $code,
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'emp_name' => $this->user->emp_name,
							'action' => 'cancle'
						);

						$this->delivery_model->add_logs($arr);
					}
					else
					{
						$this->db->trans_rollback();
					}
				}
				else
				{
					if($doc->status !== 'D')
					{
						$sc = FALSE;
						$this->error = "สถานะเอกสารไม่ถูกต้อง";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);
	}



	public function printDelivery($code)
	{
		$this->load->library('printer');

		$doc = $this->delivery_model->get($code);

		if( ! empty($doc))
		{			
			$empList = $this->delivery_model->get_delivery_employee('E', $code);
			$empName = "";

			if( ! empty($empList))
			{
				$i = 1;
				foreach($empList as $emp)
				{
					$empName .= $i == 1 ? $emp->emp_name : ", ".$emp->emp_name;
					$i++;
				}
			}

			$ds = array(
				'doc' => $doc,
				'details' => $this->delivery_model->get_details($code),
				'empName' => $empName
			);

			$this->load->view('print/print_delivery', $ds);
		}
		else
		{
			$this->page_error();
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
			'de_status',
			'shipFromDate',
			'shipToDate'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

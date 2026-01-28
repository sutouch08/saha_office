<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_request extends PS_Controller
{
	public $menu_code = 'RTRQ';
	public $menu_group_code = 'IC';
	public $title = 'Return Request';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'return_request';
		$this->load->model('return_request_model');
		$this->load->model('customers_model');
		$this->load->model('item_model');
		$this->load->model('zone_model');
		$this->load->helper('currency');
		$this->load->helper('warehouse');
		$this->load->helper('return_request');
  }


	public function index()
	{
		$filter = array(
			'code' => get_filter('code', 'rt_code', ''),
			'customer' => get_filter('', 'rt_customer', ''),
			'sap_no' => get_filter('sap_no', 'rt_sap_no', ''),
			'warehouse' => get_filter('warehouse', 'rt_warehouse', 'all'),
			'user' => get_filter('user', 'rt_user', 'all'),
			'status' => get_filter('status', 'rt_status', 'all'),
			'tempStatus' => get_filter('tempStatus', 'rt_tempStatus', 'all'),
			'from_date' => get_filter('from_date', 'rt_from_date', ''),
			'to_date' => get_filter('to_date', 'rt_to_date', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();
			$segment = 3;
			$rows = $this->return_request_model->count_rows($filter);
			$filter['data'] = $this->return_request_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$this->pagination->initialize($init);
			$this->load->view('return_request/return_request_list', $filter);
		}
	}


	public function add_new()
	{
		$this->load->view('return_request/return_request_add');
	}


	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$customer = $this->customers_model->get($ds->customer_code);

			if( ! empty($customer))
			{
				$date_add = date('Y-m-d');
				$posting_date = db_date($ds->posting_date);
				$code = $this->get_new_code($date_add);

				$arr = array(
					'code' => $code,
					'date_add' => $date_add,
					'posting_date' => $posting_date,
					'CardCode' => $customer->CardCode,
					'CardName' => $customer->CardName,
					'WhsCode' => $ds->warehouse_code,
					'GroupNum' => $customer->GroupNum,
					'SlpCode' => $customer->SlpCode,
					'CntctCode' => $customer->CntctPrsn,
					'Currency' => $ds->Currency,
					'Rate' => $ds->Rate,
					'remark' => get_null(trim($ds->remark)),
					'user' => $this->user->uname
				);

				if( ! $this->return_request_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Failed to create new document";
				}
				else
				{
					$logs = array(
						'code' => $code,
						'user_id' => $this->user->id,
						'uname' => $this->user->uname,
						'emp_name' => $this->user->emp_name,
						'action' => 'add'
					);

					$this->return_request_model->add_logs($logs);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Customer";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'code' => $sc === TRUE ? $code : NULL
		);

		echo json_encode($arr);
	}


	public function edit($code)
	{
		$doc = $this->return_request_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'P')
			{
				$details = $this->return_request_model->get_details($code);
				$baseRef = [];

				if( ! empty($details))
				{
					foreach($details as $rs)
					{
						$rs->OpenQty = ( ! empty($rs->BaseType) && ! empty($rs->BaseEntry) && $rs->BaseLine != NULL) ? $this->return_request_model->get_open_qty($rs->BaseType, $rs->BaseEntry, $rs->BaseLine) : -1;
					}
				}

				$ds = array(
					'doc' => $doc,
					'details' => $details
				);

				$this->load->view('return_request/return_request_edit', $ds);
			}
			else
			{
				redirect($this->home.'/view_detail/'.$code);
			}
		}
		else
		{
			$this->page_error();
		}
	}


	public function get_base_ref($base_type, $customer_code)
	{
		$sc = TRUE;
		$ds = [];

		if( ! empty($base_type) && ! empty($base_type))
		{
			$customer = $this->customers_model->get($customer_code);

			if(empty($customer))
			{
				$sc = FALSE;
				$ds[] = "Invalid customer code";
			}

			if($sc === TRUE)
			{
				$txt = trim($_REQUEST['term']);
				$tb = $base_type == 'IV' ? 'OINV' : 'ODLN';

				$this->ms
				->select('DocDate, DocNum')
				->where('CardCode', $customer_code);

				if($txt != "*")
				{
					$this->ms->like('DocNum', $txt);
				}

				$rs = $this->ms
				->order_by('DocNum', 'DESC')
				->limit(100)
				->get($tb);

				if($rs->num_rows() > 0)
				{
					foreach($rs->result() as $row)
					{
						$ds[] = thai_date($row->DocDate, FALSE, '.').' | '.$row->DocNum;
					}
				}
			}
		}

		echo json_encode($ds);
	}


	public function load_base_ref_details()
	{
		$sc = TRUE;
		$ds = [];
		$baseRef = $this->input->post('baseRef');
		$baseType = $this->input->post('baseType');

		$exists = $baseType == 'DO' ? $this->return_request_model->is_exists_do($baseRef) : $this->return_request_model->is_exists_inv($baseRef);

		if( ! $exists)
		{
			$sc = FALSE;
			$this->error = "Document Number '{$baseRef}' does not exists";
		}
		else
		{
			$details = $baseType == 'DO' ? $this->return_request_model->get_do_details($baseRef) : $this->return_request_model->get_invoice_details($baseRef);

			if( ! empty($details))
			{
				$no = 1;

				foreach($details as $rs)
				{
					$ds[] = array(
						'no' => $no,
						'uid' => $baseType.$rs->DocNum.'-'.$rs->LineNum,
						"baseType" => $baseType,
						"DocEntry" => $rs->DocEntry,
						"LineNum" => $rs->LineNum,
						"DocNum" => $rs->DocNum,
						"ItemCode" => $rs->ItemCode,
						"Dscription" => $rs->Dscription,
						"PriceBefDi" => $rs->PriceBefDi,
						"PriceAfVAT" => $rs->PriceAfVAT,
						"Price" => $rs->Price,
						"PriceLabel" => number($rs->PriceBefDi, 2),
						"DiscPrcnt" => round($rs->DiscPrcnt, 2),
						"Qty" => $rs->Qty,
						"QtyLabel" => number($rs->Qty, 2),
						"OpenQty" => $rs->OpenQty,
						"OpenQtyLabel" => number($rs->OpenQty, 2),
						"Currency" => $rs->Currency,
						"Rate" => $rs->Rate,
						"SlpCode" => $rs->SlpCode,
						"VatGroup" => $rs->VatGroup,
						"VatPrcnt" => $rs->VatPrcnt,
						"VatSum" => $rs->VatSum,
						"UomCode" => $rs->UomCode,
						"UomCode2" => $rs->UomCode2,
						"UomEntry" => $rs->UomEntry,
						"UomEntry2" => $rs->UomEntry2,
						"unitMsr" => $rs->unitMsr,
						"unitMsr2" => $rs->unitMsr2,
						"NumPerMsr" => $rs->NumPerMsr,
						"NumPerMsr2" => $rs->NumPerMsr2
					);

					$no++;
				}
			}
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'data' => $sc === TRUE ? $ds : NULL
		);

		echo json_encode($arr);
	}


	public function view_detail($code)
	{
		$doc = $this->return_request_model->get($code);

		if( ! empty($doc))
		{
			$doc->WhsName = warehouse_name($doc->WhsCode);
			$ds = array(
				'doc' => $doc,
				'details' => $this->return_request_model->get_details($code),
				'logs' => $this->return_request_model->get_logs($code)
			);

			$this->load->view('return_request/return_request_view_detail', $ds);
		}
		else
		{
			$this->page_error();
		}
	}


	public function save()
	{
		$sc = TRUE;
		$ex = 1;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			// print_r($ds);
			// exit();
			$doc = $this->return_request_model->get($ds->code);

			if( ! empty($doc))
			{
				if($doc->status == 'P')
				{
					if( ! empty($ds->rows))
					{
						$po_refs = [];

						$this->db->trans_begin();

						//--- update header
						$arr = array(
							'posting_date' => db_date($ds->posting_date, FALSE),
							'CardCode' => trim($ds->customer_code),
							'CardName' => trim($ds->customer_name),
							'WhsCode' => $ds->warehouse_code,
							'Currency' => $ds->Currency,
							'Rate' => $ds->Rate,
							'DocTotal' => $ds->DocTotal,
							'VatSum' => $ds->VatSum,
							'TotalQty' => $ds->TotalQty,
							'update_user' => $this->user->uname,
							'date_upd' => now(),
							'status' => $ds->save_type,
							'remark' => get_null(trim($ds->remark))
						);

						if( ! $this->return_request_model->update($ds->code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update document header";
						}

						if($sc === TRUE)
						{
							if( ! $this->return_request_model->delete_details($ds->code))
							{
								$sc = FALSE;
								$this->error = "Failed to delete prevoius line items";
							}

							if($sc === TRUE && ! empty($ds->rows))
							{
								foreach($ds->rows as $rs)
								{
									if($sc === FALSE) { break; }

									$arr = array(
										'return_id' => $doc->id,
										'return_code' => $doc->code,
										'BaseType' => $rs->BaseType,
										'BaseRef' => $rs->BaseRef,
										'BaseEntry' => $rs->BaseEntry,
										'BaseLine' => $rs->BaseLine,
										'uid' => $rs->uid,
										'LineStatus' => $ds->save_type == 'C' ? 'C' : 'O',
										'ItemCode' => $rs->ItemCode,
										'ItemName' => $rs->ItemName,
										'PriceBefDi' => $rs->PriceBefDi,
										'PriceAfVAT' => $rs->PriceAfVAT,
										'Price' => $rs->Price,
										'DiscPrcnt' => $rs->DiscPrcnt,
										'Qty' => $rs->Qty,
										'LineTotal' => $rs->LineTotal,
										'WhsCode' => $ds->warehouse_code,
										'SlpCode' => $rs->SlpCode,
										'UomCode' => $rs->UomCode,
										'UomCode2' => $rs->UomCode2,
										'UomEntry' => $rs->UomEntry,
										'UomEntry2' => $rs->UomEntry2,
										'unitMsr' => $rs->unitMsr,
										'unitMsr2' => $rs->unitMsr2,
										'NumPerMsr' => $rs->NumPerMsr,
										'NumPerMsr2' => $rs->NumPerMsr2,
										'VatGroup' => $rs->VatGroup,
										'VatRate' => $rs->VatRate,
										'VatSum' => $rs->VatSum,
										'Currency' => $rs->Currency,
										'Rate' => $rs->Rate,
										'LineStatus' => $ds->save_type == 'C' ? 'C' : 'O'
									);

									if( ! $this->return_request_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Failed to insert row item {$rs->ItemCode} : {$rs->baseCode}";
									}
								}
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
							$logs = array(
								'code' => $doc->code,
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'emp_name' => $this->user->emp_name,
								'action' => $ds->save_type == 'C' ? 'close' : 'edit'
							);

							$this->return_request_model->add_logs($logs);
						}

						if($sc === TRUE && $ds->save_type == 'C')
						{
							$this->load->library('export');

							if($this->export->export_return_request($doc->code))
							{
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'P'
								);

								$this->return_request_model->update($doc->code, $arr);
							}
							else
							{
								$ex = 0;
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'N'
								);

								$this->return_request_model->update($doc->code, $arr);
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Bin Location";
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
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'ex' => $ex
		);

		echo json_encode($arr);
	}


	public function rollback($code)
	{
		$sc = TRUE;

		$doc = $this->return_request_model->get($code);

		if( ! empty($doc))
		{
			$sap = $this->return_request_model->get_sap_doc_num($code);

			if(empty($sap))
			{
				if($doc->status == 'C')
				{
					$middle = $this->return_request_model->get_temp_exists_data($code);

					if(!empty($middle))
					{
						foreach($middle as $rows)
						{
							if( ! $this->return_request_model->drop_temp_data($rows->DocEntry))
							{
								$sc = FALSE;
								$this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
							}
						}
					}
				}

				if($sc === TRUE)
				{
					$this->db->trans_begin();

					$arr = array(
						'status' => 'P',
						'update_user' => $this->user->uname,
						'date_upd' => now()
					);

					if( ! $this->return_request_model->update($doc->code, $arr))
					{
						$sc = FALSE;
						$this->error = "Failed to update document status";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'LineStatus' => 'O'
						);

						if( ! $this->return_request_model->update_details($doc->code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update row item status";
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
						$logs = array(
							'code' => $code,
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'emp_name' => $this->user->emp_name,
							'action' => 'rollback'
						);

						$this->return_request_model->add_logs($logs);
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document number";
		}

		$this->_response($sc);
	}


	public function cancel()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$reason = $this->input->post('reason');

		if( ! empty($code))
		{
			$doc = $this->return_request_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status != 'D')
				{
					if($doc->status == 'P' OR $doc->status == 'C')
					{
						$sap = $this->return_request_model->get_sap_doc_num($code);

						if( ! empty($sap))
						{
							$sc = FALSE;
							$this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
						}

						if($sc === TRUE && $doc->status == 'C')
						{
							$middle = $this->return_request_model->get_temp_exists_data($code);

							if(!empty($middle))
							{
								foreach($middle as $rows)
								{
									if( ! $this->return_request_model->drop_temp_data($rows->DocEntry))
									{
										$sc = FALSE;
										$this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
									}
								}
							}
						}

						if($sc === TRUE)
						{
							$arr = array(
								'status' => 'D',
								'cancel_reason' => get_null($reason),
								'update_user' => $this->user->uname,
								'date_upd' => now()
							);

							$this->db->trans_begin();

							if( ! $this->return_request_model->update($doc->code, $arr))
							{
								$sc = FALSE;
								$this->error = "Failed to update document status";
							}

							if($sc === TRUE)
							{
								$arr = array(
									'LineStatus' => 'D'
								);

								if( ! $this->return_request_model->update_details($doc->code, $arr))
								{
									$sc = FALSE;
									$this->error = "Failed to update line status";
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
						}

						if($sc === TRUE)
						{
							$logs = array(
								'code' => $code,
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'emp_name' => $this->user->emp_name,
								'action' => 'cancel'
							);

							$this->return_request_model->add_logs($logs);
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Document already closed cannot be cancel";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document number";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->_response($sc);
	}


	public function do_export($code)
	{
		$sc = TRUE;

		$doc = $this->return_request_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'C')
			{
				$this->load->library('export');

				if( ! $this->export->export_return_request($code))
				{
					$sc = FALSE;
					$this->error = "Export Failed : ".$this->export->error;

					$arr = array(
						'DocNum' => NULL,
						'tempStatus' => 'N'
					);

					$this->return_request_model->update($doc->code, $arr);
				}
				else
				{
					$arr = array(
						'DocNum' => NULL,
						'tempStatus' => 'P'
					);

					$this->return_request_model->update($doc->code, $arr);
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

		$this->_response($sc);
	}


	function get_item_data()
	{
		$sc = TRUE;
		$code = trim($this->input->get('code'));
		$card_code = trim($this->input->get('CardCode'));

		if( ! empty($code))
		{
			$PriceList = $this->customers_model->get_list_num($card_code);
			$PriceList = empty($PriceList) ? 1 : $PriceList;

			$customerTax = $this->customers_model->get_tax($card_code);

			$item = $this->item_model->get($code, $PriceList);

			if(!empty($item))
			{
				$DfUom = NULL;
				$price = 0.00;
				$discount = 0.00;
				$priceAfDisc = 0.00;

				$price_list = $this->item_model->price_list($item->code, $PriceList);

				if(!empty($price_list))
				{
					$DfUom = $price_list->UomEntry;
					$price = round($price_list->Price, 2);
				}

				$uom = "";
				$UomList = $this->item_model->get_uom_list($item->UgpEntry);

				if( ! empty($UomList))
				{
					foreach($UomList as $ls)
					{
						$uom .= '<option data-qty="'.$ls->BaseQty.'" data-code="'.$ls->UomCode.'" value="'.$ls->UomEntry.'" '.is_selected($ls->UomEntry, $DfUom).'>'.$ls->UomName.'</option>';

						if($ls->UomEntry == $DfUom)
						{
							$price = round($price * $ls->BaseQty);
						}
					}
				}


				$arr = array(
					'code' => $item->code,
					'name' => $item->name,
					'uom' => $uom,
					'taxCode' => !empty($customerTax) ? $customerTax->taxCode : $item->taxCode,
					'taxRate' => !empty($customerTax) ? $customerTax->taxRate : $item->taxRate,
					'price' => $price
				);
			}
			else
			{
				$sc = FALSE;
				$this->error = "ItemCode incorrect! : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : ItemCode";
		}

		echo $sc === TRUE ? json_encode($arr) : $this->error;
	}


	public function get_item_by_barcode()
	{
		$sc = TRUE;
		$row = NULL;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			if(empty($ds->barcode))
			{
				$sc = FALSE;
				$this->error = "Barcode is required";
			}

			if($sc === TRUE && empty($ds->baseType))
			{
				$sc = FALSE;
				$this->error = "กรุณาเลือกเอกสาร";
			}

			if($sc === TRUE && empty($ds->baseRef))
			{
				$sc = FALSE;
				$this->error = "กรุณาระบุเลขที่เอกสาร";
			}

			if($sc === TRUE)
			{
				$pd = $this->item_model->getItemByBarcode($ds->barcode);

				if(empty($pd))
				{
					$sc = FALSE;
					$this->error = "Invalid barcode or barcode not found";
				}
			}

			if($sc === TRUE)
			{
				if($ds->baseType == 'DO')
				{
					$row = $this->return_request_model->get_do_item_detail($ds->baseRef, $pd->ItemCode);
				}

				if($ds->baseType == 'IV')
				{
					$row = $this->return_request_model->get_invoice_item_detail($ds->baseRef, $pd->ItemCode);
				}

				if(empty($row))
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการสินค้าในเอกสารที่กำหนด";
				}

				if($sc === TRUE)
				{
					$row->uid = $ds->baseType.$row->DocNum.'-'.$row->LineNum;
					$row->Qty = ($pd->BaseQty / $row->NumPerMsr) * $ds->qty;
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'data' => $row
		);

		echo json_encode($arr);
	}

	public function get_temp_data()
	{
		$code = $this->input->get('code');

		$data = $this->return_request_model->get_temp_data($code);

		if( ! empty($data))
		{
			$status = 'Pending';

			if($data->F_Sap === NULL)
			{
				$status = "Pending";
			}
			elseif($data->F_Sap === 'N')
			{
				$status = "Failed";
			}
			elseif($data->F_Sap === 'Y')
			{
				$status = "Success";
			}

			$arr = array(
				'DocEntry' => $data->DocEntry,
				'U_WEBORDER' => $data->U_WEBORDER,
				'CardCode' => $data->CardCode,
				'CardName' => $data->CardName,
				'F_WebDate' => thai_date($data->F_WebDate, TRUE),
				'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
				'F_Sap' => $status,
				'Message' => empty($data->Message) ? '' : $data->Message,
				'del_btn' => ($status === "Pending" OR $status === "Failed") ? 'ok' : ''
			);

			echo json_encode($arr);
		}
		else
		{
			echo 'No data found';
		}
	}


	public function remove_temp()
	{
		$sc = TRUE;
		$code = $this->input->post('U_WEBORDER');

		$temp = $this->return_request_model->get_temp_data($code);

		if( ! empty($temp))
		{
			if($temp->F_Sap == 'Y')
			{
				$sc = FALSE;
				$this->error = "Delete failed : Document already import to SAP cannot be delete";
			}
		}

		if($sc === TRUE)
		{
			if(! $this->return_request_model->drop_temp_exists_data($code))
			{
				$sc = FALSE;
				$this->error = "Delete Failed : Delete Temp Failed";
			}
			else
			{
				$arr = array(
					'tempStatus' => 'N',
					'DocNum' => NULL
				);

				$this->return_request_model->update($code, $arr);
			}
		}

		$this->_response($sc);
	}


	public function get_new_code($date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : $date;
		$Y = date('y', strtotime($date));
		$M = date('m', strtotime($date));
		$prefix = getConfig('PREFIX_RETURN_REQUEST');
		$run_digit = getConfig('RUN_DIGIT_RETURN_REQUEST');
		$pre = $prefix .'-'.$Y.$M;
		$code = $this->return_request_model->get_max_code($pre);

		if(! empty($code))
		{
			$run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
		}
		else
		{
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
		}

		return $new_code;
	}


	public function clear_filter()
	{

		$filter = array(
			'rt_code',
			'rt_customer',
			'rt_sap_no',
			'rt_warehouse',
			'rt_user',
			'rt_status',
			'rt_tempStatus',
			'rt_from_date',
			'rt_to_date'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

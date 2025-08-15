<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
	public $menu_code = 'GRPO';
	public $menu_group_code = 'IC';
	public $title = 'Goods Receipt PO';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'receive_po';
		$this->load->model('receive_po_model');
		$this->load->model('vendor_model');
		$this->load->model('item_model');
		$this->load->model('zone_model');
		$this->load->helper('currency');
		$this->load->helper('warehouse');
		$this->load->helper('receive_po');
  }


	public function index()
	{
		$filter = array(
			'code' => get_filter('code', 'gr_code', ''),
			'vendor' => get_filter('vendor', 'gr_vendor', ''),
			'po_code' => get_filter('po_code', 'gr_po_code', ''),
			'invoice' => get_filter('invoice', 'gr_invoice', ''),
			'sap_no' => get_filter('sap_no', 'gr_sap_no', ''),
			'warehouse' => get_filter('warehouse', 'gr_warehouse', 'all'),
			'user' => get_filter('user', 'gr_user', 'all'),
			'status' => get_filter('status', 'gr_status', 'all'),
			'tempStatus' => get_filter('tempStatus', 'gr_tempStatus', 'all'),
			'from_date' => get_filter('from_date', 'gr_from_date', ''),
			'to_date' => get_filter('to_date', 'gr_to_date', '')
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
			$rows = $this->receive_po_model->count_rows($filter);
			$filter['data'] = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$this->pagination->initialize($init);
			$this->load->view('receive_po/receive_po_list', $filter);
		}
	}


	public function add_new()
	{
		$this->load->view('receive_po/receive_po_add');
	}


	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$zone = $this->zone_model->get($ds->zone_code);

			if( ! empty($zone))
			{
				$date_add = date('Y-m-d');
				$posting_date = db_date($ds->posting_date);
				$code = $this->get_new_code($date_add);

				$arr = array(
					'code' => $code,
					'date_add' => $date_add,
					'posting_date' => $posting_date,
					'vendor_code' => $ds->vendor_code,
					'vendor_name' => $ds->vendor_name,
					'po_code' => get_null($ds->po_code),
					'invoice_code' => get_null($ds->invoice_code),
					'warehouse_code' => $zone->warehouse_code,
					'zone_code' => $zone->code,
					'Currency' => $ds->Currency,
					'Rate' => $ds->Rate,
					'remark' => get_null(trim($ds->remark)),
					'user' => $this->user->uname
				);

				if( ! $this->receive_po_model->add($arr))
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

					$this->receive_po_model->add_logs($logs);
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
		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			$details = $this->receive_po_model->get_details($code);
			$rows = [];

			if( ! empty($details))
			{
				$ro = getConfig('RECEIVE_OVER_PO');
				$rate = ($ro * 0.01);

				foreach($details as $rs)
				{
					$row = $this->receive_po_model->get_po_row($rs->baseEntry, $rs->baseLine);

					if( ! empty($row))
					{
						$dif = $row->Quantity - $row->OpenQty;
						$onOrder = $this->receive_po_model->get_on_order_qty($rs->ItemCode, $rs->baseEntry, $rs->baseLine);
						$onOrder = $onOrder >= $rs->Qty ? $onOrder - $rs->Qty : 0;
						$qty = $row->OpenQty - $onOrder;
						$rs->backlogs = $qty;
						$rs->limit = ($row->Quantity + ($row->Quantity * $rate)) - $dif;
						$rs->LineTotal = $rs->Qty * $rs->Price;
					}
				}
			}

			$ds = array(
				'doc' => $doc,
				'details' => $details
			);

			$this->load->view('receive_po/receive_po_edit', $ds);
		}
		else
		{
			$this->page_error();
		}
	}


	public function process($code)
	{
		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'O')
			{
				$details = $this->receive_po_model->get_details($code);

				$bc = [];
				$items = [];

				if( ! empty($details))
				{
					foreach($details as $rs)
					{
						if(empty($items[$rs->ItemCode]))
						{
							$bcList = $this->item_model->getBarcodeList($rs->ItemCode);

							if( ! empty($bcList))
							{
								$arr = "";
								$c = count($bcList);
								$i = 1;
								foreach($bcList as $bcd)
								{
									$arr .= "{\"barcode\" : \"{$bcd->Barcode}\", \"ItemCode\" : \"{$bcd->ItemCode}\", \"ItemName\" : \"{$bcd->ItemName}\", \"UomName\" : \"{$bcd->UomName}\", \"BaseQty\" : \"".round($bcd->BaseQty, 2)."\"}";
									$arr .= $i == $c ? "" : ",";
									$i++;

									if( ! empty($bcd->Barcode))
									{
										$bc[] = $bcd; //-- list of barcode : Barcode, ItemCode, UomEntry, BaseQty;

										if($rs->UomEntry == $bcd->UomEntry)
										{
											$rs->barcode = $bcd->Barcode;
											$items[$rs->ItemCode]['barcode'] = $bcd->Barcode;
										}
									}
								}

								$items[$rs->ItemCode]['barcode'] = empty($items[$rs->ItemCode]['barcode']) ? NULL : $items[$rs->ItemCode]['barcode'];
								$items[$rs->ItemCode]['data'] = htmlspecialchars("[".$arr."]");
							}
							else
							{
								$items[$rs->ItemCode]['data'] = NULL;
								$items[$rs->ItemCode]['barcode'] = NULL;
							}

							$rs->barcode = $items[$rs->ItemCode]['barcode'];
							$rs->item_data = $items[$rs->ItemCode]['data'];
						}
						else
						{
							$rs->barcode = $items[$rs->ItemCode]['barcode'];
							$rs->item_data = $items[$rs->ItemCode]['data'];
						}
					}
				}

				$ds = array(
				'doc' => $doc,
				'details' => $details,
				'po_refs' => $this->receive_po_model->get_po_refs($code),
				'bcList' => $bc
				);

				$this->load->view('receive_po/receive_po_process', $ds);
			}
			else
			{
				$this->view_detail($code);
			}
		}
		else
		{
			$this->page_error();
		}
	}


	public function view_detail($code)
	{
		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			$ds = array(
				'doc' => $doc,
				'details' => $this->receive_po_model->get_details($code),
				'po_refs' => $this->receive_po_model->get_po_refs($code),
				'logs' => $this->receive_po_model->get_logs($code)
			);

			$this->load->view('receive_po/receive_po_view_detail', $ds);
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
			$doc = $this->receive_po_model->get($ds->code);

			if( ! empty($doc))
			{
				if($doc->status == 'P')
				{
					$zone = $this->zone_model->get($ds->zone_code);

					if( ! empty($zone))
					{
						$po_refs = [];

						$this->db->trans_begin();

						//--- update header
						$arr = array(
							'posting_date' => db_date($ds->posting_date, FALSE),
							'vendor_code' => trim($ds->vendor_code),
							'vendor_name' => trim($ds->vendor_name),
							'po_code' => trim($ds->po_code),
							'invoice_code' => get_null(trim($ds->invoice_code)),
							'warehouse_code' => $zone->warehouse_code,
							'zone_code' => $zone->code,
							'Currency' => $ds->Currency,
							'Rate' => $ds->Rate,
							'DocTotal' => $ds->DocTotal,
							'VatSum' => $ds->VatSum,
							'TotalQty' => $ds->TotalQty,
							'TotalReceived' => $ds->save_type == 'C' ? $ds->TotalReceived : 0,
							'update_user' => $this->user->uname,
							'date_upd' => now(),
							'status' => $ds->save_type,
							'remark' => get_null(trim($ds->remark))
						);

						if( ! $this->receive_po_model->update($ds->code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update document header";
						}

						if($sc === TRUE)
						{
							if( ! $this->receive_po_model->delete_details($ds->code))
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
										'receive_id' => $doc->id,
										'receive_code' => $doc->code,
										'baseCode' => $rs->baseCode,
										'baseEntry' => $rs->baseEntry,
										'baseLine' => $rs->baseLine,
										'ItemCode' => $rs->ItemCode,
										'ItemName' => $rs->ItemName,
										'PriceBefDi' => $rs->PriceBefDi,
										'PriceAfVAT' => $rs->PriceAfVAT,
										'Price' => $rs->Price,
										'DiscPrcnt' => $rs->DiscPrcnt,
										'Qty' => $rs->Qty,
										'ReceiveQty' => $ds->save_type == 'C' ? $rs->ReceiveQty : 0,
										'LineTotal' => $ds->save_type == 'C' ? $rs->LineTotal : 0,
										'BinCode' => $zone->code,
										'WhsCode' => $zone->warehouse_code,
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
										'VatAmount' => $rs->VatAmount,
										'VatPerQty' => $rs->VatPerQty,
										'Currency' => $ds->Currency,
										'Rate' => $ds->Rate,
										'LineStatus' => $ds->save_type == 'C' ? 'C' : 'O'
									);

									if( ! $this->receive_po_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Failed to insert row item {$rs->ItemCode} : {$rs->baseCode}";
									}

									if( ! isset($po_refs[$rs->baseCode]))
									{
										$po_refs[$rs->baseCode] = $rs->baseCode;
									}
								}
							}
						}

						if($sc === TRUE)
						{
							if($this->receive_po_model->delete_po_refs($doc->code))
							{
								if( ! empty($po_refs))
								{
									if( ! $this->receive_po_model->add_po_refs($doc->code, $po_refs))
									{
										$sc = FALSE;
										$this->error = "Failed to insert po reference";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Failed to delete prevoius po reference";
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

							$this->receive_po_model->add_logs($logs);
						}

						if($sc === TRUE && $ds->save_type == 'C')
						{
							$this->load->library('export');

							if($this->export->export_receive($doc->code))
							{
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'P'
								);

								$this->receive_po_model->update($doc->code, $arr);
							}
							else
							{
								$ex = 0;
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'N'
								);

								$this->receive_po_model->update($doc->code, $arr);
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


	public function close_receive()
	{
		$sc = TRUE;
		$ex = 1;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
			$doc = $this->receive_po_model->get($ds->code);

			if( ! empty($doc))
			{
				if($doc->status == 'O')
				{
					$zone = $this->zone_model->get($ds->zone_code);

					if( ! empty($zone))
					{
						$po_refs = [];

						$this->db->trans_begin();

						//--- update header
						$arr = array(
							'posting_date' => db_date($ds->posting_date, FALSE),
							'vendor_code' => trim($ds->vendor_code),
							'vendor_name' => trim($ds->vendor_name),
							'po_code' => trim($ds->po_code),
							'invoice_code' => get_null(trim($ds->invoice_code)),
							'warehouse_code' => $zone->warehouse_code,
							'zone_code' => $zone->code,
							'Currency' => $ds->Currency,
							'Rate' => $ds->Rate,
							'DocTotal' => $ds->DocTotal,
							'VatSum' => $ds->VatSum,
							'TotalQty' => $ds->TotalQty,
							'TotalReceived' => $ds->TotalReceived,
							'update_user' => $this->user->uname,
							'date_upd' => now(),
							'status' => $ds->save_type,
							'remark' => get_null(trim($ds->remark))
						);

						if( ! $this->receive_po_model->update($ds->code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update document header";
						}

						if($sc === TRUE)
						{
							if( ! $this->receive_po_model->delete_details($ds->code))
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
										'receive_id' => $doc->id,
										'receive_code' => $doc->code,
										'baseCode' => $rs->baseCode,
										'baseEntry' => $rs->baseEntry,
										'baseLine' => $rs->baseLine,
										'ItemCode' => $rs->ItemCode,
										'ItemName' => $rs->ItemName,
										'PriceBefDi' => $rs->PriceBefDi,
										'PriceAfVAT' => $rs->PriceAfVAT,
										'Price' => $rs->Price,
										'DiscPrcnt' => $rs->DiscPrcnt,
										'Qty' => $rs->Qty,
										'ReceiveQty' => $rs->ReceiveQty,
										'LineTotal' => $rs->LineTotal,
										'BinCode' => $zone->code,
										'WhsCode' => $zone->warehouse_code,
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
										'VatAmount' => $rs->VatAmount,
										'VatPerQty' => $rs->VatPerQty,
										'Currency' => $ds->Currency,
										'Rate' => $ds->Rate,
										'LineStatus' => 'C'
									);

									if( ! $this->receive_po_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Failed to insert row item {$rs->ItemCode} : {$rs->baseCode}";
									}

									if( ! isset($po_refs[$rs->baseCode]))
									{
										$po_refs[$rs->baseCode] = $rs->baseCode;
									}
								}
							}
						}

						if($sc === TRUE)
						{
							if($this->receive_po_model->delete_po_refs($doc->code))
							{
								if( ! empty($po_refs))
								{
									if( ! $this->receive_po_model->add_po_refs($doc->code, $po_refs))
									{
										$sc = FALSE;
										$this->error = "Failed to insert po reference";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Failed to delete prevoius po reference";
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
								'action' => 'close'
							);

							$this->receive_po_model->add_logs($logs);
						}

						if($sc === TRUE)
						{
							$this->load->library('export');

							if($this->export->export_receive($doc->code))
							{
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'P'
								);

								$this->receive_po_model->update($doc->code, $arr);
							}
							else
							{
								$ex = 0;
								$arr = array(
									'DocNum' => NULL,
									'tempStatus' => 'N'
								);

								$this->receive_po_model->update($doc->code, $arr);
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

		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			$sap = $this->receive_po_model->get_sap_receive_doc($code);

			if(empty($sap))
			{
				if($doc->status == 'C')
				{
					$middle = $this->receive_po_model->get_temp_exists_data($code);

					if(!empty($middle))
					{
						foreach($middle as $rows)
						{
							if( ! $this->receive_po_model->drop_temp_data($rows->DocEntry))
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

					if( ! $this->receive_po_model->update($doc->code, $arr))
					{
						$sc = FALSE;
						$this->error = "Failed to update document status";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'LineStatus' => 'O'
						);

						if( ! $this->receive_po_model->update_details($doc->code, $arr))
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

						$this->receive_po_model->add_logs($logs);
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
			$doc = $this->receive_po_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status != 'D')
				{
					if($doc->status == 'P' OR $doc->status == 'O')
					{
						$arr = array(
							'status' => 'D',
							'cancel_reason' => get_null($reason),
							'update_user' => $this->user->uname,
							'date_upd' => now()
						);

						$this->db->trans_begin();

						if( ! $this->receive_po_model->update($doc->code, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update document status";
						}

						if($sc === TRUE)
						{
							$arr = array(
								'LineStatus' => 'D'
							);

							if( ! $this->receive_po_model->update_details($doc->code, $arr))
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

						if($sc === TRUE)
						{
							$logs = array(
								'code' => $code,
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'emp_name' => $this->user->emp_name,
								'action' => 'cancel'
							);

							$this->receive_po_model->add_logs($logs);
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


	public function get_po_detail()
	{
		$sc = TRUE;
		$ds = array();

		$po_code = $this->input->get('po_code');

		$po = $this->receive_po_model->get_po($po_code);

		if( ! empty($po))
		{
			$ro = getConfig('RECEIVE_OVER_PO');

			$rate = ($ro * 0.01);

			$details = $this->receive_po_model->get_po_details($po_code);

			if( ! empty($details))
			{
				$no = 1;

				foreach($details as $rs)
				{
					if($rs->OpenQty > 0)
					{
						$dif = $rs->Quantity - $rs->OpenQty;
						$onOrder = $this->receive_po_model->get_on_order_qty($rs->ItemCode, $rs->DocEntry, $rs->LineNum);
						$qty = $rs->OpenQty - $onOrder;

						$arr = array(
							'no' => $no,
							'uid' => $rs->DocEntry."-".$rs->LineNum,
							'product_code' => $rs->ItemCode,
							'product_name' => $rs->Dscription.' '.$rs->Text,
							'baseCode' => $po_code,
							'baseEntry' => $rs->DocEntry,
							'baseLine' => $rs->LineNum,
							'vatCode' => $rs->VatGroup,
							'vatRate' => $rs->VatPrcnt,
							'unitCode' => $rs->unitMsr,
							'unitMsr' => $rs->unitMsr,
							'NumPerMsr' => $rs->NumPerMsr,
							'unitMsr2' => $rs->unitMsr2,
							'NumPerMsr2' => $rs->NumPerMsr2,
							'UomEntry' => $rs->UomEntry,
							'UomEntry2' => $rs->UomEntry2,
							'UomCode' => $rs->UomCode,
							'UomCode2' => $rs->UomCode2,
							'PriceBefDi' => round($rs->PriceBefDi, 4),
							'PriceBefDiLabel' => number($rs->PriceBefDi, 4),
							'DiscPrcnt' => round($rs->DiscPrcnt, 2),
							'Price' => round($rs->Price, 4),
							'PriceAfDiscLabel' => number($rs->Price, 4),
							'PriceAfVAT' => round($rs->PriceAfVAT, 4),
							'VatPerQty' => round(($rs->PriceAfVAT - $rs->Price), 4),
							'onOrder' => $onOrder,
							'qty' => $qty,
							'qtyLabel' => number($qty, 2),
							'backlogs' => $rs->OpenQty,
							'limit' => ($rs->Quantity + ($rs->Quantity * $rate)) - $dif,
							'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
						);

						array_push($ds, $arr);
						$no++;
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบใบสั่งซื้อ";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'DocNum' => $sc === TRUE ? $po->DocNum : NULL,
			'DocCur' => $sc === TRUE ? $po->DocCur : NULL,
			'DocRate' => $sc === TRUE ? $po->DocRate : NULL,
			'CardCode' => $sc === TRUE ? $po->CardCode : NULL,
			'CardName' => $sc === TRUE ? $po->CardName : NULL,
			'DiscPrcnt' => $sc === TRUE ? $po->DiscPrcnt : NULL,
			'details' => $sc === TRUE ? $ds : NULL
		);

		echo json_encode($arr);
	}


	public function is_exists_zone_code()
	{
		$sc = TRUE;

		$code = $this->input->post('zone_code');
		$whsCode = $this->input->post('warehouse_code');

		$zone = $this->zone_model->get($code);

		if( ! $this->zone_model->is_exists_bin_code($code))
		{

		}
	}


	public function do_export($code)
	{
		$sc = TRUE;

		$doc = $this->receive_po_model->get($code);

		if( ! empty($doc))
		{
			if($doc->status == 'C')
			{
				$this->load->library('export');

				if( ! $this->export->export_receive($code))
				{
					$sc = FALSE;
					$this->error = "Export Failed : ".$this->export->error;

					$arr = array(
						'DocNum' => NULL,
						'tempStatus' => 'N'
					);

					$this->receive_po_model->update($doc->code, $arr);
				}
				else
				{
					$arr = array(
						'DocNum' => NULL,
						'tempStatus' => 'P'
					);

					$this->receive_po_model->update($doc->code, $arr);
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


	public function get_temp_data()
	{
		$code = $this->input->get('code');

		$data = $this->receive_po_model->get_temp_data($code);

		if( ! empty($data))
		{
			//$btn = "<button type='button' class='btn btn-sm btn-danger' onClick='removeTemp()'' ><i class='fa fa-trash'></i> Delete Temp</button>";

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

		$temp = $this->receive_po_model->get_temp_data($code);

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
			if(! $this->receive_po_model->drop_temp_exists_data($code))
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

				$this->receive_po_model->update($code, $arr);
			}
		}

		$this->_response($sc);
	}


	public function get_vendor_by_po_code()
	{
		$sc = TRUE;

		$po_code = $this->input->post('po_code');

		$po = $this->receive_po_model->get_po($po_code);

		if(empty($po))
		{
			$sc = FALSE;
			$this->error = "Not found !";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'code' => $sc === TRUE ? $po->CardCode : NULL,
			'name' => $sc === TRUE ? $po->CardName : NULL
		);

		echo json_encode($arr);
	}


	public function get_new_code($date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : $date;
		$Y = date('y', strtotime($date));
		$M = date('m', strtotime($date));
		$prefix = getConfig('PREFIX_GRPO');
		$run_digit = getConfig('RUN_DIGIT_GRPO');
		$pre = $prefix .'-'.$Y.$M;
		$code = $this->receive_po_model->get_max_code($pre);

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
			'gr_code',
			'gr_vendor',
			'gr_po_code',
			'gr_invoice',
			'gr_sap_no',
			'gr_warehouse',
			'gr_user',
			'gr_status',
			'gr_tempStatus',
			'gr_from_date',
			'gr_to_date'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>

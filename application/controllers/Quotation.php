<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends PS_Controller
{
	public $menu_code = 'QUOTATION';
	public $menu_group_code = 'AR';
	public $title = 'Sales Quotation';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'quotation';
		$this->load->model('quotation_model');
		$this->load->model('customers_model');
		$this->load->model('quotation_logs_model');
		$this->load->model('item_model');
		$this->load->helper('quotation');
		$this->load->helper('currency');
  }



  public function index()
  {

		$filter = array(
			'WebCode' => get_filter('WebCode', 'sq_WebCode', ''),
			'DocNum' => get_filter('DocNum', 'sq_DocNum', ''),
			'SoNo' => get_filter('SoNo', 'sq_SoNo', ''),
			'CardCode' => get_filter('CardCode', 'sq_CardCode', ''),
			'SaleName' => get_filter('SaleName', 'sq_SaleName', ''),
			'CustRef' => get_filter('CustRef', 'sq_CustRef', ''),
			'Approved' => get_filter('Approved', 'sq_Approved', 'all'),
			'SapStatus' => get_filter('SapStatus', 'sq_SapStatus', 'all'),
			'Status' => get_filter('Status', 'sq_Status', 'all'),
			'fromDate' => get_filter('fromDate', 'sq_fromDate', ''),
			'toDate' => get_filter('toDate', 'sq_toDate', ''),
			'order_by' => get_filter('order_by', 'sq_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'sq_sort_by', 'DESC'),
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->quotation_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->quotation_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('quotation/quotation_list', $filter);
  }


	public function cancle_quotation()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		$doc = $this->quotation_model->get($code);

		if(!empty($doc))
		{
			if($doc->Status == 9 OR $doc->Status == 0)
			{
				$DocNum = $this->quotation_model->get_sap_doc_num($code);
				if(empty($DocNum))
				{
					$arr = array(
						'Status' => (-1)
					);

					if(! $this->quotation_model->update($code, $arr))
					{
						$sc = FALSE;
						$this->error = "ยกเลิกเอกสารไม่สำเร็จ";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่สามารถยกเลิกได้เนื่องจากเอกสารเข้า SAP แล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เอกสารอยู่ในสถานะที่ไม่สามารถยกเลิกได้";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}


		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function add_new()
	{
		$this->title = "New Sales Quotation";

		$ds = array(
			'sale_name' => $this->user_model->get_saleman_name($this->user->sale_id)
		);

		$this->load->view('quotation/quotation_add', $ds);
	}



	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('header')); //--- Header OQUT
		$details = json_decode($this->input->post('details')); //---

		//---- add header OQUT
		if(!empty($ds))
		{
			$date = db_date($ds->DocDate);
			$code = $this->get_new_code($date);

			$arr = array(
				'code' => $code,
				'CardCode' => trim($ds->CardCode),
				'CardName' => trim($ds->CardName),
				'SlpCode' => $ds->SlpCode,
				'GroupNum' => $ds->GroupNum,
				'Term' => $ds->term,
				'CntctCode' => get_null($ds->Contact),
				'NumAtCard' => get_null($ds->CustRef),
				'DocCur' => NULL, //$ds->Currency,
				'DocRate' => 1.00, //$ds->Rate,
				'DocTotal' => $ds->docTotal,
				'DiscPrcnt' => $ds->discPrcnt,
				'RoundDif' => $ds->roundDif,
				'VatSum' => $ds->tax,
				'OcrCode' => $ds->Department,
				'OcrCode1' => $ds->Division,
				'PayToCode' => empty($ds->PayToCode) ? NULL : $ds->PayToCode,
				'ShipToCode' => empty($ds->ShipToCode) ? NULL: $ds->ShipToCode,
				'Address' => get_null($ds->BillTo),
				'Address2' => get_null($ds->ShipTo),
				'Series' => $ds->Series,
				'BeginStr' => $this->quotation_model->get_prefix($ds->Series),
				'Status' => $ds->isDraft == 1 ? 9 : 0,
				'DocDate' => sap_date($ds->DocDate, TRUE),
				'DocDueDate' => sap_date($ds->DocDueDate, TRUE),
				'TextDate' => sap_date($ds->TextDate, TRUE),
				'OwnerCode' => get_null($ds->owner),
				'Comments' => get_null($ds->comments),
				'user_id' => $this->user->id,
				'uname' => $this->user->uname,
				'sale_team' => $this->user->sale_team
			);

			$this->db->trans_begin();

			if(!$this->quotation_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "Insert Quotation Header failed";
			}
			else
			{
				//--- insert quotation success
				//--- insert detail QUT1
				if(!empty($details))
				{
					$no = 1;
					foreach($details as $rs)
					{
						if($sc === FALSE)
						{
							break;
						}

						$arr = array(
							'quotation_code' => $code,
							'LineNum' => $rs->LineNum,
							'Type' => $rs->Type, //--- 0 = item , 1 = text
							'ItemCode' => get_null(trim($rs->ItemCode)),
							'Dscription' => get_null(trim($rs->Description)),
							'ItemDetail' => get_null(trim($rs->Text)),
							'FreeText' => get_null(trim($rs->FreeTxt)),
							'Qty' => $rs->Quantity,
							'UomCode' => get_null($rs->UomCode),
							'lastQuotePrice' => $rs->lastQuotePrice,
							'lastSellPrice' => $rs->lastSellPrice,
							'basePrice' => $rs->basePrice,
							'stdPrice' => $rs->stdPrice,
							'Price' => $rs->Price,
							'priceDiffPercent' => $rs->Type == 0 ? $rs->priceDiffPercent : 0,
							'SellPrice' => $rs->sellPrice,
							'U_DISWEB' => round($rs->U_DISWEB, 2),
							'U_DISCEX' => 0,
							'DiscPrcnt' => round($rs->DiscPrcnt, 2),
							'VatGroup' => $rs->VatGroup,
							'VatRate' => $rs->VatPrcnt,
							'LineTotal' => $rs->Type == 0 ? $rs->LineTotal : 0,
							'WhsCode' => get_null(trim($rs->WhsCode)),
							'LineText' => get_null(trim($rs->LineText)),
							'AfLineNum' => $rs->AfLineNum
						);

						if( ! $this->quotation_model->add_detail($arr))
						{
							$sc = FALSE;
							$this->error = "Insert Quotation detail failed at line : {$no} @ {$rs->ItemCode}";
						}

						$no++;
					}
				}

			}

			if($sc === TRUE)
			{
				//--- write quotation logs
				$this->quotation_logs_model->add('add', $code);
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
				$must_approve = $this->must_approve($code);
				$arr = array(
					'must_approve' => $must_approve === TRUE ? 1 : 0,
					'Approved' => $must_approve === TRUE ? 'P' : 'S'
				);

				$this->quotation_model->update($code, $arr);

				if(! $must_approve && $ds->isDraft == 0)
				{
					//--- export to Middle
					$this->doExport($code);
				}

			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Quotation header data";
		}

		$message = array(
			"result" => $sc === TRUE ? 'success' : 'failed',
			"message" => $sc === TRUE ? $code : $this->error
		);

		echo json_encode($message);

	}


	public function duplicate_quotation()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if(!empty($code))
		{
			$ds = $this->quotation_model->get($code);

			if(!empty($ds))
			{
				$date = date('Y-m-d');
				$valid_till = date('Y-m-d', strtotime("+30 days"));
				//--- prepare data
				$SQCode = $this->get_new_code($date);
				$OriginalSQ = $ds->code .(empty($ds->DocNum) ? "" : ", ".$ds->DocNum);
				$arr = array(
					'code' => $SQCode,
					'CardCode' => trim($ds->CardCode),
					'CardName' => trim($ds->CardName),
					'SlpCode' => $ds->SlpCode,
					'GroupNum' => $ds->GroupNum,
					'Term' => $ds->Term,
					'CntctCode' => get_null($ds->CntctCode),
					'NumAtCard' => get_null($ds->NumAtCard),
					'DocCur' => $ds->DocCur,
					'DocRate' => $ds->DocRate,
					'DocTotal' => $ds->DocTotal,
					'DiscPrcnt' => $ds->DiscPrcnt,
					'RoundDif' => $ds->RoundDif,
					'VatSum' => $ds->VatSum,
					'OcrCode' => $ds->OcrCode,
					'OcrCode1' => $ds->OcrCode1,
					'PayToCode' => empty($ds->PayToCode) ? NULL : $ds->PayToCode,
					'ShipToCode' => empty($ds->ShipToCode) ? NULL : $ds->ShipToCode,
					'Address' => $ds->Address,
					'Address2' => $ds->Address2,
					'Series' => $ds->Series,
					'BeginStr' => $ds->BeginStr,
					'DocDate' => $date,
					'Status' => 9,
					'DocDueDate' => $valid_till,
					'TextDate' => $date,
					'U_ORIGINALSQ' => $OriginalSQ,
					'OwnerCode' => get_null($ds->OwnerCode),
					'Comments' => get_null($ds->Comments),
					'user_id' => $this->user->id,
					'uname' => $this->user->uname,
					'sale_team' => $this->user->sale_team,
					'must_approve' => $ds->must_approve,
					'Approved' => $ds->must_approve == 0 ? 'S' : 'P',
					'is_duplicate' => 1
				);


				$this->db->trans_begin();

				if(!$this->quotation_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Insert Quotation Header failed";
				}
				else
				{
					//--- insert quotation success
					//--- insert detail QUT1
					$details = $this->quotation_model->get_details($code);

					if(!empty($details))
					{

						foreach($details as $rs)
						{
							if($sc === FALSE)
							{
								break;
							}

							$arr = array(
								'quotation_code' => $SQCode,
								'LineNum' => $rs->LineNum,
								'Type' => $rs->Type, //--- 0 = item , 1 = text
								'ItemCode' => $rs->ItemCode,
								'Dscription' => $rs->Dscription,
								'ItemDetail' => $rs->ItemDetail,
								'FreeText' => $rs->FreeText,
								'Qty' => $rs->Qty,
								'UomCode' => $rs->UomCode,
								'lastQuotePrice' => $this->item_model->last_quote_price($rs->ItemCode, $ds->CardCode, $this->item_model->get_uom_id($rs->UomCode)),
								'lastSellPrice' => $this->item_model->last_sell_price($rs->ItemCode, $ds->CardCode, $this->item_model->get_uom_id($rs->UomCode)),
								'basePrice' => $rs->basePrice,
								'stdPrice' => $rs->stdPrice,
								'Price' => $rs->Price,
								'SellPrice' => $rs->SellPrice,
								'priceDiffPercent' => $rs->Type == 0 ? $rs->priceDiffPercent : 0,
								'U_DISWEB' => $rs->U_DISWEB,
								'U_DISCEX' => $rs->U_DISCEX,
								'DiscPrcnt' => $rs->DiscPrcnt,
								'VatGroup' => $rs->VatGroup,
								'VatRate' => $rs->VatRate,
								'LineTotal' => $rs->LineTotal,
								'WhsCode' => $rs->WhsCode,
								'LineText' => $rs->LineText,
								'AfLineNum' => $rs->AfLineNum
							);

							if( ! $this->quotation_model->add_detail($arr))
							{
								$sc = FALSE;
								$this->error = "Insert Quotation detail failed at line : {$no} @ {$rs->ItemCode}";
							}

						}
					}
				}

				if($sc === TRUE)
				{
					//--- write quotation logs
					$this->quotation_logs_model->add('add', $SQCode);
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
					$must_approve = $this->must_approve($code);
					$arr = array(
						'must_approve' => $must_approve === TRUE ? 1 : 0,
						'Approved' => $must_approve === TRUE ? 'P' : 'S'
					);

					$this->quotation_model->update($code, $arr);

				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid SQ Code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Required Parameter: Orignal SQ";
		}

		if($sc === TRUE)
		{
			$result = array(
				"status" => 'success',
				"code" => $SQCode
			);

			echo json_encode($result);
		}
		else
		{
			$result = array(
				"status" => 'error',
				"error" => $this->error
			);

			echo json_encode($result);
		}
	}





	function edit($code)
	{
		$in_sap = $this->quotation_model->is_sap_exists_code($code);

		if(!$in_sap)
		{
			$this->title = "Edit Sales Quotation";
			$this->load->model('stock_model');

			$header = $this->quotation_model->get($code);
			$details = $this->quotation_model->get_details($code);
			$billToCode = empty($header) ? NULL : $this->customers_model->get_address_bill_to_code($header->CardCode);
			$shipToCode = empty($header) ? NULL : $this->customers_model->get_address_ship_to_code($header->CardCode);
			$totalAmount = 0;

			if(!empty($details))
			{
				foreach($details as $rs)
				{
					$uom = "";
					$UomList = $this->item_model->get_uom_list_by_item_code($rs->ItemCode);

					if(!empty($UomList))
					{
						foreach($UomList as $ls)
						{
							$uom .= '<option data-qty="'.$ls->BaseQty.'" data-code="'.$ls->UomCode.'" value="'.$ls->UomEntry.'" '.is_selected($ls->UomCode, $rs->UomCode).'>'.$ls->UomName.'</option>';
						}
					}

					$rs->uom = $uom;
					$stock = $this->stock_model->get_stock($rs->ItemCode, NULL);
					$totalAmount += ($rs->Qty * $rs->SellPrice);
					$rs->OnHandQty = empty($stock) ? 0 : round($stock->OnHand, 2);
					$rs->IsCommited = empty($stock) ? 0 : round($stock->IsCommited, 2);
					$rs->OnOrder = empty($stock) ? 0 : round($stock->OnOrder, 2);
				}
			}


			$ds =  array(
				'header' => $header,
				'billToCode' => $billToCode,
				'shipToCode' => $shipToCode,
				'details' => $details,
				'totalAmount' => $totalAmount,
				'sale_name' => $this->user_model->get_saleman_name($header->SlpCode),
				'user_sale_name' => $this->user_model->get_saleman_name($this->user->sale_id),
				'logs' => $this->quotation_logs_model->get($code)
			);

			$this->load->view('quotation/quotation_edit', $ds);
		}
		else
		{
			set_error("Quotation already in SAP");
			redirect("{$this->home}/view_detail/{$code}");
		}

	}



	public function update()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		$ds = json_decode($this->input->post('header')); //--- Header OQUT
		$details = json_decode($this->input->post('details')); //---

		$doc = $this->quotation_model->get($code);

		if(!empty($doc))
		{
			//-- Status 1 = pending to send to SAP, 2 = In SAP , 3 = Error
			//--- Approved A = Approved, P = pendign R = Rejected, S = No need to Approved
			if($doc->Status != 2 )
			{
				//$in_sap = $this->quotation_model->is_sap_exists_draft($code);
				$in_sap = $this->quotation_model->is_sap_exists_code($code);

				if(! $in_sap)
				{
					$temps = $this->quotation_model->get_temp_quotations($code);
					if(!empty($temps))
					{
						foreach($temps as $temp)
						{
							$this->quotation_model->drop_quotation_temp_data($temp->DocEntry);
						}
					}

					//---- add header OQUT
					if(!empty($ds))
					{
						$date = db_date($ds->DocDate);

						$arr = array(
							'CardCode' => trim($ds->CardCode),
							'CardName' => trim($ds->CardName),
							'SlpCode' => $ds->SlpCode,
							'GroupNum' => $ds->GroupNum,
							'term' => $ds->term,
							'CntctCode' => get_null($ds->Contact),
							'NumAtCard' => get_null($ds->CustRef),
							'DocTotal' => $ds->docTotal,
							'DiscPrcnt' => $ds->discPrcnt,
							'RoundDif' => $ds->roundDif,
							'VatSum' => $ds->tax,
							'OcrCode' => $ds->Department,
							'OcrCode1' => $ds->Division,
							'PayToCode' => $ds->PayToCode,
							'ShipToCode' => $ds->ShipToCode,
							'Address' => get_null($ds->BillTo),
							'Address2' => get_null($ds->ShipTo),
							'Series' => $ds->Series,
							'BeginStr' => $this->quotation_model->get_prefix($ds->Series),
							'DocDate' => sap_date($ds->DocDate, TRUE),
							'DocDueDate' => sap_date($ds->DocDueDate, TRUE),
							'TextDate' => sap_date($ds->TextDate, TRUE),
							'OwnerCode' => get_null($ds->owner),
							'Comments' => get_null($ds->comments),
							'user_id' => $this->user->id,
							'uname' => $this->user->uname,
							'sale_team' => $this->user->sale_team
						);


						if($ds->isDraft == 1)
						{
							$arr['Status'] = 9;
						}

						$this->db->trans_begin();

						if(!$this->quotation_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Update Quotation Header failed";
						}
						else
						{
							//--- update quotation success

							//--- Drop old details
							if(!$this->quotation_model->drop_details($code))
							{
								$sc = FALSE;
								$this->error = "Drop Old data failed";
							}

							//--- insert detail QUT1
							if($sc === TRUE && !empty($details))
							{

								$no = 1;
								foreach($details as $rs)
								{
									if($sc === FALSE)
									{
										break;
									}


									$arr = array(
										'quotation_code' => $code,
										'LineNum' => $rs->LineNum,
										'Type' => $rs->Type, //--- 0 = item , 1 = text
										'ItemCode' => get_null(trim($rs->ItemCode)),
										'Dscription' => get_null(trim($rs->Description)),
										'ItemDetail' => get_null(trim($rs->Text)),
										'FreeText' => get_null(trim($rs->FreeTxt)),
										'Qty' => $rs->Quantity,
										'UomCode' => get_null($rs->UomCode),
										'lastQuotePrice' => $rs->lastQuotePrice,
										'lastSellPrice' => $rs->lastSellPrice,
										'basePrice' => $rs->basePrice,
										'stdPrice' => $rs->stdPrice,
										'Price' => $rs->Price,
										'priceDiffPercent' => $rs->Type == 0 ? $rs->priceDiffPercent : 0,
										'SellPrice' => $rs->sellPrice,
										'U_DISWEB' => round($rs->U_DISWEB, 2),
										'DiscPrcnt' => round($rs->DiscPrcnt, 2),
										'VatGroup' => $rs->VatGroup,
										'VatRate' => $rs->VatPrcnt,
										'LineTotal' => $rs->Type == 0 ? $rs->LineTotal : 0,
										'WhsCode' => get_null(trim($rs->WhsCode)),
										'LineText' => get_null(trim($rs->LineText)),
										'AfLineNum' => $rs->AfLineNum
									);

									if( ! $this->quotation_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert Quotation detail failed at line : {$no} @ {$rs->ItemCode}";
									}

									$no++;
								}
							}

						}

						if($sc === TRUE)
						{
							//--- write quotation logs
							$this->quotation_logs_model->add('edit', $code);
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
							$must_approve = $this->must_approve($code);
							$arr = array(
								'must_approve' => $must_approve === TRUE ? 1 : 0,
								'Approved' => $must_approve === TRUE ? 'P' : 'S'
							);

							$this->quotation_model->update($code, $arr);

							if(! $must_approve && $ds->isDraft == 0)
							{
								//--- export to Middle
								$this->doExport($code);
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Quotation header data";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Status : Document already in SAP";
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
			$this->error  = "Invalid Web Order Code : {$code}";
		}



		$message = array(
			"result" => $sc === TRUE ? 'success' : 'failed',
			"message" => $sc === TRUE ? $code : $this->error
		);

		echo json_encode($message);

	}




	//---- Preview Quotation detail
	public function view_detail($code)
	{
		$this->title = "Preview Sales Quotation";
		$this->load->model('stock_model');
		$this->load->model('sales_order_model');
		$header = $this->quotation_model->get($code);
		$in_sap = $this->quotation_model->is_sap_exists_code($code);
		$in_so = $this->sales_order_model->is_exists_sq($code);

		if(!empty($header))
		{
			$vatRate = getConfig('SALE_VAT_RATE');
			$totalAmount = 0;
			$details = $this->quotation_model->get_details($code);

			if(!empty($details))
			{
				foreach($details as $rs)
				{
					$rs->UomName = $this->item_model->get_uom_name($rs->UomCode);
					$stock = $this->stock_model->get_stock($rs->ItemCode, $rs->WhsCode);
					$totalAmount += $rs->LineTotal;
					$rs->OnHandQty = empty($stock) ? 0 : round($stock->OnHand,2);
					$rs->IsCommited = empty($stock) ? 0 : round($stock->IsCommited,2);
					$rs->OnOrder = empty($stock) ? 0 : round($stock->OnOrder, 2);
				}
			}

			$max_discount = $this->quotation_model->get_max_line_disc($code);
			$can_approve = $this->quotation_model->can_approve($this->user->uname, $header->sale_team, $max_discount);
			$contact = $this->customers_model->get_contact_person_detail($header->CntctCode);
			$header->contact_person = empty($contact) ? NULL : $contact->Name;
			$header->department_name = $this->quotation_model->get_department_name($header->OcrCode);
			$header->division_name = $this->quotation_model->get_division_name($header->OcrCode1);
			$header->series_name = $this->quotation_model->get_series_name($header->Series);
			$header->owner_name = $this->user_model->get_emp_name($header->OwnerCode);

			$ds =  array(
				'header' => $header,
				'details' => $details,
				'totalAmount' => $totalAmount,
				'vat_rate' => $vatRate * 0.01,
				'sale_name' => $this->user_model->get_saleman_name($header->SlpCode),
				'logs' => $this->quotation_logs_model->get($code),
				'can_approve' => $can_approve,
				'in_sap' => $in_sap,
				'in_so' => $in_so
			);

			$this->load->view('quotation/quotation_detail', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}






	//--- ตรวจสอบเงื่อนไขว่าต้องอนุมัติหรือไม่ ถ้าไม่เข้าเงื่อนไข ไม่ต้องอนุมัติ
	public function must_approve($code)
	{
		$doc = $this->quotation_model->get($code);

		if(!empty($doc))
		{
			$max_discount = $this->quotation_model->get_max_line_disc($code);

			//---- ต้องมีส่วนลด ถ้าไม่มีส่วนลดไม่ต้องตรวจสอบ
			//--- return TRUE if exists rule return FALSE if not exists rule
			return $this->quotation_model->is_exists_rule($doc->sale_team, $max_discount);

		}

		return FALSE;
	}


	function get_item_data()
	{
		$sc = TRUE;
		$code = trim($this->input->get('code'));
		$card_code = trim($this->input->get('CardCode'));

		if(!empty($code))
		{
			$this->load->model('stock_model');
			$PriceList = $this->customers_model->get_list_num($card_code);
			$PriceList = empty($PriceList) ? 1 : $PriceList;

			//--- เช็คว่าลูกค้าถูก SET VAT ไว้หรือไม่ ถ้าใช่ ใช้ Vat code, vat rate จาก ลูกค้าก่อน
			$customerTax = $this->customers_model->get_tax($card_code);

			$item = $this->item_model->get($code);

			if(!empty($item))
			{
				$DfUom = NULL;
				$price = 0.00;
				$discount = 0.00;
				$priceAfDisc = 0.00;

				$spPrice = $this->item_model->get_special_price($item->code, $card_code, $PriceList);
				if(!empty($spPrice))
				{
					$DfUom = $spPrice->UomEntry;
					$price = empty($spPrice->Price) ? round($spPrice->PriceAfDisc, 2) : round($spPrice->Price, 2);
					$priceAfDisc = round($spPrice->PriceAfDisc, 2);
					$discount = round($spPrice->Discount, 2);
				}
				else
				{
					$price_list = $this->item_model->price_list($item->code, $PriceList); //--- return AS object with 2 properties (Price , UomEntry)
					if(!empty($price_list))
					{
						$DfUom = $price_list->UomEntry;
						$price = round($price_list->Price, 2);
						$priceAfDisc = $price;
						$discount = 0.00;
					}
				}


				$uom = "";
				$UomList = $this->item_model->get_uom_list($item->UgpEntry);

				if(!empty($UomList))
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


				$stock = $this->stock_model->get_stock($item->code, NULL);
				$whsQty = !empty($stock) ? round($stock->OnHand,2) : 0;
				$commitQty = !empty($stock) ? round($stock->IsCommited,2) : 0;
				$orderedQty = !empty($stock) ? round($stock->OnOrder, 2) : 0;

				if($whsQty > 0)
				{
					if($stock->StockValue > 0)
					{
						$cost = round(($stock->StockValue/$whsQty), 2);
					}
				}


				$arr = array(
					'code' => $item->code,
					'name' => $item->name,
					'detail' => $item->detail,
					'freeText' => $item->ValidComm,
					'uom' => $uom,
					'taxCode' => !empty($customerTax) ? $customerTax->taxCode : $item->taxCode,
					'taxRate' => !empty($customerTax) ? $customerTax->taxRate : $item->taxRate,
					'price' => $price,
					'lastSellPrice' => empty($card_code) ? $price : $this->item_model->last_sell_price($item->code, $card_code, $DfUom),
					'lastQuotePrice' => empty($card_code) ? $price : $this->item_model->last_quote_price($item->code, $card_code, $DfUom),
					'discount' => $discount,
					'priceDiff' => $price,
					'lineAmount' => $price,
					'whsQty' => $whsQty,
					'commitQty' => $commitQty,
					'orderedQty' => $orderedQty,
					'dfWhsCode' => empty($item->dfWhsCode) ? getConfig('DEFAULT_WAREHOUSE') : $item->dfWhsCode
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


	public function get_contact_person()
	{
		$sc = TRUE;
		$code = trim($this->input->get('CardCode'));
		$ds = array();
		if(!empty($code))
		{
			$cps = $this->customers_model->get_contact_person($code);

			if(!empty($cps))
			{
				foreach($cps as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'name' => $rs->name
					);

					array_push($ds, $arr);
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : CardCode";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


	public function get_address_ship_to_code()
	{
		$code = trim($this->input->get('CardCode'));
		$ds = array();
		if(!empty($code))
		{
			$addr = $this->customers_model->get_address_ship_to_code($code);

			if(!empty($addr))
			{
				foreach($addr as $adr)
				{
					$arr = array(
						'code' => get_empty_text($adr->Address)
					);

					array_push($ds, $arr);
				}
			}
			else
			{
				$arr = array(
					'code' =>""
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
	}



	public function get_address_ship_to()
	{
		$code = trim($this->input->get('CardCode'));
		$adr_code = trim($this->input->get('Address'));
		if(!empty($code))
		{
			$adr = $this->customers_model->get_address_ship_to($code, $adr_code);

			if(!empty($adr))
			{
				$arr = array(
					'code' => get_empty_text($adr->Address),
					'address' => get_empty_text($adr->Street),
					'street' => get_empty_text($adr->StreetNo),
					'sub_district' => get_empty_text($adr->Block),
					'district' => get_empty_text($adr->City),
					'province' => get_empty_text($adr->County),
					'country' => get_empty_text($adr->Country),
					'countryName' => get_empty_text($adr->countryName),
					'postcode' => get_empty_text($adr->ZipCode)
				);

			}
			else
			{
				$arr = array(
					'code' => "",
					'address' => "",
					'street' => "",
					'sub_district' => "",
					'district' => "",
					'province' => "",
					'country' => "",
					'countryName' => "",
					'postcode' => ""
				);
			}

			echo json_encode($arr);
		}
	}



	public function get_address_bill_to_code()
	{
		$code = trim($this->input->get('CardCode'));
		$ds = array();
		if(!empty($code))
		{
			$addr = $this->customers_model->get_address_bill_to_code($code);

			if(!empty($addr))
			{
				foreach($addr as $adr)
				{
					$arr = array(
						'code' => get_empty_text($adr->Address)
					);

					array_push($ds, $arr);
				}
			}
			else
			{
				$arr = array(
					'code' => ""
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
	}


	public function get_address_bill_to()
	{
		$code = trim($this->input->get('CardCode'));
		$adr_code = trim($this->input->get('Address'));
		if(!empty($code))
		{
			$adr = $this->customers_model->get_address_bill_to($code, $adr_code);

			if(!empty($adr))
			{
				$arr = array(
					'code' => get_empty_text($adr->Address),
					'address' => get_empty_text($adr->Street),
					'street' => get_empty_text($adr->StreetNo),
					'sub_district' => get_empty_text($adr->Block),
					'district' => get_empty_text($adr->City),
					'province' => get_empty_text($adr->County),
					'country' => get_empty_text($adr->Country),
					'countryName' => get_empty_text($adr->countryName),
					'postcode' => get_empty_text($adr->ZipCode)
				);
			}
			else
			{
				$arr = array(
					'code' => "",
					'address' => "",
					'street' => "",
					'sub_district' => "",
					'district' => "",
					'province' => "",
					'country' => "",
					'countryName' => "",
					'postcode' => ""
				);
			}

			echo json_encode($arr);
		}
	}


	public function get_series()
	{
		$ds = array();
		//--- set default if month is null
		$month = $this->input->get('month');
		$month = empty($month) ? date('Y-m') : $month;
		$default = getConfig('DEFAULT_QUOTATION_SERIES');

		$options = $this->quotation_model->get_series($month);

		if(!empty($options))
		{
			foreach($options as $rs)
			{
				$arr = array(
					'code' => $rs->code,
					'name' => $rs->name,
					'is_selected' => is_selected($default, $rs->prefix)
				);

				array_push($ds, $arr);
			}
		}
		else
		{
			$arr = array(
				'code' => "",
				'name' => "Please define Series",
				'is_selected' => ""
			);

			array_push($ds, $arr);
		}

		echo json_encode($ds);
	}




	public function get_sale_by_customer()
	{
		$code = trim($this->input->get('CardCode'));

		$slp = $this->customers_model->get_slp_code_and_name($code);


		if(!empty($slp))
		{
			if($slp->SlpCode == -1)
			{
				echo 'notfound';
			}
			else
			{
				$arr = array(
					'id' => $slp->SlpCode,
					'name' => $slp->SlpName
				);

				echo json_encode($arr);
			}

		}
		else
		{
			echo 'notfound';
		}
	}


	function get_stock()
	{
		$sc = TRUE;
		$this->load->model('stock_model');
		$itemCode = trim($this->input->get('itemCode'));
		$whsCode = trim($this->input->get('whs'));

		if(!empty($itemCode))
		{
			if(!empty($whsCode))
			{
				$rs = $this->stock_model->get_stock($itemCode, $whsCode);
				if(!empty($rs))
				{

					$arr = array(
						'whsQty' => round($rs->OnHand, 2),
						'commitQty' => round($rs->IsCommited, 2),
						'orderedQty' => round(($rs->OnHand - $rs->IsCommited),2)
					);
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Item OR Whs code";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Required Parameter : WhsCode";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Required Parameter : ItemCode";
		}

		echo $sc === TRUE ? json_encode($arr) : $this->error;
	}



	public function doExport($code)
	{
		$sc = TRUE;
		$doc = $this->quotation_model->get($code);
		if(!empty($doc))
		{
			//---- Status ต้องยังไม่เข้า SAP && (ไม่ต้องอนุมัติ หรือ อนุมัติแล้ว เท่านั้น)
			if($doc->Status != 2 && ($doc->must_approve == 0 OR ($doc->Approved == 'A' OR $doc->Approved == 'S' )))
			{
				//---- check SQ already in SAP
				$sq = $this->quotation_model->get_sap_quotation($code);
				//$sq = $this->quotation_model->get_sap_quotation_draft($code); //--- check ว่า SQ เข้า draft ไปแล้วหรือยัง

				if(empty($sq))
				{
					//---- drop exists temp data
					$temp = $this->quotation_model->get_temp_quotations($code);
					if(!empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if($this->quotation_model->drop_quotation_temp_data($rows->DocEntry) === FALSE)
		          {
		            $sc = FALSE;
		            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
		          }
		        }
		      }

					if($sc === TRUE)
					{
						//--- insert heade OQUT ก่อน แล้วได้ DocEntry มาเอาไปใส่ที่อื่นต่อ
						$header = array(
							'DocDate' => sap_date($doc->DocDate, TRUE),
							'DocDueDate' => sap_date($doc->DocDueDate, TRUE),
							'CardCode' => $doc->CardCode,
							'CardName' => $doc->CardName,
							'PayToCode' => $doc->PayToCode,
							'Address' => $doc->Address,
							'ShipToCode' => $doc->ShipToCode,
							'Address2' => $doc->Address2,
							'NumAtCard' => $doc->NumAtCard,
							'VatSum' => $doc->VatSum,
							'DiscPrcnt' => $doc->DiscPrcnt,
							'DiscSum' => ($this->quotation_model->sum_line_total($code) * ($doc->DiscPrcnt * 0.01)),
							'DocCur' => $doc->DocCur,
							'DocRate' => $doc->DocRate,
							'RoundDif' => $doc->RoundDif,
							'DocTotal' => $doc->DocTotal,
							'TaxDate' => sap_date($doc->TextDate, TRUE), //--- Tax date
							'Series' => $doc->Series,
							'SlpCode' => $doc->SlpCode,
							'OwnerCode' => $doc->OwnerCode,
							'CntctCode' => $doc->CntctCode,
							'Comments' => $doc->Comments,
							'U_WEBORDER' => $doc->code,
							'U_ORIGINALSQ' => $doc->U_ORIGINALSQ,
							'F_Web' => 'A',
							'F_WebDate' => sap_date(now(), TRUE)
						);

						$docEntry = $this->quotation_model->add_sap_quotation($header);

						if($docEntry !== FALSE)
						{
							$details = $this->quotation_model->get_details($code);
							if(!empty($details))
							{
								$seqNum = 0;

								foreach($details as $rs)
								{
									//---- if text row
									if($rs->Type == 1)
									{
										$arr = array(
											'DocEntry' => $docEntry,
											'LineSeq' => $seqNum,
											'LineText' => $rs->LineText,
											'AftLineNum' => $rs->AfLineNum,
											'U_WEBORDER' => $rs->quotation_code
										);

										$this->quotation_model->add_sap_quotation_text_row($arr);
										$seqNum++;
									}
									else
									{
										$arr = array(
											'DocEntry' => $docEntry,
											'U_WEBORDER' => $rs->quotation_code,
											'LineNum' => $rs->LineNum,
											'ItemCode' => $rs->ItemCode,
											'Dscription' => $rs->Dscription,
											'Quantity' => $rs->Qty,
											'UomCode' => $rs->UomCode,
											'Price' => $rs->SellPrice,
											'LineTotal' => $rs->LineTotal,
											'DiscPrcnt' => $rs->DiscPrcnt,
											'PriceBefDi' => $rs->Price,
											'Currency' => $doc->DocCur,
											'Rate' => $doc->DocRate,
											'VatGroup' => $rs->VatGroup,
											'VatPrcnt' => $rs->VatRate,
											'PriceAfVAT' => (($rs->VatRate * 0.01) + 1) * $rs->SellPrice,
											'VatSum' => ($rs->VatRate * 0.01) * $rs->LineTotal,
											'GTotal' => (($rs->VatRate * 0.01) + 1) * $rs->LineTotal,
											'WhsCode' => $rs->WhsCode,
											'SlpCode' => $doc->SlpCode,
											'Text' => $rs->ItemDetail,
											'FreeTxt' => $rs->FreeText,
											'OcrCode' => $doc->OcrCode,
											'OcrCode2' => $doc->OcrCode1,
											'OwnerCode' => $doc->OwnerCode,
											'U_DISWEB' => $rs->U_DISWEB,
											'U_DISCEX' => $rs->U_DISCEX,
											'U_SO_LSALEPRICE' => $rs->lastSellPrice,
											'U_SQ_LSALEPRICE' => $rs->lastQuotePrice
										);

										$this->quotation_model->add_sap_quotation_row($arr);
									}

								} //--- end foreach details
							} //-- end if empty details

						} //--- end if docEntry
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "เอกสารถูกนำเข้า SAP แล้ว";
				}
			}
		}

		if($sc === TRUE)
		{
			$arr = array(
				'Status' => 1,
				'temp_date' => now()
			);

			$this->quotation_model->update($code, $arr);
		}


		return $sc;
	}



	public function unExport($code)
	{
		$sc = TRUE;

		//---- check SQ already in SAP
		$sq = $this->quotation_model->get_sap_quotation($code);

		if(empty($sq))
		{
			//---- drop exists temp data
			$temp = $this->quotation_model->get_temp_quotations($code);
			if(!empty($temp))
			{
				foreach($temp as $rows)
				{
					if($this->quotation_model->drop_quotation_temp_data($rows->DocEntry) === FALSE)
					{
						$sc = FALSE;
						$this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เอกสารเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
		}

		if($sc === TRUE)
		{
			//---- update status
			$arr = array(
				'Status' => 0,
				'temp_date' => NULL,
				'sap_date' => NULL,
				'Message' => NULL
			);

			$this->quotation_model->update($code, $arr);
		}

		return $sc;
	}


	public function sendToSAP()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));

		if(!empty($code))
		{
			if(!$this->doExport($code))
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required Parameter : code";
		}

		$this->response($sc);
	}




	public function approve()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		if(!empty($code))
		{
			$rs = $this->do_approve($code);
			if($rs === TRUE)
			{
				if(! $this->doExport($code))
				{
					$sc = FALSE;
				}
			}
			else
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}



	public function unapprove()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		if(!empty($code))
		{
			$rs = $this->un_approve($code);
			if($rs === TRUE)
			{
				if(! $this->unExport($code))
				{
					$sc = FALSE;
				}
			}
			else
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}




	public function reject()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		if(!empty($code))
		{
			if(! $this->do_reject($code))
			{
				$sc = FALSE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}




	public function do_approve($code)
	{
		$sc = TRUE;
		$doc = $this->quotation_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'P' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->quotation_model->get_max_line_disc($code);
				$can_approve = $this->quotation_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE OR $this->isSuperAdmin)
				{
					$arr = array(
						'Approved' => 'A',
						'Approver' => $this->user->uname
					);

					if($this->quotation_model->update($code, $arr))
					{
						$this->quotation_logs_model->add('approve', $code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Permission";
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
			$this->error = "Document not found";
		}

		return $sc;
	}



	//---- Un approve
	public function un_approve($code)
	{
		$sc = TRUE;
		$doc = $this->quotation_model->get($code);

		if(!empty($doc))
		{
			//--- ตัองอนุมัติแล้ว และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'A' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->quotation_model->get_max_line_disc($code);
				$can_approve = $this->quotation_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);
				$must_approve = $this->must_approve($code);
				if($can_approve === TRUE OR $this->isSuperAdmin OR $must_approve === FALSE)
				{
					$arr = array(
						'Approved' => 'P',
						'Approver' => $this->user->uname
					);

					if($this->quotation_model->update($code, $arr))
					{
						$this->quotation_logs_model->add('unapprove', $code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Permission";
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
			$this->error = "Document not found";
		}

		return $sc;
	}


	public function do_reject($code)
	{
		$sc = TRUE;
		$doc = $this->quotation_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'P' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->quotation_model->get_max_line_disc($code);
				$can_approve = $this->quotation_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE)
				{
					$arr = array(
						'Approved' => 'R',
						'Approver' => $this->user->uname
					);

					if($this->quotation_model->update($code, $arr))
					{
						$this->quotation_logs_model->add('reject', $code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Permission";
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
			$this->error = "Document not found";
		}

		return $sc;
	}



	public function un_reject($code)
	{
		$sc = TRUE;
		$doc = $this->quotation_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'R' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->quotation_model->get_max_line_disc($code);
				$can_approve = $this->quotation_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE)
				{
					$arr = array(
						'Approved' => 'P',
						'Approver' => $this->user->uname
					);

					if($this->quotation_model->update($code, $arr))
					{
						$this->quotation_logs_model->add('unreject', $code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Permission";
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
			$this->error = "Document not found";
		}

		return $sc;
	}




	public function print_quotation($code)
	{
		$this->load->library('printer');
		$doc = $this->quotation_model->get($code);
		$detail = $this->quotation_model->get_details($code);

		$details = array();


		if(!empty($detail))
		{
			$no = 0;
			foreach($detail as $rs)
			{
				if($rs->Type == 1 && $no > 0)
				{
					$noo = $no -1;
					$details[$noo]->Dscription .= PHP_EOL.$rs->LineText;
				}
				else
				{
					$rs->UomName = $this->item_model->get_uom_name($rs->UomCode);
					$details[$no] = $rs;
					$no++;
				}
			}
		}


		$customer = $this->customers_model->get_sap_contact_data($doc->CardCode);
		$sale = $this->user_model->get_sap_sale_data($doc->SlpCode);
		$doc->prefix = empty($doc->BeginStr) ? $this->quotation_model->get_prefix($doc->Series) : $doc->BeginStr;
		$doc->OwnerName = empty($doc->OwnerCode) ? "" : $this->user_model->get_emp_name($doc->OwnerCode);
		$contact_person = empty($doc->CntctCode) ? "" : $this->customers_model->get_contact_person_name($doc->CntctCode);

		$ds = array(
			'doc' => $doc,
			'details' => $details,
			'customer' => $customer,
			'contact_person' => $contact_person,
			'sale' => $sale,
			'show_discount' => TRUE
		);

		if(!empty(getConfig('DEMO')))
		{
			$this->load->view('print/print_quotation_demo', $ds);
		}
		else
		{
			$this->load->view('print/print_quotation', $ds);
		}

	}



	public function print_quotation_no_discount($code)
	{
		$this->load->library('printer');
		$doc = $this->quotation_model->get($code);
		$detail = $this->quotation_model->get_details($code);

		$details = array();


		if(!empty($detail))
		{
			$no = 0;
			foreach($detail as $rs)
			{
				if($rs->Type == 1 && $no > 0)
				{
					$noo = $no -1;
					$details[$noo]->Dscription .= PHP_EOL.$rs->LineText;
				}
				else
				{
					$rs->UomName = $this->item_model->get_uom_name($rs->UomCode);
					$details[$no] = $rs;
					$no++;
				}
			}
		}

		$customer = $this->customers_model->get_sap_contact_data($doc->CardCode);
		$sale = $this->user_model->get_sale_data($doc->SlpCode);
		$empName = empty($sale) ? "" : $sale->emp_name;
		$division_name = empty($sale) ? "" : $this->user_model->get_division_name($sale->division_code);
		$doc->prefix = empty($doc->BeginStr) ? $this->quotation_model->get_prefix($doc->Series) : $doc->BeginStr;

		$ds = array(
			'doc' => $doc,
			'details' => $details,
			'customer' => $customer,
			'empName' => $empName,
			'division_name' => $division_name,
			'show_discount' => FALSE
		);

		$this->load->view('print/print_quotation', $ds);
	}




	public function get_temp_data()
  {
    $code = $this->input->get('code'); //--- U_WEBORDER

    $data = $this->quotation_model->get_temp_data($code);
    if(!empty($data))
    {
			//$btn = "<button type='button' class='btn btn-sm btn-danger' onClick='removeTemp()'' ><i class='fa fa-trash'></i> Delete Temp</button>";

			$status = 'Pending';

			if($data->F_Sap === NULL)
			{
				$status = "Pending";
			}
			else if($data->F_Sap === 'N')
			{
				$status = "Failed";
			}
			else if($data->F_Sap === 'Y')
			{
				$sq = $this->quotation_model->get_sap_quotation($code);

				if(!empty($sq))
				{
					$status = "Success";
				}
				else
				{
					$sd = $this->quotation_model->get_sap_quotation_draft($code);
					if(!empty($sd))
					{
						$status = "Draft";
					}
					else
					{
						$status = "Success";
					}
				}
			}


      $arr = array(
        'U_WEBORDER' => $data->U_WEBORDER,
        'CardCode' => $data->CardCode,
        'CardName' => $data->CardName,
        'F_WebDate' => thai_date($data->F_WebDate, TRUE),
        'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
        'F_Sap' => $status, //$data->F_Sap === 'Y' ? 'Success' : ($data->F_Sap === 'N' ? 'Failed' : 'Pending'),
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
    $temp = $this->quotation_model->get_temp_status($code);

    if(empty($temp))
    {
      $sc = FALSE;
      $this->error = "Temp data not exists";
    }
    else if($temp->F_Sap === 'Y')
    {
      $sc = FALSE;
      $this->error = "Delete Failed : Temp Data already in SAP";
    }

    if($sc === TRUE)
    {
      if(! $this->quotation_model->drop_temp_exists_data($code))
      {
        $sc = FALSE;
        $this->error = "Delete Failed : Delete Temp Failed";
      }
			else
			{
				$arr = array(
					'Status' => 0,
					'DocNum' => NULL,
					'Message' => NULL,
					'sap_date' => NULL,
					'temp_date' => NULL
				);

				$this->quotation_model->update($code, $arr);
			}
    }


    $this->response($sc);
  }



	public function get_payment_term()
	{
		$sc = TRUE;
		$code = $this->input->get('CardCode');

		if(!empty($code))
		{
			$rs = $this->customers_model->getPaymentTerm($code);

			if(!empty($rs))
			{
				$result = $rs->code.' | '.$rs->name;
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid CardCode";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : CardCode";
		}

		echo $sc === TRUE ? $result : $this->error;
	}


	public function get_customer_price_list()
	{
		$code = $this->input->get('CardCode');
		$priceList = $this->customers_model->get_customer_price_list($code);
		if(!empty($priceList))
		{
			echo $priceList->name;
		}
		else
		{
			echo "Not found";
		}
	}


	public function get_average_cost($code)
	{
		$cost = $this->item_model->get_average_cost($code);

		if(empty($cost))
		{
			$cost = $this->item_model->get_item_cost($code);
		}

		return $cost;
	}



	public function get_last_sell_price()
	{
		$cardCode = get_null(trim($this->input->get('cardCode')));
		$itemCode = get_null(trim($this->input->get('itemCode')));
		$uomEntry = get_null($this->input->get('uomEntry'));

		$last_price = $this->item_model->last_sell_price($itemCode, $cardCode, $uomEntry);
		$last_quote = $this->item_model->last_quote_price($itemCode, $cardCode, $uomEntry);

		$arr = array(
			"lastSellPrice" => $last_price,
			"lastQuotePrice" => $last_quote
		);

		echo json_encode($arr);
	}


	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_QUOTATION');
    $run_digit = getConfig('RUN_DIGIT_QUOTATION');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->quotation_model->get_max_code($pre);
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
			'sq_WebCode',
			'sq_DocNum',
			'sq_SoNo',
			'sq_CardCode',
			'sq_CardName',
			'sq_SaleName',
			'sq_CustRef',
			'sq_Approved',
			'sq_SapStatus',
			'sq_Status',
			'sq_fromDate',
			'sq_toDate',
			'sq_order_by',
			'sq_sort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>

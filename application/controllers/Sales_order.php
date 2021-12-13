<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_order extends PS_Controller
{
	public $menu_code = 'SALESORDER';
	public $menu_group_code = 'AR';
	public $title = 'Sales Order';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'sales_order';
		$this->load->model('sales_order_model');
		$this->load->model('customers_model');
		$this->load->model('sales_order_logs_model');
		$this->load->model('item_model');
		$this->load->helper('sales_order');
		$this->load->helper('currency');
  }



  public function index()
  {
		//$this->update_status(100);

		$filter = array(
			'WebCode' => get_filter('WebCode', 'so_WebCode', ''),
			'DocNum' => get_filter('DocNum', 'so_DocNum', ''),
			'SqNo' => get_filter('SqNo', 'so_SqNo', ''),
			'DeliveryNo' => get_filter('DeliveryNo', 'so_DeliveryNo', ''),
			'InvoiceNo' => get_filter('InvoiceNo', 'so_InvoiceNo', ''),
			'CardCode' => get_filter('CardCode', 'so_CardCode', ''),
			'SaleName' => get_filter('SaleName', 'so_SaleName', ''),
			'CustRef' => get_filter('CustRef', 'so_CustRef', ''),
			'Approved' => get_filter('Approved', 'so_Approved', 'all'),
			'SapStatus' => get_filter('SapStatus', 'so_SapStatus', 'all'),
			'Status' => get_filter('Status', 'so_Status', 'all'),
			'fromDate' => get_filter('fromDate', 'so_fromDate', ''),
			'toDate' => get_filter('toDate', 'so_toDate', ''),
			'order_by' => get_filter('order_by', 'so_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'so_sort_by', 'DESC'),
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->sales_order_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->sales_order_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('sales_order/sales_order_list', $filter);
  }


	private function update_status($limit = 100)
	{
		$ds = $this->sales_order_model->get_non_so_code($limit);
		if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $temp = $this->sales_order_model->get_temp_status($rs->code);
        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $sap = $this->sales_order_model->get_sap_doc_num($rs->code);

            if(!empty($sap))
            {
              $arr = array(
                'DocNum' => $sap->DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2, //--- เข้า SAP แล้ว
                'Message' => NULL
              );

              $this->sales_order_model->update($rs->code, $arr);
            }
          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 3,
                'Message' => $temp->Message
              );

              $this->sales_order_model->update($rs->code, $arr);
            }
          }
        }
      }
    }
	}



	public function add_new()
	{
		$this->title = "New Sales Order";

		$ds = array(
			'sale_name' => $this->user_model->get_saleman_name($this->user->sale_id)
		);

		$this->load->view('sales_order/sales_order_add', $ds);
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
				'DocCur' => NULL,
				'DocRate' => 1,
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
				'BeginStr' => $this->sales_order_model->get_prefix($ds->Series),
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

			if(!$this->sales_order_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "Insert Quotation Header failed";
			}
			else
			{
				//--- insert sales_order success
				//--- insert detail
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
							'sales_order_code' => $code,
							'LineNum' => $rs->LineNum,
							'Type' => $rs->Type, //--- 0 = item , 1 = text
							'ItemCode' => get_null(trim($rs->ItemCode)),
							'Dscription' => get_null(trim($rs->Description)),
							'ItemDetail' => get_null(trim($rs->Text)),
							'FreeText' => get_null(trim($rs->FreeTxt)),
							'Qty' => $rs->Quantity,
							'UomCode' => get_null($rs->UomCode),
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


						if( ! $this->sales_order_model->add_detail($arr))
						{
							$sc = FALSE;
							$this->error = "Insert Sales order detail failed at line : {$no} @ {$rs->ItemCode}";
						}

						$no++;
					}
				}

			}

			if($sc === TRUE)
			{
				//--- write sales_order logs
				$this->sales_order_logs_model->add('add', $code);
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

				$this->sales_order_model->update($code, $arr);

				if(! $must_approve)
				{
					//--- export to Middle
					$this->doExport($code);
				}

			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Sales Order header data";
		}

		$message = array(
			"result" => $sc === TRUE ? 'success' : 'failed',
			"message" => $sc === TRUE ? $code : $this->error
		);

		echo json_encode($message);
	}


	public function duplicate_sales_order()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if(!empty($code))
		{
			$ds = $this->sales_order_model->get($code);

			if(!empty($ds))
			{
				$date = date('Y-m-d');
				$valid_till = date('Y-m-d', strtotime("+30 days"));
				//--- prepare data
				$SOCode = $this->get_new_code($date);
				$OriginalSO = $ds->code . (empty($ds->DocNum) ? "": ", ".$ds->DocNum);
				$arr = array(
					'code' => $SOCode,
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
					'DocDueDate' => $valid_till,
					'TextDate' => $date,
					'U_ORIGINALSO' => $OriginalSO,
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

				if(!$this->sales_order_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Insert Sales Order Header failed";
				}
				else
				{
					//--- insert sales_order success
					//--- insert detail QUT1
					$details = $this->sales_order_model->get_details($code);

					if(!empty($details))
					{

						foreach($details as $rs)
						{
							if($sc === FALSE)
							{
								break;
							}

							$arr = array(
								'sales_order_code' => $SOCode,
								'LineNum' => $rs->LineNum,
								'Type' => $rs->Type, //--- 0 = item , 1 = text
								'ItemCode' => $rs->ItemCode,
								'Dscription' => $rs->Dscription,
								'ItemDetail' => $rs->ItemDetail,
								'FreeText' => $rs->FreeText,
								'Qty' => $rs->Qty,
								'UomCode' => $rs->UomCode,
								'lastSellPrice' => $this->item_model->last_sell_price($rs->ItemCode, $ds->CardCode, $this->item_model->get_uom_id($rs->UomCode)),
								'basePrice' => $rs->basePrice,
								'stdPrice' => $rs->stdPrice,
								'Price' => $rs->Price,
								'priceDiffPercent' => $rs->Type == 0 ? $rs->priceDiffPercent : 0,
								'SellPrice' => $rs->SellPrice,
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

							if( ! $this->sales_order_model->add_detail($arr))
							{
								$sc = FALSE;
								$this->error = "Insert Sales Order detail failed at line : {$no} @ {$rs->ItemCode}";
							}

						}
					}
				}

				if($sc === TRUE)
				{
					//--- write sales_order logs
					$this->sales_order_logs_model->add('add', $SOCode);
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

					$this->sales_order_model->update($code, $arr);

				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid SO Code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Required Parameter: Orignal SO";
		}

		if($sc === TRUE)
		{
			$result = array(
				"status" => 'success',
				"code" => $SOCode
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



	//--- call from quotation_add.js
	//---- create sales order from quotation
	public function create_from_quotation()
	{
		$sc = TRUE;

		$sqCode = $this->input->post('quotation_code');

		if(!empty($sqCode))
		{
			$is_exists_sq = $this->sales_order_model->is_exists_sq($sqCode);

			if(!$is_exists_sq)
			{
				$this->load->model('quotation_model');
				$ds = $this->quotation_model->get($sqCode);

				if(!empty($ds))
				{
					//---- check default series
					$Series = $this->sales_order_model->get_default_series_by_prefix(getConfig('DEFAULT_SALES_ORDER_SERIES'));

					if(!empty($Series))
					{
						$qd = $this->quotation_model->get_details($ds->code);

						if(!empty($qd))
						{
							//---- create sale order
							$code = $this->get_new_code();
							$DocDueDate = date('Y-m-d', strtotime("+30 days"));

							$arr = array(
								'code' => $code,
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
								'PayToCode' => $ds->PayToCode,
								'ShipToCode' => $ds->ShipToCode,
								'Address' => $ds->Address,
								'Address2' => $ds->Address2,
								'Series' => $Series->code,
								'BeginStr' => $Series->prefix,
								'DocDate' => date('Y-m-d'),
								'DocDueDate' => NULL,
								'TextDate' => date('Y-m-d'),
								'U_SQNO' => $ds->code,
								'SqNo' => $ds->DocNum,
								'OwnerCode' => get_null($ds->OwnerCode),
								'Comments' => get_null($ds->Comments),
								'user_id' => $this->user->id,
								'uname' => $this->user->uname,
								'sale_team' => $ds->sale_team
							);

							$this->db->trans_begin();

							//--- add header
							if(! $this->sales_order_model->add($arr))
							{
								$sc = FALSE;
								$this->error = "Insert Sales Order Header failed";
							}
							else
							{
								//--- insert sales_order success
								foreach($qd as $rs)
								{
									if($sc === FALSE)
									{
										break;
									}

									$arr = array(
										'sales_order_code' => $code,
										'LineNum' => $rs->LineNum,
										'Type' => $rs->Type, //--- 0 = item , 1 = text
										'ItemCode' => $rs->ItemCode,
										'Dscription' => $rs->Dscription,
										'ItemDetail' => $rs->ItemDetail,
										'FreeText' => $rs->FreeText,
										'Qty' => $rs->Qty,
										'UomCode' => $rs->UomCode,
										'lastSellPrice' => $rs->lastSellPrice,
										'basePrice' => $rs->basePrice,
										'stdPrice' => $rs->stdPrice,
										'Price' => $rs->Price,
										'priceDiffPercent' => $rs->Type == 0 ? $rs->priceDiffPercent : 0,
										'SellPrice' => $rs->SellPrice,
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


									if( ! $this->sales_order_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert Sales Order detail failed at line : {$no} @ {$rs->ItemCode}";
									}
								} //--- end foreach

							}

							if($sc === TRUE)
							{
								$this->db->trans_commit();
							}
							else
							{
								$this->db->trans_rollback();
							}

							//---- check must approve
							if($sc === TRUE)
							{
								$must_approve = $this->must_approve($code);
								$arr = array(
									'must_approve' => $must_approve === TRUE ? 1 : 0,
									'Approved' => $must_approve === TRUE ? 'P' : 'S'
								);

								$this->sales_order_model->update($code, $arr);

							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "No item in {$sqCode}";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Default Series not defined or invalid default series";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "{$sqCode} Not found";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Can not create Sales Order from {$sqCode}. Because It was already used by another Sales order.";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Quotation Code";
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




	function edit($code)
	{
		$in_sap = $this->sales_order_model->is_sap_exists_code($code);

		if(!$in_sap)
		{
			$this->title = "Edit Sales Order";
			$this->load->model('stock_model');

			$header = $this->sales_order_model->get($code);
			$details = $this->sales_order_model->get_details($code);
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
				'logs' => $this->sales_order_logs_model->get($code)
			);

			$this->load->view('sales_order/sales_order_edit', $ds);
		}
		else
		{
			set_error("Sales Order already in SAP");
			redirect("{$this->home}/view_detail/{$code}");
		}

	}



	public function update()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		$ds = json_decode($this->input->post('header')); //--- Header OQUT
		$details = json_decode($this->input->post('details')); //---

		$doc = $this->sales_order_model->get($code);

		if(!empty($doc))
		{
			//-- Status 1 = pending to send to SAP, 2 = In SAP , 3 = Error
			//--- Approved A = Approved, P = pendign R = Rejected, S = No need to Approved
			if($doc->Status != 2 )
			{
				$in_sap = $this->sales_order_model->is_sap_exists_code($code);

				if(! $in_sap)
				{
					$temps = $this->sales_order_model->get_temp_sales_order($code);
					if(!empty($temps))
					{
						foreach($temps as $temp)
						{
							$this->sales_order_model->drop_sales_order_temp_data($temp->DocEntry);
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
							'Address' => get_null($ds->BillTo),
							'Address2' => get_null($ds->ShipTo),
							'Series' => $ds->Series,
							'BeginStr' => $this->sales_order_model->get_prefix($ds->Series),
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

						if(!$this->sales_order_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Update Sales Order Header failed";
						}
						else
						{
							//--- update sales_order success

							//--- Drop old details
							if(!$this->sales_order_model->drop_details($code))
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
										'sales_order_code' => $code,
										'LineNum' => $rs->LineNum,
										'Type' => $rs->Type, //--- 0 = item , 1 = text
										'ItemCode' => get_null(trim($rs->ItemCode)),
										'Dscription' => get_null(trim($rs->Description)),
										'ItemDetail' => get_null(trim($rs->Text)),
										'FreeText' => get_null(trim($rs->FreeTxt)),
										'Qty' => $rs->Quantity,
										'UomCode' => get_null($rs->UomCode),
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

									if( ! $this->sales_order_model->add_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert Sales Order detail failed at line : {$no} @ {$rs->ItemCode}";
									}

									$no++;
								}
							}

						}

						if($sc === TRUE)
						{
							//--- write sales_order logs
							$this->sales_order_logs_model->add('edit', $code);
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

							$this->sales_order_model->update($code, $arr);

							if(! $must_approve)
							{
								//--- export to Middle
								$this->doExport($code);
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid Sales Order header data";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Update failed : Document already in Darft";
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
		$this->title = "Preview Sales Order";
		$this->load->model('stock_model');
		$header = $this->sales_order_model->get($code);
		$in_sap = $this->sales_order_model->is_sap_exists_code($code);
		if(!empty($header))
		{
			$vatRate = getConfig('SALE_VAT_RATE');
			$totalAmount = 0;
			$details = $this->sales_order_model->get_details($code);

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

			$max_discount = $this->sales_order_model->get_max_line_disc($code);
			$can_approve = $this->sales_order_model->can_approve($this->user->uname, $header->sale_team, $max_discount);
			$contact = $this->customers_model->get_contact_person_detail($header->CntctCode);
			$header->contact_person = empty($contact) ? NULL : $contact->Name;
			$header->department_name = $this->sales_order_model->get_department_name($header->OcrCode);
			$header->division_name = $this->sales_order_model->get_division_name($header->OcrCode1);
			$header->series_name = $this->sales_order_model->get_series_name($header->Series);
			$header->owner_name = $this->user_model->get_emp_name($header->OwnerCode);

			$ds =  array(
				'header' => $header,
				'details' => $details,
				'totalAmount' => $totalAmount,
				'vat_rate' => $vatRate * 0.01,
				'sale_name' => $this->user_model->get_saleman_name($header->SlpCode),
				'logs' => $this->sales_order_logs_model->get($code),
				'can_approve' => $can_approve,
				'in_sap' => $in_sap
			);

			$this->load->view('sales_order/sales_order_detail', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}






	//--- ตรวจสอบเงื่อนไขว่าต้องอนุมัติหรือไม่ ถ้าไม่เข้าเงื่อนไข ไม่ต้องอนุมัติ
	public function must_approve($code)
	{
		$doc = $this->sales_order_model->get($code);

		if(!empty($doc))
		{
			$max_discount = $this->sales_order_model->get_max_line_disc($code);

			//---- ต้องมีส่วนลด ถ้าไม่มีส่วนลดไม่ต้องตรวจสอบ
			//--- return TRUE if exists rule return FALSE if not exists rule
			return $this->sales_order_model->is_exists_rule($doc->sale_team, $max_discount);

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

			$customerTax = $this->customers_model->get_tax($card_code);

			$item = $this->item_model->get($code, $PriceList);

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
					'priceDiff' => $price,
					'discount' => $discount,
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
				$ds = array();
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
		}

		echo json_encode($ds);
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
					'district' => get_empty_text($adr->County),
					'province' => get_empty_text($adr->City),
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
				$ds = array();
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
		}

		echo json_encode($ds);
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
					'district' => get_empty_text($adr->County),
					'province' => get_empty_text($adr->City),
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

			echo json_encode($ds);
		}
	}


	public function get_series()
	{
		$ds = array();
		//--- set default if month is null
		$month = $this->input->get('month');
		$month = empty($month) ? date('Y-m') : $month;
		$default = getConfig('DEFAULT_QUOTATION_SERIES');

		$options = $this->sales_order_model->get_series($month);

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
		$code = trim($this->input->post('CardCode'));

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
		$doc = $this->sales_order_model->get($code);
		if(!empty($doc))
		{
			//---- Status ต้องยังไม่เข้า SAP && (ไม่ต้องอนุมัติ หรือ อนุมัติแล้ว เท่านั้น)
			if($doc->Status != 2 && ($doc->must_approve == 0 OR ($doc->Approved == 'A' OR $doc->Approved == 'S' )))
			{
				//---- check SQ already in SAP
				$sq = $this->sales_order_model->get_sap_sales_order($code);
				//$sq = $this->sales_order_model->get_sap_sales_order_draft($code); //--- check ว่า SQ เข้า draft ไปแล้วหรือยัง

				if(empty($sq))
				{
					//---- drop exists temp data
					$temp = $this->sales_order_model->get_temp_sales_order($code);
					if(!empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if($this->sales_order_model->drop_sales_order_temp_data($rows->DocEntry) === FALSE)
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
							'DiscSum' => ($this->sales_order_model->sum_line_total($code) * ($doc->DiscPrcnt * 0.01)),
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
							'U_ORIGINALSO' => $doc->U_ORIGINALSO,
							'U_SQNO' => $doc->U_SQNO,
							'F_Web' => 'A',
							'F_WebDate' => sap_date(now(), TRUE)
						);

						$docEntry = $this->sales_order_model->add_sap_sales_order($header);

						if($docEntry !== FALSE)
						{
							$details = $this->sales_order_model->get_details($code);
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
											'U_WEBORDER' => $rs->sales_order_code
										);

										$this->sales_order_model->add_sap_sales_order_text_row($arr);
										$seqNum++;
									}
									else
									{
										$arr = array(
											'DocEntry' => $docEntry,
											'U_WEBORDER' => $rs->sales_order_code,
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
											'U_DISCEX' => $rs->U_DISCEX
										);

										$this->sales_order_model->add_sap_sales_order_row($arr);
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

			$this->sales_order_model->update($code, $arr);
		}


		return $sc;
	}



	public function unExport($code)
	{
		$sc = TRUE;

		//---- check SQ already in SAP
		$sq = $this->sales_order_model->get_sap_sales_order($code);

		if(empty($sq))
		{
			//---- drop exists temp data
			$temp = $this->sales_order_model->get_temp_sales_orders($code);
			if(!empty($temp))
			{
				foreach($temp as $rows)
				{
					if($this->sales_order_model->drop_sales_order_temp_data($rows->DocEntry) === FALSE)
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

			$this->sales_order_model->update($code, $arr);
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
		$doc = $this->sales_order_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'P' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->sales_order_model->get_max_line_disc($code);
				$can_approve = $this->sales_order_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE)
				{
					$arr = array(
						'Approved' => 'A',
						'Approver' => $this->user->uname
					);

					if($this->sales_order_model->update($code, $arr))
					{
						$this->sales_order_logs_model->add('approve', $code);
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
		$doc = $this->sales_order_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองอนุมัติแล้ว และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'A' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->sales_order_model->get_max_line_disc($code);
				$can_approve = $this->sales_order_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);
				$must_approve = $this->must_approve($code);
				if($can_approve === TRUE OR $must_approve === FALSE)
				{
					$arr = array(
						'Approved' => 'P',
						'Approver' => $this->user->uname
					);

					if($this->sales_order_model->update($code, $arr))
					{
						$this->sales_order_logs_model->add('unapprove', $code);
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
		$doc = $this->sales_order_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'P' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->sales_order_model->get_max_line_disc($code);
				$can_approve = $this->sales_order_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE)
				{
					$arr = array(
						'Approved' => 'R',
						'Approver' => $this->user->uname
					);

					if($this->sales_order_model->update($code, $arr))
					{
						$this->sales_order_logs_model->add('reject', $code);
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
		$doc = $this->sales_order_model->get($code);
		if(!empty($doc))
		{
			//--- ตัองยังไม่ได้อนุมัติ และ ยังไม่เข้า SAP และ ยังไม่มีเลขที่เอกสารใน SAP
			if($doc->Approved === 'R' && $doc->Status != 2 && $doc->DocNum === NULL)
			{
				//--- ตรวจสอบสิทธิ์ในการอนุาัติ
				$max_discount = $this->sales_order_model->get_max_line_disc($code);
				$can_approve = $this->sales_order_model->can_approve($this->user->uname, $doc->sale_team, $max_discount);

				if($can_approve === TRUE)
				{
					$arr = array(
						'Approved' => 'P',
						'Approver' => $this->user->uname
					);

					if($this->sales_order_model->update($code, $arr))
					{
						$this->sales_order_logs_model->add('unreject', $code);
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




	public function print_sales_order($code)
	{
		$this->load->model('stock_model');
		$this->load->library('printer');
		$doc = $this->sales_order_model->get($code);
		$detail = $this->sales_order_model->get_details($code);

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

					$stock = $this->stock_model->get_stock_zone_qty($rs->ItemCode, $rs->WhsCode);

					$rs->zone_code = empty($stock) ? NULL : $stock->zone_code;
					$rs->InStock = empty($stock) ? 0 : $stock->qty;
					$no++;
				}
			}
		}


		$customer = $this->customers_model->get_sap_contact_data($doc->CardCode);
		$sale = $this->user_model->get_sap_sale_data($doc->SlpCode);
		$doc->prefix = empty($doc->BeginStr) ? $this->sales_order_model->get_prefix($doc->Series) : $doc->BeginStr;
		$contact_person = empty($doc->CntctCode) ? "" : $this->customers_model->get_contact_person_name($doc->CntctCode);
		$doc->reference = !empty($doc->NumAtCard) ? $doc->NumAtCard : $doc->U_SQNO;

		$ds = array(
			'doc' => $doc,
			'details' => $details,
			'customer' => $customer,
			'contact_person' => $contact_person,
			'sale' => $sale,
			'show_discount' => TRUE
		);

		$this->load->view('print/print_sales_order', $ds);
	}



	// public function print_sales_order_no_discount($code)
	// {
	// 	$this->load->library('printer');
	// 	$doc = $this->sales_order_model->get($code);
	// 	$detail = $this->sales_order_model->get_details($code);
	//
	// 	$details = array();
	//
	//
	// 	if(!empty($detail))
	// 	{
	// 		$no = 0;
	// 		foreach($detail as $rs)
	// 		{
	// 			if($rs->Type == 1 && $no > 0)
	// 			{
	// 				$noo = $no -1;
	// 				$details[$noo]->Dscription .= PHP_EOL.$rs->LineText;
	// 			}
	// 			else
	// 			{
	// 				$rs->UomName = $this->item_model->get_uom_name($rs->UomCode);
	// 				$details[$no] = $rs;
	// 				$no++;
	// 			}
	// 		}
	// 	}
	//
	// 	$customer = $this->customers_model->get_sap_contact_data($doc->CardCode);
	// 	$sale = $this->user_model->get_sale_data($doc->SlpCode);
	// 	$empName = empty($sale) ? "" : $sale->emp_name;
	// 	$division_name = empty($sale) ? "" : $this->user_model->get_division_name($sale->division_code);
	// 	$doc->prefix = empty($doc->BeginStr) ? $this->sales_order_model->get_prefix($doc->Series) : $doc->BeginStr;
	//
	// 	$ds = array(
	// 		'doc' => $doc,
	// 		'details' => $details,
	// 		'customer' => $customer,
	// 		'empName' => $empName,
	// 		'division_name' => $division_name,
	// 		'show_discount' => FALSE
	// 	);
	//
	// 	$this->load->view('print/print_sales_order', $ds);
	// }




	public function get_temp_data()
  {
    $code = $this->input->get('code'); //--- U_WEBORDER

    $data = $this->sales_order_model->get_temp_data($code);
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
				$sq = $this->sales_order_model->get_sap_sales_order($code);

				if(!empty($sq))
				{
					$status = "Success";
				}
				else
				{
					$sd = $this->sales_order_model->get_sap_sales_order_draft($code);
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
    $temp = $this->sales_order_model->get_temp_status($code);

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
      if(! $this->sales_order_model->drop_temp_exists_data($code))
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

				$this->sales_order_model->update($code, $arr);
			}
    }


    $this->response($sc);
  }



	public function get_payment_term()
	{
		$sc = TRUE;
		$code = trim($this->input->get('CardCode'));

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

		$price = $this->item_model->last_sell_price($itemCode, $cardCode, $uomEntry);

		echo $price >= 0 ? $price : 0;
	}


	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SALES_ORDER');
    $run_digit = getConfig('RUN_DIGIT_SALES_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->sales_order_model->get_max_code($pre);
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
			'so_WebCode',
			'so_DocNum',
			'so_SqNo',
			'so_DeliveryNo',
			'so_InvoiceNo',
			'so_SapStatus',
			'so_CardCode',
			'so_CardName',
			'so_SaleName',
			'so_CustRef',
			'so_Approved',
			'so_Status',
			'so_fromDate',
			'so_toDate',
			'so_order_by',
			'so_sort_by'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>

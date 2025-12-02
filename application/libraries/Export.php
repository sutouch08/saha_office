<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Export
{
  protected $ci;
  public $error;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}

  public function export_order($code)
  {
    $sc = TRUE;
    $this->ci->load->model('sales_order_model');
    $doc = $this->ci->sales_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status != 2 && ($doc->must_approve == 0 OR ($doc->Approved == 'A' OR $doc->Approved == 'S' )))
      {
        $so = $this->ci->sales_order_model->get_sap_sales_order($code);

        if(empty($so))
				{
					//---- drop exists temp data
					$temp = $this->ci->sales_order_model->get_temp_sales_order($code);

					if( ! empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if($this->ci->sales_order_model->drop_sales_order_temp_data($rows->DocEntry) === FALSE)
		          {
		            $sc = FALSE;
		            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
		          }
		        }
		      }

					if($sc === TRUE)
					{
						$header = array(
							'DocDate' => sap_date($doc->DocDate, TRUE),
							'DocDueDate' => sap_date($doc->DocDueDate, TRUE),
							'CardCode' => $doc->CardCode,
							'CardName' => $doc->CardName,
							'PayToCode' => $doc->PayToCode,
							//'Address' => $doc->Address,
							'ShipToCode' => $doc->ShipToCode,
							//'Address2' => $doc->Address2,
							'NumAtCard' => $doc->NumAtCard,
							'VatSum' => $doc->VatSum,
							'DiscPrcnt' => $doc->DiscPrcnt,
							'DiscSum' => ($this->ci->sales_order_model->sum_line_total($code) * ($doc->DiscPrcnt * 0.01)),
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
							'U_Remark_Int' => $doc->U_Remark_Int,
							'U_IV_DO_print' => $doc->U_DO_IV_Print,
							'U_Delivery_Urgency' => $doc->U_Delivery_Urgency,
							'F_Web' => 'A',
							'F_WebDate' => sap_date(now(), TRUE),
							'U_Required_Delivery_Date' => sap_date($doc->DocDueDate)
						);

						$docEntry = $this->ci->sales_order_model->add_sap_sales_order($header);

						if($docEntry !== FALSE)
						{
							$details = $this->ci->sales_order_model->get_details($code);

							if( ! empty($details))
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

										$this->ci->sales_order_model->add_sap_sales_order_text_row($arr);
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
											'U_DISCEX' => $rs->U_DISCEX,
											'U_SO_LSALEPRICE' => $rs->lastSellPrice
										);

										$this->ci->sales_order_model->add_sap_sales_order_row($arr);
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
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    if($sc === TRUE)
		{
			$arr = array(
				'Status' => 1,
				'temp_date' => now()
			);

			$this->ci->sales_order_model->update($code, $arr);
		}

    return $sc;
  }

  //--- Receive PO
  //--- OPDN PDN1
  public function export_receive($code)
  {
    $sc = TRUE;
    $this->ci->load->model('receive_po_model');
    $doc = $this->ci->receive_po_model->get($code);
    $sap = $this->ci->receive_po_model->get_sap_receive_doc($code);

    if( ! empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 'C')
        {
          //---- ถ้ามีรายการที่ยังไม่ได้ถูกเอาเข้า SAP ให้ลบรายการนั้นออกก่อน(SAP เอาเข้าซ้ำไม่ได้)
          $middle = $this->ci->receive_po_model->get_temp_exists_data($code);

          if( ! empty($middle))
          {
            //--- Delete exists details
            foreach($middle as $rows)
            {
              if( ! $this->ci->receive_po_model->drop_temp_data($rows->DocEntry))
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
              }
            }
          }

          //--- หลังจากเคลียร์รายการค้างออกหมดแล้ว
          if($sc === TRUE)
          {
            $ds = array(
              'U_WEBORDER' => $doc->code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => sap_date($doc->date_add, TRUE),
              'DocDueDate' => sap_date($doc->date_add,TRUE),
              'TaxDate' => sap_date($doc->posting_date, TRUE),
              'CardCode' => $doc->vendor_code,
              'CardName' => $doc->vendor_name,
              'NumAtCard' => $doc->invoice_code,
              'VatSum' => $doc->VatSum,
              'DiscPrcnt' => $doc->DiscPrcnt,
              'DiscSum' => $doc->DiscSum,
              'DocCur' => $doc->Currency,
              'DocRate' => $doc->Rate,
              'DocTotal' => $doc->DocTotal,
              'ToWhsCode' => $doc->warehouse_code,
              'Comments' => limitText($doc->remark, 250),
              'F_Web' => 'A',
              'F_WebDate' => sap_date(now(),TRUE)
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->receive_po_model->add_sap_receive_po($ds);


            if($docEntry !== FALSE)
            {
              $details = $this->ci->receive_po_model->get_details($code);

              if( ! empty($details))
              {
                $line = 0;

                foreach($details as $rs)
                {
                  if($rs->ReceiveQty > 0)
                  {
                    $arr = array(
                      'DocEntry' => $docEntry,
                      'U_WEBORDER' => $rs->receive_code,
                      'LineNum' => $line,
                      'ShipDate' => sap_date($doc->date_add,TRUE),
                      'BaseType' => 22,
                      'BaseDocNum' => $rs->baseCode,
                      'BaseEntry' => $rs->baseEntry,
                      'BaseLine' => $rs->baseLine,
                      'ItemCode' => $rs->ItemCode,
                      'Dscription' => $rs->ItemName,
                      'Rate' => $rs->Rate,
                      'Currency' => $rs->Currency,
                      'Quantity' => $rs->ReceiveQty,
                      'Price' => $rs->Price,
                      'PriceBefDi' => $rs->PriceBefDi,
                      'PriceAfVAT' => $rs->PriceAfVAT,
                      'DiscPrcnt' => $rs->DiscPrcnt,
                      'LineTotal' => $rs->LineTotal,
                      'INMPrice' => $rs->INMPrice,
                      'VatSum' => $rs->VatAmount,
                      'VatGroup' => $rs->VatGroup,
                      'VatPrcnt' => $rs->VatRate,
                      'UomCode' => $rs->UomCode,
                      'UomEntry' => $rs->UomEntry,
                      'unitMsr' => $rs->unitMsr,
                      'NumPerMsr' => $rs->NumPerMsr,
                      'UomCode2' => $rs->UomCode2,
                      'UomEntry2' => $rs->UomEntry2,
                      'unitMsr2' => $rs->unitMsr2,
                      'NumPerMsr2' => $rs->NumPerMsr2,
                      'WhsCode' => $rs->WhsCode,
                      'FisrtBin' => $rs->BinCode,
                      'TaxStatus' => 'Y',
                      'TaxType' => 'Y'
                    );

                    if( ! $this->ci->receive_po_model->add_sap_receive_po_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
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
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }
  //--- end export Receive PO



} //--- end class

?>

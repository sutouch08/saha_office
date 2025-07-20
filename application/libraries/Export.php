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

  //--- Receive PO
  //--- OPDN PDN1
  public function export_receive($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');
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
              'DiscPrcnt' => 0.000000,
              'DiscSum' => 0.000000,
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

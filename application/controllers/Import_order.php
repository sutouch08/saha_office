<?php
class Import_order extends PS_Controller
{
  public $menu_code = 'SALESORDER';
	public $menu_group_code = 'AR';
	public $title = 'Import Sales Order';
  public $error;


  public function __construct()
  {
    parent::__construct();
		$this->load->model('sales_order_model');
		$this->load->model('customers_model');
		$this->load->model('sales_order_logs_model');
		$this->load->model('item_model');
		$this->load->helper('sales_order');
		$this->load->helper('currency');
    $this->load->model('order_import_logs_model');

    $this->load->library('excel');
    $this->load->library('export');
  }


  public function index()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $import = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;

    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import_order-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      $excel->setActiveSheetIndex(0);

      $worksheet	= $excel->getSheet(0);

      if( ! empty($worksheet))
      {
        $count = $worksheet->getHighestRow();
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

        if($count > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');
        $shipping_item = ! empty($shipping_item_code) ? $this->item_model->get($shipping_item_code) : NULL;

        if(empty($shipping_item))
        {
          $sc = FALSE;
          $this->error = "ไม่พบการตั้งค่า รหัสสินค้าค่าขนส่ง";
        }
        else
        {
          $shipping_item->UomCode = $this->item_model->get_uom_code($shipping_item->SUoMEntry);
        }

        $VatRate = getConfig('SALE_VAT_RATE');
        $VatRate = empty($VatRate) ? 0.07 : $VatRate * 0.01;
        $DefaultWarehouse = getConfig('DEFAULT_WAREHOUSE');

        if($sc === TRUE)
        {
          $ds = $this->parse_order_data($worksheet);

          if( ! empty($ds))
          {
            foreach($ds as $order)
            {
              $import++;

              $res = TRUE;
              $message = "";
              //---- เช็คว่ามีออเดอร์ที่สร้างด้วย reference แล้วหรือยัง
              //---- ถ้ายังไม่มีให้สร้างใหม่
              //---- ถ้ามีแล้วและยังไม่ได้ยกเลิก ไม่สามารถเพิ่มใหม่ได้
              $order_code = $this->sales_order_model->get_active_order_code_by_reference($order->reference);

              $DocTotal = 0;
              $TotalAmount = 0; // total amount before tax
              $TaxAmount = 0; //
              $DiscPrcnt = 0;

              if( empty($order_code) )
              {
                $this->db->trans_begin();

                $order_code = $this->get_new_code($order->DocDate);

          			$arr = array(
          				'code' => $order_code,
                  'reference' => $order->reference,
          				'CardCode' => $order->CardCode,
          				'CardName' => $order->CardName,
          				'SlpCode' => $order->SlpCode,
          				'GroupNum' => $order->GroupNum,
          				'Term' => $order->Term,
          				'CntctCode' => NULL,
          				'NumAtCard' => $order->reference,
          				'DocCur' => NULL,
          				'DocRate' => 1,
          				'DocTotal' => 0,
          				'DiscPrcnt' => 0,
          				'RoundDif' => 0,
          				'VatSum' => 0,
          				'OcrCode' => 'SAL',
          				'OcrCode1' => 'SAL-01',
          				'PayToCode' => $order->PayToCode,
          				'ShipToCode' => $order->ShipToCode,
          				'Address' => $order->Address,
          				'Address2' => $order->Address2,
          				'Series' => $order->Series,
          				'BeginStr' => $order->BeginStr,
          				'Status' => 0,
          				'DocDate' => $order->DocDate,
          				'DocDueDate' => $order->DocDueDate,
          				'TextDate' => $order->TextDate,
          				'OwnerCode' => $order->OwnerCode,
          				'Comments' => $order->Comments,
          				'U_DO_IV_Print' => get_null($order->U_DO_IV_Print),
          				'U_Delivery_Urgency' => get_null($order->U_Delivery_Urgency),
          				'user_id' => $order->user_id,
          				'uname' => $order->uname,
          				'sale_team' => $order->sale_team,
                  'Approved' => 'S',
                  'is_import' => 1
          			);

                //--- add order
                if( ! $this->sales_order_model->add($arr))
                {
                  $res = FALSE;
                  $message = "Failed to create order for orderNumber {$order->reference}";
                }

                if($res === TRUE)
                {
                  if( ! empty($order->items))
                  {
                    $line = 0;

                    foreach($order->items as $rs)
                    {
                      $arr = array(
          							'sales_order_code' => $order_code,
          							'LineNum' => $line,
          							'Type' => 0,
          							'ItemCode' => $rs->ItemCode,
          							'Dscription' => $rs->Dscription,
          							'ItemDetail' => get_null($rs->ItemDetail),
          							'FreeText' => NULL,
          							'Qty' => $rs->Qty,
          							'UomCode' => get_null($rs->UomCode),
          							'lastSellPrice' => $rs->lastSellPrice,
          							'basePrice' => $rs->basePrice,
          							'stdPrice' => $rs->stdPrice,
          							'Price' => $rs->Price,
          							'priceDiffPercent' => round($rs->priceDiffPercent, 2),
          							'SellPrice' => $rs->SellPrice,
          							'U_DISWEB' => 0,
          							'U_DISCEX' => 0,
          							'DiscPrcnt' => 0,
          							'VatGroup' => $rs->VatGroup,
          							'VatRate' => $rs->VatRate,
          							'LineTotal' => round($rs->LineTotalBefTax, 2),
          							'WhsCode' => empty($rs->WhsCode) ? $DefaultWarehouse : $rs->WhsCode
          						);

                      if( ! $this->sales_order_model->add_detail($arr))
                      {
                        $res = FALSE;
                        $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                      }
                      else
                      {
                        $TotalAmount += $rs->LineTotalBefTax;
                        $DocTotal += round($rs->LineTotalAfTax, 2);
                        $TaxAmount += $rs->VatRate > 0 ? $rs->LineTotalBefTax : 0;
                      }

                      $line++;

                      if($res == FALSE)
                      {
                        break;
                      }
                    } //--- end foreach

                    //---- if has shipping fee  add shipping sku to order
                    if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                    {
                      $taxCode = empty($order->CustomerTaxCode) ? $shipping_item->taxCode : $order->CustomerTaxCode;
                      $taxRate = empty($order->CustomerTaxRate) ? $shipping_item->taxRate : $order->CustomerTaxRate;
                      $price = $order->shipping_fee;
                      $priceBefTax = $taxRate > 0 ? remove_vat($price, $taxRate) : $price;

                      $arr = array(
          							'sales_order_code' => $order_code,
          							'LineNum' => $line,
          							'Type' => 0,
          							'ItemCode' => $shipping_item->code,
          							'Dscription' => $shipping_item->name,
          							'ItemDetail' => NULL,
          							'FreeText' => NULL,
          							'Qty' => 1,
          							'UomCode' => get_null($shipping_item->UomCode),
          							'lastSellPrice' => 0,
          							'basePrice' => $priceBefTax,
          							'stdPrice' => $priceBefTax,
          							'Price' => $priceBefTax,
          							'priceDiffPercent' => 0,
          							'SellPrice' => $priceBefTax,
          							'U_DISWEB' => 0,
          							'U_DISCEX' => 0,
          							'DiscPrcnt' => 0,
          							'VatGroup' => $taxCode,
          							'VatRate' => $taxRate,
          							'LineTotal' => $priceBefTax,
          							'WhsCode' => empty($shipping_item->dfWhsCode) ? $DefaultWarehouse : $shipping_item->dfWhsCode
          						);

                      if( ! $this->sales_order_model->add_detail($arr))
                      {
                        $res = FALSE;
                        $message = "Failed to insert shipping item row of {$order->reference}";
                      }
                      else
                      {
                        $TotalAmount += $priceBefTax;
                        $DocTotal += $price;
                        $TaxAmount += $taxRate > 0 ? $priceBefTax : 0;
                      }

                      $line++;
                    } //--- end if($order->shipping_fee)
                  } //--- end if ! empty($order->items)
                } //--- $sc === TRUE

                //-- add state
                if($res === TRUE)
                {
                  $billDiscAmount = $order->billDiscAmount > 0 ? $order->billDiscAmount : 0;
                  $DiscPrcnt = $TotalAmount > 0 ? round(($billDiscAmount / $TotalAmount) * 100, 2) : 0;
                  $amountAfterDisc = $TotalAmount - $billDiscAmount;
                  $totalDiscTax = $TaxAmount * $DiscPrcnt; //--- ส่วนลดที่คำนวนจากยอดที่ต้องคิด VAT เท่านั้น
                  $amountToPayTax = $TaxAmount - $totalDiscTax;
                  $VatSum = round($amountToPayTax * $VatRate, 2);
                  $DocTotal = round($amountAfterDisc + $VatSum, 2);

                  $arr = array(
                    'DocTotal' => $DocTotal,
                    'VatSum' => $TaxAmount * $VatRate,
                    'DiscPrcnt' => $DiscPrcnt
                  );

                  $this->sales_order_model->update($order_code, $arr);
                }

                if($res === TRUE)
                {
                  $this->db->trans_commit();
                  $success++;
                }
                else
                {
                  $this->db->trans_rollback();
                  $failed++;
                }

                //--- add logs
                $logs = array(
                  'reference' => $order->reference,
                  'order_code' => $order_code,
                  'action' => 'A', //-- A = add , U = update
                  'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                  'message' => $res === TRUE ? NULL : $message,
                  'user' => $this->user->uname
                );

                $this->order_import_logs_model->add($logs);

                if($res === TRUE)
                {
                  $this->export->export_order($order_code);
                }
              }
              else
              {
                if($order->force_update)
                {
                  $doc = $this->sales_order_model->get($order_code);

                  if( ! empty($doc) && ($doc->Status != 1 OR $doc->Status != 2 ))
                  {
                    $this->db->trans_begin();

                    $arr = array(
                      'reference' => $order->reference,
              				'CardCode' => $order->CardCode,
              				'CardName' => $order->CardName,
              				'SlpCode' => $order->SlpCode,
              				'GroupNum' => $order->GroupNum,
              				'Term' => $order->Term,
              				'CntctCode' => NULL,
              				'NumAtCard' => $order->reference,
              				'DocCur' => NULL,
              				'DocRate' => 1,
              				'DocTotal' => 0,
              				'DiscPrcnt' => 0,
              				'RoundDif' => 0,
              				'VatSum' => 0,
              				'OcrCode' => 'SAL',
              				'OcrCode1' => 'SAL-01',
              				'PayToCode' => $order->PayToCode,
              				'ShipToCode' => $order->ShipToCode,
              				'Address' => $order->Address,
              				'Address2' => $order->Address2,
              				'Series' => $order->Series,
              				'BeginStr' => $order->BeginStr,
              				'Status' => 0,
              				'DocDate' => $order->DocDate,
              				'DocDueDate' => $order->DocDueDate,
              				'TextDate' => $order->TextDate,
              				'OwnerCode' => $order->OwnerCode,
              				'Comments' => $order->Comments,
              				'U_DO_IV_Print' => get_null($order->U_DO_IV_Print),
              				'U_Delivery_Urgency' => get_null($order->U_Delivery_Urgency),
              				'user_id' => $order->user_id,
              				'uname' => $order->uname,
              				'sale_team' => $order->sale_team,
                      'Approved' => 'S',
                      'is_import' => 1
              			);

                    if( ! $this->sales_order_model->update($order_code, $arr))
                    {
                      $res = FALSE;
                      $message = "Failed to update order {$order_code} for {$order->reference}";
                    }

                    if($res === TRUE)
                    {
                      //---- drop previous order rows
                      if( ! $this->sales_order_model->drop_details($order_code))
                      {
                        $res = FALSE;
                        $message = "Failed to remove previous order rows";
                      }
                      else
                      {
                        if( ! empty($order->items))
                        {
                          $line = 0;

                          foreach($order->items as $rs)
                          {
                            $arr = array(
                							'sales_order_code' => $order_code,
                							'LineNum' => $line,
                							'Type' => 0,
                							'ItemCode' => $rs->ItemCode,
                							'Dscription' => $rs->Dscription,
                							'ItemDetail' => get_null($rs->ItemDetail),
                							'FreeText' => NULL,
                							'Qty' => $rs->Qty,
                							'UomCode' => get_null($rs->UomCode),
                							'lastSellPrice' => $rs->lastSellPrice,
                							'basePrice' => $rs->basePrice,
                							'stdPrice' => $rs->stdPrice,
                							'Price' => $rs->Price,
                							'priceDiffPercent' => round($rs->priceDiffPercent, 2),
                							'SellPrice' => $rs->SellPrice,
                							'U_DISWEB' => 0,
                							'U_DISCEX' => 0,
                							'DiscPrcnt' => 0,
                							'VatGroup' => $rs->VatGroup,
                							'VatRate' => $rs->VatRate,
                							'LineTotal' => round($rs->LineTotalBefTax, 2),
                							'WhsCode' => empty($rs->WhsCode) ? $DefaultWarehouse : $rs->WhsCode
                						);

                            if( ! $this->sales_order_model->add_detail($arr))
                            {
                              $res = FALSE;
                              $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                            }
                            else
                            {
                              $TotalAmount += $rs->LineTotalBefTax;
                              $DocTotal += round($rs->LineTotalAfTax, 2);
                              $TaxAmount += $rs->VatRate > 0 ? $rs->LineTotalBefTax : 0;
                            }

                            $line++;

                            if($res == FALSE)
                            {
                              break;
                            }

                          } //--- end foreach

                          //---- if has shipping fee  add shipping sku to order
                          if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                          {
                            $taxCode = empty($order->CustomerTaxCode) ? $shipping_item->taxCode : $order->CustomerTaxCode;
                            $taxRate = empty($order->CustomerTaxRate) ? $shipping_item->taxRate : $order->CustomerTaxRate;
                            $price = $order->shipping_fee;
                            $priceBefTax = $taxRate > 0 ? remove_vat($price, $taxRate) : $price;

                            $arr = array(
                							'sales_order_code' => $order_code,
                							'LineNum' => $line,
                							'Type' => 0,
                							'ItemCode' => $shipping_item->code,
                							'Dscription' => $shipping_item->name,
                							'ItemDetail' => NULL,
                							'FreeText' => NULL,
                							'Qty' => 1,
                							'UomCode' => get_null($shipping_item->UomCode),
                							'lastSellPrice' => 0,
                							'basePrice' => $priceBefTax,
                							'stdPrice' => $priceBefTax,
                							'Price' => $priceBefTax,
                							'priceDiffPercent' => 0,
                							'SellPrice' => $priceBefTax,
                							'U_DISWEB' => 0,
                							'U_DISCEX' => 0,
                							'DiscPrcnt' => 0,
                							'VatGroup' => $taxCode,
                							'VatRate' => $taxRate,
                							'LineTotal' => $priceBefTax,
                							'WhsCode' => empty($shipping_item->dfWhsCode) ? $DefaultWarehouse : $shipping_item->dfWhsCode
                						);

                            if( ! $this->sales_order_model->add_detail($arr))
                            {
                              $res = FALSE;
                              $message = "Failed to insert shipping item row of {$order->reference}";
                            }
                            else
                            {
                              $TotalAmount += $priceBefTax;
                              $DocTotal += $price;
                              $TaxAmount += $taxRate > 0 ? $priceBefTax : 0;
                            }

                            $line++;
                          } //--- end if($order->shipping_fee)
                        } //--- end if ! empty($order->items)
                      }
                    }

                    //-- add state
                    if($res === TRUE)
                    {
                      $billDiscAmount = $order->billDiscAmount > 0 ? $order->billDiscAmount : 0;
                      $DiscPrcnt = $TotalAmount > 0 ? round(($billDiscAmount / $TotalAmount) * 100, 2) : 0;
                      $amountAfterDisc = $TotalAmount - $billDiscAmount;
                      $totalDiscTax = $TaxAmount * $DiscPrcnt; //--- ส่วนลดที่คำนวนจากยอดที่ต้องคิด VAT เท่านั้น
                      $amountToPayTax = $TaxAmount - $totalDiscTax;
                      $VatSum = round($amountToPayTax * $VatRate, 2);
                      $DocTotal = round($amountAfterDisc + $VatSum, 2);

                      $arr = array(
                        'DocTotal' => $DocTotal,
                        'VatSum' => $TaxAmount * $VatRate,
                        'DiscPrcnt' => $DiscPrcnt
                      );

                      $this->sales_order_model->update($order_code, $arr);
                    }

                    if($res === TRUE)
                    {
                      $this->db->trans_commit();
                      $success++;
                    }
                    else
                    {
                      $this->db->trans_rollback();
                      $failed++;
                    }

                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                      'message' => $res === TRUE ? NULL : $message,
                      'user' => $this->user->uname
                    );

                    $this->order_import_logs_model->add($logs);

                    if($res === TRUE)
                    {
                      $this->export->export_order($order_code);
                    }
                  }
                  else
                  {
                    $failed++;
                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => 'E', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                      'message' => "Invalid order status",
                      'user' => $this->user->uname
                    );

                    $this->order_import_logs_model->add($logs);
                  }
                }
                else
                {
                  $skip++;
                  //--- add logs
                  $logs = array(
                    'reference' => $order->reference,
                    'order_code' => $order_code,
                    'action' => 'A', //-- A = add , U = update
                    'status' => 'D', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                    'message' => "{$order->reference} already exists",
                    'user' => $this->user->uname
                  );

                  $this->order_import_logs_model->add($logs);
                }
              } //--- end if order exists
            } //--- end foreach
          } //--- end if ! empty ds
          else
          {
            $sc = FALSE;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Cannot get data from import file : empty data collection";
      }
    } //-- end upload success

    $message = "Imported : {$import} <br/> Success : {$success} <br/> Failed : {$failed} <br/> Skip : {$skip}";
    $message .= $failed > 0 ? "<br/><br/> พบรายการที่ไม่สำเร็จ กรุณาตรวจสอบ Import logs" : "";

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? $message : $this->error
    );

    echo json_encode($arr);
  }


  private function parse_order_data($sheet)
  {
    $sc = TRUE;

    if( ! empty($sheet))
    {
      $rows = $sheet->getHighestRow();
      $prefix = getConfig('DEFAULT_SALES_ORDER_SERIES');
      $month = date('Y-m');
      $series = $this->sales_order_model->get_series_code($prefix, $month);

      if(empty($series))
      {
        $this->error = "Document Series is not defined";
        return FALSE;
      }

      $customerTax = NULL;
      $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');
      $shipping_item = ! empty($shipping_item_code) ? $this->item_model->get($shipping_item_code) : NULL;

      if( empty($shipping_item))
      {
        $this->error = "Shipping item code is not defined";
        return FALSE;
      }

      $ds = array(); //---- ได้เก็บข้อมูล orders

      //--- เก็บ customer cache
      $customerCache = array();

      $itemsCache = array(); //--- เก็บ item cache

      $headCol = array(
        'A' => 'Order No',
        'B' => 'Date',
        'C' => 'Customer Code',
        'D' => 'Item Code',
        'E' => 'Item Name',
        'F' => 'Qty',
        'G' => 'Uom',
        'H' => 'Sell Price',
        'I' => 'Bill Discount',
        'J' => 'Shipping Fee',
        'K' => 'Remark',
        'L' => 'Force Update'
      );

      $i = 1;
      //---- รวมข้อมูลให้เป็น array ก่อนนำไปใช้สร้างออเดอร์
      while($i <= $rows)
      {
        if($sc === FALSE)
        {
          break;
        }

        if($i == 1)
        {
          foreach($headCol as $col => $field)
          {
            $value = $sheet->getCell($col.$i)->getValue();

            if(empty($value) OR $value !== $field)
            {
              $sc = FALSE;
              $this->error .= 'Column '.$col.' Should be '.$field.'<br/>';
            }
          }

          if($sc === FALSE)
          {
            $this->error .= "<br/><br/>You should download new template !";
            break;
          }

          $i++;
        }
        else
        {
          $rs = [];

          foreach($headCol as $col => $field)
          {
            $column = $col.$i;

            $rs[$col] = $sheet->getCell($column)->getValue();
          }

          if($sc === TRUE && ! empty($rs['A']) && ! empty($rs['C']) && ! empty($rs['D']))
          {
            //--- ใช้ orderNumber เป็น key array
            $ref_code = trim($rs['A']);

            //--- เช็คว่ามี key อยู่แล้วหรือไม่
            //--- ถ้ายังไม่มีให้สร้างใหม่ ถ้ามีแล้ว ให้เพิ่ม รายการสินค้าเข้าไป
            if( ! isset($ds[$ref_code]))
            {
              $cell = $sheet->getCell("B{$i}");
              $date = trim($cell->getValue());

              if (PHPExcel_Shared_Date::isDateTime($cell))
              {
                $dateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($date);
                $date_add = $dateTimeObject->format('Y-m-d');
              }
              else
              {
                $date_add = db_date($date);
              }

              //--- check date format only check not convert
              if( ! is_valid_date($date_add))
              {
                $sc = FALSE;
                $this->error = "Invalid Date format at Line {$i} : {$date}";
              }

              //---- check customers
              $customer_code = trim($rs['C']);

              if( ! empty($customer_code))
              {
                //--- check customer Cache
                if( ! isset($customerCache[$customer_code]))
                {
                  $customer = $this->customers_model->get($customer_code);

                  if( ! empty($customer))
                  {
                    $billTo = $this->customers_model->get_address_bill_to($customer_code);
                    $shipTo = $this->customers_model->get_address_ship_to($customer_code);

                    $customer->BillToCode = empty($billTo) ? NULL : $billTo->Address;
                    $customer->Address = empty($billTo) ? NULL : parse_address($billTo);
                    $customer->ShipToCode = empty($shipTo) ? NULL : $shipTo->Address;
                    $customer->Address2 = empty($shipTo) ? NULL : parse_address($shipTo);
                    $customer->customerTax = $this->customers_model->get_tax($customer_code);

                    $customerCache[$customer_code] = $customer;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid customer at Line {$i}";
                  }
                }

                $customer = $customerCache[$customer_code];
                $customerTax = $customer->customerTax;
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Customer Code is required at Line {$i} <br/>";
              }

              //--- check item cache
              $item_code = trim($rs['D']);
              $item_name = trim($rs['E']);

              if( ! empty($item_code))
              {
                if( ! isset($itemsCache[$item_code]))
                {
                  $item = $this->item_model->get($item_code);

                  if( ! empty($item))
                  {
                    if( ! empty($item_name))
                    {
                      $item->name = $item_name;
                    }

                    $item->taxCode = empty($customerTax) ? $item->taxCode : $customerTax->taxCode;
                    $item->taxRate = empty($customerTax) ? $item->taxRate : $customerTax->taxRate;

                    $itemsCache[$item->code] = $item;
                    $item_code = $item->code;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid Item code '{$item_code}' at Line {$i} <br/>";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Item Code code is required at Line {$i} <br/>";
              }

              $item = empty($itemsCache[$item_code]) ? NULL : $itemsCache[$item_code];

              if($sc === TRUE)
              {
                $shipping_fee = empty($rs['J']) ? 0.00 : trim($rs['J']);
                $shipping_fee = is_numeric($shipping_fee) ? $shipping_fee : 0.00;

                $billDiscount = empty($rs['I']) ? 0.00 : str_replace(",", "", $rs['I']);
                $billDiscount = is_numeric($billDiscount) ? $billDiscount : 0;

                //-- remark
                $remark = get_null(trim($rs['K']));

                $qty = empty(trim($rs['F'])) ? 1 : str_replace(',', '', $rs['F']);
                $qty = is_numeric($qty) ? $qty : 1;

                $uomEntry = $item->SUoMEntry;
                $uomCode = $this->item_model->get_uom_code($item->SUoMEntry);

                if( ! empty(trim($rs['G'])))
                {
                  $uom = $this->item_model->get_uom_by_item_code_and_uom_name($item_code, trim($rs['G']));
                  $uomEntry = empty($uom) ? $uomEntry : $uom->UomEntry;
                  $uomCode = empty($uom) ? $uomCode : $uom->UomCode;
                }

                $price = empty($rs['H']) ? 0.00 : str_replace(",", "", $rs['H']);
                $price = is_numeric($price) ? $price : 0;
                $priceBefTax = $item->taxRate > 0 ? remove_vat($price, $item->taxRate) : $price;

                $basePrice = $item->price;
                $priceDiff = $basePrice - $priceBefTax;
                $priceDiffPercent = 0; //--- price diffrence in percentage

                if($priceDiff != 0 && $basePrice != 0 && $priceBefTax != 0)
                {
                  $priceDiffPercent = ($priceDiff / $basePrice) * 100;
                }

                $last_sell_price = $this->item_model->last_sell_price($item_code, $customer_code, $uomEntry);

                //--- total_amount
                $LineTotalBefTax = $priceBefTax * $qty;
                $LineTotalAfTax = $price * $qty;

                $ds[$ref_code] = (object) array(
                'reference' => $ref_code,
                'CardCode' => $customer->CardCode,
                'CardName' => $customer->CardName,
                'CustomerTaxCode' => empty($customerTax) ? NULL : $customerTax->taxCode,
                'CustomerTaxRate' => empty($customerTax) ? NULL : $customerTax->taxRate,
                'SlpCode' => $this->user->sale_id,
                'GroupNum' => $customer->GroupNum,
                'Term' => $customer->TermName,
                'PayToCode' => $customer->BillToCode,
                'ShipToCode' => $customer->ShipToCode,
                'Address' => $customer->Address,
                'Address2' => $customer->Address2,
                'Series' => $series,
                'BeginStr' => $prefix,
                'DocDate' => sap_date($date_add, TRUE),
                'DocDueDate' => sap_date($date_add, TRUE),
                'TextDate' => sap_date($date_add, TRUE),
                'OwnerCode' => $this->user->emp_id,
                'Comments' => $remark,
                'U_DO_IV_Print' => 'เปิดบิล IV',
                'U_Delivery_Urgency' => 'ส่งทันทีเมื่อพร้อม',
                'user_id' => $this->user->id,
                'uname' => $this->user->uname,
                'sale_team' => $this->user->sale_team,
                'shipping_fee' => $shipping_fee,
                'billDiscAmount' => $billDiscount,
                'force_update' => (trim($rs['L']) == 1 OR trim($rs['L']) == 'Y' OR trim($rs['L']) == 'y') ? TRUE : FALSE,
                'items' => []
                );

                $row = (object) array(
                'ItemCode' => $item->code,
                'Dscription' => $item->name,
                'ItemDetail' => $item->detail,
                'Qty' => $qty,
                'UomCode' => get_null($uomCode),
                'lastSellPrice' => $last_sell_price,
                'basePrice' => $item->price,
                'stdPrice' => $item->price,
                'Price' => $priceBefTax,
                'priceDiffPercent' => $priceDiffPercent,
                'SellPrice' => $priceBefTax,
                'VatGroup' => $item->taxCode,
                'VatRate' => $item->taxRate,
                'LineTotalBefTax' => $LineTotalBefTax,
                'LineTotalAfTax' => $LineTotalAfTax,
                'WhsCode' => get_null($item->dfWhsCode)
                );

                $ds[$ref_code]->items[$item->code] = $row;
              }
            }
            else
            {
              //--- check item cache
              $item_code = trim($rs['D']);
              $item_name = trim($rs['E']);

              if( ! empty($item_code))
              {
                if( ! isset($itemsCache[$item_code]))
                {
                  $item = $this->item_model->get($item_code);

                  if( ! empty($item))
                  {
                    if( ! empty($item_name))
                    {
                      $item->name = $item_name;
                    }

                    $item->taxCode = empty($customerTax) ? $item->taxCode : $customerTax->taxCode;
                    $item->taxRate = empty($customerTax) ? $item->taxRate : $customerTax->taxRate;

                    $itemsCache[$item->code] = $item;
                    $item_code = $item->code;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid Item code '{$item_code}' at Line {$i} <br/>";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Item Code code is required at Line {$i} <br/>";
              }

              if($sc === TRUE)
              {
                $item = $itemsCache[$item_code];

                $qty = empty(trim($rs['F'])) ? 1 : str_replace(',', '', $rs['F']);
                $qty = is_numeric($qty) ? $qty : 1;

                $uomEntry = $item->SUoMEntry;
                $uomCode = $this->item_model->get_uom_code($item->SUoMEntry);

                if( ! empty(trim($rs['G'])))
                {
                  $uom = $this->item_model->get_uom_by_item_code_and_uom_name($item_code, trim($rs['G']));
                  $uomEntry = empty($uom) ? $uomEntry : $uom->UomEntry;
                  $uomCode = empty($uom) ? $uomCode : $uom->UomCode;
                }

                $price = empty($rs['H']) ? 0.00 : str_replace(",", "", $rs['H']);
                $price = is_numeric($price) ? $price : 0;
                $priceBefTax = $item->taxRate > 0 ? remove_vat($price, $item->taxRate) : $price;

                $basePrice = $item->price;
                $priceDiff = $basePrice - $priceBefTax;
                $priceDiffPercent = 0; //--- price diffrence in percentage

                if($priceDiff != 0 && $basePrice != 0 && $priceBefTax != 0)
                {
                  $priceDiffPercent = ($priceDiff / $basePrice) * 100;
                }

                $last_sell_price = $this->item_model->last_sell_price($item_code, $customer_code, $uomEntry);

                //--- total_amount
                $LineTotalBefTax = $priceBefTax * $qty;
                $LineTotalAfTax = $price * $qty;

                $isUpdate = FALSE;

                if(isset($ds[$ref_code]->items[$item->code]))
                {
                  $row = $ds[$ref_code]->items[$item->code];

                  if($row->UomCode == $uomCode && $row->Price == $priceBefTax)
                  {
                    $newQty = $row->Qty + $qty;
                    $TotalBefTax = $row->LineTotalBefTax + $LineTotalBefTax;
                    $TotalAfTax = $row->LineTotalAfTax + $LineTotalAfTax;

                    $ds[$ref_code]->items[$item->code]->Qty = $newQty;
                    $ds[$ref_code]->items[$item->code]->LineTotalBefTax = $TotalBefTax;
                    $ds[$ref_code]->items[$item->code]->LineTotalAfTax = $TotalAfTax;

                    $isUpdate = TRUE;
                  }
                }

                if( ! $isUpdate)
                {
                  $row = (object) array(
                  'ItemCode' => $item->code,
                  'Dscription' => $item->name,
                  'ItemDetail' => $item->detail,
                  'Qty' => $qty,
                  'UomCode' => get_null($uomCode),
                  'lastSellPrice' => $last_sell_price,
                  'basePrice' => $item->price,
                  'stdPrice' => $item->price,
                  'Price' => $priceBefTax,
                  'priceDiffPercent' => $priceDiffPercent,
                  'SellPrice' => $priceBefTax,
                  'VatGroup' => $item->taxCode,
                  'VatRate' => $item->taxRate,
                  'LineTotalBefTax' => $LineTotalBefTax,
                  'LineTotalAfTax' => $LineTotalAfTax,
                  'WhsCode' => get_null($item->dfWhsCode)
                  );

                  $ds[$ref_code]->items[$item->code] = $row;
                }
              }
            } //--- end if( ! isset($ds[$ref_code]));
          } //--- end i

          $i++;
        } //--- end if $i == 1
      } //---- end foreach collection
    }
    else
    {
      $sc = FALSE;
      $this->error = "Empty data collection";
    }

    return $sc === TRUE ? $ds : FALSE;
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
}

 ?>

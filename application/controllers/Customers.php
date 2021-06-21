<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends PS_Controller
{
  public $menu_code = 'CUSTOMER';
	public $menu_group_code = 'BP';
	public $title = 'Business Partner Master';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'customers';
    $this->load->model('customers_model');
    $this->load->helper('customer');
    $this->load->helper('currency');
  }


  public function index()
  {
		$filter = array(
      'code' => get_filter('code', 'cust_code', ''),
      'LeadCode' => get_filter('LeadCode', 'cust_LeadCode', ''),
      'CardName' => get_filter('CardName', 'cust_CardName', ''),
      'Status' => get_filter('Status', 'cust_Status', 'all'),
      'order_by' => get_filter('order_by', 'cust_order_by', 'code'),
			'sort_by' => get_filter('sort_by', 'cust_sort_by', 'DESC')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 3; //-- url segment
		$rows = $this->customers_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$customers = $this->customers_model->get_list($filter, $perpage, $this->uri->segment($segment));


    $filter['data'] = $customers;

		$this->pagination->initialize($init);
    $this->load->view('customer/customer_list', $filter);
  }


  public function add_new()
  {
    $this->title = "Add New Bussiness Partner";
    $tax = $this->customers_model->get_tax_group(getConfig('SALE_VAT_CODE'));
    $ds = array(
      'tax' => $tax,
      'properties' => $this->customers_model->get_properties_list()
    );

    $this->load->view('customer/customer_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;

    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data))
    {
      //--- customer table
      $code = $this->get_new_code();

      $cust = array();

      $cust['code'] = $code;

      if(!empty($data->customer))
      {
        foreach($data->customer as $key => $val)
        {
          $cust[$key] = get_null($val);
        }

        $cust['validFrom'] = empty(get_null($data->customer->validFrom)) ? NULL : db_date($data->customer->validFrom);
        $cust['validTo'] = empty(get_null($data->customer->validTo)) ? NULL : db_date($data->customer->validTo);
        $cust['frozenFrom'] = empty(get_null($data->customer->frozenFrom)) ? NULL : db_date($data->customer->frozenFrom);
        $cust['frozenTo'] = empty(get_null($data->customer->frozenTo)) ? NULL : db_date($data->customer->frozenTo);

        $cust['uname'] = $this->user->uname;
        $cust['sale_team'] = $this->user->sale_team;
        $cust['user_id'] = $this->user->id;
      }


      if(!empty($data->props))
      {
        //--- QryGroup1 - QryGroup64
        foreach($data->props as $rs)
        {
          $cust[$rs->name] = $rs->value;
        }
      }


      //--- insert Customer data table customer
      if($this->customers_model->add($cust))
      {
        //--- contact_person
        if(!empty($data->contactPerson))
        {
          foreach($data->contactPerson as $rs)
          {
            $contactPerson = array(
              'CardCode' => $data->customer->LeadCode,
              'Name' => trim($rs->Name),
              'FirstName' => get_null(trim($rs->FirstName)),
              'MiddleName' => get_null(trim($rs->MiddleName)),
              'LastName' => get_null(trim($rs->LastName)),
              'Title' => get_null(trim($rs->Title)),
              'Position' => get_null(trim($rs->Position)),
              'Address' => get_null(trim($rs->Address)),
              'Tel1' => get_null(trim($rs->Tel1)),
              'Tel2' => get_null(trim($rs->Tel2)),
              'Cellolar' => get_null(trim($rs->Cellolar)),
              'Fax' => get_null(trim($rs->Fax)),
              'E_MailL' => get_null(trim($rs->E_MailL)),
              'Notes1' => get_null(trim($rs->Notes1)),
              'Notes2' => get_null(trim($rs->Notes2)),
              'BirthDate' => empty($rs->BirthDate) ? NULL : sap_date($rs->BirthDate)
            );

            $this->customers_model->add_contact($contact);
          }
        }

        //---- Bill to
        if(!empty($data->billTo))
        {
          foreach($data->billTo as $rs)
          {
            $billTo = array(
                'CardCode' => $data->customer->LeadCode,
                'Address' => get_null(trim($rs->Address)),
                'Address2' => get_null(trim($rs->Address2)),
                'Address3' => get_null(trim($rs->Address3)),
                'Street' => get_null(trim($rs->Street)),
                'StreetNo' => get_null(trim($rs->StreetNo)),
                'Block' => get_null(trim($rs->Block)),
                'County' => get_null(trim($rs->County)),
                'City' => get_null(trim($rs->City)),
                'ZipCode' => get_null(trim($rs->ZipCode)),
                'Country' => get_null(trim($rs->Country)),
                'AdresType' => 'B'
            );

            $this->customers_model->add_address($billTo);
          }
        }

        //--- Ship To
        if(!empty($data->shipTo))
        {
          foreach($data->shipTo as $rs)
          {
            $shipTo = array(
                'CardCode' => $data->customer->LeadCode,
                'Address' => get_null(trim($rs->Address)),
                'Address2' => get_null(trim($rs->Address2)),
                'Address3' => get_null(trim($rs->Address3)),
                'Street' => get_null(trim($rs->Street)),
                'StreetNo' => get_null(trim($rs->StreetNo)),
                'Block' => get_null(trim($rs->Block)),
                'County' => get_null(trim($rs->County)),
                'City' => get_null(trim($rs->City)),
                'ZipCode' => get_null(trim($rs->ZipCode)),
                'Country' => get_null(trim($rs->Country)),
                'AdresType' => 'S'
            );

            $this->customers_model->add_address($shipTo);
          }
        }


        if($sc === TRUE)
        {
          $this->doExport($code);
        }
      }
      else
      {
        $sc = FALSE;
        $error = $this->db->error();
        $this->error = $error['message'];
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "No data found";
    }

    $this->response($sc);
  }



  public function edit($code)
  {
    $this->title = "Edit Bussiness Partner";
    $customer = $this->customers_model->get($code);

    if(!empty($customer))
    {
      $tax = $this->customers_model->get_tax_group(getConfig('SALE_VAT_CODE'));
      $contact = $this->customers_model->get_contact($customer->LeadCode);
      $billTo = $this->customers_model->get_bill_to($customer->LeadCode);
      $shipTo = $this->customers_model->get_ship_to($customer->LeadCode);
      $customer->project_name = $this->customers_model->get_project_name($customer->ProjectCod);
      $exists = $this->customers_model->is_sap_exists_code($code);


      $ds = array(
        'data' => $customer,
        'contact' => $contact,
        'billTo' => $billTo,
        'shipTo' => $shipTo,
        'properties' => $this->customers_model->get_properties_list(),
        'is_exists' => $exists
      );

      $this->load->view('customer/customer_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }

  }



  public function update($code)
  {
    $sc = TRUE;

    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data))
    {
      $customer = $this->customers_model->get($code);

      if(!empty($customer))
      {
        if($customer->Status != 2)  //----- 2 = Already In SAP
        {
          //--- ตรวจสอบใน SAP ว่ามีอยู่จริงๆหรือเปล่า กันพลาด
          if(! $this->customers_model->is_sap_exists_code($code))
          {

            if(!empty($data->customer))
            {

              $LeadCode = $data->customer->LeadCode; //--- new lead code

              //--- drop temp
              if($this->customers_model->drop_temp_exists_data($code))
              {
                $cust = array();

                foreach($data->customer as $key => $val)
                {
                  $cust[$key] = get_null($val);
                }

                $cust['validFrom'] = empty(get_null($data->customer->validFrom)) ? NULL : db_date($data->customer->validFrom);
                $cust['validTo'] = empty(get_null($data->customer->validTo)) ? NULL : db_date($data->customer->validTo);
                $cust['frozenFrom'] = empty(get_null($data->customer->frozenFrom)) ? NULL : db_date($data->customer->frozenFrom);
                $cust['frozenTo'] = empty(get_null($data->customer->frozenTo)) ? NULL : db_date($data->customer->frozenTo);
                $cust['uname'] = $this->user->uname;
                $cust['sale_team'] = $this->user->sale_team;
                $cust['user_id'] = $this->user->id;

                if(!empty($data->props))
                {
                  //--- QryGroup1 - QryGroup64
                  foreach($data->props as $rs)
                  {
                    $cust[$rs->name] = $rs->value;
                  }
                }


                //--- insert Customer data table customer
                if($this->customers_model->update($code, $cust))
                {
                  //--- Drop current contact person
                  $this->customers_model->drop_contact_person($customer->LeadCode);
                  //--- Drop current billTo and ShipTo address
                  $this->customers_model->drop_address($customer->LeadCode);


                  //--- insert new contact person
                  if(!empty($data->contactPerson))
                  {
                    foreach($data->contactPerson as $rs)
                    {
                      $contactPerson = array(
                        'CardCode' => $data->customer->LeadCode,
                        'Name' => trim($rs->Name),
                        'FirstName' => get_null(trim($rs->FirstName)),
                        'MiddleName' => get_null(trim($rs->MiddleName)),
                        'LastName' => get_null(trim($rs->LastName)),
                        'Title' => get_null(trim($rs->Title)),
                        'Position' => get_null(trim($rs->Position)),
                        'Address' => get_null(trim($rs->Address)),
                        'Tel1' => get_null(trim($rs->Tel1)),
                        'Tel2' => get_null(trim($rs->Tel2)),
                        'Cellolar' => get_null(trim($rs->Cellolar)),
                        'Fax' => get_null(trim($rs->Fax)),
                        'E_MailL' => get_null(trim($rs->E_MailL)),
                        'Notes1' => get_null(trim($rs->Notes1)),
                        'Notes2' => get_null(trim($rs->Notes2)),
                        'BirthDate' => empty($rs->BirthDate) ? NULL : sap_date($rs->BirthDate)
                      );

                      $this->customers_model->add_contact($contact);
                    }
                  }

                  //---- Bill to
                  if(!empty($data->billTo))
                  {
                    foreach($data->billTo as $rs)
                    {
                      $billTo = array(
                          'CardCode' => $data->customer->LeadCode,
                          'Address' => get_null(trim($rs->Address)),
                          'Address2' => get_null(trim($rs->Address2)),
                          'Address3' => get_null(trim($rs->Address3)),
                          'Street' => get_null(trim($rs->Street)),
                          'StreetNo' => get_null(trim($rs->StreetNo)),
                          'Block' => get_null(trim($rs->Block)),
                          'County' => get_null(trim($rs->County)),
                          'City' => get_null(trim($rs->City)),
                          'ZipCode' => get_null(trim($rs->ZipCode)),
                          'Country' => get_null(trim($rs->Country)),
                          'AdresType' => 'B'
                      );

                      $this->customers_model->add_address($billTo);
                    }
                  }

                  //--- Ship To
                  if(!empty($data->shipTo))
                  {
                    foreach($data->shipTo as $rs)
                    {
                      $shipTo = array(
                          'CardCode' => $data->customer->LeadCode,
                          'Address' => get_null(trim($rs->Address)),
                          'Address2' => get_null(trim($rs->Address2)),
                          'Address3' => get_null(trim($rs->Address3)),
                          'Street' => get_null(trim($rs->Street)),
                          'StreetNo' => get_null(trim($rs->StreetNo)),
                          'Block' => get_null(trim($rs->Block)),
                          'County' => get_null(trim($rs->County)),
                          'City' => get_null(trim($rs->City)),
                          'ZipCode' => get_null(trim($rs->ZipCode)),
                          'Country' => get_null(trim($rs->Country)),
                          'AdresType' => 'S'
                      );

                      $this->customers_model->add_address($shipTo);
                    }
                  }


                  if($sc === TRUE)
                  {
                    $this->doExport($code);
                  }
                }
                else
                {
                  $sc = FALSE;
                  $error = $this->db->error();
                  $this->error = $error['message']; //"Failed : Update Customer Failed";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Update Failed : Drop exists temp data failes";
              }

            }

          }
          else
          {
            $sc = FALSE;
            $this->error = "Cannot Update : BP Code '{$code}' Aready In SAP";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Cannot Update : BP Code '{$code}' Aready In SAP";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid BP Code : {$code}";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "No data found";
    }

    $this->response($sc);
  }




  public function get_empty_contact()
  {
    $contact = new stdClass();
    $arr = array(
      'CardCode',
      'Name',
      'FirstName',
      'MiddleName',
      'LastName',
      'Title',
      'Position',
      'Address',
      'Tel1',
      'Tel2',
      'Cellolar',
      'Fax',
      'E_MailL',
      'Notes1',
      'Notes2',
      'BirthDate'
    );

    foreach($arr as $rs)
    {
      $contact->$rs = NULL;
    }

    return $contact;
  }


  public function get_empty_address()
  {
    $addr = new stdClass();
    $arr = array(
      'CardCode' => NULL,
      'Address' => '00000',
      'Address2' => '00000',
      'Address3' => 'สำนักงานใหญ่',
      'Street' => NULL,
      'StreetNo' => NULL,
      'Block' => NULL,
      'County' => NULL,
      'City' => NULL,
      'Country' => NULL,
      'ZipCode' => NULL,
      'AdresType' => NULL
    );

    foreach($arr as $key => $val)
    {
      $addr->$key = $val;
    }

    return array($addr);
  }



  public function get_preview_data()
  {
    $code = $this->input->get('LeadCode');
    if(!empty($code))
    {
      $rs = $this->customers_model->get_preview_data($code);
      if(!empty($rs))
      {
        $arr = array(
          'U_WEBORDER' => $rs->code,
          'LeadCode' => $rs->LeadCode,
          'LeadName' => $rs->CardName,
          'Currency' => $rs->Currency,
          'LicTradNum' => $rs->LicTradNum,
          'OwnerName' => $rs->OwnerName,
          'Customer_level' => $rs->U_LEVEL,
          'Phone1' => $rs->Phone1,
          'Phone2' => $rs->Phone2
        );

        echo json_encode($arr);
      }
      else
      {
        echo "Invalid LeadCode";
      }
    }
    else
    {
      echo "Missing required parameter: LeadCode";
    }
  }


  public function do_export()
  {
    $sc = TRUE;
    $code = $this->input->post('web_code');
    if(!empty($code))
    {
      if(! $this->doExport($code) )
      {
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter : LeadCode";
    }

    $this->response($sc);
  }


  public function doExport($code)
  {
    $sc = TRUE;

    //--- check exists in SAP
    $exists = $this->customers_model->is_sap_exists_code($code); //-- web_code

    if(!$exists)
    {

      //--- customer data
      $rs = $this->customers_model->get($code); //--- web_code
      $web_order = $rs->code;
      $leadCode = $rs->LeadCode;

      //--- check temp
      $temp = $this->customers_model->is_temp_exists_data($rs->code); //-- web order

      if($temp)
      {
        //--- drop exists temp
        $this->customers_model->drop_temp_exists_data($rs->code); //--- web order
      }

      //--- export customer
      $arr = array(
        'CardCode' => $rs->LeadCode,
        'CardName' => $rs->CardName,
        'CardType' => $rs->CardType,
        'CardFName' => $rs->CardFName,
        'GroupCode' => $rs->GroupCode,
        'Currency' => $rs->Currency,
        'LicTradNum' => $rs->LicTradNum,
        'OwnerCode' => $rs->OwnerCode,
        'Phone1' => $rs->Phone1,
        'Phone2' => $rs->Phone2,
        'Cellular' => $rs->Cellular,
        'Fax' => $rs->Fax,
        'E_Mail' => $rs->E_Mail,
        'IntrntSite' => $rs->IntrntSite,
        'Indicator' => $rs->Indicator,
        'ProjectCod' => $rs->ProjectCod,
        'IndustryC' => $rs->IndustryC,
        'CmpPrivate' => $rs->CmpPrivate,
        'validFor' => $rs->validFor,
        'validFrom' => empty($rs->validFrom) ? NULL : sap_date($rs->validFrom),
        'validTo' => empty($rs->validTo) ? NULL : sap_date($rs->validTo),
        'frozenFor' => $rs->frozenFor,
        'frozenFrom' => empty($rs->frozenFrom) ? NULL : sap_date($rs->frozenFrom),
        'frozenTo' => empty($rs->frozenTo) ? NULL : sap_date($rs->frozenTo),
        'Notes' => $rs->Notes,
        'SlpCode' => $rs->SlpCode,
        'ChannlBP' => $rs->ChannlBP,
        'Territory' => $rs->Territory,
        'GroupNum' => $rs->GroupNum,
        'ListNum' => $rs->ListNum,
        'Discount' => $rs->Discount,
        'CreditLine' => $rs->CreditLine,
        'DebtLine' => $rs->DebtLine,
        'FatherCard' => $rs->FatherCard,
        'FatherType' => $rs->FatherType,
        'DebPayAcct' => $rs->DebPayAcct,
        'DpmClear' => $rs->DpmClear,
        'DpmIntAct' => $rs->DpmIntAct,
        'VatStatus' => $rs->VatStatus,
        'ECVatGroup' => $rs->ECVatGroup,
        'DeferrTax' => $rs->DeferrTax,
        'Free_Text' => $rs->FreeText,
        'U_BEX_TYPE' => $rs->U_BEX_TYPE,
        'U_LEVEL' => $rs->U_LEVEL,
        'U_CHECKDATE' => $rs->U_CHECKDATE,
        'U_BILLDATE' => $rs->U_BILLDATE,
        'U_COMMISSION' => $rs->U_COMMISSION,
        'U_BEX_CUST_LC' => $rs->U_BEX_CUST_LC,
        'U_BEX_CUST_LE2' => $rs->U_BEX_CUST_LE2,
        'U_BEX_CUST_3LT' => $rs->U_BEX_CUST_3LT,
        'U_BEX_CUST_RUNN' => $rs->U_BEX_CUST_RUNN,
        'U_WEBORDER' => $rs->code,
        'F_Web' => 'A',
        'F_WebDate' => sap_date(now(), TRUE)
      );

      $i = 1;
      while($i <= 64)
      {
        $name = "QryGroup{$i}";
        $arr[$name] = $rs->$name;
        $i++;
      }

      //--- insert To database OCRD
      if(! $this->customers_model->add_sap_customer($arr))
      {
        $sc = FALSE;
        $this->error = "Insert Lead Failed";
      }
      else
      {

        //--- contact person data
        $contact = $this->customers_model->get_contact($leadCode);

        //--- contact person OCPR
        if(!empty($contact))
        {
          foreach($contact as $ra)
          {
            $arr = array(
              'CardCode' => $ra->CardCode,
              'Name' => $ra->Name,
              'FirstName' => $ra->FirstName,
              'MiddleName' => $ra->MiddleName,
              'LastName' => $ra->LastName,
              'Title' => $ra->Title,
              'Position' => $ra->Position,
              'Address' => $ra->Address,
              'Tel1' => $ra->Tel1,
              'Tel2' => $ra->Tel2,
              'Cellolar' => $ra->Cellolar,
              'Fax' => $ra->Fax,
              'E_MailL' => $ra->E_MailL,
              'Notes1' => $ra->Notes1,
              'Notes2' => $ra->Notes2,
              'BirthDate' => empty($ra->BirthDate) ? NULL : sap_date($ra->BirthDate),
              'U_WEBORDER' => $web_order,
              'F_Web' => 'A',
              'F_WebDate' => sap_date(now(), TRUE)
            );

            if(! $this->customers_model->add_sap_contact_person($arr))
            {
              $sc = FALSE;
              $this->error = "Insert Contact Person Failed";
            }
          }
        }

        //--- billTo address CRD1
        //--- bill to data
        $billTo = $this->customers_model->get_bill_to($leadCode);

        if(!empty($billTo))
        {
          foreach($billTo as $rs)
          {
            $arr = array(
              'CardCode' => $rs->CardCode,
              'Address' => $rs->Address,
              'Address2' => $rs->Address2,
              'Address3' => $rs->Address3,
              'Street' => $rs->Street,
              'StreetNo' => $rs->StreetNo,
              'Block' => $rs->Block,
              'County' => $rs->County,
              'City' => $rs->City,
              'Country' => $rs->Country,
              'ZipCode' => $rs->ZipCode,
              'AdresType' => $rs->AdresType,
              'U_WEBORDER' => $web_order,
              'F_Web' => 'A',
              'F_WebDate' => sap_date(now(), TRUE)
            );

            if(! $this->customers_model->add_sap_address($arr))
            {
              $sc = FALSE;
              $this->error = "Insert BillTo Address Failed";
            }
          } //--- end foreach

        }


        //--- ShipTo Address CRD1
        //--- ship to data
        $shipTo = $this->customers_model->get_ship_to($leadCode);

        if(!empty($shipTo))
        {
          foreach($shipTo as $rs)
          {
            $arr = array(
              'CardCode' => $rs->CardCode,
              'Address' => $rs->Address,
              'Address2' => $rs->Address2,
              'Address3' => $rs->Address3,
              'Street' => $rs->Street,
              'StreetNo' => $rs->StreetNo,
              'Block' => $rs->Block,
              'County' => $rs->County,
              'City' => $rs->City,
              'Country' => $rs->Country,
              'ZipCode' => $rs->ZipCode,
              'AdresType' => $rs->AdresType,
              'U_WEBORDER' => $web_order,
              'F_Web' => 'A',
              'F_WebDate' => sap_date(now(), TRUE)
            );

            if(! $this->customers_model->add_sap_address($arr))
            {
              $sc = FALSE;
              $this->error = "Insert ShipTo Address Failed";
            }
          } //--- foreach
        }

        $arr = array(
          'Status' => 1,
          'temp_date' => now(),
          'sap_date' => NULL,
          'Message' => NULL
        );

        $this->customers_model->update($web_order, $arr);
      }

    } //--- not exists

    return $sc;
  }




  public function get_temp_data()
  {
    $code = $this->input->get('code'); //--- U_WEBORDER

    $data = $this->customers_model->get_temp_data($code);
    if(!empty($data))
    {
      $arr = array(
        'U_WEBORDER' => $data->U_WEBORDER,
        'LeadCode' => $data->CardCode,
        'LeadName' => $data->CardName,
        'F_WebDate' => thai_date($data->F_WebDate, TRUE),
        'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
        'F_Sap' => $data->F_Sap === 'Y' ? 'Success' : ($data->F_Sap === 'N' ? 'Failed' : 'Pending'),
        'Message' => empty($data->Message) ? '' : $data->Message,
        'del_btn' => $data->F_Sap === 'Y' ? '' : 'ok'
      );

      echo json_encode($arr);
    }
    else
    {
      echo 'No data found';
    }
  }

  public function is_exists_code()
  {
    $leadCode = $this->input->get('leadCode');
    $code = $this->input->get('code');

    if($this->customers_model->is_exists_lead_code($leadCode, $code))
    {
      echo 'duplicate';
    }
    else
    {
      echo 'OK';
    }
  }

  public function get_debPayAcct()
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    $qr  = "SELECT AcctCode AS code, AcctName AS name ";
    $qr .= "FROM OACT ";
    $qr .= "WHERE AcctCode LIKE N'%{$this->ms->escape_str($txt)}%' ";
    $qr .= "OR AcctName LIKE N'%{$this->ms->escape_str($txt)}%' ";
    $qr .= "ORDER BY AcctCode ASC ";
    $qr .= "OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";
    $qs  = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code .' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }



  public function remove_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('U_WEBORDER');
    $temp = $this->customers_model->get_temp_status($code);

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
      if(! $this->customers_model->drop_temp_exists_data($code))
      {
        $sc = FALSE;
        $this->error = "Delete Failed : Delete Temp Failed";
      }
      {
        $arr = array(
          'Status' => 0,
          'temp_date' => NULL,
          'sap_date' => NULL,
          'Message' => NULL
        );

        $this->customers_model->update($code, $arr);
      }
    }


    $this->response($sc);
  }



  public function delete()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      $cust = $this->customers_model->get($code);
      if(!empty($cust))
      {
        if($cust->Status != 2)
        {
          if(! $this->customers_model->is_sap_exists_code($code))
          {
            //--- remove temp data
            if(! $this->customers_model->drop_temp_exists_data($code))
            {
              $sc = FALSE;
              $this->error = "Failed : Delete Temp data not successful";
            }
            else
            {
              if(! $this->customers_model->delete($code, $cust->LeadCode))
              {
                $sc = FALSE;
                $this->error = "Failed : Delete customer data failed";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid BP Status : BP already in SAP";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid BP Status (2) : BP already in SAP";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid BP Code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter : code";
    }

    $this->response($sc);
  }

  public function get_running_number()
  {
    $pre = trim($this->input->get('prefix'));
    $run_digit = 4;
    $code = $this->customers_model->get_sap_max_code($pre);

    if(!empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $run_no = sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $run_no = '0001';
    }

    echo $run_no;
  }

  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('Y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_BP');
    $run_digit = getConfig('RUN_DIGIT_BP');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->customers_model->get_max_code($pre);

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
  //----- old code




  public function clear_filter()
	{
    $filter = array( 'cust_code', 'cust_LeadCode', 'cust_CardName','cust_Status','cust_sort_by','cust_order_by');
    clear_filter($filter);
	}
}

?>

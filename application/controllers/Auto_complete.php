<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_complete extends PS_Controller
{
  //public $ms;
  public function __construct()
  {
    parent::__construct();
    //$this->ms = $this->load->database('ms', TRUE); //--- SAP database
  }

  public function get_zone_code_and_name($warehouse_code = NULL)
	{
		$sc = array();

		$txt = trim($_REQUEST['term']);

    $qr = "SELECT BinCode AS code, SL1Code AS name FROM OBIN WHERE Disabled = 'N' ";

    if( ! empty($warehouse_code))
    {
      $qr .= "AND WhsCode = '{$warehouse_code}' ";
    }

    if($txt != '*')
    {
      $qr .= "AND (BinCode LIKE N'%{$txt}%' OR SL1Code LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY BinCode ASC OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

		$qs = $this->ms->query($qr);

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$sc[] = $rs->code. ' | '.$rs->name;
			}
		}
		else
		{
			$sc[] = "not found";
		}


		echo json_encode($sc);
	}


  public function get_bp_code_and_name()
  {
    $df_cust = getConfig('DEFAULT_CUSTOMER_CODE');

    $txt = trim($_REQUEST['term']);
    $sc = array();

    $qr  = "SELECT CardCode AS code, CardName AS name ";
    $qr .= "FROM OCRD ";
    $qr .= "WHERE CardType IN('C', 'L', 'S') ";

    $qr .= "AND validFor = 'Y' ";

    if($txt !== '*')
    {
      $qr .= "AND (CardCode = '{$df_cust}' OR CardCode LIKE N'%{$this->ms->escape_str($txt)}%' OR CardName LIKE N'%{$this->ms->escape_str($txt)}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }


    echo json_encode($sc);
  }


  public function get_item_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $arr = explode('*', $txt);

    $sc = array();
    $qr = "SELECT ItemCode AS code, ItemName AS name ";
    $qr .= "FROM OITM ";
    $qr .= "WHERE validFor = 'Y' AND SellItem = 'Y' ";

    if(count($arr) > 1)
    {
      foreach($arr as $ar)
      {
        $qr .= "AND (ItemCode LIKE N'%{$this->ms->escape_str($ar)}%' OR ItemName LIKE N'%{$this->ms->escape_str($ar)}%') ";
      }
    }
    else
    {
      $qr .= "AND (ItemCode LIKE N'%{$this->ms->escape_str($txt)}%' OR ItemName LIKE N'%{$this->ms->escape_str($txt)}%') ";
    }

    $qr .= "ORDER BY ItemCode ASC ";
    $qr .= "OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";
    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code.' | '.$rd->name;
      }
    }
    else {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_vendor_code_and_name()
  {
    $txt = trim($_REQUEST['term']);
    $sc = [];

    $qr  = "SELECT CardCode AS code, CardName AS name ";
    $qr .= "FROM OCRD ";
    $qr .= "WHERE CardType = 'S' ";

    $qr .= "AND validFor = 'Y' ";

    if($txt !== '*')
    {
      $qr .= "AND (CardCode LIKE N'%{$this->ms->escape_str($txt)}%' OR CardName LIKE N'%{$this->ms->escape_str($txt)}%') ";
    }

    $qr .= "ORDER BY CardCode ASC OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }


    echo json_encode($sc);
  }


  public function get_po_code($vendor = NULL)
  {
    $sc = array();
    $txt = trim($_REQUEST['term']);

    $this->ms->select('DocNum, CardCode, CardName')->where('DocStatus', 'O');

    if( ! empty($vendor))
    {
      $this->ms->where('CardCode', $vendor);
    }

    if($txt != '*')
    {
      $this->ms->group_start();
      $this->ms->like('DocNum', $txt);
      $this->ms->or_like('NumAtCard', $txt);
      $this->ms->group_end();
    }

    $po = $this->ms->order_by('DocNum', 'ASC')->limit(100)->get('OPOR');

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $row)
      {
        $sc[] = $row->DocNum. ' | '.$row->CardCode.' | '.$row->CardName;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }


  public function get_customer_code_and_name()
  {
    $df_cust = getConfig('DEFAULT_CUSTOMER_CODE');

    $txt = trim($_REQUEST['term']);
    $sc = array();

    $qr  = "SELECT CardCode AS code, CardName AS name ";
    $qr .= "FROM OCRD ";
    $qr .= "WHERE CardType IN('C', 'L') ";

    $qr .= "AND validFor = 'Y' ";

    if($txt !== '*')
    {
      $qr .= "AND (CardCode = '{$df_cust}' OR CardCode LIKE N'%{$this->ms->escape_str($txt)}%' OR CardName LIKE N'%{$this->ms->escape_str($txt)}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }


    echo json_encode($sc);
  }


  public function sub_district()
  {
    $sc = array();
    $adr = $this->db->like('tumbon', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function district()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('amphur', $_REQUEST['term'])
    ->group_by('amphur')
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function get_delivery_zone()
  {
    $sc = array();
    $adr = $this->db
    ->where('active', 1)
    ->group_start()
    ->like('district', $_REQUEST['term'])
    ->or_like('province', $_REQUEST['term'])
    ->or_like('zipCode', $_REQUEST['term'])
    ->group_end()
    ->limit(20)
    ->get('delivery_zone');

    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->id.'>>'.$rs->district.'>>'.$rs->province.'>>'.$rs->zipCode;
      }
    }

    echo json_encode($sc);
  }


  public function get_document($objType = 23, $cardCode = NULL)
	{
		$type = array(
			'23' => 'OQUT',
			'17' => 'ORDR',
			'1470000113' => 'OPRQ'
		);

    if($objType >= 23)
    {
      $searchText = trim($_REQUEST['term']);

  		$qr  = "SELECT DocNum, DocDate, CardName ";
  		$qr .= "FROM {$type[$objType]} ";
      $qr .= "WHERE DocNum != '' ";

      if(!empty($cardCode))
  		{
  			$qr .= "AND CardCode = '{$cardCode}' ";
  		}
  		else
  		{
  			$sale_in = $this->user_model->get_sale_in();

  			if(!empty($sale_in))
  	    {
  	      $qr .= "AND SlpCode IN({$sale_in}) ";
  	    }
  	    else
  	    {
  	      $qr .= "AND SlpCode = {$this->user->sale_id} ";
  	    }
  		}

  		if($searchText != '*')
  		{
  			$qr .= "AND (DocNum LIKE N'%{$this->ms->escape_str($searchText)}%' ";
  			$qr .= "OR CardCode LIKE N'%{$this->ms->escape_str($searchText)}%' ";
  			$qr .= "OR CardName LIKE N'%{$this->ms->escape_str($searchText)}%' ";
  			$qr .= "OR Comments LIKE N'%{$this->ms->escape_str($searchText)}%') ";
  		}

  		$qr .= "ORDER BY DocDate DESC ";
      $qr .= "OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";


  		$qs = $this->ms->query($qr);

  		if($qs->num_rows() > 0)
  		{
  			$ds = array();
  			foreach($qs->result() as $rs)
  			{
          $ds[] = $rs->DocNum.' | '.thai_date($rs->DocDate, FALSE, '-').' | '.$rs->CardName;
  			}
  		}
  		else
  		{
  			$ds[] = "not found";
  		}
    }
    else
    {
      $ds = array("not found");
    }


    echo json_encode($ds);
	}


  public function get_employee()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $qr  = "SELECT firstName, lastName, empID FROM OHEM ";
    $qr .= "WHERE Active = 'Y' ";
    if($txt != '*')
    {
      $qr .= "AND (firstName LIKE N'%{$txt}%' OR lastName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $emp = $this->ms->query($qr);

    if($emp->num_rows() > 0)
    {
      foreach($emp->result() as $rs)
      {
        $sc[] = $rs->firstName.' '.$rs->lastName.' | '.$rs->empID;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }


  public function get_web_employee()
  {
    $sc = array();
    $txt = trim($_REQUEST['term']);

    $this->db->select('emp_name, emp_id');

    if($txt != '*')
    {
      $this->db->like('emp_name', $txt);
    }

    if(! $this->isSalesAdmin && ! $this->isAdmin && ! $this->isSuperAdmin)
    {

      if(!$this->isLead)
      {
        $this->db->where('emp_id', $this->user->emp_id);
      }
      else
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
    }

    $this->db->order_by('emp_name', 'ASC')->limit(50);
    $emp = $this->db->get('user');

    if($emp->num_rows() > 0)
    {
      foreach($emp->result() as $rs)
      {
        $sc[] = $rs->emp_name.' | '.$rs->emp_id;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }


  public function get_user_and_emp()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $emp = $this->db
    ->select('uname, emp_name')
    ->where('ugroup !=','superAdmin')
    ->group_start()
    ->like('uname', $txt)
    ->or_like('emp_name', $txt)
    ->group_end()
    ->limit(20)
    ->get('user');


    if($emp->num_rows() > 0)
    {
      foreach($emp->result() as $rs)
      {
        $sc[] = $rs->uname.' | '.$rs->emp_name;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }


  public function get_warehouse_code_and_name()
  {
    $sc = array();
    $txt = $_REQUEST['term'];

    $qr  = "SELECT WhsCode AS code, WhsName AS name FROM OWHS ";

    if($txt !== '*')
    {
      $qr .= "WHERE WhsCode LIKE N'%{$txt}%' OR WhsName LIKE N'%{$txt}%' ";
    }

    $qr .= "ORDER BY WhsCode ASC OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }


  public function get_tax_code_and_name()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->ms->select('Code AS code, Name AS name');
    if($txt !== '*')
    {
      $this->ms->like('Code', $txt);
    }
    $qs = $this->ms->limit(20)->get('OVTG');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'Not found';
    }

    echo json_encode($sc);
  }

} //-- end class
?>

<?php
class Customers_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_non_sap_code($limit = 100)
  {
    $rs = $this->db->select('code')->where_in('Status', array(1, 3))->get('customer');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_contact_data($code)
  {
    $rs = $this->ms
    ->select('OCRD.LicTradNum, OCRD.Phone1, OCRD.Phone2, OCRD.Cellular, OCRD.Fax, OCRD.E_Mail')
    ->select('OCTG.PymntGroup, OCTG.ExtraDays AS term')
    ->from('OCRD')
    ->JOIN('OCTG', 'OCRD.GroupNum = OCTG.GroupNum', 'left')
    ->where('CardCode', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('customer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('customer', $ds);
    }

    return  FALSE;
  }



  public function get_tax($code)
  {
    $rs = $this->ms
    ->select('OVTG.Code AS taxCode, OVTG.Rate AS taxRate')
    ->from('OCRD')
    ->join('OVTG', 'OVTG.Code = OCRD.ECVatGroup', 'left')
    ->where('OCRD.CardCode', $code)
    ->where('OCRD.ECVatGroup IS NOT NULL', NULL, FALSE)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_contact($code)
  {
    $rs = $this->db->where('CardCode', $code)->get('contact_person');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_list_num($code)
  {
    $rs = $this->ms->select('ListNum')->where('CardCode', $code)->get('OCRD');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->ListNum;
    }

    return NULL;
  }


  public function drop_contact_person($LeadCode)
  {
    return $this->db->where('CardCode', $LeadCode)->delete('contact_person');
  }


  public function get_project_name($project_code)
  {
    $rs = $this->ms->select('PrjName AS name')->where('PrjCode', $project_code)->get('OPRJ');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_bill_to($code)
  {
    $rs = $this->db->where('AdresType', 'B')->where('CardCode', $code)->get('customer_address');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_ship_to($code)
  {
    $rs = $this->db->where('AdresType', 'S')->where('CardCode', $code)->get('customer_address');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_ship_to_data($cardCode, $shipToCode)
  {
    $qr = "SELECT * FROM CRD1 WHERE CardCode = N'{$cardCode}' AND AdresType = 'S' AND Address = N'{$shipToCode}'";
    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function drop_address($LeadCode)
  {
    return $this->db->where('CardCode', $LeadCode)->delete('customer_address');
  }



  public function get_preview_data($code)
  {
    $rs = $this->db
    ->select('code, LeadCode, CardName, Currency, LicTradNum, OwnerName, U_LEVEL, Phone1, Phone2')
    ->where('LeadCode', $code)
    ->get('customer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_data($code) //-- web order
  {
    $rs = $this->mc
    ->select('U_WEBORDER, CardCode, CardName, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEBORDER', $code)
    ->get('OCRD');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('customer', $ds);
    }

    return FALSE;
  }


  public function add_contact(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('contact_person', $ds);
    }

    return FALSE;
  }


  public function add_address(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('customer_address', $ds);
    }

    return FALSE;
  }



  public function add_sap_customer(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('OCRD', $ds);
    }

    return FALSE;
  }


  public function add_sap_contact_person(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('OCPR', $ds);
    }

    return FALSE;
  }


  public function add_sap_address(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('CRD1', $ds);
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    //$this->db->where('SlpCode', $this->user->sale_id);

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['LeadCode']))
    {
      $this->db->like('LeadCode', $ds['LeadCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if($this->isAdmin === FALSE)
    {
      if($this->isLead)
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
      else
      {
        $this->db->where('user_id', $this->user->id);
      }
    }

    return $this->db->count_all_results('customer');
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'id' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    //$this->db->where('SlpCode', $this->user->sale_id);

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }


    if(!empty($ds['LeadCode']))
    {
      $this->db->like('LeadCode', $ds['LeadCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if($this->isAdmin === FALSE)
    {
      if($this->isLead)
      {
        $this->db->where('sale_team', $this->user->sale_team);
      }
      else
      {
        $this->db->where('user_id', $this->user->id);
      }
    }

    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_contact_person_name($id)
  {
    $rs = $this->ms->select('Name AS name')->where('CntctCode', $id)->get('OCPR');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_contact_person($code)
  {
    $rs = $this->ms->select('CntctCode AS id, Name AS name')->where('CardCode', $code)->get('OCPR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_contact_person_detail($CntctCode)
  {
    $rs = $this->ms->where('CntctCode', $CntctCode)->get('OCPR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_address_ship_to($CardCode, $address = '00000')
  {
    $qr  = "SELECT CRD1.*, OCRY.Name AS countryName ";
    $qr .= "FROM CRD1 ";
    $qr .= "LEFT JOIN OCRY ON CRD1.Country = OCRY.Code ";
    $qr .= "WHERE CRD1.AdresType = 'S' ";
    $qr .= "AND CRD1.CardCode = '{$CardCode}' ";
    $qr .= "AND CRD1.Address = N'{$address}' ";

    $rs = $this->ms->query($qr);

    // $rs = $this->ms
    // ->select('CRD1.*, OCRY.Name AS countryName')
    // ->from('CRD1')
    // ->join('OCRY', 'CRD1.Country = OCRY.Code', 'left')
    // ->where('CRD1.AdresType', 'S')
    // ->where('CRD1.CardCode', $CardCode)
    // ->where('CRD1.Address', $address)
    // ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_address_ship_to_code($CardCode)
  {
    $rs = $this->ms
    ->select('Address')
    ->where('AdresType', 'S')
    ->where('CardCode', $CardCode)
    ->get('CRD1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_address_bill_to($CardCode, $address = '00000')
  {
    $qr  = "SELECT CRD1.*, OCRY.Name AS countryName ";
    $qr .= "FROM CRD1 ";
    $qr .= "LEFT JOIN OCRY ON CRD1.Country = OCRY.Code ";
    $qr .= "WHERE CRD1.AdresType = 'B' ";
    $qr .= "AND CRD1.CardCode = '{$CardCode}' ";
    $qr .= "AND CRD1.Address = N'{$address}' ";

    $rs = $this->ms->query($qr);

    // $rs = $this->ms
    // ->select('CRD1.*, OCRY.Name AS countryName')
    // ->from('CRD1')
    // ->join('OCRY', 'CRD1.Country = OCRY.Code', 'left')
    // ->where('CRD1.AdresType', 'B')
    // ->where('CRD1.CardCode', $CardCode)
    // ->where('CRD1.Address', $address)
    // ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_address_bill_to_code($CardCode)
  {
    $rs = $this->ms
    ->select('Address')
    ->where('AdresType', 'B')
    ->where('CardCode', $CardCode)
    ->get('CRD1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_prefix()
  {
    $rs = $this->ms
    ->distinct()
    ->select('U_BEX_TYPE AS prefix')
    ->where('U_BEX_TYPE IS NOT NULL', NULL, FALSE)
    ->order_by('U_BEX_TYPE', 'ASC')
    ->get('OCRD');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getGroupCode()
  {
    $rs = $this->ms
    ->select('GroupCode AS code, GroupName AS name')
    ->where('GroupType', 'C')
    ->where('Locked', 'N')
    ->order_by('GroupCode', 'ASC')
    ->get('OCRG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getGroupNum()
  {
    $rs = $this->ms
    ->select('GroupNum AS code, PymntGroup AS name')
    ->order_by('GroupNum', 'ASC')
    ->get('OCTG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getPaymentTerm($CardCode)
  {
    $rs = $this->ms
    ->select('OCTG.GroupNum AS code, OCTG.PymntGroup AS name')
    ->from('OCRD')
    ->join('OCTG', 'OCRD.GroupNum = OCTG.GroupNum')
    ->where('OCRD.CardCode', $CardCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_industry()
  {
    $rs = $this->ms
    ->select('IndCode AS code, IndDesc AS name')
    ->order_by('IndCode', 'ASC')
    ->get('OOND');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_territory()
  {
    $rs = $this->ms
    ->select('territryID AS code, descript AS name')
    ->where('inactive', 'N')
    ->get('OTER');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_price_list()
  {
    $rs = $this->ms
    ->select("ListNum AS code, ListName AS name")
    ->get('OPLN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_customer_price_list($code)
  {
    $rs = $this->ms
    ->select('OPLN.ListNum AS code, OPLN.ListName AS name')
    ->from('OCRD')
    ->join('OPLN', 'OCRD.ListNum = OPLN.ListNum', 'left')
    ->where('OCRD.CardCode', $code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_tax_group($code) //--- OVTG
  {
    $rs = $this->ms
    ->select('Code AS code, Name AS name')
    ->where('Code', $code)
    ->get('OVTG');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_properties_list() //--- OCQG
  {
    $rs = $this->ms
    ->select('GroupCode AS code, GroupName AS name')
    ->order_by('GroupCode', 'ASC')
    ->get('OCQG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_customer_level_list()
  {
    $rs = $this->ms
    ->distinct()
    ->select('U_LEVEL AS code')
    ->where('U_LEVEL IS NOT NULL', NULL, FALSE)
    ->get('OCRD');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_customer_indicator() //-- OIDC
  {
    $rs = $this->ms->get('OIDC');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_lead_code($leadCode, $code = NULL)
  {
    if(!empty($code))
    {
      $this->db->where('code !=', $code);
    }

    $rs = $this->db->where('leadCode', $leadCode)->get('customer');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_sap_exists_code($code)
  {
    $rs = $this->ms->where('U_WEBORDER', $code)->get('OCRD');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

  public function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OCRD');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_sap_card_code($code)
  {
    $rs = $this->ms
    ->select('CardCode')
    ->where('U_WEBORDER', $code)
    ->get('OCRD');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function is_temp_exists_data($code)
  {
    $rs = $this->mc->where('U_WEBORDER', $code)->count_all_results('OCRD');
    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function drop_temp_exists_data($code)
  {
    $this->mc->trans_start();
    $this->mc->where('U_WEBORDER', $code)->delete('OCRD');
    $this->mc->where('U_WEBORDER', $code)->delete('CRD1');
    $this->mc->where('U_WEBORDER', $code)->delete('OCPR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }



  public function delete($code, $leadCode = NULL)
  {
    $this->db->trans_start();

    if(!empty($leadCode))
    {
      //--- delete contact
      $this->db->where('CardCode', $leadCode)->delete('contact_person');
      //--- delete address
      $this->db->where('CardCode', $leadCode)->delete('customer_address');
    }

    $this->db->where('code', $code)->delete('customer');

    $this->db->trans_complete();

    return $this->db->trans_status();
  }



  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('customer');

    return $rs->row()->code;
  }



  public function get_slp_code_and_name($card_code)
  {
    if(! is_null($card_code))
		{
			$rs = $this->ms
			->select('OCRD.SlpCode, OSLP.SlpName')
			->from('OCRD')
			->join('OSLP', 'OCRD.SlpCode = OSLP.SlpCode', 'left')
			->where('OCRD.CardCode', $card_code)
			->get();

			if($rs->num_rows() === 1)
			{
				return $rs->row();
			}
		}

    return NULL;
  }



  public function get_sap_max_code($pre)
  {
    $rs = $this->ms
    ->select_max('CardCode')
    ->where('CardType', 'L')
    ->like('CardCode', $pre, 'after')
    ->order_by('CardCode', 'DESC')
    ->get('OCRD');

    return $rs->row()->CardCode;
  }
}
?>

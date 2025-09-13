<?php
class Customers_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->ms
    ->select('C.CardCode, C.CardName, C.GroupCode, C.GroupNum, C.ListNum, C.SlpCode, C.CntctPrsn')
    ->select('C.CreditLine, C.VatStatus, C.LicTradNum')
    ->select('P.GroupNum AS TermCode, P.PymntGroup AS TermName')
    ->from('OCRD AS C')
    ->join('OCTG AS P', 'C.GroupNum = P.GroupNum', 'left')
    ->where('C.CardCode', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
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


  public function get_list_num($code)
  {
    $rs = $this->ms->select('ListNum')->where('CardCode', $code)->get('OCRD');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->ListNum;
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

}
?>

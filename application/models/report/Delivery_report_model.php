<?php
class Delivery_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_internal_remark($inv_code)
  {
    $rs = $this->ms->select('U_Remark_Int')->where('DocNum', $inv_code)->get('OINV');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->U_Remark_Int;
    }

    return NULL;
  }


  public function get_iv_data($ds)
  {
    $this->ms
    ->distinct()
    ->select("o.DocDate, o.DocNum, o.CardCode, o.CardName, o.OwnerCode, o.DocTotal")
    ->select("o.Address2, o.Comments, o.U_Delivery_Urgency, o.U_Deliver_status")
    ->select("o.U_Deliver_date, o.U_Required_Delivery_Date, o.U_Deliver_Doc")
    ->select("ad.ZipCode, ad.City, em.firstName, em.lastName")
    ->from("INV1 AS d")
    ->join("OINV AS o", "d.DocEntry = o.DocEntry", "left")
    ->join("CRD1 AS ad", "o.CardCode = ad.CardCode AND o.ShipToCode = ad.Address AND ad.AdresType = 'S'", "left")
    ->join("OHEM AS em", "o.OwnerCode = em.empId", "left")
    ->where('o.CANCELED', 'N')
    ->where('o.DocStatus !=', 'C')
    ->where('o.U_BEX_DO IS NULL', NULL, FALSE)
    ->where('d.BaseType !=', 15)
    ->group_start()
    ->where('o.U_Deliver_status IS NULL', NULL, FALSE)
    ->or_where("o.U_Deliver_status !=", 4)
    ->group_end();

    if($ds['date_type'] == 'R')
    {
      $this->ms
      ->where('o.U_Required_Delivery_Date >=', from_date($ds['from_date']))
      ->where('o.U_Required_Delivery_Date <=', to_date($ds['to_date']));
    }
    else
    {
      $this->ms
      ->where('o.DocDate >=', from_date($ds['from_date']))
      ->where('o.DocDate <=', to_date($ds['to_date']));
    }

    if($ds['all_cust'] != 1 && ! empty($ds['from_cust_code']) && ! empty($ds['to_cust_code']))
    {
      $this->ms
      ->where('o.CardCode >=', $ds['from_cust_code'])
      ->where('o.CardCode <=', $ds['to_cust_code']);
    }

    $rs = $this->ms->order_by('o.DocNum', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_do_data($ds)
  {
    $this->ms
    ->select("o.DocDate, o.DocNum, o.CardCode, o.CardName, o.OwnerCode, o.DocTotal")
    ->select("o.Address2, o.Comments, o.U_Remark_Int, o.U_Delivery_Urgency, o.U_Deliver_status")
    ->select("o.U_Deliver_date, o.U_Required_Delivery_Date, o.U_Deliver_Doc")
    ->select("ad.ZipCode, ad.City, em.firstName, em.lastName")
    ->from("ODLN AS o")
    ->join("CRD1 AS ad", "o.CardCode = ad.CardCode AND o.ShipToCode = ad.Address AND ad.AdresType = 'S'", "left")
    ->join("OHEM AS em", "o.OwnerCode = em.empId", "left")
    ->where('o.CANCELED', 'N')
    ->group_start()
    ->where('o.U_Deliver_status IS NULL', NULL, FALSE)
    ->or_where("o.U_Deliver_status !=", 4)
    ->group_end();

    if($ds['date_type'] == 'R')
    {
      $this->ms
      ->where('o.U_Required_Delivery_Date >=', from_date($ds['from_date']))
      ->where('o.U_Required_Delivery_Date <=', to_date($ds['to_date']));
    }
    else
    {
      $this->ms
      ->where('o.DocDate >=', from_date($ds['from_date']))
      ->where('o.DocDate <=', to_date($ds['to_date']));
    }

    if($ds['all_cust'] != 1 && ! empty($ds['from_cust_code']) && ! empty($ds['to_cust_code']))
    {
      $this->ms
      ->where('o.CardCode >=', $ds['from_cust_code'])
      ->where('o.CardCode <=', $ds['to_cust_code']);
    }

    $rs = $this->ms->order_by('o.DocNum', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_delivery_doc($code, $type = "IV")
  {
    $rs = $this->db
    ->select('delivery_code, line_status, remark')
    ->where('DocType', $type)
    ->where('DocNum', $code)
    ->where('line_status !=', 'D')
    ->order_by('date_add', 'DESC')
    ->limit(1)
    ->get('delivery_details');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_route_by_zip_code($zip_code)
  {
    $rs = $this->db
    ->distinct('rt.id')
    ->select('rt.name')
    ->from('delivery_route_detail AS rd')
    ->join('delivery_route AS rt', 'rd.route_id = rt.id', 'left')
    ->where('rd.zipCode', $zip_code)
    ->group_by('rt.id')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_route_by_zip_code_and_city($zip_code, $city)
  {
    $rs = $this->db
    ->distinct('rt.id')
    ->select('rt.name')
    ->from('delivery_route_detail AS rd')
    ->join('delivery_route AS rt', 'rd.route_id = rt.id', 'left')
    ->where('rd.zipCode', $zip_code)
    ->where('rd.district', $city)
    ->group_by('rt.id')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_route_by_zip_code_and_id($zip_code, $route_id)
  {
    $rs = $this->db
    ->distinct('rt.id')
    ->select('rt.name')
    ->from('delivery_route_detail AS rd')
    ->join('delivery_route AS rt', 'rd.route_id = rt.id', 'left')
    ->where('rd.zipCode', $zip_code)
    // ->where('rd.district', $city)
    ->where('rt.id', $route_id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class

 ?>

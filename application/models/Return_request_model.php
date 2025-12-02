<?php
class Return_request_model extends CI_Model
{
  private $tb = "return_request";
  private $td = "return_request_detail";
  private $log = "return_request_logs";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('return_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('return_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('return_code', $code)->delete($this->td);
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['sap_no']))
    {
      $this->db->like('DocNum', $ds['sap_no']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['tempStatus']) && $ds['tempStatus'] != 'all')
    {
      $this->db->where('tempStatus', $ds['tempStatus']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['sap_no']))
    {
      $this->db->like('DocNum', $ds['sap_no']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['tempStatus']) && $ds['tempStatus'] != 'all')
    {
      $this->db->where('tempStatus', $ds['tempStatus']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    $rs = $this->db
    ->order_by('id', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);


    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_inv($code)
  {
    $count = $this->ms
    ->where('DocNum', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('OINV');

    return $count === 1 ? TRUE : FALSE;
  }


  public function is_exists_do($code)
  {
    $count = $this->ms
    ->where('DocNum', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('ODLN');

    return $count === 1 ? TRUE : FALSE;
  }


  public function get_invoice_details($code)
  {
    $rs = $this->ms
    ->select('ivd.DocEntry, ivd.LineNum, iv.DocNum')
    ->select('ivd.ItemCode, ivd.Dscription, ivd.PriceBefDi, ivd.PriceAfVAT, ivd.Price')
    ->select('ivd.DiscPrcnt, ivd.Quantity AS Qty, ivd.OpenQty, ivd.Currency, ivd.Rate, ivd.SlpCode')
    ->select('ivd.VatGroup, ivd.VatPrcnt, ivd.VatSum')
    ->select('ivd.UomCode, ivd.UomCode2, ivd.UomEntry, ivd.UomEntry2')
    ->select('ivd.unitMsr, ivd.unitMsr2, ivd.NumPerMsr, ivd.NumPerMsr2')
    ->from('INV1 AS ivd')
    ->join('OINV AS iv', 'ivd.DocEntry = iv.DocEntry', 'left')
    ->where('iv.DocNum', $code)
    ->where('iv.CANCELED', 'N')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_do_details($code)
  {
    $rs = $this->ms
    ->select('dd.DocEntry, dd.LineNum, do.DocNum')
    ->select('dd.ItemCode, dd.Dscription, dd.PriceBefDi, dd.PriceAfVAT, dd.Price')
    ->select('dd.DiscPrcnt, dd.Quantity AS Qty, dd.OpenQty, dd.Currency, dd.Rate, dd.SlpCode')
    ->select('dd.VatGroup, dd.VatPrcnt, dd.VatSum')
    ->select('dd.UomCode, dd.UomCode2, dd.UomEntry, dd.UomEntry2')
    ->select('dd.unitMsr, dd.unitMsr2, dd.NumPerMsr, dd.NumPerMsr2')
    ->from('DLN1 AS dd')
    ->join('ODLN AS do', 'dd.DocEntry = do.DocEntry', 'left')
    ->where('do.DocNum', $code)
    ->where('do.CANCELED', 'N')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_open_qty($baseType, $baseEntry, $baseLine)
  {

    if($baseType === 'IV')
    {
      $this->ms->from('INV1');
    }

    if($baseType == 'DO')
    {
      $this->ms->from('DLN1');
    }

    $rs = $this->ms
    ->select('OpenQty')
    ->where('DocEntry', $baseEntry)
    ->where('LineNum', $baseLine)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->OpenQty;
    }

    return 0;
  }
  

  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_sap_return_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('ORRR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms->select('DocNum')->where('U_WEBORDER', $code)->where('CANCELED', 'N')->get('ORRR');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function get_temp_data($code)
  {
    $rs = $this->mc
    ->select('DocEntry, U_WEBORDER, CardCode, CardName, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEBORDER', $code)
    ->get('ORRR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_exists_data($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_WEBORDER', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('ORRR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_status($code)
  {
    $rs = $this->mc
    ->select('F_Sap, F_SapDate, Message')
    ->where('U_WEBORDER', $code)
    ->order_by('DocEntry', 'DESC')
    ->get('ORRR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_sap_return_request(array $ds = array())
  {
    $rs = $this->mc->insert('ORRR', $ds);

    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_return_request($code, $ds)
  {
    return $this->mc->where('U_WEBORDER', $code)->update('ORRR', $ds);
  }


  public function add_sap_return_request_detail(array $ds = array())
  {
    return $this->mc->insert('RRR1', $ds);
  }


  public function drop_temp_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('RRR1');
    $this->mc->where('DocEntry', $docEntry)->delete('ORRR');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  public function drop_temp_exists_data($code)
  {
    $this->mc->trans_start();
    $this->mc->where('U_WEBORDER', $code)->delete('RRR1');
    $this->mc->where('U_WEBORDER', $code)->delete('ORRR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function get_doc_status($code)
  {
    $rs = $this->ms
    ->select('DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('ORRR');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocStatus;
    }

    return 'O';
  }


  public function getSyncList($limit = 100)
  {
    $syncDays = 30;
    $from_date = from_date(date('Y-m-d', strtotime("-{$syncDays} days")));

    $rs = $this->db
    ->select('code')
    ->where('date_add >', $from_date)
    ->where('status', 'C')
    ->where_in('tempStatus', ['P', 'F'])
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get('return_request');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add_logs(array $ds = array())
  {
    return $this->db->insert($this->log, $ds);
  }


  public function get_logs($code)
  {
    $rs = $this->db->where('code', $code)->order_by('id', 'ASC')->get($this->log);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //-- end class

 ?>

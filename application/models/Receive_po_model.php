<?php
class Receive_po_model extends CI_Model
{
  private $tb = "receive_po";
  private $td = "receive_po_details";
  private $tr = "receive_po_ref";
  private $log = "receive_po_logs";

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
    $rs = $this->db->where('receive_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_po_refs($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tr);

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


  public function add_po_refs($code, array $po_refs = array())
  {
    if( ! empty($po_refs))
    {
      $qr = "INSERT INTO receive_po_ref (code, po_code) VALUES ";

      $i = 1;

      foreach($po_refs as $po_code)
      {
        $qr .= $i == 1 ? "('{$code}', '{$po_code}')" : ", ('{$code}', '{$po_code}')";
        $i++;
      }

      return $this->db->query($qr);
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
      return $this->db->where('receive_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('receive_code', $code)->delete($this->td);
  }


  public function delete_po_refs($code)
  {
    return $this->db->where('code', $code)->delete($this->tr);
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! empty($ds['po_code']))
    {
      $codes = $this->get_receive_codes_by_po_no($ds['po_code']);

      if( ! empty($codes))
      {
        $this->db->where_in('code', $codes);
      }
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if( ! empty($ds['sap_no']))
    {
      $this->db->like('DocNum', $ds['sap_no']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
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

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! empty($ds['po_code']))
    {
      $codes = $this->get_receive_codes_by_po_no($ds['po_code']);

      if( ! empty($codes))
      {
        $this->db->where_in('code', $codes);
      }
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if( ! empty($ds['sap_no']))
    {
      $this->db->like('DocNum', $ds['sap_no']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
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

    // echo $this->db->order_by('id', 'DESC')->limit($perpage, $offset)->get_compiled_select($this->tb);

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


  public function get_po($po_code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum, DocStatus, CardCode, CardName, DocCur, DocRate, DiscPrcnt')
    ->where('DocNum', $po_code)
    ->where('CANCELED', 'N')
    ->get('OPOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_details($po_code)
  {
    $rs = $this->ms
    ->select('d.DocEntry, d.LineNum, d.ItemCode, d.Dscription, d.Text')
    ->select('d.Quantity, d.LineStatus, d.OpenQty, d.Price')
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt, d.INMPrice')
		->select('d.Currency, d.Rate, d.VatGroup, d.VatPrcnt, d.unitMsr')
    ->select('d.unitMsr, d.NumPerMsr, d.unitMsr2, d.NumPerMsr2')
    ->select('d.UomEntry, d.UomEntry2, d.UomCode, d.UomCode2')
    ->from('POR1 AS d')
    ->join('OPOR AS o', 'd.DocEntry = o.DocEntry', 'left')
    ->where('o.DocNum', $po_code)
    ->where('o.DocStatus', 'O')
    ->where('d.LineStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_po_detail($po_code, $item_code)
  {
    $rs = $this->ms
    ->select('POR1.DocEntry, POR1.LineNum, POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus, POR1.OpenQty, POR1.PriceAfVAT AS price')
    ->select('POR1.Currency, POR1.Rate, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->where('POR1.ItemCode', $item_code)
    ->where('OPOR.DocStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_row($docEntry, $lineNum)
  {
    $rs = $this->ms
    ->select('POR1.DocEntry, POR1.LineNum, POR1.CodeBars')
    ->select('POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus')
    ->select('POR1.OpenQty, POR1.Price, POR1.PriceBefDi, POR1.PriceAfVAT, POR1.DiscPrcnt')
		->select('POR1.Currency, POR1.Rate, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->where('DocEntry', $docEntry)
    ->where('LineNum', $lineNum)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_data($po_code)
  {
    $rs = $this->ms
    ->select('POR1.Currency, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->limit(1)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_on_order_qty($itemCode, $baseEntry, $baseLine)
  {
    $rs = $this->db
    ->select_sum('rd.ReceiveQty')
    ->from('receive_po_details AS rd')
    ->join('receive_po AS ro', 'rd.receive_code = ro.code', 'left')
    ->where('ro.status !=', 'D')
    ->where('ro.DocNum IS NULL', NULL, FALSE)
    ->where('rd.baseEntry', $baseEntry)
    ->where('rd.baseLine', $baseLine)
    ->where('rd.ItemCode', $itemCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->ReceiveQty > 0 ? $rs->row()->ReceiveQty : 0;
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


  public function get_sap_receive_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms->select('DocNum')->where('U_WEBORDER', $code)->where('CANCELED', 'N')->get('OPDN');

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
    ->get('OPDN');

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
    ->get('OPDN');

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
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_sap_receive_po(array $ds = array())
  {
    $rs = $this->mc->insert('OPDN', $ds);

    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_receive_po($code, $ds)
  {
    return $this->mc->where('U_WEBORDER', $code)->update('OPDN', $ds);
  }


  public function add_sap_receive_po_detail(array $ds = array())
  {
    return $this->mc->insert('PDN1', $ds);
  }


  public function drop_temp_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('PDN1');
    $this->mc->where('DocEntry', $docEntry)->delete('OPDN');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  public function drop_temp_exists_data($code)
  {
    $this->mc->trans_start();
    $this->mc->where('U_WEBORDER', $code)->delete('PDN1');
    $this->mc->where('U_WEBORDER', $code)->delete('OPDN');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function get_doc_status($code)
  {
    $rs = $this->ms
    ->select('DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('OPDN');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocStatus;
    }

    return 'O';
  }


  public function get_receive_codes_by_po_no($po_no)
  {
    $codes = ["xxx"];

    $rs = $this->db->query("SELECT code FROM receive_po_ref WHERE po_code LIKE '%{$po_no}%' GROUP BY  code");

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $codes[] = $rd->code;
      }
    }

    return $codes;
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
    ->get('receive_po');

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

<?php
class Receive_po_model extends CI_Model
{
  private $tb = "receive_po";
  private $td = "receive_po_details";

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
      $this->db->like('po_code', $ds['po_code']);
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
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
      $this->db->like('po_code', $ds['po_code']);
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
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
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt')
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
    ->select('POR1.DocEntry, POR1.LineNum, POR1.CodeBars AS barcode')
    ->select('POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus')
    ->select('POR1.OpenQty, POR1.PriceAfVAT AS price')
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
} //-- end class

 ?>

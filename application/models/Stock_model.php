<?php
class Stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock($itemCode, $whsCode = NULL)
  {
    $this->ms
    ->select_sum('OnHand')
    ->select_sum('IsCommited')
    ->select_sum('OnOrder')
    ->select_sum('StockValue')
    ->where('ItemCode', $itemCode);
    if($whsCode !== NULL && $whsCode !== "")
    {
      $this->ms->where('WhsCode', $whsCode);
    }

    $rs = $this->ms->get('OITW');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_committed_stock($itemCode, $whsCode = NULL)
  {
    $rs = $this->ms
    ->select_sum('IsCommited', 'committed')
    ->where('ItemCode', $itemCode)
    ->where('WhsCode', $whsCode)
    ->get('OITW');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->committed;
    }

    return 0;
  }


  public function get_stock_each_warehouse($itemCode, $whList = NULL)
  {
    $this->ms
    ->select('WhsCode')
    ->select('(OnHand - IsCommited) AS OnHandQty')
    ->where('ItemCode', $itemCode);

    if(!empty($whList) && is_array($whList))
    {
      $this->ms->where_in('WhsCode', $whList);
    }

    $this->ms->order_by('WhsCode', 'ASC');

    $rs = $this->ms->get('OITW');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_stock_zone_qty($itemCode, $WhsCode)
  {
    $rs = $this->ms
    ->select('OBIN.SL1Code AS zone_code')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry')
    ->where('OIBQ.ItemCode', $itemCode)
    ->where('OIBQ.WhsCode', $WhsCode)
    ->order_by('OIBQ.OnHandQty', 'DESC')
    ->limit(1)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function getStockZone($itemCode, $binCode)
  {
    $rs = $this->ms
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry')
    ->where('OIBQ.ItemCode', $itemCode)
    ->where('OBIN.BinCode', $binCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  //--- ยอดรวมสินค้าทั้งหมดทุกคลัง (ไม่รวมคลังฝากสินค้า)
  public function get_onhand_stock($item)
  {
    $buffer = getConfig('BUFFER_WAREHOUSE');
    $this->ms->select_sum('OnHand')->where('ItemCode', $item);

    if($buffer != "" && $buffer != NULL)
    {
      $this->ms->where_not_in('WhsCode', array($buffer));
    }

    $rs = $this->ms->get('OITW');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->OnHand;
    }

    return 0;
  }


  public function get_stock_in_zone($itemCode)
  {
    $buffer = getConfig('BUFFER_WAREHOUSE');

    $this->ms
    ->select('OBIN.BinCode, OBIN.SL1Code AS code')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry')
    ->where('OIBQ.ItemCode', $itemCode);

    if($buffer != "" && $buffer != NULL)
    {
      $this->ms->where_not_in('OBIN.WhsCode', array($buffer));
    }

    $this->ms->order_by('OIBQ.OnHandQty', 'DESC');

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_all_stock_in_zone($binCode)
  {
    $rs = $this->ms
    ->select('OITM.ItemCode, OITM.ItemName, OITM.InvntryUom AS unitMsr, OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode')
    ->where('OBIN.BinCode', $binCode)
    ->where('OIBQ.OnHandQty !=', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_filter_stock_in_zone($binCode, $item)
  {
    $qr  = "SELECT OITM.ItemCode, OITM.ItemName, OITM.InvntryUom AS unitMsr, OIBQ.OnHandQty AS qty ";
    $qr .= "FROM OIBQ ";
    $qr .= "JOIN OBIN ON OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs ";
    $qr .= "JOIN OITM ON OIBQ.ItemCode = OITM.ItemCode ";
    $qr .= "WHERE ";
    $qr .= "OBIN.BinCode = '{$binCode}' ";
    $qr .= "AND OIBQ.OnHandQty != 0 ";

    if(! empty($item))
    {
      $qr .= "AND (OITM.ItemCode LIKE N'%{$item}%' OR OITM.ItemName LIKE N'%{$item}%') ";
    }
    

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}
?>

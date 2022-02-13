<?php
class Picking_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_details($absEntry)
  {
    $rs = $this->db->where('AbsEntry', $absEntry)->get('pick_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_item($absEntry, $ItemCode)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('ItemCode', $ItemCode)
    ->get('pick_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_detail_by_item_uom($absEntry, $orderCode, $ItemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $ItemCode)
    ->where('UomEntry', $UomEntry)
    ->get('pick_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details_by_item_other_uom($absEntry, $orderCode, $ItemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $ItemCode)
    ->where('UomEntry !=', $UomEntry)
    ->order_by('BaseQty', 'ASC')
    ->get('pick_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function update_picked_qty($id, $PickQtty, $BasePickQty)
  {
    $this->db
    ->set("PickQtty", "PickQtty + {$PickQtty}", FALSE)
    ->set("BasePickQty", "BasePickQty + {$BasePickQty}", FALSE)
    ->where('id', $id);

    return $this->db->update('pick_details');
  }


  public function get_buffer_zone($BinCode, $ItemCode)
  {
    $rs = $this->db
    ->select_sum('Qty')
    ->where('BinCode', $BinCode)
    ->where('ItemCode', $ItemCode)
    ->get('buffer');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Qty;
    }

    return 0;
  }



  public function get_sku_buffer_zone($BinCode, $ItemCode)
  {
    $rs = $this->db
    ->select_sum('BasePickQty', 'Qty')
    ->where('BinCode', $BinCode)
    ->where('ItemCode', $ItemCode)
    ->get('buffer');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Qty;
    }

    return 0;
  }


  public function add_buffer(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('buffer', $ds);
    }

    return FALSE;
  }


  public function update_buffer_qty($id, $Qty, $InvQty)
  {
    return $this->db
    ->set("Qty", "Qty + {$Qty}", FALSE)
    ->set("BasePickQty", "BasePickQty + {$InvQty}", FALSE)
    ->where("id", $id)
    ->update("buffer");
  }



  public function get_unique_buffer($absEntry, $orderCode, $itemCode, $binCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('BinCode', $binCode)
    ->where('UomEntry', $UomEntry)
    ->get('buffer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function add_prepare(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('picking_detail', $ds);
    }

    return FALSE;
  }



  public function update_prepare_qty($id, $Qty, $InvQty)
  {
    return $this->db
    ->set("Qty", "Qty + {$Qty}", FALSE)
    ->set("BasePickQty", "BasePickQty + {$InvQty}", FALSE)
    ->where('id', $id)
    ->update('picking_detail');
  }



  public function get_unique_prepare($absEntry, $orderCode, $itemCode, $binCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('BinCode', $binCode)
    ->where('UomEntry', $UomEntry)
    ->get('picking_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_list($ds = array(), $perpage = 20, $offset = 0, $status = 'R')
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $qr = "SELECT * FROM pick_list WHERE Status = '{$status}' AND Canceled = 'N' ";

    if(!empty($ds['WebCode']))
    {
      $qr .= "AND DocNum LIKE '%{$ds['WebCode']}%' ";
    }

    if(!empty($ds['Uname']))
    {
      $qr .= "AND uname LIKE '%{$ds['Uname']}%' ";
    }


    if(isset($ds['SoNo']) && $ds['SoNo'] != "")
    {
      $qr .= "AND AbsEntry IN((SELECT DISTINCT AbsEntry FROM pick_row WHERE OrderCode LIKE '%{$ds['SoNo']}%')) ";
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $qr .= "AND CreateDate >= '".from_date($ds['fromDate'])."' AND CreateDate <= '".to_date($ds['toDate'])."' ";
    }

    $qr .= "ORDER BY {$order_by} {$sort_by} LIMIT {$perpage} OFFSET ".get_zero($offset);

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows($ds = array(), $status = 'R')
  {
    $qr = "SELECT COUNT(*) AS rows FROM pick_list WHERE Status = '{$status}' AND Canceled = 'N' ";

    if(!empty($ds['WebCode']))
    {
      $qr .= "AND DocNum LIKE '%{$ds['WebCode']}%' ";
    }

    if(!empty($ds['Uname']))
    {
      $qr .= "AND uname LIKE '%{$ds['Uname']}%' ";
    }


    if(isset($ds['SoNo']) && $ds['SoNo'] != "")
    {
      $qr .= "AND AbsEntry IN((SELECT DISTINCT AbsEntry FROM pick_row WHERE OrderCode LIKE '%{$ds['SoNo']}%')) ";
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $qr .= "AND CreateDate >= '".from_date($ds['fromDate'])."' AND CreateDate <= '".to_date($ds['toDate'])."' ";
    }

    $rs = $this->db->query($qr);

    return $rs->row()->rows;
  }






  public function check_bin_code($code)
  {
    $rs = $this->ms->select('AbsEntry')->where('BinCode', $code)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


}


 ?>

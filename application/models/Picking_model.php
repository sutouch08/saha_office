<?php
class Picking_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_buffer_by_pick_detail($absEntry, $orderCode, $itemCode, $uomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('UomEntry', $uomEntry)
    ->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }



  public function get_order_code($absEntry, $itemCode)
  {
    $rs = $this->db
    ->select('OrderCode')
    ->where('AbsEntry', $absEntry)
    ->where('ItemCode', $itemCode)
    ->get('pick_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->OrderCode;
    }

    return NULL;
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


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('pick_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details_by_order($AbsEntry, $orderCode)
  {
    $rs = $this->db
    ->where('AbsEntry', $AbsEntry)
    ->where('OrderCode', $orderCode)
    ->get('pick_details');

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


  public function get_detail_by_item($absEntry, $orderCode, $ItemCode)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $ItemCode)
    ->get('pick_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
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



  public function delete_pick_detail($id)
  {
    return $this->db->where('id', $id)->delete('pick_details');
  }



  public function delete_pick_row($absEntry, $orderCode, $itemCode, $uomEntry)
  {
    return $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('UomEntry', $uomEntry)
    ->delete('pick_row');
  }


  public function update_picked_qty($id, $BasePickQty)
  {
    $this->db
    ->set("BasePickQty", "BasePickQty + {$BasePickQty}", FALSE)
    ->where('id', $id);

    return $this->db->update('pick_details');
  }


  public function get_buffer_zone($BinCode, $ItemCode)
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


  public function update_buffer_qty($id, $InvQty)
  {
    return $this->db
    ->set("BasePickQty", "BasePickQty + {$InvQty}", FALSE)
    ->where("id", $id)
    ->update("buffer");
  }



  public function restore_buffer(array $ds = array())
  {
    if(!empty($ds))
    {
      $bf = $this->get_unique_buffer($ds['AbsEntry'], $ds['OrderCode'], $ds['ItemCode'], $ds['BinCode'], $ds['UomEntry']);

      if( ! empty($bf))
      {
        return $this->update_buffer_qty($bf->id, $ds['BasePickQty']);
      }
      else
      {
        return $this->add_buffer($ds);
      }
    }

    return FALSE;
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


  public function get_prepare($absEntry, $orderCode, $itemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('UomEntry', $UomEntry)
    ->get('picking_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_prepare_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('picking_detail');

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



  public function update_prepare_qty($id, $InvQty)
  {
    return $this->db
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


  public function delete_prepare($id)
  {
    return $this->db->where('id', $id)->delete('picking_detail');
  }


  public function delete_prepares($absEntry, $orderCode, $itemCode, $UomEntry)
  {
    return $this->db
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $itemCode)
    ->where('UomEntry', $UomEntry)
    ->delete('picking_detail');
  }



  public function get_list($ds = array(), $perpage = 20, $offset = 0, $status = 'R')
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $qr = "SELECT * FROM pick_list WHERE Status = '{$status}' ";

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
    $qr = "SELECT COUNT(*) AS rows FROM pick_list WHERE Status = '{$status}' ";

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

<?php
class Pick_model extends CI_Model
{
  /* tables
  ** pick_list => OPKL
  ** pick_row => PKL1
  ** pick_detail => PKL2 (with bin detail)
  ** ORDR for order
  ** RDR1 for order details
  */

  public function __construct()
  {
    parent::__construct();
  }

  //--- Get Pick List Document
  public function get($AbsEntry)
  {
    if(!empty($AbsEntry))
    {
      $rs = $this->db->where('AbsEntry', $AbsEntry)->get('pick_list');

      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return NULL;
  }



  public function get_by_code($DocNum)
  {
    $rs = $this->db->where('DocNum', $DocNum)->get('pick_list');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  //---- Get pick rows
  public function get_pick_rows($AbsEntry)
  {
    $rs = $this->db->where('AbsEntry', $AbsEntry)->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sum_pick_rows($AbsEntry)
  {
    $rs = $this->db
    ->select('AbsEntry, OrderCode, OrderEntry, OrderLine, CardName, ItemCode, ItemName, UomEntry, UomEntry2, UomCode, unitMsr, unitMsr2, BaseQty')
    ->select_sum('RelQtty')
    ->select_sum('BaseRelQty')
    ->where('AbsEntry', $AbsEntry)
    ->group_by(array("OrderCode", "ItemCode", "UomEntry"))
    ->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_order_list($absEntry)
  {
    $rs = $this->db
    ->select('OrderCode')
    ->where('AbsEntry', $absEntry)
    ->group_by('OrderCode')
    ->order_by('OrderCode', 'ASC')
    ->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_pick_orders($code)
  {
    $rs = $this->db
    ->distinct()
    ->select('pick_list.AbsEntry, pick_list.DocNum, pick_list.CreateDate, pick_row.OrderCode, pick_row.OrderEntry')
    ->from('pick_row')
    ->join('pick_list', 'pick_row.AbsEntry = pick_list.AbsEntry', 'left')
    ->where('pick_list.DocNum', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_order_rows($AbsEntry, $orderCode)
  {
    return $this->db->where('AbsEntry', $AbsEntry)->where('OrderCode', $orderCode)->count_all_results('pick_row');
  }


  public function get_pick_rows_by_item($AbsEntry, $ItemCode)
  {
    $rs = $this->db
    ->where('AbsEntry', $AbsEntry)
    ->where('ItemCode', $ItemCode)
    ->where('PickQtty <', 'RelQtty', FALSE)
    ->order_by('PickEntry', 'ASC')
    ->get('pick_row');

    if($rs->num_rows()> 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_pick_rows_by_item_uom($AbsEntry, $OrderCode, $ItemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('AbsEntry', $AbsEntry)
    ->where('ItemCode', $ItemCode)
    ->where('OrderCode', $OrderCode)
    ->where('UomEntry', $UomEntry)
    ->where('PickQtty <', 'RelQtty', FALSE)
    ->order_by('PickEntry', 'ASC')
    ->get('pick_row');

    if($rs->num_rows()> 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_picking_details($docNum, $orderCode)
  {
    $rs = $this->db->where('DocNum', $docNum)->where('OrderCode', $orderCode)->get('picking_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_picking_rows($absEntry)
  {
    $qr  = "SELECT ItemCode, ItemName, SUM(RelQtty) AS RelQtty, ";
    $qr .= "SUM(BaseRelQty) AS BaseRelQty, UomEntry, UomCode, unitMsr, UomEntry2 AS InvUom, BaseQty ";
    $qr .= "FROM pick_row ";
    $qr .= "WHERE AbsEntry = {$absEntry} ";
    $qr .= "Group BY ItemCode, UomEntry ";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_picking_details_exists($absEntry)
  {
    $row = $this->db->where('AbsEntry', $absEntry)->count_all_results('pick_details');

    if($row > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function remove_pick_row($AbsEntry, $PickEntry)
  {
    return $this->db
    ->where('AbsEntry', $AbsEntry)
    ->where('PickEntry', $PickEntry)
    ->delete('pick_row');
  }


  public function getOrderRow($DocEntry, $LineNum)
  {
    $rs = $this->ms
    ->select('ORDR.DocNum, ORDR.CardName')
    ->select('RDR1.DocEntry, RDR1.LineNum, RDR1.ItemCode, RDR1.Dscription AS ItemName, RDR1.Quantity, RDR1.OpenQty')
    ->select('RDR1.UomEntry, RDR1.UomEntry2, RDR1.UomCode, RDR1.UomCode2, RDR1.unitMsr, RDR1.unitMsr2')
    ->from('RDR1')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry', 'left')
    ->where('RDR1.DocEntry', $DocEntry)
    ->where('RDR1.LineNum', $LineNum)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function getOpenRows($DocEntry)
  {
    $rs = $this->ms
    ->select('RDR1.DocEntry, RDR1.LineNum, RDR1.LineStatus, RDR1.ItemCode, RDR1.Dscription AS ItemName')
    ->select('RDR1.Quantity, RDR1.OpenQty')
    ->select('RDR1.UomEntry, RDR1.UomEntry2, RDR1.UomCode, RDR1.UomCode2, RDR1.unitMsr, RDR1.unitMsr2')
    ->select('ORDR.DocNum, ORDR.CardCode, ORDR.CardName')
    ->from('RDR1', 'RDR1.DocEntry = ORDR.DocEntry')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry', 'left')
    ->where('RDR1.DocEntry', $DocEntry)
    ->where('ORDR.DocStatus', 'O')
    ->where('ORDR.CANCELED', 'N')
    ->where('RDR1.OpenQty >', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_prev_release_qty($OrderEntry, $OrderLine)
  {
    $unfinished = $this->get_unfinished_qty($OrderEntry, $OrderLine);
    $finished = $this->get_finished_qty($OrderEntry, $OrderLine);

    return $unfinished + $finished;
  }



  public function get_unfinished_qty($OrderEntry, $OrderLine)
  {
    $rs = $this->db
    ->select_sum('BaseRelQty')
    ->where('OrderEntry', $OrderEntry)
    ->where('OrderLine', $OrderLine)
    ->where_in('PickStatus',array('N', 'P', 'R'))
    ->get('pick_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BaseRelQty;
    }

    return 0;
  }



  public function get_finished_qty($OrderEntry, $OrderLine)
  {
    $rs = $this->db
    ->select_sum('BasePickQty')
    ->where('OrderEntry', $OrderEntry)
    ->where('OrderLine', $OrderLine)
    ->where_in('PickStatus', array('Y', 'C'))
    ->get('pick_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BasePickQty;
    }

    return 0;
  }


  public function get_committed_stock($ItemCode)
  {
    $rs = $this->db
    ->select_sum('BaseRelQty')
    ->where('ItemCode', $ItemCode)
    ->where('LineStatus', 'O')
    ->get('pick_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BaseRelQty;
    }

    return 0;
  }


  public function get_committed_stock_by_pick_list($absEntry, $ItemCode)
  {
    $rs = $this->db
    ->select_sum('BaseRelQty')
    ->where('AbsEntry', $absEntry)
    ->where('ItemCode', $ItemCode)
    ->where('LineStatus', 'O')
    ->get('pick_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BaseRelQty;
    }

    return 0;
  }



  public function get_list($ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'DocNum' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];
    // $offset = empty($offset) ? 0 : $offset;

    $qr = "SELECT * FROM pick_list WHERE AbsEntry > 0 ";

    if(!empty($ds['WebCode']))
    {
      $qr .= "AND DocNum LIKE '%{$ds['WebCode']}%' ";
    }

    if(!empty($ds['Uname']))
    {
      $qr .= "AND uname LIKE '%{$ds['Uname']}%' ";
    }

    if($ds['Status'] != 'all')
    {
      $qr .= "AND Status LIKE '{$ds['Status']}' ";
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



  public function count_rows($ds = array())
  {
    $qr = "SELECT COUNT(*) AS rows FROM pick_list WHERE AbsEntry > 0 ";

    if(!empty($ds['WebCode']))
    {
      $qr .= "AND DocNum LIKE '%{$ds['WebCode']}%' ";
    }

    if(!empty($ds['Uname']))
    {
      $qr .= "AND uname LIKE '%{$ds['Uname']}%' ";
    }

    if($ds['Status'] != 'all')
    {
      $qr .= "AND Status LIKE '{$ds['Status']}' ";
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


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->db->insert('pick_list', $ds);

      if($rs)
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function add_row(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('pick_row', $ds);
    }

    return FALSE;
  }



  public function add_pick_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('pick_details', $ds);
    }

    return FALSE;
  }


  public function drop_pick_details($absEntry)
  {
    return $this->db->where('AbsEntry', $absEntry)->delete('pick_details');
  }


  public function update($AbsEntry, array $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('AbsEntry', $AbsEntry)->update('pick_list', $ds);
    }

    return FALSE;
  }



  public function update_pick_qtty($absEntry, $pickEntry, $PickQtty, $BasePickQty)
  {
    $this->db
    ->set("PickQtty", "PickQtty + {$PickQtty}", FALSE)
    ->set("BasePickQty", "BasePickQty + {$BasePickQty}", FALSE)
    ->where('AbsEntry', $absEntry)
    ->where('PickEntry', $pickEntry);

    return $this->db->update('pick_row');
  }


  public function set_row_status($absEntry, $orderCode, $ItemCode, $UomEntry, $status)
  {
    return $this->db
    ->set('PickStatus', $status)
    ->where('AbsEntry', $absEntry)
    ->where('OrderCode', $orderCode)
    ->where('ItemCode', $ItemCode)
    ->where('UomEntry', $UomEntry)
    ->update('pick_row');
  }



  public function cancle_pick_rows($absEntry)
  {
    return $this->db->set('PickStatus', 'D')->set('LineStatus', 'D')->where('AbsEntry', $absEntry)->update('pick_row');
  }


  public function set_rows_status($absEntry, $status)
  {
    return $this->db->set('PickStatus', $status)->where('AbsEntry', $absEntry)->update('pick_row');
  }


  public function un_close_rows($absEntry, $orderCode)
  {
    return $this->db->set('PickStatus', 'Y')->where('AbsEntry', $absEntry)->where('OrderCode', $orderCode)->update('pick_row');
  }


  public function is_exists_row($AbsEntry, $OrderEntry, $OrderLine)
  {
    $rs = $this->db
    ->where('AbsEntry', $AbsEntry)
    ->where('OrderEntry', $OrderEntry)
    ->where('OrderLine', $OrderLine)
    ->count_all_results('pick_row');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_all_closed($AbsEntry)
  {
    $row = $this->db->where('AbsEntry', $AbsEntry)->where('PickStatus !=', 'C')->count_all_results('pick_row');

    if($row > 0)
    {
      return FALSE;
    }

    return TRUE;
  }


  public function is_all_open($absEntry)
  {
    $row = $this->db->where('AbsEntry', $absEntry)->where('PickStatus', 'C')->count_all_results('pick_row');

    if($row > 0)
    {
      return FALSE;
    }

    return TRUE;
  }




  public function release($AbsEntry)
  {
    $this->db->trans_begin();
    $ds = array(
      'Status' => 'R',
      'state' => 'release'
    );

    $rs = $this->db->where('AbsEntry', $AbsEntry)->update('pick_list', $ds);
    $rd = $this->db->set('PickStatus', 'R')->where('AbsEntry', $AbsEntry)->update('pick_row');

    if($rs && $rd)
    {
      $this->db->trans_commit();
      return TRUE;
    }
    else
    {
      $this->trans_rollback();
      return FALSE;
    }
  }


  public function unrelease($AbsEntry)
  {
    $this->db->trans_begin();
    $ds = array(
      'Status' => 'N',
      'state' => 'edit'
    );

    $rs = $this->db->where('AbsEntry', $AbsEntry)->update('pick_list', $ds);
    $rd = $this->db->set('PickStatus', 'N')->where('AbsEntry', $AbsEntry)->update('pick_row');

    if($rs && $rd)
    {
      $this->db->trans_commit();
      return TRUE;
    }
    else
    {
      $this->trans_rollback();
      return FALSE;
    }
  }


  public function drop_current_rows($AbsEntry)
  {
    return $this->db->where('AbsEntry', $AbsEntry)->delete('pick_row');
  }



  public function get_state($AbsEntry)
  {
    $rs = $this->db->select('Status')->where('AbsEntry', $AbsEntry)->get('pick_list');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('DocNum')
    ->like('DocNum', $pre, 'after')
    ->order_by('DocNum', 'DESC')
    ->get('pick_list');

    return $rs->row()->DocNum;
  }


  public function count_so($absEntry)
  {
    return $this->db->where('AbsEntry', $absEntry)->group_by('OrderCode')->count_all_results('pick_row');
  }


  public function count_item_line($absEntry)
  {
    return $this->db->where('AbsEntry', $absEntry)->count_all_results('pick_row');
  }


} //---- end class

 ?>

<?php
class Transfer_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_pack_details_by_box_id($box_id)
  {
    $rs = $this->db->where('box_id', $box_id)->get('pack_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('transfer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('transfer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($transfer_id)
  {
    $rs = $this->db->where('transfer_id', $transfer_id)->get('transfer_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_code($code)
  {
    $rs = $this->db
    ->select('transfer.code, transfer_details.*, pick_list.AbsEntry AS pick_list_id')
    ->from('transfer_details')
    ->join('transfer', 'transfer_details.transfer_id = transfer.id', 'left')
    ->join('pick_list', 'pick_list.DocNum = transfer_details.pickCode', 'left')
    ->where('transfer.code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sum_order_item_details($transfer_id)
  {
    $rs = $this->db
    ->select_sum('Qty')
    ->select('pickCode, orderCode, ItemCode, UomEntry')
    ->where('transfer_id', $transfer_id)
    ->group_by(array('orderCode', 'ItemCode', 'UomEntry'))
    ->get('transfer_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }



  public function get_order_codes($transfer_id)
  {
    $rs = $this->db
    ->distinct()
    ->select('orderCode')
    ->where('transfer_id', $transfer_id)
    ->get('transfer_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('transfer', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('transfer_details', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('transfer', $ds);
  }


  public function update_by_code($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('transfer', $ds);
  }


  public function update_order_line($DocEntry, $LineNum, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->ms->where('DocEntry', $DocEntry)->where('LineNum', $LineNum)->update('RDR1', $ds);
    }

    return FALSE;
  }



  public function update_order_location($docNum, $bin_loc)
  {
    return $this->ms->set('U_locknum', $bin_loc)->where('DocNum', $docNum)->update('ORDR');
  }



  public function cancle_details($transfer_id)
  {
    return $this->db->set('is_cancle', 1)->where('transfer_id', $transfer_id)->update('transfer_details');
  }



  public function get_pick_id_by_code($pickCode)
  {
    $rs = $this->db->select('AbsEntry')->where('DocNum', $pickCode)->get('pick_list');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->AbsEntry;
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
    ->order_by('PickEntry', 'ASC')
    ->get('pick_row');

    if($rs->num_rows()> 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_order_line($DocEntry, $LineNum)
  {
    $rs = $this->ms
    ->select('DocEntry, OpenQty, U_Packed, U_PrevPacked, U_TransferCode')
    ->where('DocEntry', $DocEntry)
    ->where('LineNum', $LineNum)
    ->get('RDR1');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_pallet_items($pallet_id)
  {
    $rs = $this->db
    ->select('pack_result.*')
    ->select('pack_row.UomEntry2, pack_row.UomCode2, pack_row.unitMsr2')
    ->from('pack_result')
    ->join('pack_row', 'pack_result.packCode = pack_row.packCode AND pack_result.pickCode = pack_row.pickCode AND pack_result.orderCode = pack_row.orderCode AND pack_result.ItemCode = pack_row.ItemCode AND pack_result.UomEntry = pack_row.UomEntry', 'left')
    ->where_in('pack_row.Status', array('Y', 'C'))
    ->where('pack_result.pallet_id', $pallet_id)
    ->get();


    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->select('tr.*')
    ->from('transfer_details AS td')
    ->join('transfer AS tr', 'td.transfer_id = tr.id', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('tr.code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('td.orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('td.pickCode', $ds['pickCode']);
    }

    if(!empty($ds['packCode']))
    {
      $this->db->like('td.packCode', $ds['packCode']);
    }

    if(!empty($ds['palletCode']))
    {
      $this->db->like('tr.palletCode', $ds['palletCode']);
    }

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if($ds['Status'] != 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    return $this->db->group_by('td.transfer_id')->count_all_results();
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $this->db
    ->select('tr.*')
    ->from('transfer_details AS td')
    ->join('transfer AS tr', 'td.transfer_id = tr.id', 'left')
    ->where('tr.id IS NOT NULL', NULL, FALSE);

    if(!empty($ds['code']))
    {
      $this->db->like('tr.code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('td.orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('td.pickCode', $ds['pickCode']);
    }

    if(!empty($ds['packCode']))
    {
      $this->db->like('td.packCode', $ds['packCode']);
    }

    if(!empty($ds['palletCode']))
    {
      $this->db->like('tr.palletCode', $ds['palletCode']);
    }

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if($ds['Status'] != 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    $rs = $this->db
    ->group_by('td.transfer_id')
    ->order_by($order_by, $sort_by)
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }



  public function get_sap_transfer($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_transfer($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_WEBORDER', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_data($code) //-- web order
  {
    $rs = $this->mc
    ->select('U_WEBORDER, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEBORDER', $code)
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_status($code)
  {
    $rs = $this->mc->select('DocEntry, F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_last_temp_transfer($code)
  {
    $rs = $this->mc
    ->select('DocEntry, F_WebDate, F_Sap, F_SapDate, Message')
    ->where('U_WEBORDER', $code)
    ->order_by('DocEntry', 'DESC')
    ->limit(1)
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_transfer_temp_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('OWTR');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  public function add_sap_transfer(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_transfer_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('WTR1', $ds);
    }

    return FALSE;
  }



  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('transfer');

    return $rs->row()->code;
  }



  public function getSyncList($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array('P', 'F'))
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get('transfer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}

?>

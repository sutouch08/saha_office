<?php
class Pack_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('pack_list');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('pack_list');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }




  public function get_rows($code)
  {
    $rs = $this->db->where('packCode', $code)->get('pack_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_row($code, $itemCode)
  {
    $rs = $this->db->where('packCode', $code)->where('ItemCode', $itemCode)->get('pack_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_pack_details($code)
  {
    $rs = $this->db->where('packCode', $code)->get('pack_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_detail_by_item($code, $itemCode)
  {
    $rs = $this->db
    ->where('packCode', $code)
    ->where('ItemCode', $itemCode)
    ->get('pack_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details_by_item_other_uom($code, $itemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('packCode', $code)
    ->where('ItemCode', $itemCode)
    ->where('UomEntry !=', $UomEntry)
    ->order_by('BaseQty', 'ASC')
    ->get('pack_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pack_results($code)
  {
    $rs = $this->db
    ->select('pack_result.*')
    ->select('pack_row.ItemName, pack_row.UomEntry2, pack_row.UomCode2, pack_row.unitMsr2')
    ->from('pack_result')
    ->join('pack_row', 'pack_result.packCode = pack_row.packCode AND pack_result.OrderCode = pack_row.orderCode AND pack_result.ItemCode = pack_row.ItemCode', 'left')
    ->where('pack_result.packCode', $code)
    ->get();

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
      if($this->db->insert('pack_list', $ds))
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
      return $this->db->insert('pack_row', $ds);
    }

    return FALSE;
  }



  public function update($id, $ds)
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('pack_list', $ds);
    }

    return FALSE;
  }


  public function update_by_code($code, $ds)
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('pack_list', $ds);
    }

    return FALSE;
  }


  public function delete_pack_boxes($code)
  {
    return $this->db->where('packCode', $code)->delete('pack_box');
  }



  public function set_rows_status($code, $status)
  {
    return $this->db->set('Status', $status)->where('packCode', $code)->update('pack_row');
  }



  public function get_card_name($AbsEntry, $orderCode)
  {
    $rs = $this->db
    ->distinct()
    ->select('CardName')
    ->where('AbsEntry', $AbsEntry)
    ->where('orderCode', $orderCode)
    ->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->CardName;
    }

    return NULL;
  }



  public function get_pick_rows_by_so($pickId, $orderCode)
  {

    $rs = $this->db
    ->select('AbsEntry, OrderCode, ItemCode, ItemName, UomEntry, UomEntry2, UomCode, UomCode2, unitMsr, unitMsr2, BaseQty')
    ->select_sum('BasePickQty')
    ->where('AbsEntry', $pickId)
    ->where('OrderCode', $orderCode)
    ->where('PickStatus', 'Y')
    ->where('LineStatus', 'O')
    ->where('BasePickQty >', 0)
    ->group_by(array("OrderCode", "ItemCode", "UomEntry"))
    ->order_by('ItemCode', 'ASC')
    ->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_finish_so_list()
  {
    $this->db->distinct();
    $this->db
    ->select('orderCode')
    ->from('pick_row')
    ->join('pick_list', 'pick_row.AbsEntry = pick_list.AbsEntry', 'left')
    ->where('pick_list.Status', 'Y')
    ->where('pick_row.PickStatus', 'Y')
    ->order_by('orderCode', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_pick_list_by_so($orderCode)
  {
    $this->db->distinct();
    $rs = $this->db
    ->select('DocNum')
    ->from('pick_row')
    ->join('pick_list', 'pick_row.AbsEntry = pick_list.AbsEntry', 'left')
    ->where('pick_row.orderCode', $orderCode)
    ->where('pick_list.Status', 'Y')
    ->where('pick_row.PickStatus', 'Y')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_list($ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];


    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('pickCode', $ds['pickCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
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
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('pack_list');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows($ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('pickCode', $ds['pickCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
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
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results('pack_list');
  }



  public function get_pallet_code($packCode)
  {
    $rs = $this->db
    ->distinct()
    ->select('pallet.code')
    ->from('pack_details')
    ->join('pallet', 'pack_details.pallet_id = pallet.id', 'left')
    ->where('pack_details.packCode', $packCode)
    ->get();

    if($rs->num_rows() > 0)
    {
      $arr = array();

      foreach($rs->result() as $row)
      {
        $arr[] = $row->code;
      }

      return $arr;
    }

    return NULL;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('pack_list');

    return $rs->row()->code;
  }


  public function update_sap_pack_code($orderCode, $packCode)
  {
    return $this->ms->set('U_PA_No', $packCode)->where('DocNum', $orderCode)->update('ORDR');
  }


} //---- end class

 ?>

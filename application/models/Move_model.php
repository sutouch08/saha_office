<?php
class Move_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function  count_rows(array $ds = array())
  {
    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['fromWhsCode'] != "")
    {
      $this->db->like('fromWhsCode', $ds['fromWhsCode']);
    }

    if($ds['toWhsCode'] != "")
    {
      $this->db->like('toWhsCode', $ds['toWhsCode']);
    }

    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('Status', $ds['status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results('move');
  }




  public function  get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['fromWhsCode'] != "")
    {
      $this->db->like('fromWhsCode', $ds['fromWhsCode']);
    }

    if($ds['toWhsCode'] != "")
    {
      $this->db->like('toWhsCode', $ds['toWhsCode']);
    }

    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('Status', $ds['status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get('move');

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
      $rs = $this->db->insert('move', $ds);

      if($rs)
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('move_temp', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('move', $ds);
    }

    return FALSE;
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('move');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('move');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($move_id)
  {
    $rs = $this->db->where('move_id', $move_id)->get('move_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_temp_details($move_id)
  {
    $rs = $this->db->where('move_id', $move_id)->get('move_temp');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_qty($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select('Qty')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('BinCode', $BinCode)
    ->get('move_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Qty;
    }

    return 0;
  }


  public function get_move_qty($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select_sum('Qty')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('fromBinCode', $BinCode)
    ->where('valid', 0)
    ->get('move_details');

    return round($rs->row()->Qty, 2);
  }



  public function get_temp_id($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('BinCode', $BinCode)
    ->get('move_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }



  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("Qty", "Qty + {$qty}", FALSE)->where('id', $id)->update('move_temp');
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('move');

    return $rs->row()->code;
  }


} //--- end class
 ?>

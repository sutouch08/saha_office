<?php
class Transfer_bin_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('transfer_bin', $ds);
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('transfer_bin', $ds);
    }

    return FALSE;
  }




  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('transfer_bin');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('transfer_bin');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('transfer_bin');
  }




  public function is_exists_code($code)
  {
    $this->db->where('code', $code);
    
    $rs = $this->db->get('transfer_bin');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(isset($ds['code']) && $ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['uname']) && $ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('createDate >=', from_date($ds['fromDate']));
      $this->db->where('createDate <=', to_date($ds['toDate']));
    }

    return  $this->db->count_all_results('transfer_bin');
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if(isset($ds['code']) && $ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['uname']) && $ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('createDate >=', from_date($ds['fromDate']));
      $this->db->where('createDate <=', to_date($ds['toDate']));
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get('transfer_bin');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



} //--- end class


 ?>

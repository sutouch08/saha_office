<?php
class Approver_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('quotation_approver');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    return $this->db->insert('quotation_approver', $ds);
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('id', $id);

      return $this->db->update('quotation_approver', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('quotation_approver');
  }


  function count_rows(array $ds = array())
  {
    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['sale_team']) && $ds['sale_team'] !== 'all')
    {
      $this->db->where('sale_team', $ds['sale_team']);
    }

    if(!empty($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results('quotation_approver');
  }





  function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'uname' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['sale_team']) && $ds['sale_team'] !== 'all')
    {
      $this->db->where('sale_team', $ds['sale_team']);
    }

    if(!empty($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('quotation_approver');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //---- End class

 ?>

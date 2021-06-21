<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_team_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('sale_team');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all()
  {
    $rs = $this->db->get('sale_team');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    return $this->db->insert('sale_team', $ds);
  }


  public function update($code, $ds = array())
  {
    if(!empty($ds) && !empty($code))
    {
      return $this->db->where('code', $code)->update('sale_team', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('sale_team');
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results('sale_team');

  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('sale_team');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_code($code, $old_code = NULL)
  {
    $this->db->where('code', $code);

    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->get('sale_team');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_name($name, $old_name = NULL)
  {
    $this->db->where('name', $name);

    if(!empty($old_name))
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->get('sale_team');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('sale_team');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }

} //--- end model
 ?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver_model extends CI_Model
{
  private $tb = 'driver';

  public function __construct()
  {
    parent::__construct();
  }



  public function add($ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }



  public function update($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('emp_id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('emp_id', $id)->delete($this->tb);
  }



  public function get($id)
  {
    $rs = $this->db->where('emp_id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('emp_id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->firstName.' '.$rs->row()->lastName;
    }

    return NULL;
  }


  public function get_all(array $type = array('D', 'E'), $active = FALSE)
  {
    if($active)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->where_in('type', $type)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['emp_name']))
    {
      $this->db->like('emp_name', $ds['emp_name']);
    }

    if(!empty($ds['type']) && $ds['type'] != "all")
    {
      $this->db->where('type', $ds['type']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db->order_by('emp_name', 'ASC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['emp_name']))
    {
      $this->db->like('emp_name', $ds['emp_name']);
    }

    if(!empty($ds['type']) && $ds['type'] != "all")
    {
      $this->db->where('type', $ds['type']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_exists($name, $emp_id = NULL)
  {
    if( ! empty($emp_id))
    {
      $this->db->where('emp_id !=', $emp_id);
    }

    $count = $this->db->where('emp_name', $name)->count_all_results($this->tb);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function has_transection($emp_id)
  {
    return FALSE;
  }

} //--- end model
 ?>

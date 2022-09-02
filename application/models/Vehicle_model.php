<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_model extends CI_Model
{
  private $tb = 'vehicle';

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
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }



  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all($active = FALSE)
  {
    if($active)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->firstName.' '.$rs->row()->lastName;
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db->order_by('name', 'ASC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_exists($name)
  {
    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function has_transection($id)
  {
    return FALSE;
  }

} //--- end model
 ?>

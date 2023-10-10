<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route_model extends CI_Model
{
  private $tb = 'delivery_route';

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


  public function add_zone(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('delivery_route_detail', $ds);
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

  public function get_details($id)
  {
    $rs = $this->db->where('route_id', $id)->get('delivery_route_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_details($id)
  {
    return $this->db->where('route_id', $id)->delete('delivery_route_detail');
  }


  public function count_zone($id)
  {
    return $this->db->where('route_id', $id)->count_all_results('delivery_route_detail');
  }


  public function get_all($active = TRUE)
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
      return $rs->row()->name;
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


  public function is_exists($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_zone($id, $zone_id)
  {
    $count = $this->db->where('route_id', $id)->where('zone_id', $zone_id)->count_all_results('delivery_route_detail');

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

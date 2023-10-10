<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_zone_model extends CI_Model
{
  private $tb = 'delivery_zone';

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
    $sc = TRUE;

    $this->db->trans_begin();
    $a = $this->db->where('zone_id', $id)->delete('delivery_route_detail');
    $b = $this->db->where('id', $id)->delete($this->tb);

    if($a && $b)
    {
      $this->db->trans_commit();
    }
    else
    {
      $this->db->trans_rollback();
      $sc = FALSE;
    }

    return $sc;
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


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['zipCode']))
    {
      $this->db->like('zipCode', $ds['zipCode']);
    }

    if( ! empty($ds['district']))
    {
      $this->db->like('district', $ds['district']);
    }

    if( ! empty($ds['province']))
    {
      $this->db->like('province', $ds['province']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db->order_by('zipCode', 'ASC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['zipCode']))
    {
      $this->db->like('zipCode', $ds['zipCode']);
    }

    if( ! empty($ds['district']))
    {
      $this->db->like('district', $ds['district']);
    }

    if( ! empty($ds['province']))
    {
      $this->db->like('province', $ds['province']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_exists($district, $province, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('district', $district)->where('province', $province)->count_all_results($this->tb);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

} //--- end model
 ?>

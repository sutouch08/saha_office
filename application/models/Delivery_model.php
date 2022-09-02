<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_model extends CI_Model
{
  private $tb = 'delivery';
  private $td = 'delivery_details';
  private $te = 'delivery_employee';

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


  public function add_delivery_employee($ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->te, $ds);
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


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($code)
  {
    $rs = $this->db->where('delivery_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_delivery_employee($type = 'A', $delivery_code)
  {
    if($type != 'A' && ($type == 'E' OR $type == 'D'))
    {
      $this->db->where('type', $type);
    }

    $rs = $this->db->where('delivery_code', $delivery_code)->get($this->te);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['driver']) && $ds['driver'] != 'all')
    {
      $this->db->where('driver_id', $ds['driver']);
    }

    if( ! empty($ds['vehicle']) && $ds['vehicle'] != 'all')
    {
      $this->db->where('vehicle_id', $ds['vehicle']);
    }

    if( ! empty($ds['route']) && $ds['route'] != 'all')
    {
      $this->db->where('route_id', $ds['route']);
    }

    if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    if( ! empty($ds['uname']) && $ds['uname'] != 'all')
    {
      $this->db->where('uname', $ds['uname']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if(isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['driver']) && $ds['driver'] != 'all')
    {
      $this->db->where('driver_id', $ds['driver']);
    }

    if( ! empty($ds['vehicle']) && $ds['vehicle'] != 'all')
    {
      $this->db->where('vehicle_id', $ds['vehicle']);
    }

    if( ! empty($ds['route']) && $ds['route'] != 'all')
    {
      $this->db->where('route_id', $ds['route']);
    }

    if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    if( ! empty($ds['uname']) && $ds['uname'] != 'all')
    {
      $this->db->where('uname', $ds['uname']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }

} //--- end model
 ?>

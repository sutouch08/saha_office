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


  public function add_detail(array $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->insert($this->td, $ds);
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


  public function drop_delivery_employee($code)
  {
    return $this->db->where('delivery_code', $code)->delete($this->te);
  }


  public function release_order($code)
  {
    $arr = array(
      'status'=> 'R',
      'ReleaseDate' => now()
    );

    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function release_details($code)
  {
    $arr = array(
      'line_status' => 'R',
      'release_date' => date('Y-m-d')
    );

    return $this->db->where('delivery_code', $code)->update($this->td, $arr);
  }


  public function un_release_order($code)
  {
    $arr = array(
      'status'=> 'O',
      'ReleaseDate' => NULL
    );

    return $this->db->where('code', $code)->update($this->tb, $arr);
  }


  public function un_release_details($code)
  {
    $arr = array(
      'line_status' => 'O',
      'release_date' => NULL
    );

    return $this->db->where('delivery_code', $code)->update($this->td, $arr);
  }



  public function update($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($code) && ! empty($ds))
    {
      return $this->db->where('delivery_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function drop_details($code)
  {
    return $this->db->where('delivery_code', $code)->delete($this->td);
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


  public function get_finish_details($code)
  {
    $rs = $this->db
    ->where('delivery_code', $code)
    ->where('line_status', 'C')
    ->where_in('type', array('D', 'P'))
    ->where('DocType IS NOT NULL', NULL, FALSE)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function finish_iv_doc_num($docNum, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->ms->where('DocNum', $docNum)->update('OINV', $ds);
    }

    return FALSE;
  }


  public function finish_do_doc_num($docNum, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->ms->where('DocNum', $docNum)->update('ODLN', $ds);
    }

    return FALSE;
  }


  public function finish_cn_doc_num($docNum, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->ms->where('DocNum', $docNum)->update('ORDN', $ds);
    }

    return FALSE;
  }


  public function finish_pb_doc_num($docNum, $ds = array())
  {
    return TRUE;

    if( ! empty($ds))
    {
      return $this->ms->where('DocNum', $docNum)->update('OINV', $ds);
    }

    return FALSE;
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

    if( ! empty($ds['shipFromDate']) && ! empty($ds['shipToDate']))
    {
      $this->db
      ->where('ShipDate >=', from_date($ds['shipFromDate']))
      ->where('ShipDate <=', to_date($ds['shipToDate']));
    }

    if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
    {
      $this->db
      ->where('DocDate >=', from_date($ds['fromDate']))
      ->where('DocDate <=', to_date($ds['toDate']));
    }

    if($ds['uname'] != '')
    {
      $this->db->like('uname', $ds['uname']);
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

    if( ! empty($ds['shipFromDate']) && ! empty($ds['shipToDate']))
    {
      $this->db
      ->where('ShipDate >=', from_date($ds['shipFromDate']))
      ->where('ShipDate <=', to_date($ds['shipToDate']));
    }

    if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
    {
      $this->db
      ->where('DocDate >=', from_date($ds['fromDate']))
      ->where('DocDate <=', to_date($ds['toDate']));
    }

    if($ds['uname'] != '')
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_loaded($docNum, $docType, $delivery_code = NULL)
  {
    if( ! empty($delivery_code))
    {
      $this->db->where('delivery_code !=', $delivery_code);
    }

    $count = $this->db
    ->where('type', 'P')
    ->where('DocType', $docType)
    ->where('line_status !=', 'D')
    ->where('result_status', 1)
    ->where('DocNum', $docNum)
    ->count_all_results($this->td);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
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


  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('delivery_logs', $ds);
    }

    return FALSE;
  }


  public function get_logs($code)
  {
    $rs = $this->db->where('code', $code)->get('delivery_logs');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end model
 ?>

<?php
class Delivery_details_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('delivery_details AS d')
    ->join('delivery AS o', 'd.delivery_code = o.code', 'left');

    if($ds['delivery_code'] != '')
    {
      $this->db->like('d.delivery_code', $ds['delivery_code']);
    }

    if($ds['driver_id'] != 'all')
    {
      $this->db->where('o.driver_id', $ds['driver_id']);
    }

    if($ds['vehicle_id'] != 'all')
    {
      $this->db->where('o.vehicle_id', $ds['vehicle_id']);
    }

    if($ds['route_id'] != 'all')
    {
      $this->db->where('o.route_id', $ds['route_id']);
    }

    if($ds['CardCode'] != '')
    {
      $this->db->like('d.CardCode', $ds['CardCode']);
    }

    if($ds['CardName'] != '')
    {
      $this->db->like('d.CardName', $ds['CardName']);
    }

    if($ds['contact'] != '')
    {
      $this->db->like('d.contact', $ds['contact']);
    }

    if($ds['type'] != 'all')
    {
      $this->db->where('type', $ds['type']);
    }

    if($ds['DocType'] != 'all')
    {
      if($ds['DocType'] == 'NULL')
      {
        $this->db->where('d.DocType IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('d.DocType', $ds['DocType']);
      }
    }

    if($ds['DocNum'] != '')
    {
      $this->db->like('d.DocNum', $ds['DocNum']);
    }

    if($ds['result_status'] != 'all')
    {
      $this->db->where('d.result_status', $ds['result_status']);
    }

    if($ds['line_status'] != 'all')
    {
      $this->db->where('d.line_status', $ds['line_status']);
    }

    if($ds['release_from'] != '' && $ds['release_to'] != '')
    {
      $this->db
      ->group_start()
      ->where('d.release_date >=', from_date($ds['release_from']))
      ->where('d.release_date <=', to_date($ds['release_to']))
      ->group_end();
    }

    if($ds['finish_from'] != '' && $ds['finish_to'] != '')
    {
      $this->db
      ->group_start()
      ->where('d.finish_date >=', from_date($ds['finish_from']))
      ->where('d.finish_date <=', to_date($ds['finish_to']))
      ->group_end();
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db
      ->group_start()
      ->where('o.date_add >=', from_date($ds['form_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->group_end();
    }

    if($ds['uname'] != '')
    {
      $this->db->like('o.uname', $ds['uname']);
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('d.*, o.driver_name, o.vehicle_name, o.route_name, o.date_add AS doc_date, o.uname')
    ->from('delivery_details AS d')
    ->join('delivery AS o', 'd.delivery_code = o.code', 'left');

    if($ds['delivery_code'] != '')
    {
      $this->db->like('d.delivery_code', $ds['delivery_code']);
    }

    if($ds['driver_id'] != 'all')
    {
      $this->db->where('o.driver_id', $ds['driver_id']);
    }

    if($ds['vehicle_id'] != 'all')
    {
      $this->db->where('o.vehicle_id', $ds['vehicle_id']);
    }

    if($ds['route_id'] != 'all')
    {
      $this->db->where('o.route_id', $ds['route_id']);
    }

    if($ds['CardCode'] != '')
    {
      $this->db->like('d.CardCode', $ds['CardCode']);
    }

    if($ds['CardName'] != '')
    {
      $this->db->like('d.CardName', $ds['CardName']);
    }

    if($ds['contact'] != '')
    {
      $this->db->like('d.contact', $ds['contact']);
    }

    if($ds['type'] != 'all')
    {
      $this->db->where('type', $ds['type']);
    }

    if($ds['DocType'] != 'all')
    {
      if($ds['DocType'] == 'NULL')
      {
        $this->db->where('d.DocType IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('d.DocType', $ds['DocType']);
      }
    }

    if($ds['DocNum'] != '')
    {
      $this->db->like('d.DocNum', $ds['DocNum']);
    }

    if($ds['result_status'] != 'all')
    {
      $this->db->where('d.result_status', $ds['result_status']);
    }

    if($ds['line_status'] != 'all')
    {
      $this->db->where('d.line_status', $ds['line_status']);
    }

    if($ds['release_from'] != '' && $ds['release_to'] != '')
    {
      $this->db
      ->group_start()
      ->where('d.release_date >=', from_date($ds['release_from']))
      ->where('d.release_date <=', to_date($ds['release_to']))
      ->group_end();
    }

    if($ds['finish_from'] != '' && $ds['finish_to'] != '')
    {
      $this->db
      ->group_start()
      ->where('d.finish_date >=', from_date($ds['finish_from']))
      ->where('d.finish_date <=', to_date($ds['finish_to']))
      ->group_end();
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db
      ->group_start()
      ->where('o.date_add >=', from_date($ds['form_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->group_end();
    }

    if($ds['uname'] != '')
    {
      $this->db->like('o.uname', $ds['uname']);
    }

    $rs = $this->db->order_by('d.delivery_code')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>

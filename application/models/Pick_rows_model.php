<?php
class Pick_rows_model extends CI_Model
{
  /* tables
  ** pick_list => OPKL
  ** pick_row => PKL1
  ** pick_detail => PKL2 (with bin detail)
  ** ORDR for order
  ** RDR1 for order details
  */

  public function __construct()
  {
    parent::__construct();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if(!empty($ds['DocNum']))
    {
      $this->db->like('code', $ds['DocNum']);
    }

    if(!empty($ds['OrderCode']))
    {
      $this->db->like('OrderCode', $ds['OrderCode']);
    }

    if(!empty($ds['ItemCode']))
    {
      $this->db
      ->group_start()
      ->like('ItemCode', $ds['ItemCode'])
      ->or_like('ItemName', $ds['ItemCode'])
      ->group_end();
    }

    if(isset($ds['PickStatus']) && $ds['PickStatus'] != 'all')
    {
      $this->db->where('PickStatus', $ds['PickStatus']);
    }

    if(isset($ds['LineStatus']) && $ds['LineStatus'] != 'all')
    {
      $this->db->where('LineStatus', $ds['LineStatus']);
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get('pick_row');

    if($rs->num_rows() > 0)
    {
      return  $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['DocNum']))
    {
      $this->db->like('code', $ds['DocNum']);
    }

    if(!empty($ds['OrderCode']))
    {
      $this->db->like('OrderCode', $ds['OrderCode']);
    }

    if(!empty($ds['ItemCode']))
    {
      $this->db
      ->group_start()
      ->like('ItemCode', $ds['ItemCode'])
      ->or_like('ItemName', $ds['ItemCode'])
      ->group_end();
    }

    if(isset($ds['PickStatus']) && $ds['PickStatus'] != 'all')
    {
      $this->db->where('PickStatus', $ds['PickStatus']);
    }

    if(isset($ds['LineStatus']) && $ds['LineStatus'] != 'all')
    {
      $this->db->where('LineStatus', $ds['LineStatus']);
    }

    return $this->db->count_all_results('pick_row');
  }


} //---- end class

 ?>

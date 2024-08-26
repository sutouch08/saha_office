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
    $order_by = empty($ds['order_by']) ? 'DocNum' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $this->db
    ->select('o.DocNum')
    ->select('r.*')
    ->from('pick_row AS r')
    ->join('pick_list AS o', 'r.AbsEntry = o.AbsEntry', 'left');

    if(!empty($ds['DocNum']))
    {
      $this->db->like('o.DocNum', $ds['DocNum']);
    }

    if(!empty($ds['OrderCode']))
    {
      $this->db->like('r.OrderCode', $ds['OrderCode']);
    }

    if(!empty($ds['ItemCode']))
    {
      $this->db
      ->group_start()
      ->like('r.ItemCode', $ds['ItemCode'])
      ->or_like('r.ItemName', $ds['ItemCode'])
      ->group_end();
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return  $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('pick_row AS r')
    ->join('pick_list AS o', 'r.AbsEntry = o.AbsEntry');

    if(!empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
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

    return $this->db->count_all_results();
  }


} //---- end class

 ?>

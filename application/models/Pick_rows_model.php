<?php
class Pick_rows_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('pl.DocNum, pl.CreateDate, pl.Status, pl.state, pl.uname')
    ->select('pr.*')
    ->from('pick_row AS pr')
    ->join('pick_list AS pl', 'pr.AbsEntry = pl.AbsEntry', 'left');

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('pl.DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['OrderCode']))
    {
      $this->db->like('pr.OrderCode', $ds['OrderCode']);
    }

    if( ! empty($ds['ItemCode']))
    {
      $this->db
      ->group_start()
      ->like('pr.ItemCode', $ds['ItemCode'])
      ->or_like('pr.ItemName', $ds['ItemCode'])
      ->group_end();
    }


    if( ! empty($ds['Status']) && $ds['Status'] != 'all')
    {
      $this->db->where('pr.PickStatus', $ds['Status']);
    }


    if( ! empty($ds['LineStatus']) && $ds['LineStatus'] != 'all')
    {
      $this->db->where('pr.LineStatus', $ds['LineStatus']);
    }

    if( ! empty($ds['uname']))
    {
      $this->db->like('pl.uname', $ds['uname']);
    }



    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('pl.CreateDate >=', from_date($ds['fromDate']));
      $this->db->where('pl.CreateDate <=', to_date($ds['toDate']));
    }

    $rs = $this->db->order_by('pl.DocNum', 'DESC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return  $rs->result();
    }


    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('pick_row AS pr')
    ->join('pick_list AS pl', 'pr.AbsEntry = pl.AbsEntry', 'left');

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('pl.DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['OrderCode']))
    {
      $this->db->like('pr.OrderCode', $ds['OrderCode']);
    }

    if( ! empty($ds['ItemCode']))
    {
      $this->db
      ->group_start()
      ->like('pr.ItemCode', $ds['ItemCode'])
      ->or_like('pr.ItemName', $ds['ItemCode'])
      ->group_end();
    }

    if( ! empty($ds['Status']) && $ds['Status'] != 'all')
    {
      $this->db->where('pr.PickStatus', $ds['Status']);
    }


    if( ! empty($ds['LineStatus']) && $ds['LineStatus'] != 'all')
    {
      $this->db->where('pr.LineStatus', $ds['LineStatus']);
    }

    if( ! empty($ds['uname']))
    {
      $this->db->like('pl.uname', $ds['uname']);
    }



    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('pl.CreateDate >=', from_date($ds['fromDate']));
      $this->db->where('pl.CreateDate <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results();
  }


} //---- end class

 ?>

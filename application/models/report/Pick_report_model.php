<?php
class Pick_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data($allUser = TRUE, $users = array(), $selectDate = 'Finish', $fromDate = NULL, $toDate = NULL)
  {
    $fromDate = is_null($fromDate) ? date('Y-m-01') : from_date($fromDate);
    $toDate = is_null($toDate) ? date('Y-m-t') : to_date($toDate);

    $this->db
    ->select('o.CreateDate, o.StartPick, o.FinishPick')
    ->select('p.*')
    ->from('pick_details AS p')
    ->join('pick_list AS o', 'p.AbsEntry = o.AbsEntry', 'left')
    ->where_in('o.Status', array('Y', 'C'));

    if( ! $allUser && ! empty($users))
    {
      $this->db->where_in('p.user_id', $users);
    }

    if($selectDate == 'SO')
    {
      $this->db->group_start();
      $this->db->where('p.OrderDate >=', $fromDate)->where('p.OrderDate <=', $toDate);
      $this->db->group_end();
    }

    if($selectDate == 'Finish')
    {
      $this->db->group_start();
      $this->db->where('o.FinishPick >=', $fromDate)->where('o.FinishPick <=', $toDate);
      $this->db->group_end();
    }

    if($selectDate == 'DocDate')
    {
      $this->db->group_start();
      $this->db->where('o.CreateDate >=', $fromDate)->where('o.CreateDate <=', $toDate);
      $this->db->group_end();
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_users_in($users)
  {
    $rs = $this->db->select('uname')->where_in('id', $users)->get('user');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}

 ?>

<?php
class Cancle_model extends CI_Model
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


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('cancle');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('cancle', $ds);
    }

    return FALSE;
  }



  public function update($id, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('id', $id)->update('cancle', $ds);
    }

    return FALSE;

  }

  

  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('cancle');
  }



  public function delete_selected(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete('cancle');
    }

    return FALSE;
  }


  public function get_items_list($itemCode)
  {
    $rs = $this->db->where('ItemCode', $itemCode)->get('cancle');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'DocNum' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

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

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }


    if(!empty($ds['BinCode']))
    {
      $this->db->like('BinCode', $ds['BinCode']);
    }


    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('date_upd >=', from_date($ds['fromDate']));
      $this->db->where('date_upd <=', to_date($ds['toDate']));
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get('cancle');

    if($rs->num_rows() > 0)
    {
      return  $rs->result();
    }


    return NULL;
  }



  public function count_rows(array $ds = array())
  {
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

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }


    if(!empty($ds['BinCode']))
    {
      $this->db->like('BinCode', $ds['BinCode']);
    }


    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('date_upd >=', from_date($ds['fromDate']));
      $this->db->where('date_upd <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results('cancle');
  }


} //---- end class

 ?>

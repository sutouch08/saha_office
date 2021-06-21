<?php
class Discount_rule_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('approve_rule');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    return $this->db->insert('approve_rule', $ds);
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('id', $id);

      return $this->db->update('approve_rule', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('approve_rule');
  }


  function count_rows(array $ds = array())
  {
    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['sale_team']) && $ds['sale_team'] !== 'all')
    {
      $this->db->where('sale_team', $ds['sale_team']);
    }

    if(!empty($ds['min_disc']))
    {
      $this->db->where('min_disc >=', $ds['min_disc']);
    }

    if(!empty($ds['max_disc']))
    {
      $this->db->where('min_disc <=', $ds['max_disc']);
    }

    if($ds['active'] !== 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results('approve_rule');
  }





  function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'approve_rule.id' : 'approve_rule.'.$ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $this->db
    ->select('approve_rule.*, sale_team.name AS sale_team_name')
    ->from('approve_rule')
    ->join('sale_team', 'approve_rule.sale_team = sale_team.code', 'left');

    if(!empty($ds['name']))
    {
      $this->db->like('approve_rule.name', $ds['name']);
    }

    if(!empty($ds['sale_team']) && $ds['sale_team'] !== 'all')
    {
      $this->db->where('approve_rule.sale_team', $ds['sale_team']);
    }

    if(!empty($ds['min_disc']))
    {
      $this->db->where('approve_rule.min_disc >=', $ds['min_disc']);
    }

    if(!empty($ds['max_disc']))
    {
      $this->db->where('approve_rule.min_disc <=', $ds['max_disc']);
    }

    if($ds['active'] !== 'all')
    {
      $this->db->where('approve_rule.active', $ds['active']);
    }


    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //---- End class

 ?>

<?php
class Pallet_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get($id) {
    $rs = $this->db->where('id', $id)->get('pallet');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_pallet_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get('pallet');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_pallet_list($code)
  {
    $rs = $this->db
    ->select('pl.id, pl.code')
    ->select('pp.PackCode')
    ->from('pallet_row AS pp')
    ->join('pallet AS pl', 'pp.pallet_id = pl.id', 'left')
    ->where('pp.PackCode', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pallet_row($pallet_id, $packCode)
  {
    $rs = $this->db
    ->where('pallet_id', $pallet_id)
    ->where('PackCode', $packCode)
    ->get('pallet_row');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_pack_list_by_pallet($pallet_id)
  {
    $rs = $this->db->where('pallet_id', $pallet_id)->get('pallet_row');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add($code)
  {
    $arr = array('code' => $code);

    if($this->db->insert('pallet', $arr))
    {
      return $this->db->insert_id();
    }

    return FALSE;
  }



  public function add_row(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('pallet_row', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function update($id, $arr)
  {
    return $this->db->where('id', $id)->update('pallet', $arr);
  }


  public function update_by_code($code, $arr)
  {
    return $this->db->where('code', $code)->update('pallet', $arr);
  }


  public function delete_row($pallet_id, $packCode)
  {
    return $this->db->where('pallet_id', $pallet_id)->where('PackCode', $packCode)->delete('pallet_row');
  }


  public function delete_pack_rows($packCode)
  {
    return $this->db->where('PackCode', $packCode)->delete('pallet_row');
  }



  public function count_box($id)
  {
    return $this->db->where('pallet_id', $id)->count_all_results('pack_box');
  }




  public function get_selected_pallet($arr)
  {
    $rs = $this->db->where_in('id', $arr)->get('pallet');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('pallet');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


} //---- end class

 ?>

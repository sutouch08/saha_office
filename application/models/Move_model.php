<?php
class Move_model extends CI_Model
{
  private $tb = "move";
  private $td = "move_details";
  private $temp = "move_temp";

  public function __construct()
  {
    parent::__construct();
  }


  public function  count_rows(array $ds = array())
  {
    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['fromWhsCode'] != "")
    {
      $this->db->like('fromWhsCode', $ds['fromWhsCode']);
    }

    if($ds['toWhsCode'] != "")
    {
      $this->db->like('toWhsCode', $ds['toWhsCode']);
    }

    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('Status', $ds['status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results($this->tb);
  }




  public function  get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['fromWhsCode'] != "")
    {
      $this->db->like('fromWhsCode', $ds['fromWhsCode']);
    }

    if($ds['toWhsCode'] != "")
    {
      $this->db->like('toWhsCode', $ds['toWhsCode']);
    }

    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('Status', $ds['status']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=', to_date($ds['toDate']));
    }

    $rs = $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->db->insert($this->tb, $ds);

      if($rs)
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }



  public function add_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->temp, $ds);
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }



  public function update_by_code($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update($this->tb, $ds);
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }



  public function delete_temp($id)
  {
    return $this->db->where('id', $id)->delete($this->temp);
  }




  public function delete_all_temp($move_id)
  {
    return $this->db->where('move_id', $move_id)->delete($this->temp);
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_code($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($move_id)
  {
    $rs = $this->db->where('move_id', $move_id)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_temp_details($move_id)
  {
    $rs = $this->db->where('move_id', $move_id)->get($this->temp);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_temp_qty($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select('Qty')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('BinCode', $BinCode)
    ->get($this->temp);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Qty;
    }

    return 0;
  }



  public function get_temp_product($move_id, $ItemCode)
  {
    $rs = $this->db
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->get($this->temp);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_move_qty($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select_sum('Qty')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('fromBinCode', $BinCode)
    ->where('valid', 0)
    ->get($this->td);

    return round($rs->row()->Qty, 2);
  }



  public function get_detail_id($move_id, $ItemCode, $fromBinCode, $toBinCode)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('fromBinCode', $fromBinCode)
    ->where('toBinCode', $toBinCode)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }



  public function get_temp_id($move_id, $ItemCode, $BinCode)
  {
    $rs = $this->db
    ->select('id')
    ->where('move_id', $move_id)
    ->where('ItemCode', $ItemCode)
    ->where('BinCode', $BinCode)
    ->get($this->temp);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }



  public function update_move_qty($id, $qty)
  {
    return $this->db->set("Qty", "Qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }



  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("Qty", "Qty + {$qty}", FALSE)->where('id', $id)->update($this->temp);
  }



  public function valid_details($move_id, $valid)
  {
    return $this->db->set('valid', $valid)->where('move_id', $move_id)->update($this->td);
  }



  public function valid_details_by_code($code, $valid)
  {
    return $this->db->set('valid', $valid)->where('move_code', $code)->update($this->td);
  }



  public function is_exists_temp($move_id)
  {
    $count = $this->db->where('move_id', $move_id)->count_all_results($this->temp);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function drop_zero_temp()
  {
    return $this->db->where('Qty <', 1, FALSE)->delete($this->temp);
  }



  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }




  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }




  public function get_sap_transfer($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_WEBORDER', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_temp_transfer($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_WEBORDER', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_temp_data($code) //-- web order
  {
    $rs = $this->mc
    ->select('U_WEBORDER, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEBORDER', $code)
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_temp_status($code)
  {
    $rs = $this->mc->select('DocEntry, F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_transfer_temp_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('OWTR');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }



  public function add_sap_transfer(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }



  public function add_sap_transfer_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('WTR1', $ds);
    }

    return FALSE;
  }



  public function getSyncList($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array('P', 'F'))
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //--- end class
 ?>

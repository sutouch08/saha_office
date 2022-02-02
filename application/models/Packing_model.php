<?php
class Packing_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_box_list($code)
  {
    $rs = $this->db
    ->select('pack_box.id, pack_box.box_no')
    ->select_sum('pack_details.qty')
    ->from('pack_box')
    ->join('pack_details', 'pack_box.id = pack_details.box_id AND pack_box.packCode = pack_details.packCode', 'left')
    ->where('pack_box.packCode', $code)
    ->group_by('pack_box.id')
    ->order_by('box_no', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function update_box($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('pack_box', $ds);
    }

    return FALSE;
  }



  public function get_pack_boxes($code)
  {
    $rs = $this->db->where('packCode', $code)->order_by('id', 'ASC')->get('pack_box');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_box($code, $barcode)
  {
    $rs = $this->db
    ->where('packCode', $code)
    ->where('code', $barcode)
    ->get('pack_box');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_box_by_id($box_id)
  {
    $rs = $this->db
    ->where('id', $box_id)
    ->get('pack_box');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_selected_boxes($arr)
  {
    $rs = $this->db->where_in('id', $arr)->get('pack_box');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pack_box_details($packCode, $box_id)
  {
    $rs = $this->db->where('packCode', $packCode)->where('box_id', $box_id)->get('pack_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_last_box_no($code)
  {
    $rs = $this->db
    ->select_max('box_no', 'box_no')
    ->where('packCode', $code)
    ->get('pack_box');

    return intval($rs->row()->box_no);
  }


  public function delete_box($box_id)
  {
    return $this->db->where('id', $box_id)->delete('pack_box');
  }



  public function add_new_box($code, $barcode, $box_no)
  {
    $arr = array(
      'code' => $barcode,
      'packCode' => $code,
      'box_no' => $box_no
    );

    $rs = $this->db->insert('pack_box', $arr);

    if($rs)
    {
      return $this->db->insert_id();
    }

    return FALSE;
  }


  public function get_pack_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('pack_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function update_pack_details(array $ds = array())
  {
    if(!empty($ds))
    {
      $id = $this->get_pack_detail_id($ds['packCode'], $ds['ItemCode'], $ds['box_id']);

      if($id)
      {
        return $this->db
        ->set("qty", "qty + {$ds['qty']}", FALSE)
        ->set("BasePackQty", "BasePackQty + {$ds['BasePackQty']}", FALSE)
        ->where("id", $id)
        ->update("pack_details");
      }
      else
      {
        return $this->db->insert('pack_details', $ds);
      }
    }

    return FALSE;

  }


  public function update_pack_row($id, $PackQtty, $BasePackQty)
  {
    return $this->db
    ->set("PackQtty", "PackQtty + {$PackQtty}", FALSE)
    ->set("BasePackQty", "BasePackQty + {$BasePackQty}", FALSE)
    ->where('id', $id)
    ->update('pack_row');
  }



  public function delete_pack_detail($id)
  {
    return $this->db->where('id', $id)->delete('pack_details');
  }


  public function get_pack_detail_id($packCode, $itemCode, $box_id)
  {
    $rs = $this->db
    ->select('id')
    ->where('packCode', $packCode)
    ->where('ItemCode', $itemCode)
    ->where('box_id', $box_id)
    ->get('pack_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }



  public function get_buffer_uom($DocNum, $orderCode, $ItemCode, $UomEntry)
  {
    $rs = $this->db
    ->where('DocNum', $DocNum)
    ->where('orderCode', $orderCode)
    ->where('ItemCode', $ItemCode)
    ->where('UomEntry', $UomEntry)
    ->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_buffer($id, $Qty, $BasePickQty)
  {
    return $this->db
    ->set("Qty", "Qty - {$Qty}", FALSE)
    ->set("BasePickQty", "BasePickQty - {$BasePickQty}", FALSE)
    ->where('id', $id)
    ->update('buffer');
  }



  public function drop_buffer($id)
  {
    return $this->db->where('id', $id)->delete('buffer');
  }



  public function add_result(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('pack_result', $ds);
    }

    return FALSE;
  }


  public function get_list($ds = array(), $perpage = 20, $offset = 0, $status = 'N')
  {
    $order_by = empty($ds['order_by']) ? 'code' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $this->db->where('Status', $status);

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('pickCode', $ds['pickCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
    }

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }

    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    $this->db->order_by($order_by, $sort_by)->limit($perpage, $offset);

    $rs = $this->db->get('pack_list');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows($ds = array(), $status = 'N')
  {
    $this->db->where('Status', $status);

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['orderCode']))
    {
      $this->db->like('orderCode', $ds['orderCode']);
    }

    if(!empty($ds['pickCode']))
    {
      $this->db->like('pickCode', $ds['pickCode']);
    }

    if(!empty($ds['CardName']))
    {
      $this->db->like('CardName', $ds['CardName']);
    }

    if(!empty($ds['uname']))
    {
      $this->db->like('uname', $ds['uname']);
    }


    if(!empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db
      ->where('date_add >=', from_date($ds['fromDate']))
      ->where('date_add <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results('pack_list');
  }


} //---- end class

 ?>

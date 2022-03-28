<?php
class Warehouse_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_warehouse_list()
  {
    $rs = $this->ms
    ->select('WhsCode AS code, WhsName AS name')
    ->order_by('WhsCode', 'ASC')
    ->get('OWHS');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_warehouse_code($binCode)
  {
    $rs = $this->ms->select('WhsCode')->where('BinCode', $binCode)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->WhsCode;
    }

    return NULL;
  }


  public function is_exists_bin_code($whsCode, $binCode)
  {
    $rs = $this->ms->select('AbsEntry')->where('WhsCode', $whsCode)->where('BinCode', $binCode)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_warehouse($whsCode)
  {
    $rs = $this->ms->select('WhsCode')->where('WhsCode', $whsCode)->get('OWHS');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }

}
?>

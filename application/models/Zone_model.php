<?php
class Zone_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function is_exists_bin_code($binCode)
  {
    $rs = $this->ms->select('AbsEntry')->where('BinCode', $binCode)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function getName($binCode)
  {
    $rs = $this->ms->select('SL1Code AS BinName')->where('BinCode', $binCode)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->BinName;
    }

    return NULL;
  }

}

?>

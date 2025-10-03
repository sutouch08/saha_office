<?php
function select_warehouse($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('warehouse_model');
  $list = $ci->warehouse_model->get_warehouse_list();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_zone($binCode = NULL, $WhsCode = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('zone_model');

  $list = $ci->zone_model->getBinList($binCode, $WhsCode);

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" data-whs="'.$rs->warehouse_code.'" data-name="'.$rs->name.'" '.is_selected($rs->code, $binCode).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $ds;
}

 ?>

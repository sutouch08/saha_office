<?php

function select_sales_team($code = NULL)
{
  $CI =& get_instance();
  $CI->load->model('sales_team_model');
  $result = $CI->sales_team_model->get_all();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}
 ?>

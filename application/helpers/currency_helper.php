<?php

function select_currency($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('tool_model');
  $options = $CI->tool_model->get_currency();

  if(!empty($options))
  {
    $df = getConfig('CURRENCY');
    foreach($options as $rs)
    {
      if($code === NULL)
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($df, $rs->code).'>'.$rs->code.'</option>';
      }
      else
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
      }
    }
  }

  return $sc;
}


function select_country($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('tool_model');
  $options = $CI->tool_model->get_country_list();

  if(!empty($options))
  {
    $df = getConfig('COUNTRY');
    foreach($options as $rs)
    {
      if($code === NULL)
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($df, $rs->code).'>'.$rs->name.'</option>';
      }
      else
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
      }
    }
  }

  return $sc;
}


 ?>

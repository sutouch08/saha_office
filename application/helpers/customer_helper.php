<?php
function select_GroupCode($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $options = $CI->customers_model->getGroupCode(); //--- OCRG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_GroupNum($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $options = $CI->customers_model->getGroupNum(); //--- OCTG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_DebPayAcct($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $options = $CI->customers_model->getDebPayAcct(); //--- OCRG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' => '.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_sale($code='')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $options = $CI->customers_model->getSlp();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function customer_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $rs = $CI->customers_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc[] = $cs->code;
    }
  }

  return $sc;
}


function select_customer_prefix($prefix = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('customers_model');
  $options = $CI->customers_model->get_prefix();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->prefix.'" '.is_selected($prefix, $rs->prefix).'>'.$rs->prefix.'</option>';
    }
  }

  return $sc;
}


function select_industry($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('customers_model');
  $options = $ci->customers_model->get_industry();
  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_bp_type($code = NULL)
{
  $sc = '';
  $arr = array(
    'C' => 'Company',
    'I' => 'Private',
    'G' => 'Government',
    'E' => 'Employee'
  );

  foreach($arr as $key => $val)
  {
    $sc .= '<option value="'.$key.'" '.is_selected($code, $key).'>'.$val.'</option>';
  }

  return $sc;
}


function select_territory($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('customers_model');
  $options = $ci->customers_model->get_territory();
  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_price_list($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('customers_model');
  $options = $ci->customers_model->get_price_list();
  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_customer_level($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('customers_model');
  $options = $ci->customers_model->get_customer_level_list();
  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
    }
  }

  return $sc;
}


function select_indicator($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('customers_model');
  $options = $ci->customers_model->get_customer_indicator();
  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->Code.'" '.is_selected($code, $rs->Code).'>'.$rs->Name.'</option>';
    }
  }

  return $sc;
}



 ?>

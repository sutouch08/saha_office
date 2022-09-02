<?php

function select_driver($type = array('D', 'E'), $id = NULL, $active = FALSE)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('driver_model');

  if( ! is_array($type))
  {
    $type = array($type);
  }

  $option = $ci->driver_model->get_all($type, $active);

  if( ! empty($option))
  {
    foreach($option as $rs)
    {
      $sc .= "<option value='{$rs->emp_id}' ".is_selected($id, $rs->emp_id).">{$rs->emp_name}</option>";
    }
  }

  return $sc;

}



function select_vehicle($id = NULL, $active = FALSE)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('vehicle_model');
 
  $option = $ci->vehicle_model->get_all($active);

  if( ! empty($option))
  {
    foreach($option as $rs)
    {
      $sc .= "<option value='{$rs->id}' ".is_selected($id, $rs->id).">{$rs->name}</option>";
    }
  }

  return $sc;
}


function select_route($id = NULL, $active = FALSE)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('route_model');

  $option = $ci->route_model->get_all($active);

  if( ! empty($option))
  {
    foreach($option as $rs)
    {
      $sc .= "<option value='{$rs->id}' ".is_selected($id, $rs->id).">{$rs->name}</option>";
    }
  }

  return $sc;
}


function get_delivery_employee_name($type = 'E', $code)
{
  $name = "";

  $ci =& get_instance();
  $ci->load->model('delivery_model');

  $emp = $ci->delivery_model->get_delivery_employee($type, $code);

  if( ! empty($emp))
  {
    $i = 1;

    foreach($emp as $rs)
    {
      $name .= $i == 1 ? $rs->emp_name : ", ".$rs->emp_name;
      $i++;
    }
  }

  return $name;
}

 ?>

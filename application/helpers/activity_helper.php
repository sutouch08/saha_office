<?php
function select_subject($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');
  $type = empty($code) ? NULL : ($code === 'all' ? NULL : $ci->activity_model->get_subject_type($code));

  $qs = $ci->activity_model->get_subject($type);

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
    }
  }

  return $sc;
}



function select_type($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');

  $qs = $ci->activity_model->get_type();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
    }
  }

  return $sc;
}

function select_project($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');

  $qs = $ci->activity_model->get_project();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
    }
  }

  return $sc;
}


function select_contact_person($code = 0)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');

  $CardCode = empty($code) ? NULL : $ci->activity_model->get_card_code($code);

  if(!empty($CardCode))
  {
    $qs = $ci->activity_model->get_contact_by_card_code($CardCode);

    if(!empty($qs))
    {
      foreach($qs as $rs)
      {
        $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
      }
    }
  }

  return $sc;
}


function select_location($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');

  $qs = $ci->activity_model->get_location();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
    }
  }

  return $sc;
}



function select_sap_user($code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('activity_model');

  $qs = $ci->activity_model->get_sap_user();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($code, $rs->code).">{$rs->name}</option>";
    }
  }

  return $sc;
}


function action_name($code)
{
  $action = array(
    'C' => 'Phone Call',
    'M' => 'Meeting',
    'T' => 'Task',
    'E' => 'Notes',
    'P' => 'Campaignx',
    'N' => 'Other'
  );

  return $action[$code];
}

function get_time_from_int($int)
{
  $arr = str_split($int);

  if(count($arr) === 4)
  {
    return $arr[0].$arr[1].':'.$arr[2].$arr[3];
  }
  else
  {
    return '0'.$arr[0].':'.$arr[1].$arr[2];
  }
}


 ?>

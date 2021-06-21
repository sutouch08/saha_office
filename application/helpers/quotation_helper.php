<?php
//--- Quotation helper

// function select_currency($code = NULL)
// {
//   $ds = '';
//   $ci =& get_instance();
//   $ci->load->model('quotation_model');
//   $qs = $ci->quotation_model->get_all_currency();
//
//   if(!empty($qs))
//   {
//     $df = getConfig('CURRENCY');
//
//     foreach($qs as $rs)
//     {
//       if($code === NULL)
//       {
//         $ds .= '<option value="'.$rs->code.'" '.is_selected($df, $rs->code).'>'.$rs->code.'</option>';
//       }
//       else
//       {
//         $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
//       }
//     }
//   }
//
//   return $ds;
// }


function select_project($code = NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_all_open_project();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' : '.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_sale_type($code = NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_all_sale_type();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' : '.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_series($posting_date = NULL, $code = NULL)
{
  $month = empty($posting_date) ? date('Y-m') : date('Y-m', strtotime($posting_date));
  $default = getConfig('DEFAULT_QUOTATION_SERIES');

  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_series($month);

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      if(!empty($code))
      {
        $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
      }
      else
      {
        $ds .= '<option value="'.$rs->code.'" '.is_selected($default, $rs->prefix).'>'.$rs->name.'</option>';
      }
    }
  }
  else
  {
    $ds = '<opton value="">Please define Series</option>';
  }

  return $ds;
}


function select_uom($code=NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_all_oum();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
    }
  }

  return $ds;
}


function select_tax_code($code=NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_all_tax_code();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" data-rate="'.$rs->rate.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
    }
  }

  return $ds;
}


function select_whs($code=NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->quotation_model->get_all_whs();
  $code = empty($code) ? getConfig('DEFAULT_WAREHOUSE') : $code;

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.'</option>';
    }
  }

  return $ds;
}


function select_contact_person($CardCode, $id = NULL)
{
  $ds = '';
  $ci =& get_instance();
  $ci->load->model('quotation_model');
  $qs = $ci->customers_model->get_contact_person($CardCode);

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function logs_action_name($name)
{
  $arr = array(
    'add' => 'สร้างโดย',
    'edit' => 'แก้ไขโดย',
    'cancle' => 'ยกเลิกโดย',
    'approve' => 'อนุมัติโดย',
    'unapprove' => 'ยกเลิกการอนุมัติโดย',
    'reject' => 'ปฏิเสธการอนุมัติโดย',
    'unreject' => 'ยกเลิกการปฏิเสธโดย'
  );

  return $arr[$name];
}

 ?>

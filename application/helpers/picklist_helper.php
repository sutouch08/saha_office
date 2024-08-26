<?php
function pick_status_label($status = 'N')
{
  $label = 'รอดำเนินการ';

  switch($status)
  {
    case 'R' :
      $label = 'รอจัด';
      break;
    case 'P' :
      $label = 'กำลังจัด';
      break;
    case 'Y' :
      $label = 'จัดแล้ว';
      break;
    case 'C' :
      $label = 'Closed';
      break;
    case 'D' :
      $label = 'ยกเลิก';
      break;
    case 'N' :
      $label ='รอดำเนินการ';
      break;
  }

  return $label;
}

function line_status_label($status = 'O')
{
  $label = 'Open';

  switch($status)
  {
    case 'O' :
      $label = 'Open';
      break;    
    case 'C' :
      $label = 'Closed';
      break;
    case 'D' :
      $label = 'Cancelled';
      break;
  }

  return $label;
}

 ?>

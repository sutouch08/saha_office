<?php
  function return_status_label($status)
  {
    $label = [
      'P' => 'Draft',
      'O' => 'Pending',
      'C' => 'Closed',
      'D' => 'Canceled'
    ];

    return empty($label[$status]) ? 'Unknow' : $label[$status];
  }


  function return_status_color($status)
  {
    $default = 'blue';

    $label = [
      'P' => 'blue',
      'O' => 'purple',
      'C' => 'green',
      'D' => 'grey'
    ];

    return empty($label[$status]) ? 'Unknow' : $label[$status];
  }
 ?>

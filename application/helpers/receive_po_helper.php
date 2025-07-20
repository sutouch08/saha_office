<?php
  function grpo_status_label($status)
  {
    $label = [
      'P' => 'Draft',
      'O' => 'Pending',
      'C' => 'Closed',
      'D' => 'Canceled'
    ];

    return empty($label[$status]) ? 'Unknow' : $label[$status];
  }
 ?>

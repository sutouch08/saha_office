<?php $this->load->view('sales_order/style_sheet'); ?>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#content" aria-expanded="true">Contents</a></li>
    <li class=""><a data-toggle="tab" href="#logistic" aria-expanded="true">Logistics</a></li>
  </ul>

  <div class="tab-content">
    <?php $this->load->view('sales_order/sales_order_tab_content'); ?>
    <?php $this->load->view('sales_order/sales_order_tab_logistic'); ?>
  </div>
</div>
<hr class="padding-5"/>

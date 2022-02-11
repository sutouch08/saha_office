<div class="row">
  <!--- left column -->
  <div class="col-sm-4 col-xs-12 padding-5">
    <table class="table">
      <tr>
        <td class="width-40 text-right">Sale Employee : </td>
        <td class="width-60"> : <?php echo $sale_name; ?></td>
      </tr>
      <tr>
        <td class="width-40 text-right">Owner : </td>
        <td class="width-60"> : <?php echo $header->owner_name; ?></td>
      </tr>
      <tr>
        <td class="width-40 text-right">Remark : </td>
        <td class="width-60"><?php echo $header->Comments; ?></td>
      </tr>
    </table>
  </div>

  <!--- Middle column -->
  <div class="col-sm-4 col-xs-12 padding-5"> </div>


  <!--- right column -->
  <div class="col-sm-4 col-xs-12 padding-5">
    <table class="table table-striped">
      <tr>
        <td class="width-60 text-right">Total Before Discount</td>
        <td class="width-40 text-right"><?php echo number($totalAmount,2); ?></td>
      </tr>
      <tr>
        <td class="width-60 text-right">Discount <?php echo number($header->DiscPrcnt, 2); ?> %</td>
        <td class="width-40 text-right"><?php echo number($totalAmount * ($header->DiscPrcnt * 0.01), 2); ?></td>
      </tr>
      <tr>
        <td class="width-60 text-right">Rounding</td>
        <td class="width-40 text-right"><?php echo number($header->RoundDif, 2); ?></td>
      </tr>
      <tr>
        <td class="width-60 text-right">Tax</td>
        <td class="width-40 text-right"><?php echo number($totalVat, 2); ?></td>
      </tr>
      <tr>
        <td class="width-60 text-right">Total</td>
        <td class="width-40 text-right"><?php echo number($header->DocTotal, 2); ?></td>
      </tr>
    </table>
  </div>

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>

  <div class="col-sm-6 col-xs-6 padding-5">  </div>
  <div class="col-sm-6 col-xs-6 padding-5">
    <?php if(!empty($logs)) : ?>
      <p class="pull-right" style="font-size:12px; font-style: italic; color:#c5d0dc;">
      <?php foreach($logs as $log) : ?>
        <?php echo "*".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp; {$log->emp_name}  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
      <?php endforeach; ?>
      </p>
    <?php endif; ?>
  </div>
</div>

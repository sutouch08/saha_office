<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:900px;">
      <thead>
        <tr>
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:100px;">SO No.</th>
          <th class="middle" style="max-width:200px;">ลูกค้า</th>
          <th class="middle" style="max-width:250px;">สินค้า</th>
          <th class="middle text-right" style="width:100px;">ราคา</th>
          <th class="middle text-right" style="width:100px;">Released</th>
          <th class="middle text-right" style="width:100px;">UOM (SO)</th>
          <th class="middle text-right" style="width:120px;">Base Released</th>
          <th class="middle text-right" style="width:100px;">Base Picked</th>
          <th class="middle text-right" style="width:100px;">Base Balance</th>
        </tr>
      </thead>
      <tbody id="pick-list-items">
        <?php
          $totalReleased = 0;
          $totalBaseReleased = 0;
          $totalPicked = 0;
          $totalBalance = 0;
          ?>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php $balance = $rs->BaseRelQty - $rs->BasePickQty; ?>
            <tr>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo $rs->OrderCode; ?></td>
              <td class="middle"><?php echo $rs->CardName; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?> | <?php echo $rs->ItemName; ?></td>
              <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->RelQtty, 2); ?></td>
              <td class="middle text-right"><?php echo $rs->UomCode; ?></td>
              <td class="middle text-right"><?php echo number($rs->BaseRelQty, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->BasePickQty, 2); ?></td>
              <td class="middle text-right"><?php echo number($balance, 2); ?></td>
            </tr>
            <?php
              $no++;
              $totalReleased += $rs->RelQtty;
              $totalBaseReleased += $rs->BaseRelQty;
              $totalPicked += $rs->BasePickQty;
              $totalBalance += $balance;
            ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr style="font-size:14px;">
          <td colspan="7" class="text-right">รวม</td>
          <td class="middle text-right" id="totalBaseReleased"><?php echo number($totalBaseReleased, 2); ?></td>
          <td class="middle text-right" id="totalPicked"><?php echo number($totalPicked, 2); ?></td>
          <td class="middle text-right" id="totalBalance"><?php echo number($totalBalance, 2); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:900px;">
      <thead>
        <tr>
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:100px;">SO No.</th>
          <th class="middle" style="width:200px;">Customer</th>
          <th class="middle" style="width:250px;">Items</th>
          <th class="middle text-right" style="width:100px;">UOM</th>
          <th class="middle text-right" style="width:100px;">Order</th>
          <th class="middle text-right" style="width:100px;">Open</th>
          <th class="middle text-right" style="width:100px;">Prev Released</th>
          <th class="middle text-right" style="width:100px;">Qty</th>
          <th class="middle text-right" style="width:100px;">Available(UOM)</th>

        </tr>
      </thead>
      <tbody id="pick-list-items">
        <?php
          $totalOrderQty = 0;
          $totalOpenQty = 0;
          $totalPrevRelease = 0;
          $totalQty = 0;
          ?>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php
            $red = $rs->RelQtty > $rs->OnHand ? 'red' : '';
            $rowNum = $rs->OrderEntry.$rs->OrderLine;
            ?>
            <tr class="<?php echo $red; ?>" id="row-<?php echo $rowNum; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo $rs->OrderCode; ?></td>
              <td class="middle"><?php echo $rs->CardName; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?> | <?php echo $rs->ItemName; ?></td>
              <td class="middle text-right"><?php echo $rs->unitMsr; ?></td>
              <td class="middle text-right"><?php echo number($rs->OrderQty, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->OpenQty, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->PrevRelease, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->RelQtty, 2); ?></td>
              <td class="middle text-right" id="onhand-<?php echo $rowNum; ?>"><?php echo number($rs->OnHand, 2); ?>(<?php echo $rs->unitMsr2; ?>)</td>

            </tr>
            <?php
              $no++;
              $totalOrderQty += $rs->OrderQty;
              $totalOpenQty += $rs->OpenQty;
              $totalPrevRelease += $rs->PrevRelease;
              $totalQty += $rs->RelQtty;
            ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr style="font-size:14px;">
          <td colspan="5" class="text-right">รวม</td>
          <td class="middle text-right" id="totalOrderQty"><?php echo number($totalOrderQty, 2); ?></td>
          <td class="middle text-right" id="totalOpenQty"><?php echo number($totalOpenQty, 2); ?></td>
          <td class="middle text-right" id="totalPrevRelease"><?php echo number($totalPrevRelease, 2); ?></td>
          <td class="middle text-right" id="totalQty"><?php echo number($totalQty, 2); ?></td>
          <td class="middle text-right"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

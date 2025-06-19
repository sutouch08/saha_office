<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1370px;">
      <thead>
        <tr>
          <th class="fix-width-50 middle text-center">#</th>
          <th class="fix-width-100 middle text-center">SO No.</th>
          <th class="fix-width-200 middle">ลูกค้า</th>
          <th class="fix-width-250 middle">สินค้า</th>
          <th class="fix-width-100 middle text-right">ราคา</th>
          <th class="fix-width-100 middle text-right">Released</th>
          <th class="fix-width-100 middle text-right">UOM (SO)</th>
          <th class="fix-width-120 middle text-right">Base Released</th>
          <th class="fix-width-100 middle text-right">Base Picked</th>
          <th class="fix-width-100 middle text-right">Base Balance</th>
          <th class="fix-width-100 middle text-center">Status</th>
          <th class="fix-width-50"></th>
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
            <?php $id = $rs->AbsEntry.$rs->PickEntry; ?>
            <tr id="row-<?php echo $id; ?>" data-so="<?php echo $rs->OrderCode; ?>" data-item="<?php echo $rs->ItemCode; ?>">
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
              <td class="middle text-center"><?php echo $rs->LineStatus == 'D' ? 'Canceled' : ($rs->LineStatus == 'C' ? 'Closed' : 'Open'); ?></td>
              <td class="middle text-center">
              <?php if($rs->BasePickQty == 0) : ?>
                <button type="button"
                class="btn btn-danger btn-minier"
                onclick="removePickRow(<?php echo $id; ?>, <?php echo $rs->AbsEntry; ?>, <?php echo $rs->PickEntry; ?>)">
                <i class="fa fa-trash"></i>
              </button>
              <?php endif; ?>
              </td>
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
          <td class=""></td>
          <td class=""></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1350px;">
      <thead>
        <tr>
          <th class="fix-width-50 middle text-center">#</th>
          <th class="fix-width-100 middle text-center">SO No.</th>
          <th class="fix-width-200 middle">Customer</th>
          <th class="fix-width-250 middle">Items</th>
          <th class="fix-width-100 middle text-right">Price</th>
          <th class="fix-width-100 middle text-right">UOM</th>
          <th class="fix-width-100 middle text-right">Order</th>
          <th class="fix-width-100 middle text-right">Open</th>
          <th class="fix-width-100 middle text-right">Qty</th>
          <th class="fix-width-100 middle text-right">Available</th>
          <th class="fix-width-100 middle text-center">Status</th>
          <th class="fix-width-50"></th>
        </tr>
      </thead>
      <tbody id="pick-list-items">
        <?php
          $totalOrderQty = 0;
          $totalOpenQty = 0;
          $totalQty = 0;
          ?>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php
            $red = $rs->BaseRelQty > $rs->OnHand ? 'red' : '';
            $rowNum = $rs->OrderEntry.$rs->OrderLine;
            ?>
            <tr class="row-tr <?php echo $red; ?>" id="row-<?php echo $rowNum; ?>" data-no="<?php echo $rowNum; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo $rs->OrderCode; ?></td>
              <td class="middle"><?php echo $rs->CardName; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?> | <?php echo $rs->ItemName; ?></td>
              <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="middle text-right"><?php echo $rs->unitMsr; ?></td>
              <td class="middle text-right" id="orderQty-<?php echo $rowNum; ?>"><?php echo number($rs->OrderQty, 2); ?></td>
              <td class="middle text-right" id="openQty-<?php echo $rowNum; ?>"><?php echo number($rs->OpenQty, 2); ?></td>
              <td class="middle text-right" id="qty-<?php echo $rowNum; ?>"><?php echo number($rs->RelQtty, 2); ?></td>
              <td class="middle text-right" id="onhand-<?php echo $rowNum; ?>"><?php echo number(($rs->OnHand/$rs->BaseQty), 2); ?></td>
              <td class="middle text-center"><?php echo $rs->LineStatus == 'D' ? 'Canceled' : ($rs->LineStatus == 'C' ? 'Closed' : 'Open'); ?></td>
              <td class="middle text-center">
                <button type="button"
                class="btn btn-danger btn-minier"
                onclick="removeRow(<?php echo $rowNum.", ".$rs->AbsEntry.", ".$rs->PickEntry; ?>)">
                <i class="fa fa-trash"></i>
              </button>
              </td>
            </tr>
            <?php
              $no++;
              $totalOrderQty += $rs->OrderQty;
              $totalOpenQty += $rs->OpenQty;
              $totalQty += $rs->BaseRelQty;
            ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr style="font-size:14px;">
          <td colspan="6" class="text-right">รวม</td>
          <td class="middle text-right" id="totalOrderQty"><?php echo number($totalOrderQty, 2); ?></td>
          <td class="middle text-right" id="totalOpenQty"><?php echo number($totalOpenQty, 2); ?></td>
          <td class="middle text-right" id="totalQty"><?php echo number($totalQty, 2); ?></td>
          <td class="middle text-right"></td>
          <td></td>
          <td class=""></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

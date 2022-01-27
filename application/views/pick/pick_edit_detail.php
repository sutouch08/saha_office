<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="width:100%; min-width:900px;">
      <thead>
        <tr>
          <th class="middle text-center" style="width:40px;">
            <label>
              <input type="checkbox" class="ace" id="check-item-all" onchange="checkItemAll()">
              <span class="lbl"></span>
            </label>
          </th>
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:100px;">Order No.</th>
          <th class="middle" style="width:200px;">Customer</th>
          <th class="middle" style="width:150px;">Item Code</th>
          <th class="middle" style="width:250px;">Description</th>
          <th class="middle text-center" style="width:100px;">UOM</th>
          <th class="middle text-right" style="width:100px;">Order</th>
          <th class="middle text-right" style="width:100px;">Open</th>
          <th class="middle text-right" style="width:100px;">Released</th>
          <th class="middle text-right" style="width:100px;">Balance</th>
          <th class="middle text-right" style="min-width:100px;">Qty</th>
          <th class="middle text-right" colspan="2" style="width:100px;">Avail.(UOM)</th>
          <th class="middle text-right" style="width:50px;"></th>
        </tr>
      </thead>
      <tbody id="pick-list-items">
        <?php
          $totalOrderQty = 0;
          $totalOpenQty = 0;
          $totalPrevRelease = 0;
          $totalAvailableQty = 0;
          $totalQty = 0;
          ?>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php $red = ($rs->Qty > $rs->OnHand) ? 'red' : ''; ?>
            <tr id="row-<?php echo $rs->rowNum; ?>" class="<?php echo $red; ?>">
              <td class="middle text-center">
                <label>
                  <input type="checkbox" class="ace check-item" id="check-item-<?php echo $rs->rowNum; ?>"
                  data-docentry="<?php echo $rs->OrderEntry; ?>" data-linenum="<?php echo $rs->OrderLine; ?>" value="<?php echo $rs->rowNum; ?>">
                  <span class="lbl"></span>
                </label>
              </td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center" id="orderCode-<?php echo $rs->rowNum; ?>"><?php echo $rs->OrderCode; ?></td>
              <td class="middle" id="customer-<?php echo $rs->rowNum; ?>"><?php echo $rs->CardName; ?></td>
              <td class="middle" id="itemCode-<?php echo $rs->rowNum; ?>"><?php echo $rs->ItemCode; ?></td>
              <td class="middle" id="itemName-<?php echo $rs->rowNum; ?>"><?php echo $rs->ItemName; ?></td>
              <td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
              <td class="middle text-right" id="order-<?php echo $rs->rowNum; ?>"><?php echo number($rs->OrderQty, 2); ?></td>
              <td class="middle text-right" id="open-<?php echo $rs->rowNum; ?>"><?php echo number($rs->OpenQty, 2); ?></td>
              <td class="middle text-right" id="release-<?php echo $rs->rowNum; ?>"><?php echo number($rs->PrevRelease, 2); ?></td>
              <td class="middle text-right" id="available-<?php echo $rs->rowNum; ?>"><?php echo number($rs->AvailableQty, 2); ?></td>

              <td class="middle text-right">
                <input type="text"
                  class="form-control input-sm text-right pick-qty"
                  name="qty[<?php echo $rs->rowNum; ?>]"
                  id="qty-<?php echo $rs->rowNum; ?>"
                  value="<?php echo $rs->Qty; ?>" />

                  <input type="hidden" id="UomEntry-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->UomEntry; ?>">
                  <input type="hidden" id="UomEntry2-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->UomEntry2; ?>">
                  <input type="hidden" id="UomCode-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->UomCode; ?>">
                  <input type="hidden" id="UomCode2-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->UomCode2; ?>">
                  <input type="hidden" id="unitMsr-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->unitMsr; ?>">
                  <input type="hidden" id="unitMsr2-<?php echo $rs->rowNum; ?>" value="<?php echo $rs->unitMsr2; ?>">
              </td>
              <td class="middle text-right" style="padding-right:0px;" id="available-<?php echo $rs->rowNum; ?>"><?php echo number($rs->OnHand, 2); ?></td>
              <td class="middle" style="padding-right:0px; padding-left:5px;"><?php echo "({$rs->unitMsr2})"; ?></td>
              <td class="middle text-right">
                <button type="button" class="btn btn-minier btn-danger" onclick="removeRow(<?php echo $rs->rowNum; ?>)"><i class="fa fa-trash"></i></button>
              </td>
            </tr>
            <?php
              $no++;
              $totalOrderQty += $rs->OrderQty;
              $totalOpenQty += $rs->OpenQty;
              $totalPrevRelease += $rs->PrevRelease;
              $totalAvailableQty += $rs->AvailableQty;
              $totalQty += $rs->Qty;
            ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr style="font-size:14px;">
          <td colspan="6" class="text-right">รวม</td>
          <td class="middle text-right" id="totalOrderQty"><?php echo number($totalOrderQty, 2); ?></td>
          <td class="middle text-right" id="totalOpenQty"><?php echo number($totalOpenQty, 2); ?></td>
          <td class="middle text-right" id="totalPrevRelease"><?php echo number($totalPrevRelease, 2); ?></td>
          <td class="middle text-right" id="totalAvaibleQty"><?php echo number($totalAvailableQty, 2); ?></td>
          <td class="middle text-right" id="totalQty"><?php echo number($totalQty, 2); ?></td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>


<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{rowNum}}" class="{{red}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace check-item" id="check-item-{{rowNum}}"
        data-docentry="{{OrderEntry}}" data-linenum="{{OrderLine}}" value="{{rowNum}}">
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no">1</td>
    <td class="middle text-center" id="orderCode-{{rowNum}}">{{OrderCode}}</td>
    <td class="middle" id="customer-{{rowNum}}">{{CardName}}</td>
    <td class="middle" id="itemCode-{{rowNum}}">{{ItemCode}}</td>
    <td class="middle" id="itemName-{{rowNum}}">{{ItemName}}</td>
    <td class="middle text-center">{{unitMsr}}</td>
    <td class="middle text-right" id="order-{{rowNum}}">{{OrderQty}}</td>
    <td class="middle text-right" id="open-{{rowNum}}">{{OpenQty}}</td>
    <td class="middle text-right" id="release-{{rowNum}}">{{PrevRelease}}</td>
    <td class="middle text-right" id="available-{{rowNum}}">{{AvailableQty}}</td>
    <td class="middle text-right">
      <input type="number" class="form-control input-sm text-right pick-qty" name="qty[{{rowNum}}]" id="qty-{{rowNum}}" value="{{Qty}}"/>
      <input type="hidden" id="UomEntry-{{rowNum}}" value="{{UomEntry}}">
      <input type="hidden" id="UomEntry2-{{rowNum}}" value="{{UomEntry2}}">
      <input type="hidden" id="UomCode-{{rowNum}}" value="{{UomCode}}">
      <input type="hidden" id="UomCode2-{{rowNum}}" value="{{UomCode2}}">
      <input type="hidden" id="unitMsr-{{rowNum}}" value="{{unitMsr}}">
      <input type="hidden" id="unitMsr2-{{rowNum}}" value="{{unitMsr2}}">
    </td>
    <td class="middle text-right" id="onHand-{{rowNum}}">{{OnHand}}</td>
    <td class="middle" style="padding-right:0px; padding-left:5px;">{{unitMsr2}}</td>
    <td class="middle text-right">
      <button type="button" class="btn btn-minier btn-danger" onclick="removeRow({{rowNum}})"><i class="fa fa-trash"></i></button>
    </td>
  </tr>
  </script>


<style>
  .table > tr > td {
    padding:3px;
  }
</style>

<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-sm btn-info" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-sm btn-warning" onclick="removeRow()">Delete Row</button>
  </div>
  <div class="divider-hidden">

  </div>
  <div class="col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="table-layout: fixed; min-width: 100%; width:2480px;">
      <thead>
        <tr class="font-size-10">
          <th class="middle text-center" style="width:50px;"></th>
          <th class="middle text-center" style="width:50px;">#</th>
          <th class="middle text-center" style="width:80px;">Type</th>
          <th class="middle text-center" style="width:200px;">Item Code</th>
          <th class="middle text-center" style="width:250px;">Item Description.</th>
          <th class="middle text-center" style="width:200px;">Item Detail</th>
          <th class="middle text-center" style="width:150px;">รหัสสมบูรณ์</th>
          <th class="middle text-center" style="width:100px;">Quantity</th>
          <th class="middle text-center" style="width:100px;">Uom</th>
          <th class="middle text-center" style="width:100px;">STD Price</th>
          <th class="middle text-center" style="width:100px;">Last Sell Price</th>
          <th class="middle text-center" style="width:100px;">Price</th>
          <th class="middle text-center" style="width:100px;">ส่วนต่างราคา(%)</th>
          <th class="middle text-center" style="width:100px;">ส่วนลด(%)</th>
          <th class="middle text-center" style="width:100px;">Tax Code</th>
          <th class="middle text-center" style="width:100px;">มูค่า/หน่วย หลังส่วนลด(ก่อน vat)</th>
          <th class="middle text-center" style="width:150px;">มูลค่ารวม (ก่อน vat)</th>
          <th class="middle text-center" style="width:150px;">Whs</th>
          <th class="middle text-center" style="width:100px;">In Stock</th>
          <th class="middle text-center" style="width:100px;">Commited</th>
          <th class="middle text-center" style="width:100px;">Ordered</th>
        </tr>
      </thead>
      <tbody id="details-template">
        <?php $rows = 5; ?>
        <?php $no = 1; ?>
        <?php $whs = select_whs(); ?>
        <?php if(!empty($details)) : ?>
        <?php   foreach($details as $ds) : ?>
          <tr id="row-<?php echo $no; ?>">
            <td class="middle text-center">
              <input type="checkbox" class="ace chk" id="chk-<?php echo $no; ?>" value="<?php echo $no; ?>"/>
              <span class="lbl"></span>
            </td>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center">
              <select class="form-control input-sm toggle-text" id="type-<?php echo $no; ?>" onchange="toggleText($(this))" data-no="<?php echo $no; ?>">
                <option value="0" <?php echo is_selected('0', $ds->Type); ?>>-</option>
                <option value="1" <?php echo is_selected('1', $ds->Type); ?>>Text</option>
              </select>
            </td>
            <?php if($ds->Type == 1) : ?>
              <td colspan="17">
                <textarea id="text-<?php echo $no; ?>" class="autosize autosize-transition" style="height:100px; width:800px;"><?php echo $ds->LineText; ?></textarea>
              </td>
            <?php else : ?>
            <td class="middle">
              <input type="text" class="form-control input-sm input-item-code" data-id="<?php echo $no; ?>" id="itemCode-<?php echo $no; ?>" value="<?php echo $ds->ItemCode; ?>"/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm input-item-name" id="itemName-<?php echo $no; ?>" value="<?php echo $ds->Dscription; ?>"/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm input-item-detail" id="itemDetail-<?php echo $no; ?>" value="<?php echo $ds->ItemDetail; ?>" />
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm free-text" maxlength="100" id="freeText-<?php echo $no; ?>" value="<?php echo $ds->FreeText; ?>" />
            </td>

            <td class="middle">
              <input type="text" class="form-control input-sm text-right number input-qty" id="qty-<?php echo $no; ?>" onkeyup="recalAmount($(this))" value="<?php echo number($ds->Qty, 2); ?>"/>
            </td>
            <td class="middle">
              <select class="form-control input-sm uom" id="uom-<?php echo $no; ?>" onchange="recalPrice($(this))">
                <?php echo $ds->uom; ?>
              </select>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-right number" id="stdPrice-<?php echo $no; ?>" value="<?php echo number($ds->stdPrice, 2); ?>" disabled/>
              <input type="hidden" id="basePrice-<?php echo $no; ?>" value="<?php echo $ds->basePrice; ?>"/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-right number" id="lstPrice-<?php echo $no; ?>" value="<?php echo number($ds->lastSellPrice, 2); ?>" readonly disabled/>
              <input type="hidden" id="lastSellPrice-<?php echo $no; ?>" value="<?php echo $ds->lastSellPrice; ?>"/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-right number input-price" id="price-<?php echo $no; ?>" value="<?php echo number($ds->Price, 2); ?>" onkeyup="recalAmount($(this))"/>
            </td>
            <td class="middle">
              <input type="number" class="form-control input-sm text-right" id="priceDiff-<?php echo $no; ?>" value="<?php echo $ds->priceDiffPercent; ?>" disabled/>
            </td>
            <td class="middle">
              <input type="number" class="form-control input-sm text-right number input-disc1" id="disc1-<?php echo $no; ?>" value="<?php echo $ds->U_DISWEB; ?>" onkeyup="recalAmount($(this))"/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-center tax-code" id="taxCode-<?php echo $no; ?>" data-rate="<?php echo $ds->VatRate; ?>" value="<?php echo $ds->VatGroup; ?>" disabled/>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-right" id="priceAfDiscBfTax-<?php echo $no; ?>" value="<?php echo number($ds->SellPrice, 2); ?>" disabled>
            </td>
            <td class="middle">
              <input type="text" class="form-control input-sm text-right number input-amount" id="lineAmount-<?php echo $no; ?>" value="<?php echo number($ds->LineTotal, 2); ?>" disabled />
              <input type="hidden" class="lineDisc" id="lineDiscPrcnt-<?php echo $no; ?>" value="<?php echo $ds->DiscPrcnt; ?>">
            </td>

            <td class="middle">
              <select class="form-control inpt-sm whs" id="whs-<?php echo $no; ?>" onchange="getStock(<?php echo $no; ?>)">
                <?php echo select_whs($ds->WhsCode); ?>
              </select>
            </td>

            <td class="middle">
              <input type="number" class="form-control input-sm text-right whs-qty" id="whsQty-<?php echo $no; ?>" value="<?php echo number($ds->OnHandQty); ?>" disabled/>
            </td>
            <td class="middle">
              <input type="number" class="form-control input-sm text-right commit-qty" id="commitQty-<?php echo $no; ?>" value="<?php echo number($ds->IsCommited); ?>" disabled/>
            </td>
            <td class="middle">
              <input type="number" class="form-control input-sm text-right ordered-qty" id="orderedQty-<?php echo $no; ?>" value="<?php echo number($ds->OnOrder); ?>" disabled/>
            </td>

          <?php endif; ?>
          </tr>
          <?php $no++; ?>
        <?php   endforeach; ?>
        <?php endif; ?>

        <?php while($no <= $rows) : ?>
        <tr id="row-<?php echo $no; ?>">
          <td class="middle text-center">
            <input type="checkbox" class="ace chk" id="chk-<?php echo $no; ?>" value="<?php echo $no; ?>"/>
            <span class="lbl"></span>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle text-center">
            <select class="form-control input-sm toggle-text" id="type-<?php echo $no; ?>" onchange="toggleText($(this))" data-no="<?php echo $no; ?>">
              <option value="0">-</option>
              <option value="1">Text</option>
            </select>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm input-item-code" data-id="<?php echo $no; ?>" id="itemCode-<?php echo $no; ?>" value=""/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm input-item-name" id="itemName-<?php echo $no; ?>" value=""/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm input-item-detail" id="itemDetail-<?php echo $no; ?>" value="" />
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm free-text" maxlength="100" id="freeText-<?php echo $no; ?>" value="" />
          </td>

          <td class="middle">
            <input type="text" class="form-control input-sm text-right number input-qty" id="qty-<?php echo $no; ?>" onkeyup="recalAmount($(this))" value=""/>
          </td>
          <td class="middle">
            <select class="form-control input-sm uom" id="uom-<?php echo $no; ?>" onchange="recalPrice($(this))">

            </select>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-right number" id="stdPrice-<?php echo $no; ?>" value="" disabled/>
            <input type="hidden" id="basePrice-<?php echo $no; ?>" value="0"/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-right number" id="lstPrice-<?php echo $no; ?>" readonly disabled/>
            <input type="hidden" id="lastSellPrice-<?php echo $no; ?>" value="0"/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-right number input-price" id="price-<?php echo $no; ?>" value="" onkeyup="recalAmount($(this))"/>
          </td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right" id="priceDiff-<?php echo $no; ?>" value="" disabled/>
          </td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right number input-disc1" id="disc1-<?php echo $no; ?>" value="" onkeyup="recalAmount($(this))"/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-center tax-code" id="taxCode-<?php echo $no; ?>" data-rate="0" value="" disabled/>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-right" id="priceAfDiscBfTax-<?php echo $no; ?>" value="" disabled>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-right number input-amount" id="lineAmount-<?php echo $no; ?>" value="" disabled />
            <input type="hidden" class="lineDisc" id="lineDiscPrcnt-<?php echo $no; ?>" value="">
          </td>

          <td class="middle">
            <select class="form-control inpt-sm whs" id="whs-<?php echo $no; ?>" onchange="getStock(<?php echo $no; ?>)">
              <?php echo $whs; ?>
            </select>
          </td>

          <td class="middle">
            <input type="number" class="form-control input-sm text-right whs-qty" id="whsQty-<?php echo $no; ?>" value="" disabled/>
          </td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right commit-qty" id="commitQty-<?php echo $no; ?>" value="" disabled/>
          </td>
          <td class="middle">
            <input type="number" class="form-control input-sm text-right ordered-qty" id="orderedQty-<?php echo $no; ?>" value="" disabled/>
          </td>
        </tr>
          <?php $no++; ?>
        <?php endwhile; ?>
      </tbody>
      <tfoot>

      </tfoot>
    </table>
  </div>
</div>
<hr class="padding-5"/>
<input type="hidden"  id="top-row" value="<?php echo ($no-1); ?>" />
<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{no}}">
    <td class="middle text-center">
      <input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
      <span class="lbl"></span>
    </td>
    <td class="middle text-center no">{{no}}</td>
    <td class="middle text-center">
      <select class="form-control input-sm toggle-text" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
        <option value="0">-</option>
        <option value="1">Text</option>
      </select>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm input-item-code" data-id="{{no}}" id="itemCode-{{no}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm input-item-name" id="itemName-{{no}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm input-item-detail" id="itemDetail-{{no}}" />
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm free-text" maxlength="100" id="freeText-{{no}}" />
    </td>

    <td class="middle">
      <input type="text" class="form-control input-sm text-right number input-qty" id="qty-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="middle">
      <select class="form-control input-sm uom" id="uom-{{no}}" onchange="recalPrice($(this))"></select>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-right number" id="stdPrice-{{no}}" readonly disabled/>
      <input type="hidden" id="basePrice-{{no}}" value="0"/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-right number" id="lstPrice-{{no}}" readonly disabled/>
      <input type="hidden" id="lastSellPrice-{{no}}" value="0"/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-right number input-price" id="price-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="middle">
      <input type="number" class="form-control input-sm text-right" id="priceDiff-{{no}}" readonly disabled/>
    </td>
    <td class="middle">
      <input type="number" class="form-control input-sm text-right number input-disc1" id="disc1-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-center tax-code" id="taxCode-{{no}}" data-rate="0.00" value="" disabled/>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-right" id="priceAfDiscBfTax-{{no}}" value="" disabled>
    </td>
    <td class="middle">
      <input type="text" class="form-control input-sm text-right number input-amount" id="lineAmount-{{no}}" onkeyup="recalDiscount($(this))" disabled/>
      <input type="hidden" class="lineDisc" id="lineDiscPrcnt-{{no}}" value="0">
    </td>

    <td class="middle">
      <select class="form-control inpt-sm whs" id="whs-{{no}}" onchange="getStock({{no}})">
        <?php echo $whs; ?>
      </select>
    </td>

    <td class="middle">
      <input type="number" class="form-control input-sm text-right whs-qty" id="whsQty-{{no}}" disabled />
    </td>
    <td class="middle">
      <input type="number" class="form-control input-sm text-right commit-qty" id="commitQty-{{no}}" disabled/>
    </td>
    <td class="middle">
      <input type="number" class="form-control input-sm text-right ordered-qty" id="orderedQty-{{no}}" disabled/>
    </td>
  </tr>
</script>

<script id="normal-template" type="text/x-handlebarsTemplate">
<td class="middle text-center">
  <input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
  <span class="lbl"></span>
</td>
<td class="middle text-center no">{{no}}</td>
<td class="middle text-center">
  <select class="form-control input-sm toggle-text" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
    <option value="0" selected>-</option>
    <option value="1">Text</option>
  </select>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm input-item-code" data-id="{{no}}" id="itemCode-{{no}}" />
</td>
<td class="middle">
  <input type="text" class="form-control input-sm input-item-name" id="itemName-{{no}}" />
</td>
<td class="middle">
  <input type="text" class="form-control input-sm input-item-detail" id="itemDetail-{{no}}" />
</td>
<td class="middle">
  <input type="text" class="form-control input-sm free-text" maxlength="100" id="freeText-{{no}}" />
</td>

<td class="middle">
  <input type="text" class="form-control input-sm text-right number input-qty" id="qty-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="middle">
  <select class="form-control input-sm uom" id="uom-{{no}}" onchange="recalPrice($(this))"></select>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-right number" id="stdPrice-{{no}}" readonly disabled/>
  <input type="hidden" id="basePrice-{{no}}" value="0"/>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-right number" id="lstPrice-{{no}}" readonly disabled/>
  <input type="hidden" id="lastSellPrice-{{no}}" value="0"/>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-right number input-price" id="price-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="middle">
  <input type="number" class="form-control input-sm text-right" id="priceDiff-{{no}}" readonly disabled/>
</td>
<td class="middle">
  <input type="number" class="form-control input-sm text-right number input-disc1" id="disc1-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-center tax-code" id="taxCode-{{no}}" data-rate="0.00" value="" disabled/>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-right" id="priceAfDiscBfTax-{{no}}" value="" disabled>
</td>
<td class="middle">
  <input type="text" class="form-control input-sm text-right number input-amount" id="lineAmount-{{no}}" onkeyup="recalDiscount($(this))" disabled/>
  <input type="hidden" class="lineDisc" id="lineDiscPrcnt-{{no}}" value="0">
</td>

<td class="middle">
  <select class="form-control inpt-sm whs" id="whs-{{no}}">
    <?php echo $whs; ?>
  </select>
</td>

<td class="middle">
  <input type="number" class="form-control input-sm text-right whs-qty" id="whsQty-{{no}}" disabled/>
</td>
<td class="middle">
  <input type="number" class="form-control input-sm text-right commit-qty" id="commitQty-{{no}}" disabled/>
</td>
<td class="middle">
  <input type="number" class="form-control input-sm text-right ordered-qty" id="orderedQty-{{no}}" disabled/>
</td>
</script>

<script id="text-template" type="text/x-handlebarsTemplate">
  <td class="middle text-center">
    <input type="checkbox" class="ace" id="chk-{{no}}" value="{{no}}"/>
    <span class="lbl"></span>
  </td>
  <td class="middle text-center no">{{no}}</td>
  <td class="middle text-center">
    <select class="form-control input-sm toggle-text" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
      <option value="0">-</option>
      <option value="1" selected>Text</option>
    </select>
  </td>
  <td colspan="18">
    <textarea id="text-{{no}}" class="autosize autosize-transition" style="height:150px; width:800px;"></textarea>
  </td>
</script>

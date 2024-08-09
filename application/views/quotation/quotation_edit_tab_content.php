<input type="hidden"  id="top-row" value="10" />
<div class="tab-pane fade active in" id="content">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="height:375px; overflow:scroll; padding:0px; border-top:solid 1px #dddddd;">
      <!-- <table class="table table-bordered" style="table-layout: fixed; min-width: 100%; width:2480px;"> -->
      <table class="table table-bordered tableFixHead" style="table-layout: fixed; min-width:2620px; margin-bottom:20px;">
        <thead>
          <tr class="font-size-10">
            <th class="fix-width-40 middle text-center fix-no fix-header"></th>
            <th class="fix-width-40 middle text-center fix-check fix-header"></th>
            <th class="fix-width-40 middle text-center fix-add fix-header"></th>
            <th class="fix-width-80 middle text-center fix-type fix-header">Type</th>
            <th class="fix-width-200 middle text-center fix-item fix-header">Item Code</th>
            <th class="fix-width-250 middle text-center">Item Description.</th>
            <th class="fix-width-200 middle text-center">Item Detail</th>
            <th class="fix-width-150 middle text-center">รหัสสมบูรณ์</th>
            <th class="fix-width-100 middle text-center">Quantity</th>
            <th class="fix-width-100 middle text-center">Uom</th>
            <th class="fix-width-100 middle text-center">STD Price</th>
            <th class="fix-width-100 middle text-center">ราคาเสนอล่าสุด</th>
            <th class="fix-width-100 middle text-center">ราคาขายล่าสุด</th>
            <th class="fix-width-100 middle text-center">Price</th>
            <th class="fix-width-100 middle text-center">ส่วนต่างราคา(%)</th>
            <th class="fix-width-100 middle text-center">ส่วนลด(%)</th>
            <th class="fix-width-100 middle text-center">Tax Code</th>
            <th class="fix-width-100 middle text-center">มูค่า/หน่วย หลังส่วนลด(ก่อน vat)</th>
            <th class="fix-width-100 middle text-center">มูลค่ารวม (ก่อน vat)</th>
            <th class="fix-width-100 middle text-center">GP Margin(%)</th>
            <th class="fix-width-120 middle text-center">Whs</th>
            <th class="fix-width-100 middle text-center">In Stock</th>
            <th class="fix-width-100 middle text-center">Commited</th>
            <th class="fix-width-100 middle text-center">Ordered</th>
          </tr>
        </thead>
        <tbody id="details-template">
          <?php $rows = 10; ?>
          <?php $no = 1; ?>
          <?php $whs = select_whs(); ?>
          <?php if(!empty($details)) : ?>
          <?php   foreach($details as $ds) : ?>
            <?php $cost = $ds->cost * $ds->baseQty; ?>
            <?php $gp = $ds->SellPrice - $cost; ?>
            <?php $gp = $ds->SellPrice > 0 ? round((($gp/$ds->SellPrice)*100), 2) : $gp; ?>
            <tr id="row-<?php echo $no; ?>">
              <input type="hidden" id="baseCost-<?php echo $no; ?>" value="<?php echo $ds->cost; ?>" />
              <input type="hidden" id="cost-<?php echo $no; ?>" value="<?php echo $cost; ?>" />
              <td class="text-center fix-no no" scope="row"><?php echo $no; ?></td>
              <td class="text-center fix-check" scope="row">
                <input type="checkbox" class="ace chk" id="chk-<?php echo $no; ?>" value="<?php echo $no; ?>"/>
                <span class="lbl"></span>
              </td>
              <td class=" text-center fix-add" scope="row">
                <a class="pointer" href="javascript:insertBefore(<?php echo $no; ?>)" title="Insert before"><i class="fa fa-plus"></i></a>
              </td>
              <td class="text-center fix-type" scope="row">
                <select class="form-control input-xs toggle-text" id="type-<?php echo $no; ?>" onchange="toggleText($(this))" data-no="<?php echo $no; ?>">
                  <option value="0" <?php echo is_selected('0', $ds->Type); ?>>-</option>
                  <option value="1" <?php echo is_selected('1', $ds->Type); ?>>Text</option>
                </select>
              </td>
              <?php if($ds->Type == 1) : ?>
                <td colspan="19">
                  <textarea id="text-<?php echo $no; ?>" class="autosize autosize-transition" style="height:100px; width:800px;"><?php echo $ds->LineText; ?></textarea>
                </td>
              <?php else : ?>
              <td class="fix-item" scope="row">
                <input type="text" class="form-control input-xs input-item-code" data-id="<?php echo $no; ?>" id="itemCode-<?php echo $no; ?>" value="<?php echo $ds->ItemCode; ?>"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs input-item-name" id="itemName-<?php echo $no; ?>" value="<?php echo $ds->Dscription; ?>"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs input-item-detail" id="itemDetail-<?php echo $no; ?>" value="<?php echo $ds->ItemDetail; ?>" />
              </td>
              <td class="">
                <input type="text" class="form-control input-xs free-text" maxlength="100" id="freeText-<?php echo $no; ?>" value="<?php echo $ds->FreeText; ?>" />
              </td>

              <td class="">
                <input type="text" class="form-control input-xs text-right number input-qty" id="qty-<?php echo $no; ?>" onkeyup="recalAmount($(this))" value="<?php echo number($ds->Qty, 2); ?>"/>
              </td>
              <td class="">
                <select class="form-control input-xs uom" id="uom-<?php echo $no; ?>" onchange="recalPrice($(this))">
                  <?php echo $ds->uom; ?>
                </select>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right number" id="stdPrice-<?php echo $no; ?>" value="<?php echo number($ds->stdPrice, 2); ?>" disabled/>
                <input type="hidden" id="basePrice-<?php echo $no; ?>" value="<?php echo $ds->basePrice; ?>"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right number" id="lstQuote-<?php echo $no; ?>" value="<?php echo number($ds->lastQuotePrice, 2); ?>" readonly disabled/>
                <input type="hidden" id="lastQuotePrice-<?php echo $no; ?>" value="<?php echo $ds->lastQuotePrice; ?>"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right number" id="lstPrice-<?php echo $no; ?>" value="<?php echo number($ds->lastSellPrice, 2); ?>" readonly disabled/>
                <input type="hidden" id="lastSellPrice-<?php echo $no; ?>" value="<?php echo $ds->lastSellPrice; ?>"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right number input-price" id="price-<?php echo $no; ?>" value="<?php echo number($ds->Price, 2); ?>" onkeyup="recalAmount($(this))"/>
              </td>
              <td class="">
                <input type="number" class="form-control input-xs text-right" id="priceDiff-<?php echo $no; ?>" value="<?php echo $ds->priceDiffPercent; ?>" disabled/>
              </td>
              <td class="">
                <input type="number" class="form-control input-xs text-right number input-disc1" id="disc1-<?php echo $no; ?>" value="<?php echo $ds->U_DISWEB; ?>" onkeyup="recalAmount($(this))"/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-center tax-code" id="taxCode-<?php echo $no; ?>" data-rate="<?php echo $ds->VatRate; ?>" value="<?php echo $ds->VatGroup; ?>" disabled/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right" id="priceAfDiscBfTax-<?php echo $no; ?>" value="<?php echo number($ds->SellPrice, 2); ?>" disabled>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right number input-amount" id="lineAmount-<?php echo $no; ?>" value="<?php echo number($ds->LineTotal, 2); ?>" disabled />
                <input type="hidden" class="lineDisc" id="lineDiscPrcnt-<?php echo $no; ?>" value="<?php echo $ds->DiscPrcnt; ?>">
              </td>

              <td class="">
                <input type="text" class="form-control input-xs text-right" id="gp-<?php echo $no; ?>" value="<?php echo number($gp, 2); ?>" readonly disabled>
              </td>

              <td class="">
                <select class="form-control input-xs whs" id="whs-<?php echo $no; ?>" onchange="getStock(<?php echo $no; ?>)">
                  <?php echo select_whs($ds->WhsCode); ?>
                </select>
              </td>

              <td class="">
                <input type="text" class="form-control input-xs text-right whs-qty" id="whsQty-<?php echo $no; ?>" value="<?php echo number($ds->OnHandQty); ?>" disabled/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right commit-qty" id="commitQty-<?php echo $no; ?>" value="<?php echo number($ds->IsCommited); ?>" disabled/>
              </td>
              <td class="">
                <input type="text" class="form-control input-xs text-right ordered-qty" id="orderedQty-<?php echo $no; ?>" value="<?php echo number($ds->OnOrder); ?>" disabled/>
              </td>

            <?php endif; ?>
            </tr>
            <?php $no++; ?>
          <?php   endforeach; ?>
          <?php endif; ?>

          <?php while($no <= $rows) : ?>
          <tr id="row-<?php echo $no; ?>">
            <input type="hidden" id="baseCost-<?php echo $no; ?>" value="0.00" />
            <input type="hidden" id="cost-<?php echo $no; ?>" value="0.00" />
            <td class="text-center fix-no no" scope="row"><?php echo $no; ?></td>
            <td class="text-center fix-check" scope="row">
              <input type="checkbox" class="ace chk" id="chk-<?php echo $no; ?>" value="<?php echo $no; ?>"/>
              <span class="lbl"></span>
            </td>
            <td class=" text-center fix-add" scope="row">
              <a class="pointer" href="javascript:insertBefore(<?php echo $no; ?>)" title="Insert before"><i class="fa fa-plus"></i></a>
            </td>
            <td class="text-center fix-type" scope="row">
              <select class="form-control input-xs toggle-text" id="type-<?php echo $no; ?>" onchange="toggleText($(this))" data-no="<?php echo $no; ?>">
                <option value="0">-</option>
                <option value="1">Text</option>
              </select>
            </td>
            <td class="fix-item" scope="row">
              <input type="text" class="form-control input-xs input-item-code" data-id="<?php echo $no; ?>" id="itemCode-<?php echo $no; ?>" value=""/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs input-item-name" id="itemName-<?php echo $no; ?>" value=""/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs input-item-detail" id="itemDetail-<?php echo $no; ?>" value="" />
            </td>
            <td class="">
              <input type="text" class="form-control input-xs free-text" maxlength="100" id="freeText-<?php echo $no; ?>" value="" />
            </td>

            <td class="">
              <input type="text" class="form-control input-xs text-right number input-qty" id="qty-<?php echo $no; ?>" onkeyup="recalAmount($(this))" value=""/>
            </td>
            <td class="">
              <select class="form-control input-xs uom" id="uom-<?php echo $no; ?>" onchange="recalPrice($(this))">

              </select>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right number" id="stdPrice-<?php echo $no; ?>" value="" disabled/>
              <input type="hidden" id="basePrice-<?php echo $no; ?>" value="0"/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right number" id="lstQuote-<?php echo $no; ?>" readonly disabled/>
              <input type="hidden" id="lastQuotePrice-<?php echo $no; ?>" value="0"/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right number" id="lstPrice-<?php echo $no; ?>" readonly disabled/>
              <input type="hidden" id="lastSellPrice-<?php echo $no; ?>" value="0"/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right number input-price" id="price-<?php echo $no; ?>" value="" onkeyup="recalAmount($(this))"/>
            </td>
            <td class="">
              <input type="number" class="form-control input-xs text-right" id="priceDiff-<?php echo $no; ?>" value="" disabled/>
            </td>
            <td class="">
              <input type="number" class="form-control input-xs text-right number input-disc1" id="disc1-<?php echo $no; ?>" value="" onkeyup="recalAmount($(this))"/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-center tax-code" id="taxCode-<?php echo $no; ?>" data-rate="0" value="" disabled/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right" id="priceAfDiscBfTax-<?php echo $no; ?>" value="" disabled>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right number input-amount" id="lineAmount-<?php echo $no; ?>" value="" disabled />
              <input type="hidden" class="lineDisc" id="lineDiscPrcnt-<?php echo $no; ?>" value="">
            </td>

            <td class="">
              <input type="text" class="form-control input-xs text-right" id="gp-<?php echo $no; ?>" value="0.00" readonly disabled>
            </td>

            <td class="">
              <select class="form-control input-xs whs" id="whs-<?php echo $no; ?>" onchange="getStock(<?php echo $no; ?>)">
                <?php echo $whs; ?>
              </select>
            </td>

            <td class="">
              <input type="text" class="form-control input-xs text-right whs-qty" id="whsQty-<?php echo $no; ?>" value="" disabled/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right commit-qty" id="commitQty-<?php echo $no; ?>" value="" disabled/>
            </td>
            <td class="">
              <input type="text" class="form-control input-xs text-right ordered-qty" id="orderedQty-<?php echo $no; ?>" value="" disabled/>
            </td>
          </tr>
            <?php $no++; ?>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <input type="hidden" id="row-no" value="<?php echo $no; ?>" />
    <div class="divider-hidden"></div>
    <div class="col-sm-12 col-xs-12 padding-5">
      <button type="button" class="btn btn-sm btn-info" onclick="addRow()">Add Row</button>
      <button type="button" class="btn btn-sm btn-warning" onclick="removeRow()">Delete Row</button>
    </div>
  </div>


</div>
<script id="row-template" type="text/x-handlebarsTemplate">
  <tr id="row-{{no}}">
    <input type="hidden" id="cost-{{no}}" value="0.00" />
    <input type="hidden" id="baseCost-{{no}}" value="0.00" />
    <td class="text-center fix-no no" scope="row">{{no}}</td>
    <td class="text-center fix-check" scope="row">
      <input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
      <span class="lbl"></span>
    </td>
    <td class=" text-center fix-add" scope="row">
      <a class="pointer" href="javascript:insertBefore({{no}})" title="Insert before"><i class="fa fa-plus"></i></a>
    </td>
    <td class="text-center fix-type" scope="row">
      <select class="form-control input-xs toggle-text" data-numrows="{{no}}" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
        <option value="0">-</option>
        <option value="1">Text</option>
      </select>
    </td>
    <td class="fix-item" scope="row">
      <input type="text" class="form-control input-xs input-item-code" data-id="{{no}}" id="itemCode-{{no}}" />
    </td>
    <td class="">
      <input type="text" class="form-control input-xs input-item-name" id="itemName-{{no}}" />
    </td>
    <td class="">
      <input type="text" class="form-control input-xs input-item-detail" id="itemDetail-{{no}}" />
    </td>
    <td class="">
      <input type="text" class="form-control input-xs free-text" maxlength="100" id="freeText-{{no}}" />
    </td>

    <td class="">
      <input type="text" class="form-control input-xs text-right number input-qty" id="qty-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="">
      <select class="form-control input-xs uom" id="uom-{{no}}" onchange="recalPrice($(this))"></select>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right number" id="stdPrice-{{no}}" readonly disabled/>
      <input type="hidden" id="basePrice-{{no}}" value="0"/>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right number" id="lstQuote-{{no}}" readonly disabled/>
      <input Type="hidden" id="lastQuotePrice-{{no}}" value="0" />
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right number" id="lstPrice-{{no}}" readonly disabled/>
      <input type="hidden" id="lastSellPrice-{{no}}" value="0"/>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right number input-price" id="price-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="">
      <input type="number" class="form-control input-xs text-right" id="priceDiff-{{no}}" readonly disabled/>
    </td>
    <td class="">
      <input type="number" class="form-control input-xs text-right number input-disc1" id="disc1-{{no}}" onkeyup="recalAmount($(this))"/>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-center tax-code" id="taxCode-{{no}}" data-rate="0.00" value="" disabled/>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right" id="priceAfDiscBfTax-{{no}}" value="" disabled>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right number input-amount" id="lineAmount-{{no}}" onkeyup="recalDiscount($(this))" disabled/>
      <input type="hidden" class="lineDisc" id="lineDiscPrcnt-{{no}}" value="0">
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right" id="gp-{{no}}" value="" readonly disabled>
    </td>
    <td class="">
      <select class="form-control input-xs whs" id="whs-{{no}}" onchange="getStock({{no}})">
        <?php echo $whs; ?>
      </select>
    </td>

    <td class="">
      <input type="text" class="form-control input-xs text-right whs-qty" id="whsQty-{{no}}" disabled />
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right commit-qty" id="commitQty-{{no}}" disabled/>
    </td>
    <td class="">
      <input type="text" class="form-control input-xs text-right ordered-qty" id="orderedQty-{{no}}" disabled/>
    </td>
  </tr>
</script>

<script id="normal-template" type="text/x-handlebarsTemplate">
<td class="text-center fix-no no" scope="row">{{no}}</td>
<td class="text-center fix-check" scope="row">
  <input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
  <span class="lbl"></span>
</td>
<td class="text-center fix-add" scope="row">
  <a class="pointer" href="javascript:insertBefore({{no}})" title="Insert before"><i class="fa fa-plus"></i></a>
</td>
<td class="text-center fix-type" scope="row">
  <select class="form-control input-xs toggle-text" data-numrows="{{no}}" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
    <option value="0" selected>-</option>
    <option value="1">Text</option>
  </select>
</td>
<td class="fix-item" scope="row">
  <input type="text" class="form-control input-xs input-item-code" data-id="{{no}}" id="itemCode-{{no}}" />
</td>
<td class="">
  <input type="text" class="form-control input-xs input-item-name" id="itemName-{{no}}" />
</td>
<td class="">
  <input type="text" class="form-control input-xs input-item-detail" id="itemDetail-{{no}}" />
</td>
<td class="">
  <input type="text" class="form-control input-xs free-text" maxlength="100" id="freeText-{{no}}" />
</td>

<td class="">
  <input type="text" class="form-control input-xs text-right number input-qty" id="qty-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="">
  <select class="form-control input-xs uom" id="uom-{{no}}" onchange="recalPrice($(this))"></select>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right number" id="stdPrice-{{no}}" readonly disabled/>
  <input type="hidden" id="basePrice-{{no}}" value="0"/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right number" id="lstQuote-{{no}}" readonly disabled/>
  <input Type="hidden" id="lastQuotePrice-{{no}}" value="0" />
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right number" id="lstPrice-{{no}}" readonly disabled/>
  <input type="hidden" id="lastSellPrice-{{no}}" value="0"/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right number input-price" id="price-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="">
  <input type="number" class="form-control input-xs text-right" id="priceDiff-{{no}}" readonly disabled/>
</td>
<td class="">
  <input type="number" class="form-control input-xs text-right number input-disc1" id="disc1-{{no}}" onkeyup="recalAmount($(this))"/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-center tax-code" id="taxCode-{{no}}" data-rate="0.00" value="" disabled/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right" id="priceAfDiscBfTax-{{no}}" value="" disabled>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right number input-amount" id="lineAmount-{{no}}" onkeyup="recalDiscount($(this))" disabled/>
  <input type="hidden" class="lineDisc" id="lineDiscPrcnt-{{no}}" value="0">
</td>

<td class="">
  <input type="text" class="form-control input-xs text-right" id="gp-{{no}}" value="" readonly disabled>
</td>

<td class="">
  <select class="form-control input-xs whs" id="whs-{{no}}">
    <?php echo $whs; ?>
  </select>
</td>

<td class="">
  <input type="text" class="form-control input-xs text-right whs-qty" id="whsQty-{{no}}" disabled/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right commit-qty" id="commitQty-{{no}}" disabled/>
</td>
<td class="">
  <input type="text" class="form-control input-xs text-right ordered-qty" id="orderedQty-{{no}}" disabled/>
</td>
</script>

<script id="text-template" type="text/x-handlebarsTemplate">
  <td class="text-center fix-no no" scope="row">{{no}}</td>
  <td class="text-center fix-check" scope="row">
    <input type="checkbox" class="ace chk" id="chk-{{no}}" value="{{no}}"/>
    <span class="lbl"></span>
  </td>
  <td class=" text-center fix-add" scope="row">
    <a class="pointer" href="javascript:insertBefore({{no}})" title="Insert before"><i class="fa fa-plus"></i></a>
  </td>
  <td class="text-center fix-type" scope="row">
    <select class="form-control input-xs toggle-text" data-numrows="{{no}}" id="type-{{no}}" data-no="{{no}}" onchange="toggleText($(this))">
      <option value="0">-</option>
      <option value="1" selected>Text</option>
    </select>
  </td>
  <td colspan="20">
    <textarea id="text-{{no}}" class="autosize autosize-transition" style="height:150px; width:800px;"></textarea>
  </td>
</script>

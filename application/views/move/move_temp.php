<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
		<label>Location (ปลายทาง)</label>
		<select class="width-100" id="to-zone" onchange="getProductInZone()">
			<option value="">Select</option>
			<?php echo select_zone(NULL, $doc->toWhsCode); ?>
		</select>
	</div>
  <!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
    <label>Location (ปลายทาง)</label>
    <input type="text" class="form-control input-sm" id="toZone-barcode" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">change</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-set-to-zone" onclick="getZoneTo()">OK</button>
    <button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-new-to-zone" onclick="newToZone()">เปลี่ยน</button>
  </div> -->

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty-to" value="1" />
  </div>

  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm" id="barcode-item-to" />
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">ok</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-to-zone" onclick="addToZone()">OK</button>
  </div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 margin-top-10 table-responsive"
    id="temp-table" style="min-height:350px; border-top:solid 1px #ccc;">
    <table class="table table-striped" style="margin-bottom:0px; min-width:1080px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-120 middle">Item Code</th>
          <th class="min-width-300 middle">Item Name</th>
          <th class="fix-width-120 middle text-center">Dflt Bin</th>
          <th class="fix-width-120 middle text-center">From Bin</th>
          <th class="fix-width-100 middle text-center">In Temp</th>
          <th class="fix-width-100 middle text-center">Qty</th>
          <th class="fix-width-800 middle text-center">Uom.</th>
          <th class="fix-width-120 middle text-right"></th>
        </tr>
      </thead>
      <tbody id="temp-list">

      </tbody>
    </table>
  </div>
</div>

<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
		<tr>
			<td colspan="5" class="text-right"><strong>รวม</strong></td>
			<td class="middle text-center" id="temp-total">{{ total }}</td>
			<td></td><td></td><td></td>
		</tr>
		{{else}}
			<tr class="font-size-12" id="row-temp-{{id}}">
        <input type="hidden" class="temp-qty" id="tempQty-{{id}}" value="{{qty}}" />
				<td class="middle text-center temp-no">{{ no }}</td>
				<td class="middle">{{ itemCode }}</td>
				<td class="middle">{{ itemName }}</td>
        <td class="middle text-center">{{ defaultBin }}</td>
				<td class="middle text-center">{{ binCode }}</td>
				<td class="middle text-center" id="tempLabel-{{id}}">{{label_qty}}</td>
        <td class="text-center">
  				<input type="number" class="form-control input-sm text-center" id="inputTempQty-{{id}}" />
  			</td>
				<td class="middle text-center">{{unitMsr}}</td>
				<td class="middle text-right">
          <button class="btn btn-mini btn-primary" id="btnTemp-{{id}}" onclick="addItemToZone({{id}})">ย้ายเข้า</button>
					<button class="btn btn-mini btn-danger" onclick="deleteTemp({{ id }}, '{{ itemCode }}')"><i class="fa fa-trash"></i></button>
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>

<script>
	$('#to-zone').select2();
</script>

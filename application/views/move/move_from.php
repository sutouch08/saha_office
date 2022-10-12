<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-8 padding-5">
		<label>Location (ต้นทาง)</label>
		<input type="text" class="form-control input-sm" id="fromZone-barcode" />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">newZone</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-set-zone" onclick="getZoneFrom()">OK</button>
		<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-new-zone" onclick="newFromZone()" >เปลี่ยน</button>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8 padding-5">
		<label>Item filter</label>
		<input type="text" class="form-control input-sm" id="item-filter" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-filter" onclick="setFromZoneFilter()">OK</button>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center" id="qty-from" value="1" disabled />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm" id="barcode-item-from" disabled />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">ok</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-temp" onclick="addToTemp()" disabled>OK</button>
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 margin-top-10 table-responsive"
		id="zone-table" style="min-height:350px; border-top:solid 1px #ccc;">
    	<table class="table table-striped" style="margin-bottom:0px; min-width:840px;">
      	<thead>
          <tr>
          	<th class="fix-width-40 text-center">#</th>
            <th class="fix-width-120">Item Code</th>
            <th class="fix-width-300">Item Name</th>
            <th class="fix-width-100 text-center">In stock</th>
						<th class="fix-width-100 text-center">Qty</th>
						<th class="fix-width-100 text-center">Uom.</th>
						<th class="fix-width-80 text-center"></th>
          </tr>
          </thead>
          <tbody id="zone-list"> </tbody>
        </table>
    </div>
</div>

<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="7" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr>
			<input type="hidden" id="binQty-{{itemCode}}" value="{{qty}}" />
			<td class="text-center">{{ no }}</td>
		  <td class="">{{ itemCode }}</td>
		  <td>{{ itemName }}</td>
		  <td class="text-center" id="binLabel-{{itemCode}}">{{ label_qty }}</td>
			<td class="text-center">
				<input type="number" class="form-control input-sm text-center" id="inputBinQty-{{itemCode}}" />
			</td>
			<td class="text-center">{{ unitMsr}}</td>
			<td class="text-center">
				<button type="button" class="btn btn-xs btn-primary" id="btnBin-{{itemCode}}" onclick="addItemToTemp('{{itemCode}}', '{{itemName}}')">ย้ายออก</button>
			</td>
		</tr>
	{{/if}}
{{/each}}
</script>

<!--  Search Product -->
<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เอกสาร</label>
		<select class="form-control input-sm c" id="base-type">
			<option value="">เลือก</option>
			<option value="IV">IV</option>
			<option value="DO">DO</option>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center c"	id="base-ref" value="" placeholder="เลขที่เอกสาร" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-get-po" onclick="getBaseRefDetails()">Load</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearBaseRef()">Clear</button>
	</div>

	<div class="col-lg-1 col-sm-12 visible-lg visible-sm">&nbsp;</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label>Qty</label>
		<input type="number" class="form-control input-sm text-center c" id="item-qty" value="1" />
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>Barcode</label>
		<input type="text" class="form-control input-sm text-center c" id="barcode-item" placeholder="สแกนบาร์โค้ดสินค้า" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3">
		<label class="not-show">OK</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addRow()">OK</button>
	</div>

	<div class="col-lg-1 col-xs-12 visible-lg visible-xs">&nbsp;</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show hidden-xs">confirm</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeChecked()">ลบ</button>
	</div>
</div>

<div class="divider-hidden">	</div>

<div class="modal fade" id="base-ref-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:1200px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="base-ref-title"></h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="table-layout: fixed; min-width:760px;">
          <thead>
						<tr class="font-size-11">
	            <th class="fix-width-40 text-center">#</th>
	            <th class="fix-width-200 text-center">รหัส</th>
	            <th class="min-width-200 text-center">สินค้า</th>
							<th class="fix-width-80 text-center">ราคา</th>
							<th class="fix-width-80 text-center">ส่วนลด</th>
	            <th class="fix-width-80 text-center">จำนวนขาย</th>
	            <th class="fix-width-80 text-center">จำนวนคืน</th>
						</tr>
          </thead>
          <tbody id="base-ref-table">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="returnAll()">คืนทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addToReturn()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>

<script id="base-ref-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="font-size-11" id="rows-{{uid}}">
			<td class="middle text-center">{{no}}</td>
			<td class="middle">{{ItemCode}}</td>
			<td class="middle">{{Dscription}}</td>
			<td class="middle text-center">{{PriceLabel}}</td>
			<td class="middle text-center">{{DiscPrcnt}} %</td>
			<td class="middle text-center">{{OpenQtyLabel}}</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-center base-ref-qty"
					id="base-ref-{{uid}}"
					data-uid="{{uid}}"
					data-code="{{ItemCode}}"
					data-name="{{Dscription}}"
					data-basetype="{{baseType}}"
					data-basecode="{{DocNum}}"
					data-baseentry="{{DocEntry}}"
					data-baseline="{{LineNum}}"
					data-open="{{OpenQty}}"
					data-price="{{Price}}"
					data-bfprice="{{PriceBefDi}}"
					data-afprice="{{PriceAfVAT}}"
					data-discprcnt="{{DiscPrcnt}}"
					data-vatcode="{{VatGroup}}"
					data-vatrate="{{VatPrcnt}}"
					data-unitmsr="{{unitMsr}}"
					data-numpermsr="{{NumPerMsr}}"
					data-uomentry="{{UomEntry}}"
					data-uomcode="{{UomCode}}"
					data-unitmsr2="{{unitMsr2}}"
					data-numpermsr2="{{NumPerMsr2}}"
					data-uomentry2="{{UomEntry2}}"
					data-uomcode2="{{UomCode2}}"
					data-slp="{{SlpCode}}"
					value=""	/>
			</td>
		</tr>
	{{/each}}
</script>

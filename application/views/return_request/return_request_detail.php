
<div class="row" style="margin-left:-7px; margin-right:-7px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="receiveTable">
		<table class="table table-bordered tableFixHead" style="font-size:11px; min-width:1110px; margin-bottom:0;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center fix-header">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="toggleCheckAll($(this))">
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="fix-width-100 text-center fix-header">รหัสสินค้า</th>
					<th class="min-width-250 text-center fix-header">ชื่อสินค้า</th>
					<th class="fix-width-80 text-center fix-header">หน่วยนับ</th>
					<th class="fix-width-80 text-center fix-header">เอกสาร</th>
					<th class="fix-width-80 text-center fix-header">เลขที่</th>
					<th class="fix-width-80 text-center fix-header">ราคา</th>
					<th class="fix-width-80 text-center fix-header">ส่วนลด</th>
					<th class="fix-width-80 text-center fix-header">Open</th>
					<th class="fix-width-100 text-center fix-header">จำนวน</th>
					<th class="fix-width-100 text-center fix-header">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="return-table">
			<?php $totalQty = 0; ?>
			<?php $totalAmount = 0; ?>
			<?php if( ! empty($details)) : ?>
				<?php $no = 1; ?>
				<?php foreach($details as $rs) : ?>
					<?php $uid = $rs->uid; ?>
					<?php $active = empty($rs->LineStatus) ? NULL : ($rs->LineStatus == 'O' ? NULL : 'disabled'); ?>
					<?php $limit = empty($rs->limit) ? -1 : $rs->limit; ?>
					<?php $readonly = empty($rs->BaseRef) ? "" : "readonly"; ?>
							<tr id="row-<?php echo $uid; ?>" class="return-rows">
								<td class="middle text-center">
									<label><input type="checkbox" class="ace chk" value="<?php echo $uid; ?>" /><span class="lbl"></span></label>
								</td>
								<td class="middle text-center no"><?php echo $no; ?></td>
								<td class="middle">
									<input type="text" class="form-control input-xs text-label item-code" id="item-code-<?php echo $uid; ?>"
									data-prev="<?php echo $rs->ItemCode; ?>" data-uid="<?php echo $uid; ?>" data-id="<?php echo $rs->id; ?>"
									value="<?php echo $rs->ItemCode; ?>" <?php echo $readonly; ?> />
								</td>
								<td class="middle">
									<input type="text" class="form-control input-xs text-label" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" readonly />
								</td>
								<td class="middle text-center"><?php echo $rs->UnitMsr; ?></td>
								<td class="middle text-center"><?php echo $rs->BaseType; ?></td>
								<td class="middle text-center"><?php echo $rs->BaseRef; ?></td>
								<td class="middle text-center">
									<input type="text" class="form-control input-xs text-label text-center price"
									id="item-price-<?php echo $uid; ?>"
									onchange="recalAmount('<?php echo $uid; ?>')"
									value="<?php echo number($rs->PriceBefDi, 2); ?>" <?php echo $readonly; ?> />
								</td>
								<td class="middle text-center">
									<input type="text" class="form-control input-xs text-label text-center disc"
									id="item-disc-<?php echo $uid; ?>"
									onchange="recalAmount('<?php echo $uid; ?>')"
									value="<?php echo number($rs->DiscPrcnt, 2); ?>" <?php echo $readonly; ?> />
								</td>
								<td class="middle text-center"><?php echo number($rs->OpenQty, 2); ?></td>
								<td class="middle text-center">
									<input type="text" class="form-control input-xs text-right text-label row-qty"
										id="<?php echo $uid; ?>"
										onchange="recalAmount('<?php echo $uid; ?>')"
										data-uid="<?php echo $uid; ?>"
										data-id="<?php echo $rs->id; ?>"
										data-code="<?php echo $rs->ItemCode; ?>"
										data-name="<?php echo $rs->ItemName; ?>"
										data-basetype="<?php echo $rs->BaseType; ?>"
										data-basecode="<?php echo $rs->BaseRef; ?>"
										data-baseline="<?php echo $rs->BaseLine; ?>"
										data-baseentry="<?php echo $rs->BaseEntry; ?>"
										data-price="<?php echo $rs->Price; ?>"
										data-discprcnt="<?php echo $rs->DiscPrcnt; ?>"
										data-bfprice="<?php echo $rs->PriceBefDi; ?>"
										data-afprice="<?php echo $rs->PriceAfVAT; ?>"
										data-vatcode="<?php echo $rs->VatGroup; ?>"
										data-vatrate="<?php echo $rs->VatRate; ?>"
										data-unitmsr="<?php echo $rs->UnitMsr; ?>"
										data-unitmsr2="<?php echo $rs->UnitMsr2; ?>"
										data-numpermsr="<?php echo $rs->NumPerMsr; ?>"
										data-numpermsr2="<?php echo $rs->NumPerMsr2; ?>"
										data-uomentry="<?php echo $rs->UomEntry; ?>"
										data-uomentry2="<?php echo $rs->UomEntry2; ?>"
										data-uomcode="<?php echo $rs->UomCode; ?>"
										data-uomcode2="<?php echo $rs->UomCode2; ?>"
										data-open="<?php echo $rs->OpenQty; ?>"
										data-slp="<?php echo $rs->SlpCode; ?>"
										value="<?php echo number($rs->Qty, 2); ?>" <?php echo $active; ?>/>
								</td>
								<td class="middle text-right">
									<input type="text" class="form-control input-xs text-right text-label row-total" id="row-total-<?php echo $uid; ?>" value="<?php echo number($rs->LineTotal, 2); ?>" readonly />
									<input type="hidden" id="row-vat-amount-<?php echo $uid; ?>" value="<?php echo $rs->VatSum; ?>" />
								</td>
							</tr>
							<?php $no++; ?>
							<?php $totalQty += $rs->Qty; ?>
							<?php $totalAmount += $rs->LineTotal; ?>
						<?php endforeach; ?>
					<?php endif; ?>
			</tbody>
		</table>
  </div>

	<div class="col-lg-12 col-md-12 col-xs-12 padding-5" style="padding-top:5px;">
		<button type="button" class="btn btn-white btn-primary" onclick="addBlankRow()">Add Row</button>
		<button type="button" class="btn btn-white btn-warning" onclick="removeChecked()">Delete Row</button>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider"></div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user->emp_name; ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $this->user->uname; ?>" />
        </div>
      </div>
    </div>
  </div>


	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-right-0">
    <div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-3 col-sm-2 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->TotalQty, 2); ?>" disabled>
        </div>
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal - $doc->VatSum, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>

</div> <!-- row -->


<script id="row-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{uid}}" class="return-rows">
			<td class="middle text-center">
				<label><input type="checkbox" class="ace chk" value="{{uid}}" /><span class="lbl"></span></label>
			</td>
			<td class="middle text-center no"></td>
			<td class="middle">
				<input type="text" class="form-control input-xs text-label item-code" id="item-code-{{uid}}" data-prev="{{ItemCode}}" data-uid="{{uid}}" value="{{ItemCode}}" />
			</td>
			<td class="middle">
				<input type="text" class="form-control input-xs text-label" id="item-name-{{uid}}" value="{{ItemName}}" readonly />
				</td>
			<td class="middle text-center">{{unitMsr}}</td>
			<td class="middle text-center">{{baseType}}</td>
			<td class="middle text-center">{{DocNum}}</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-xs text-label text-center price" id="item-price-{{uid}}" onchange="recalPrice('{{uid}}')" value="{{PriceLabel}}" readonly/>
			</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-xs text-label text-center disc" id="item-disc-{{uid}}" onchange="recalAmount('{{uid}}')" value="{{DiscPrcnt}}" readonly/>
			</td>
			<td class="middle text-center">{{OpenQtyLabel}}</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-xs text-right row-qty"
					id="{{uid}}"
          onchange="recalAmount('{{uid}}')"
					data-uid="{{uid}}"
					data-input="load"
					data-code="{{ItemCode}}"
          data-name="{{ItemName}}"
					data-basetype="{{baseType}}"
					data-basecode="{{DocNum}}"
          data-baseline="{{LineNum}}"
          data-baseentry="{{DocEntry}}"
					data-price="{{Price}}"
					data-discprcnt="{{DiscPrcnt}}"
					data-bfprice="{{PriceBefDi}}"
					data-afprice="{{PriceAfVAT}}"
					data-vatcode="{{VatGroup}}"
          data-vatrate="{{VatRate}}"
					data-unitmsr="{{unitMsr}}"
					data-unitmsr2="{{unitMsr2}}"
					data-numpermsr="{{NumPerMsr}}"
					data-numpermsr2="{{NumPerMsr2}}"
					data-uomentry="{{UomEntry}}"
					data-uomentry2="{{UomEntry2}}"
					data-uomcode="{{UomCode}}"
					data-uomcode2="{{UomCode2}}"
          data-open="{{OpenQty}}"
					data-slp="{{SlpCode}}"
          value="{{QtyLabel}}" />
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-xs text-right text-label row-total" id="row-total-{{uid}}" value="{{LineTotal}}" readonly />
				<input type="hidden" id="row-vat-amount-{{uid}}" value="{{VatAmount}}" />
			</td>
		</tr>
	{{/each}}
</script>

<script id="barcode-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{uid}}" class="return-rows">
		<td class="middle text-center">
			<label><input type="checkbox" class="ace chk" value="{{uid}}" /><span class="lbl"></span></label>
		</td>
		<td class="middle text-center no"></td>
		<td class="middle">
			<input type="text" class="form-control input-xs text-label item-code" id="item-code-{{uid}}" data-prev="{{ItemCode}}" data-uid="{{uid}}" value="{{ItemCode}}" />
		</td>
		<td class="middle">
			<input type="text" class="form-control input-xs text-label" id="item-name-{{uid}}" value="{{ItemName}}" readonly />
			</td>
		<td class="middle text-center">{{unitMsr}}</td>
		<td class="middle text-center">{{baseType}}</td>
		<td class="middle text-center">{{DocNum}}</td>
		<td class="middle text-center">
			<input type="text" class="form-control input-xs text-label text-center price" id="item-price-{{uid}}" onchange="recalPrice('{{uid}}')" value="{{PriceLabel}}" readonly/>
		</td>
		<td class="middle text-center">
			<input type="text" class="form-control input-xs text-label text-center disc" id="item-disc-{{uid}}" onchange="recalAmount('{{uid}}')" value="{{DiscPrcnt}}" readonly/>
		</td>
		<td class="middle text-center">{{OpenQtyLabel}}</td>
		<td class="middle text-center">
			<input type="text" class="form-control input-xs text-right row-qty"
				id="{{uid}}"
				onchange="recalAmount('{{uid}}')"
				data-uid="{{uid}}"
				data-input="barcode"
				data-code="{{ItemCode}}"
				data-name="{{ItemName}}"
				data-basetype="{{baseType}}"
				data-basecode="{{DocNum}}"
				data-baseline="{{LineNum}}"
				data-baseentry="{{DocEntry}}"
				data-price="{{Price}}"
				data-discprcnt="{{DiscPrcnt}}"
				data-bfprice="{{PriceBefDi}}"
				data-afprice="{{PriceAfVAT}}"
				data-vatcode="{{VatGroup}}"
				data-vatrate="{{VatRate}}"
				data-unitmsr="{{unitMsr}}"
				data-unitmsr2="{{unitMsr2}}"
				data-numpermsr="{{NumPerMsr}}"
				data-numpermsr2="{{NumPerMsr2}}"
				data-uomentry="{{UomEntry}}"
				data-uomentry2="{{UomEntry2}}"
				data-uomcode="{{UomCode}}"
				data-uomcode2="{{UomCode2}}"
				data-open="{{OpenQty}}"
				data-slp="{{SlpCode}}"
				value="{{QtyLabel}}" />
		</td>
		<td class="middle text-right">
			<input type="text" class="form-control input-xs text-right text-label row-total" id="row-total-{{uid}}" value="{{LineTotal}}" readonly />
			<input type="hidden" id="row-vat-amount-{{uid}}" value="{{VatAmount}}" />
		</td>
	</tr>
</script>

<script id="new-row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{uid}}" class="return-rows">
	<td class="middle text-center">
		<label><input type="checkbox" class="ace chk" value="{{uid}}" /><span class="lbl"></span></label>
	</td>
	<td class="middle text-center no"></td>
	<td class="middle">
		<input type="text" class="form-control input-xs text-label item-code" id="item-code-{{uid}}" data-prev="" data-uid="{{uid}}" value="" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-xs text-label" id="item-name-{{uid}}" value="" readonly />
	</td>
	<td class="middle  text-center">
		<select class="form-control input-xs text-label uom" id="uom-{{uid}}" onchange="recalPrice($(this))"></select>
	</td>
	<td class="middle text-center"></td>
	<td class="middle text-center"></td>
	<td class="middle text-center">
		<input type="text" class="form-control input-xs text-label text-center price" id="item-price-{{uid}}" onchange="recalPrice('{{uid}}')" value="0.00" />
	</td>
	<td class="middle text-center">
		<input type="text" class="form-control input-xs text-label text-center disc" id="item-disc-{{uid}}" onchange="recalAmount('{{uid}}')" value="0.00" />
	</td>
	<td class="middle text-center"></td>
	<td class="middle text-center">
		<input type="text" class="form-control input-xs text-right row-qty"
			id="{{uid}}"
			onchange="recalAmount('{{uid}}')"
			data-uid="{{uid}}"
			data-input="{{input}}"
			data-code=""
			data-name=""
			data-basetype=""
			data-basecode=""
			data-baseline=""
			data-baseentry=""
			data-price=""
			data-discprcnt=""
			data-bfprice=""
			data-afprice=""
			data-vatcode=""
			data-vatrate=""
			data-unitmsr=""
			data-unitmsr2=""
			data-numpermsr=""
			data-numpermsr2=""
			data-uomentry=""
			data-uomentry2=""
			data-uomcode=""
			data-uomcode2=""
			data-open=""
			data-slp=""
			value="" />
	</td>
	<td class="middle text-right">
		<input type="text" class="form-control input-sm text-right text-label row-total" id="row-total-{{uid}}" value="" readonly />
		<input type="hidden" id="row-vat-amount-{{uid}}" value="0.00" />
	</td>
</tr>
</script>

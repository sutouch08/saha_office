<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<button type="button" class="btn btn-sm btn-success" id="btn-save" onclick="saveAdd()" disabled><i class="fa fa-save"></i> &nbsp; Save</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $code; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="docDate" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center" id="toWhsCode" value="<?php echo $toWhsCode; ?>" readonly />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-5-harf col-xs-6 padding-5">
		<label>พื้นที่จัดเก็บ</label>
		<input type="text" class="form-control input-sm text-center" id="toBinCode" value=""  placeholder="ระบุ พื้นที่จัดเก็บ"/>
	</div>
	<div class="col-lg-3 col-md-2-harf col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" max-length="254" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>Pallet No.</label>
		<input type="text" class="form-control input-sm text-center" id="pallet-code" placeholder="ค้นหาพาเลท" autofocus/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add" onclick="addToList()">Add</button>
		<button type="button" class="btn btn-xs btn-warning btn-block hide" id="btn-change" onclick="changePallet()">Change</button>
	</div>


	<div class="col-xs-6 visible-xs">	</div>

</div>

<hr class="padding-5">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered" style="min-width:1100px;">
			<thead>
				<tr>
					<th class="middle text-center" style="width:50px;">#</th>
					<th class="middle text-center" style="width:100px;">Item</th>
					<th class="middle text-center" style="width:200px;">Description</th>
					<th class="middle text-center" style="width:100px;">Pallet No.</th>
					<th class="middle text-center" style="width:100px;">SO No.</th>
					<th class="middle text-center" style="width:100px;">Pick No.</th>
					<th class="middle text-center" style="width:100px;">Pack No.</th>
					<th class="middle text-center" style="width:100px;">ต้นทาง</th>
					<th class="middle text-center" style="width:100px;">ปลายทาง</th>
					<th class="middle text-center" style="width:100px;">จำนวน</th>
					<th class="middle text-center" style="width:50px;">Uom</th>
				</tr>
			</thead>
			<tbody id="transfer-table">
			<?php $no = 1; ?>
			<?php if(!empty($details)) : ?>
				<?php foreach($details as $rs) : ?>
				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->ItemCode; ?></td>
					<td class="middle"></td>
					<td class="middle text-center"><?php echo $rs->palletCode; ?></td>
					<td class="middle text-center"><?php echo $rs->orderCode; ?></td>
					<td class="middle text-center"><?php echo $rs->pickCode; ?></td>
					<td class="middle text-center"><?php echo $rs->packCode; ?></td>
					<td class="middle text-center"><?php echo $rs->fromBin; ?></td>
					<td class="middle text-center"><?php echo $rs->toBin; ?></td>
					<td class="middle text-right"><?php echo round($rs->qty, 2); ?></td>
					<td class="middle"><?php echo $rs->unitMsr; ?></td>
				</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="text-right" style="font-size:18px;">รวม</td>
					<td colspan="2" class="text-center" style="font-size:18px;" id="total-qty">0</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script id="transfer-template" type="text/x-handlebarsTemplate">
hu;;
++++++++++++++++++++<tr id="row-{{id}}">
	<td class="middle text-center no"></td>
	<td class="middle">{{ItemCode}}</td>
	<td class="middle">{{ItemName}}</td>
	<td class="middle text-center">{{palletCode}}</td>
	<td class="middle text-center">{{orderCode}}</td>
	<td class="middle text-center">{{pickCode}}</td>
	<td class="middle text-center">{{packCode}}</td>
	<td class="middle text-center">{{fromBin}}</td>
	<td class="middle text-center">{{toBin}}</td>
	<td class="middle text-right qty">{{qty}}</td>
	<td class="middle">{{unitMsr}}</td>
</tr>

</script>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

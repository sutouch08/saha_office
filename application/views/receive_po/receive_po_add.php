<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100" value="" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" id="date-add" value="<?php echo date('d-m-Y'); ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code" placeholder="รหัสผู้ขาย" value=""/>
	</div>
	<div class="col-lg-5-harf col-md-6-harf col-sm-6 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" readonly/>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice" placeholder="ใบส่งสินค้า" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Currency</label>
		<select class="width-100" id="DocCur" onchange="changeRate()">
			<?php echo select_currency("THB"); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" id="DocRate" value="1.00" />
	</div>

	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" onchange="zoneInit()">
			<option value="">Select</option>
			<?php echo select_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>
	<div class="col-lg-6 col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100" id="remark" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">Add</button>
	</div>

	<input type="hidden" name="receive_code" id="receive_code" value="" />
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
	<input type="hidden" id="no" value="0" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
				<i class="ace-icon fa fa-save icon-on-left"></i>
				บันทึก
				<i class="ace-icon fa fa-angle-down icon-on-right"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="primary">
					<a href="javascript:checkLimit(0)">บันทึกเป็นดราฟท์</a>
				</li>
				<li class="success">
					<a href="javascript:checkLimit(1)">บันทึกรับเข้าทันที</a>
				</li>
				<li class="purple">
					<a href="javascript:checkLimit(3)">บันทึกรอรับ</a>
				</li>
			</ul>
		</div>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="width-100 text-center r" id="date-add" data-prev="<?php echo thai_date($doc->date_add); ?>" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="width-100 text-center r" id="vendor-code" placeholder="รหัสผู้ขาย" data-prev="<?php echo $doc->vendor_code; ?>" value="<?php echo $doc->vendor_code; ?>" disabled/>
	</div>
	<div class="col-lg-5-harf col-md-6-harf col-sm-6 col-xs-8 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="width-100 r" id="vendor-name" placeholder="ชื่อผู้ขาย" data-prev="<?php echo $doc->vendor_code; ?>" value="<?php echo $doc->vendor_name; ?>" readonly disabled/>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="width-100 text-center r" id="invoice" placeholder="ใบส่งสินค้า" data-prev="<?php echo $doc->invoice_code; ?>" value="<?php echo $doc->invoice_code; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Currency</label>
		<select class="width-100 r" id="DocCur" data-prev="<?php echo $doc->Currency; ?>" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" data-prev="<?php echo $doc->Rate; ?>" id="DocRate" value="<?php echo $doc->Rate; ?>" disabled />
	</div>

	<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse" data-prev="<?php echo $doc->warehouse_code; ?>" onchange="zoneInit()" disabled>
			<option value="">Select</option>
			<?php echo select_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-6 col-md-10-harf col-sm-10-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> Edit</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="changeHeader()"><i class="fa fa-save"></i> Update</button>
	</div>

	<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('receive_po/receive_po_control'); ?>
<?php $this->load->view('receive_po/receive_po_detail'); ?>

<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/receive_po/receive_po_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

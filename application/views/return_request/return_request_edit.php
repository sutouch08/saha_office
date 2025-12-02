<?php $this->load->view('include/header'); ?>
<?php $this->load->view('return_request/style'); ?>
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
					<a href="javascript:save('P')">บันทึกเป็นดราฟท์</a>
				</li>
				<li class="success">
					<a href="javascript:save('C')">บันทึกทันที</a>
				</li>
			</ul>
		</div>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Doc Date</label>
		<input type="text" class="width-100 text-center r" id="date-add" data-prev="<?php echo thai_date($doc->date_add); ?>" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Posting</label>
		<input type="text" class="width-100 text-center r" id="posting-date" data-prev="<?php echo thai_date($doc->posting_date); ?>" value="<?php echo thai_date($doc->posting_date); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="width-100 text-center r" id="customer-code" placeholder="รหัสผู้ขาย" data-prev="<?php echo $doc->CardCode; ?>" value="<?php echo $doc->CardCode; ?>" />
	</div>
	<div class="col-lg-5 col-md-5-harf col-sm-5 col-xs-12 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="width-100 r" id="customer-name" placeholder="ชื่อผู้ขาย" data-prev="<?php echo $doc->CardName; ?>" value="<?php echo $doc->CardName; ?>" readonly />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Currency</label>
		<select class="width-100 r" id="DocCur" data-prev="<?php echo $doc->Currency; ?>" >
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" data-prev="<?php echo $doc->Rate; ?>" id="DocRate" value="<?php echo $doc->Rate; ?>"  />
	</div>

	<div class="col-lg-4 col-md-8 col-sm-8 col-xs-12 padding-5">
		<label>คลัง</label>
		<select class="width-100 r" id="warehouse">
			<option value="">เลือก</option>
			<?php echo select_warehouse($doc->WhsCode); ?>
		</select>
	</div>

	<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" id="remark" data-prev="<?php echo $doc->remark; ?>" value="<?php echo $doc->remark; ?>"/>
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="sale-vat-code" value="<?php echo getConfig('SALE_VAT_CODE'); ?>" />
	<input type="hidden" id="sale-vat-rate" value="<?php echo getConfig('SALE_VAT_RATE'); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('return_request/return_request_control'); ?>
<?php $this->load->view('return_request/return_request_detail'); ?>

<script>
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/return_request/return_request.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/return_request/return_request_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/return_request/return_request_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

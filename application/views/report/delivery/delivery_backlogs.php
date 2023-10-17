<?php $this->load->view('include/header'); ?>
<?php $this->load->view('report/delivery/style_sheet.php'); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
		<h2 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Export</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="hidden-print padding-5"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-lg-2 col-md-2-harf col-sm-3-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<select class="form-control input-sm" id="dateType" name="dateType">
			<option value="P">Posting Date</option>
			<option value="R">Required Delivery Date</option>
		</select>
	</div>
	<?php $from_date = date_create(date('d-m-Y', strtotime("-365 days"))); ?>
	<?php $min_date = date_create(date('d-m-Y', strtotime("2023-09-01"))); ?>
	<?php $from_date = $from_date < $min_date ? $min_date : $from_date; ?>
	<?php $to_date = date_create(date('d-m-Y')); ?>
  <div class="col-lg-2-harf col-md-2-harf col-sm-4 col-xs-6 padding-5">
    <label class="not-show">วันที่</label>
    <div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $from_date->format('d-m-Y');?>" placeholder="จาก" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $to_date->format('d-m-Y'); ?>" placeholder="ถึง" readonly />
		</div>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ประเภทเอกสาร</label>
		<select class="form-control input-sm" id="docType" name="docType">
			<option value="all">ทั้งหมด</option>
			<option value="IV">IV</option>
			<option value="DO">DO</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block">ลูกค้า</label>
    <select class="form-control input-sm" id="allCust" name="allCust" onchange="toggleCustomer()">
			<option value="1">ทั้งหมด</option>
			<option value="0">ระบุ</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label class="not-show">start</label>
		<input type="text" class="form-control input-sm text-center" name="custFrom" id="custFrom" placeholder="รหัสลูกค้าเริ่มต้น" disabled/>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label class="not-show">End</label>
		<input type="text" class="form-control input-sm text-center" name="custTo" id="custTo" placeholder="รหัสลูกค้าสิ้นสุด" disabled/>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-8 padding-5">
		<label>สายการจัดส่ง</label>
		<select class="form-control input-sm" id="route" name="route">
			<option value="all">ทั้งหมด</option>
			<?php echo select_route(NULL, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>การจัดสาย</label>
		<select class="form-control input-sm" id="delivery_state" name="delivery_state">
			<option value="all">ทั้งหมด</option>
			<option value="O">Open</option>
			<option value="R">Release</option>
			<option value="C">Closed</option>
			<option value="NULL">ไม่ได้จัดสาย</option>
		</select>
	</div>

	<input type="hidden" id="token" name="token">
</div>
</form>
<hr class="padding-5 margin-top-15 margin-bottom-15">
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive"  style="padding-top:1px; max-height:600px;">
		<table class="table table-bordered tableFixHead" style="min-width:2300px; margin-bottom:20px;">
			<tr class="font-size-12 freez">
				<th class="fix-width-40 text-center">#</th>
				<th class="fix-width-100 text-center">วันที่เอกสาร</th>
				<th class="fix-width-100 text-center">วันที่นัดจัดส่ง</th>
				<th class="fix-width-100 text-center">ประเภทเอกสาร</th>
				<th class="fix-width-100">เลขที่เอกสาร</th>
				<th class="fix-width-100">ใบจัดสาย</th>
				<th class="fix-width-100 text-center">สถานะใบจัดสาย</th>
				<th class="fix-width-100 text-center">จำนวนวันค้างส่ง</th>
				<th class="fix-width-100 text-center">ความเร่งด่วน</th>
				<th class="fix-width-100 text-center">รหัสลูกค้า</th>
				<th class="fix-width-100 text-center">ชื่อลูกค้า</th>
				<th class="fix-width-100 text-center">มูลค่าบิล</th>
				<th class="fix-width-100 text-center">สถานะการจัดส่ง</th>
				<th class="fix-width-100 text-center">วันที่สถานะ</th>
				<th class="fix-width-300 text-center">Ship To</th>
				<th class="fix-width-100 text-center">Zip Code</th>
				<th class="fix-width-200 text-center">เส้นทางการจัดส่งแนะนำ</th>
				<th class="fix-width-100 text-center">Remark</th>
				<th class="fix-width-200 text-center">Remark Internal</th>
				<th class="fix-width-100 text-center">ชื่อผู้ขาย</th>
			</tr>
			<tbody id="result">

			</tbody>
		</table>
  </div>
</div>

<script id="report-template" type="text/x-handlebars-template">
{{#each this}}
    <tr class="font-size-12 {{color}}">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{DocDate}}</td>
      <td class="middle text-center">{{Required_date}}</td>
			<td class="middle text-center">{{DocType}}</td>
      <td class="middle text-center">{{DocNum}}</td>
      <td class="middle text-center">{{Delivery_code}}</td>
      <td class="middle text-center">{{Delivery_state}}</td>
      <td class="middle text-center">{{Diff_date}}</td>
			<td class="middle">{{Urgency_text}}</td>
      <td class="middle text-center">{{CardCode}}</td>
      <td class="middle">{{CardName}}</td>
      <td class="middle text-right">{{DocTotal}}</td>
      <td class="middle text-center">{{Deliver_status}}</td>
			<td class="middle text-center">{{Deliver_date}}</td>
      <td class="middle">{{ShipTo}}</td>
			<td class="middle text-center">{{ZipCode}}</td>
			<td class="middle">{{Route}}</td>
			<td class="middle">{{Remark}}</td>
			<td class="middle">{{RemarkInternal}}</td>
			<td class="middle">{{Owner}}</td>
    </tr>
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/delivery_backlogs.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

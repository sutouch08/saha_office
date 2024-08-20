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
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>วันที่</label>
		<select class="form-control input-sm" id="dateType" name="dateType">
			<option value="P">Doc Date</option>
			<option value="R">Due Date</option>
		</select>
	</div>
	<?php $from_date = date_create(date('01-m-Y')); ?>
	<?php $to_date = date_create(date('d-m-Y')); ?>
  <div class="col-lg-2-harf col-md-2-harf col-sm-4 col-xs-8 padding-5">
    <label class="not-show">วันที่</label>
    <div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 from-date text-center e" id="fromDate" name="fromDate" value="<?php echo $from_date->format('d-m-Y');?>" placeholder="จาก" />
			<input type="text" class="form-control input-sm width-50 to-date text-center e" id="toDate" name="toDate" value="<?php echo $to_date->format('d-m-Y'); ?>" placeholder="ถึง"  />
		</div>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block">ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center" name="customer" id="customer" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่ออเดอร์</label>
		<input type="text" class="form-control input-sm text-center" name="soCode" id="soCode" />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสสินค้า</label>
		<input type="text" class="form-control input-sm text-center" name="itemCode" id="itemCode" />
	</div>
	<input type="hidden" id="token" name="token">
</div>
</form>
<hr class="padding-5 margin-top-15 margin-bottom-15">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1"  style="padding:0px; min-height:300px; max-height:600px; overflow:auto;">
		<table class="table table-bordered tableFixHead" style="min-width:1800px; margin-bottom:20px;">
			<tr class="font-size-12 freez">
				<th class="fix-width-40 text-center">#</th>
				<th class="fix-width-100 text-center">Doc Date</th>
				<th class="fix-width-100 text-center">Due Date</th>
				<th class="fix-width-100 text-center">Order No.</th>
				<th class="fix-width-100 text-center">Item Code</th>
				<th class="fix-width-250 text-center">Description</th>
				<th class="fix-width-100 text-center">Uom</th>
				<th class="fix-width-100 text-center">Price</th>
				<th class="fix-width-100 text-center">Ordered</th>
				<th class="fix-width-100 text-center">Open</th>
				<th class="fix-width-100 text-center">Released</th>
				<th class="fix-width-100 text-center">Balance</th>
				<th class="fix-width-100 text-center">Available</th>
				<th class="fix-width-100 text-center">Code</th>
				<th class="fix-width-300 text-center">Name</th>
			</tr>
			<tbody id="result">

			</tbody>
		</table>
  </div>
</div>

<script id="report-template" type="text/x-handlebars-template">
{{#each this}}
    <tr class="font-size-12 {{color}}">
      <td class="text-center">{{no}}</td>
      <td class="text-center">{{DocDate}}</td>
      <td class="text-center">{{DocDueDate}}</td>
			<td class="text-center">{{DocNum}}</td>
      <td class="">{{ItemCode}}</td>
      <td class="">{{Dscription}}</td>
      <td class="text-center">{{unitMsr}}</td>
      <td class="text-right">{{Price}}</td>
			<td class="text-right">{{Qty}}</td>
      <td class="text-right">{{OpenQty}}</td>
      <td class="text-right">{{Released}}</td>
      <td class="text-right">{{OnHand}}</td>
      <td class="text-right">{{Available}}</td>
			<td class="">{{CardCode}}</td>
			<td class="">{{CardName}}</td>
    </tr>
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/sales_order_backlogs.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

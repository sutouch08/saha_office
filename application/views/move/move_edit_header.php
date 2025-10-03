
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" id="docDate" value="<?php echo thai_date($doc->DocDate, FALSE); ?>" readonly disabled />
	</div>
	<div class="col-lg-3-harf col-md-3-harf col-sm-4 col-xs-12 padding-5">
		<label>คลังต้นทาง</label>
		<select class="width-100 edit" id="fromWhsCode" onchange="fromZoneInit()" disabled>
			<option value="">ไม่ระบุ</option>
			<?php echo select_warehouse($doc->fromWhsCode); ?>
		</select>
		<!-- <input type="text" class="form-control input-sm text-center edit" id="fromWhsCode" value="<?php echo $doc->fromWhsCode; ?>" disabled /> -->
	</div>
	<div class="col-lg-3-harf col-md-3-harf col-sm-4-harf col-xs-12 padding-5">
		<label>คลังปลายทาง</label>
		<select class="width-100 edit" id="toWhsCode" onchange="toZoneInit()" disabled>
			<option value="">ไม่ระบุ</option>
			<?php echo select_warehouse($doc->toWhsCode); ?>
		</select>
		<!-- <input type="text" class="form-control input-sm text-center edit" id="toWhsCode" value="<?php echo $doc->toWhsCode; ?>" disabled /> -->
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-12 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->uname; ?>" disabled />
	</div>

	<div class="col-lg-11 col-md-11 col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" id="remark" max-length="254" value="<?php echo escape_quot($doc->remark); ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-12 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="edit()">Edit</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()">Update</button>
	</div>
</div>

<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
<hr class="margin-top-15 padding-5">

<script>
	$('#fromWhsCode').select2();
	$('#toWhsCode').select2();
</script>

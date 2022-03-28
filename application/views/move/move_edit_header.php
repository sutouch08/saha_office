
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" id="docDate" value="<?php echo thai_date($doc->DocDate, FALSE); ?>" readonly disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm text-center edit" id="fromWhsCode" value="<?php echo $doc->fromWhsCode; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center edit" id="toWhsCode" value="<?php echo $doc->toWhsCode; ?>" disabled />
	</div>

	<div class="col-lg-5 col-md-4 col-sm-3 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" id="remark" max-length="254" value="<?php echo escape_quot($doc->remark); ?>" disabled/>
	</div>

	<div class="col-xs-6 visible-xs">	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="edit()">Edit</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()">Update</button>
	</div>
</div>

<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />

<hr class="margin-top-15 padding-5 hidden-xs">

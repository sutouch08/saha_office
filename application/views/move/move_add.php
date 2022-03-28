<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $code; ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="docDate" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm text-center" id="fromWhsCode" value="" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center" id="toWhsCode" value="" />
	</div>

	<div class="col-lg-5 col-md-4 col-sm-3 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" max-length="254" />
	</div>

	<div class="col-xs-6 visible-xs">	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add" onclick="add()">Add</button>
	</div>
</div>

<hr class="margin-top-15 padding-5 hidden-xs">


<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

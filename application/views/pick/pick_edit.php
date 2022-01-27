<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> &nbsp; Save</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->CreateDate, FALSE); ?>" disabled />
	</div>
	<div class="col-lg-9 col-md-7-harf col-sm-7-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-xs-6 visible-xs">	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="edit()">แก้ไข</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">Update</button>
	</div>

	<input type="hidden" id="AbsEntry" value="<?php echo $doc->AbsEntry; ?>" />
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10">

<?php $this->load->view('pick/pick_control'); ?>
<?php $this->load->view('pick/pick_edit_detail'); ?>



<script src="<?php echo base_url(); ?>scripts/pick/pick.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pick/pick_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pick/pick_control.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

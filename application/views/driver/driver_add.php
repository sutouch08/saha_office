<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">พนักงาน</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control input-sm" id="emp_id">
				<option value="">Please Select</option>
				<?php echo select_employee(); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภท</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control input-sm" id="type">
				<option value="D">คนขับ</option>
				<option value="E">เด็กติดรถ</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="active" id="active" checked/>
				<span class="lbl">&nbsp; &nbsp;Active</span>
			</label>
    </div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="saveAdd()"><i class="fa fa-save"></i> Add</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

</form>

<script src="<?php echo base_url(); ?>scripts/driver/driver.js"></script>
<?php $this->load->view('include/footer'); ?>

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
			<input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $emp_name; ?>" />
			<input type="hidden" id="emp_id" value="<?php echo $emp_id; ?>" />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภท</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control input-sm" id="type">
				<option value="D" <?php echo is_selected('D', $type); ?>>คนขับ</option>
				<option value="E" <?php echo is_selected('E', $type); ?>>เด็กติดรถ</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="active" id="active" <?php echo is_checked('1', $active); ?>/>
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
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

</form>

<script src="<?php echo base_url(); ?>scripts/driver/driver.js"></script>
<?php $this->load->view('include/footer'); ?>

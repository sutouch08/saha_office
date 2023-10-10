<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">อำเภอ</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm" id="district" maxlength="100" value="<?php echo $district; ?>" autofocus />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="district-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">จังหวัด</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm" id="province" maxlength="100" value="<?php echo $province; ?>"/>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="province-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสไปรษณีย์</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm input-medium" id="zipCode" maxlength="10" value="<?php echo $zipCode; ?>" />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="zipCode-error"></div>
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
	<input type="hidden" id="id" value="<?php echo $id; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/delivery_zone/delivery_zone.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

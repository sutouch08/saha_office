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
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เส้นทาง</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm" id="name" maxlength="100" value="<?php echo $name; ?>" autofocus />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ความยาก</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm input-mini" id="level">
				<option value="1" <?php echo is_selected('1', $level); ?>> 1 </option>
				<option value="2" <?php echo is_selected('2', $level); ?>> 2 </option>
				<option value="3" <?php echo is_selected('3', $level); ?>> 3 </option>
				<option value="4" <?php echo is_selected('4', $level); ?>> 4 </option>
				<option value="5" <?php echo is_selected('5', $level); ?>> 5 </option>
				<option value="6" <?php echo is_selected('6', $level); ?>> 6 </option>
				<option value="7" <?php echo is_selected('7', $level); ?>> 7 </option>
				<option value="8" <?php echo is_selected('8', $level); ?>> 8 </option>
				<option value="9" <?php echo is_selected('9', $level); ?>> 9 </option>
			</select>
			<span class="help-block">ระดับความยากของเส้นทาง 1-9 ตัวเลขมากหมายถึงยากมาก</span>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="active" id="active" <?php echo is_checked('1', $active); ?> />
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

<script src="<?php echo base_url(); ?>scripts/route/route.js"></script>
<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-users"></i> <?php echo $this->title; ?></h3>
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
    <label class="col-sm-3 control-label no-padding-right">Sales Team Code</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="code" id="code" class="width-100 code" value="<?php echo $data->code; ?>" onkeyup="validCode(this)" disabled />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Sales Team Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $data->name; ?>" required autofocus />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="status" id="status" <?php echo is_checked('1', $data->status); ?> />
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
	<input type="hidden" id="old_code" value="<?php echo $data->code; ?>" />
	<input type="hidden" id="old_name" value="<?php echo $data->name; ?>" />
	<input type="hidden" id="old_status" value="<?php echo ($data->status == 1 ? 'Y' : 'N'); ?>" />

</form>

<script src="<?php echo base_url(); ?>scripts/sale_team/sale_team.js"></script>
<?php $this->load->view('include/footer'); ?>

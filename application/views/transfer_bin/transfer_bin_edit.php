<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
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
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Bin code</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="code" id="code" class="form-control input-sm code" value="<?php echo $data->code; ?>" disabled />
			<input type="hidden" id="id" value="<?php echo $data->id; ?>" />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Bin name</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" class="form-control input-sm" max-length="100" value="<?php echo $data->name; ?>" required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

		<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12control-label no-padding-right"></label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

</form>

<script src="<?php echo base_url(); ?>scripts/transfer_bin/transfer_bin.js"></script>
<?php $this->load->view('include/footer'); ?>

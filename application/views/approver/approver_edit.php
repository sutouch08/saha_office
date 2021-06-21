<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"> <?php echo $this->title; ?></h3>
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
    <label class="col-sm-3 control-label no-padding-right">User Name</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="uname" id="uname" class="width-100" value="<?php echo $data->uname; ?>" autofocus required />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Employee</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="emp_name" id="emp_name" class="width-100" value="<?php echo $data->name; ?>" readonly />
    </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Sales Team</label>
    <div class="col-xs-12 col-sm-4">
			<select class="form-control input-sm" name="sale_team" id="sale_team">
				<option value="">เลือก</option>
				<?php echo select_sales_team($data->sale_team); ?>
				<option value="all" <?php echo is_selected('all', $data->sale_team); ?>>ทั้งหมด</option>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-team-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Max Discount</label>
    <div class="col-xs-8 col-sm-1 col-1-harf">
			<div class="input-group">
				<input type="number" name="discount" id="discount" class="width-100 text-right" value="<?php echo $data->max_discount; ?>" maxlength="6" placeholder="0-100" required/>
				<span class="input-group-addon"><i class="ace-icon fa fa-percent"></i></span>
			</div>

    </div>
		<div class="help-block col-xs-12 col-sm-reset inline " id="discount-error">(User can approve Greater than or equal to the specified number)</div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="status" id="status" <?php echo is_checked($data->status, '1'); ?> />
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
        <button type="button" class="btn btn-sm btn-success" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>"/>

</form>

<script src="<?php echo base_url(); ?>scripts/approver/approver.js"></script>
<?php $this->load->view('include/footer'); ?>

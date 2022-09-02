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
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm" disabled>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>">
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>ทะเบียนรถ</label>
		<select class="form-control input-sm" name="vehicle" id="vehicle">
			<option value="">Please Select</option>
			<?php echo select_vehicle(NULL, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>พนักงานขับรถ</label>
		<select class="form-control input-sm" name="driver" id="driver">
			<option value="">Please Select</option>
			<?php echo select_driver('D', NULL, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>เด็กรถ</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="showSupportList()">เลือก</button>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label class="display-block not-show">x</label>
		<input type="text" class="form-control input-sm" id="support-label" placeholder="Please select" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เส้นทาง</label>
		<select class="form-control input-sm" name="route" id="route">
			<option value="">Please Select</option>
			<?php echo select_route(NULL, TRUE); ?>
		</select>
	</div>


	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
	</div>

</div>


<div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin-top:48px;">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom:0px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">เลือกพนักงาน</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
				<?php if( ! empty($supportList)) : ?>
					<?php foreach($supportList as $rs) : ?>
	          <div class="col-sm-12 col-xs-12" style="padding-top:15px;">
							<label>
								<input type="checkbox" class="ace chk" value="<?php echo $rs->emp_id; ?>" data-empname="<?php echo $rs->emp_name; ?>"/>
								<span class="lbl">&nbsp; &nbsp;<?php echo $rs->emp_name; ?></span>
							</label>
	          </div>
					<?php endforeach; ?>
				<?php endif; ?>
        </div>
      </div>

			<div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="addChecked()">เพิ่มรายการที่เลือก</button>
          <button type="button" class="btn btn-sm btn-default" onClick="closeModal()">Close</button>
      </div>
    </div>
  </div>
</div>



<script src="<?php echo base_url(); ?>scripts/delivery/delivery.js"></script>
<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> &nbsp; Save</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>

<?php $this->load->view('move/move_edit_header'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="padding-top:15px;">
		<div class="tabbable">
			<ul id="myTab1" class="nav nav-tabs">
				<li class="active"><a href="#move-tab" data-toggle="tab" onclick="getMoveTable()">รายการโอนย้าย</a></li>
				<li class=""><a href="#from-tab" data-toggle="tab">ย้ายสินค้าออก</a></li>
			  <li class=""><a href="#temp-tab" data-toggle="tab" onclick="getMoveIn()">ย้ายสินค้าเข้า</a></li>
			</ul>

			<div class="tab-content width-100">
				<div class="tab-pane fade  active in" id="move-tab"><?php $this->load->view('move/move_to'); ?></div>
				<div class="tab-pane fade" id="from-tab"><?php $this->load->view('move/move_from'); ?></div>
				<div class="tab-pane fade" id="temp-tab"><?php $this->load->view('move/move_temp'); ?></div>
			</div>
		</div>
	</div>
</div>


<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />
<input type="hidden" name="to_zone_code" id="to_zone_code" value="" />

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

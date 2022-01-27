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
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SO No.</label>
		<select class="form-control input-sm" id="orderCode" name="orderCode" onchange="updatePickList()">
			<option value="">เลือก</option>
			<?php if(!empty($so_list)) : ?>
				<?php foreach($so_list as $so) : ?>
					<option value="<?php echo $so->orderCode; ?>"><?php echo $so->orderCode; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Pick List No.</label>
		<select class="form-control input-sm" id="pickList" name="pickList">
			<option>เลือก SO No.</option>
		</select>
	</div>
	<div class="col-xs-6 visible-xs">	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()">สร้าง</button>
	</div>
</div>


<script id="picklist-template" type="text/x-handlebarsTemplate">
	<option value="">เลือก</option>
	{{#each this}}
		<option value="{{docNum}}">{{docNum}}</option>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/pack/pack.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pack/pack_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

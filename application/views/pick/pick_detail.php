<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>

	<div class="col-xs-12 padding-5 visible-xs">
    <h4 class="margin-top-10 margin-bottom-10 text-center" style="padding:5px; background-color:#e5e5e5;">
      <?php echo $this->title; ?>
    </h4>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<?php if($doc->Status == 'N') : ?>
				<button type="button" class="btn btn-xs btn-warning" onclick="goEdit(<?php echo $doc->AbsEntry; ?>)">แก้ไข</button>
				<button type="button" class="btn btn-xs btn-success" onclick="releasePickList()">Release</button>
				<?php endif; ?>
				<?php if($doc->Status == 'R') : ?>
					<button type="button" class="btn btn-xs btn-danger" onclick="unReleasePickList()">Unrelease</button>
				<?php endif; ?>
				<?php if($doc->Status !== 'N') : ?>
					<button type="button" class="btn btn-xs btn-info" onclick="printPickLabel()">Print Pick Slip</button>
				<?php endif; ?>
				<?php if($doc->Status != 'D' && $doc->Status != 'C') : ?>
					<button type="button" class="btn btn-xs btn-danger" onclick="canclePick(<?php echo $doc->AbsEntry; ?>, '<?php echo $doc->DocNum; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
				<?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<?php if($doc->Status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->CreateDate, TRUE); ?>" disabled />
	</div>
	<div class="col-lg-8-harf col-md-8 col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-xs-6 visible-xs">	</div>

	<input type="hidden" id="AbsEntry" value="<?php echo $doc->AbsEntry; ?>" />
	<input type="hidden" id="pickCode" value="<?php echo $doc->DocNum; ?>" />
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10">

<?php
	if($doc->Status == 'N')
	{
		$this->load->view('pick/pick_detail_pending');
	}
	else
	{
		$this->load->view('pick/pick_detail_released');
	}
	?>


<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <?php if(!empty($logs)) : ?>
      <p class="pull-right text-right" style="font-size:10px; font-style: italic; color:#777;">
      <?php foreach($logs as $log) : ?>
        <?php echo "*".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp;( {$log->emp_name} ) &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
      <?php endforeach; ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/pick/pick.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pick/pick_detail.js?v=<?php echo date('YmdH'); ?>"></script>


<?php $this->load->view('include/footer'); ?>

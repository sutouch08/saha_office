<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> &nbsp; Save</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<?php $this->load->view('move/move_edit_header'); ?>
<?php
		if($doc->Status == 'N')
		{
			$this->load->view('move/move_control');
		}
 ?>

 <?php
 		if($method == 'normal')
		{
			$this->load->view('move/move_detail');
		}
		else
		{
			$this->load->view('move/move_detail_barcode');
		}
  ?>


<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

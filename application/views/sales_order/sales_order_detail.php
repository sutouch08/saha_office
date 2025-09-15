<?php $this->load->view('include/header'); ?>
<style>
	.bg-green {
		background-color: #dfebd5;
	}

	td {
		white-space: normal;
		border:0 !important;
	}
</style>
<script src="<?php echo base_url(); ?>assets/js/jquery.autosize.js"></script>
<div class="row">
	<div class="col-sm-4 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-8 col-xs-12 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-xs btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<?php if($header->Status == 2) : ?>
				<button type="button" class="btn btn-xs btn-info" onclick="printSalesOrder('<?php echo $header->code; ?>')"><i class="fa fa-print"></i> ใบสั่งขาย</button>
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-primary" onclick="duplicateSO()"><i class="fa fa-copy"></i> คัดลอกใบสั่งขาย</button>
				<?php if($header->Approved !== 'A' && !$in_sap && ($header->Status == 0 OR $header->Status == 9)) : ?>
					<button type="button" class="btn btn-xs btn-warning" onclick="goEdit('<?php echo $header->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
				<?php endif; ?>
				<?php if($header->Status == 0 && $header->must_approve == 1 && $header->Approved === 'P' && ($can_approve OR $this->isSuperAdmin)) : ?>
					<button type="button" class="btn btn-xs btn-success" onclick="doApprove()"><i class="fa fa-check"></i> อนุมัติ</button>
					<button type="button" class="btn btn-xs btn-danger" onclick="doReject()"><i class="fa fa-times"></i> ไม่อนุมัติ</button>
				<?php endif; ?>
				<?php if($header->Approved === 'A' && empty($header->DocNum)) : ?>
					<button type="button" class="btn btn-xs btn-warning" onclick="unApprove()">ยกเลิกการอนุมัติ</button>
				<?php endif; ?>
				<?php if(($header->Status == 1 OR $header->Status == 3) && ($header->Approved === 'A' OR $header->Approved === 'S')) : ?>
					<button type="button" class="btn btn-xs btn-success" onclick="sendToSAP()"><i class="fa fa-send"></i> Send To SAP</button>
				<?php endif; ?>
				<?php if($header->Status >= 0) : ?>
					<button type="button" class="btn btn-xs btn-danger" onclick="cancelSo('<?php echo $header->code; ?>')"><i class="fa fa-times"></i> Cancel</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="">
<?php $this->load->view('sales_order/sales_order_preview_header'); ?>
<?php $this->load->view('sales_order/sales_order_preview_detail'); ?>
<?php $this->load->view('sales_order/sales_order_preview_footer'); ?>

<?php
	if($header->Status == -1)
	{
		$this->load->view('cancle_watermark');
	}
?>

<input type="hidden" name="code" id="code" value="<?php echo $header->code; ?>" />
<input type="hidden" id="sq-code" value="<?php echo $header->code; ?>" />
</form>


<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_add.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

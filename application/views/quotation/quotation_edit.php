<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.autosize.js"></script>
<style>
	.form-group {
		margin-bottom: 5px;
	}
	.input-icon > .ace-icon {
		z-index: 1;
	}
</style>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="">
<?php $this->load->view('quotation/quotation_edit_header'); ?>
<?php $this->load->view('quotation/quotation_edit_detail'); ?>
<?php $this->load->view('quotation/quotation_edit_footer'); ?>

<input type="hidden" id="sale_id" value="<?php echo $header->SlpCode; ?>" />
<input type="hidden" id="month" value="<?php echo date('Y-m'); ?>" />
</form>

<?php $this->load->view('quotation/quotation_ship_to_modal'); ?>
<?php $this->load->view('quotation/quotation_bill_to_modal'); ?>


<script id="contact-template" type="text/x-handlebarsTemplate">
		<option value=""></option>
		{{#each this}}
			<option value="{{id}}">{{name}}</option>
		{{/each}}
</script>

<script id="ship-to-template" type="text/x-handlebarsTemplate">
		{{#each this}}
			<option value="{{code}}">{{code}}</option>
		{{/each}}
</script>

<script id="bill-to-template" type="text/x-handlebarsTemplate">
		{{#each this}}
			<option value="{{code}}">{{code}}</option>
		{{/each}}
</script>

<script id="series-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<option value="{{code}}" {{is_selected}}>{{name}}</option>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/quotation/quotation.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/quotation/quotation_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/address.js"></script>



<?php $this->load->view('include/footer'); ?>

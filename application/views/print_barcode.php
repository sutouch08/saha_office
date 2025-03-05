
<?php $this->load->view('include/header'); ?>
<style>
	label.search-label {
		font-size:12px;
	}
</style>
<div class="row hidden-print">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<?php if(!empty($data)) : ?>
      <button type="button" class="btn btn-sm btn-primary" onclick="print()"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
			<?php endif; ?>
    </p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form name="upload-form" method="post" enctype="multipart/form-data" action="<?php echo current_url(); ?>">
<div class="row hidden-print">
	<div class="col-lg-2 col-md-2-harf col-sm-4 col-xs-6 padding-5">
		<label class="display-block not-show">file</label>
		<input type="file" class="form-control input-sm" name="uploadFile" id="uploadFile" accept=".xlsx" required />

	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>สูง (mm)</label>
		<input type="number" class="form-control input-sm text-center" name="b_height" value="<?php echo $height; ?>" />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>กว้าง (mm)</label>
		<input type="number" class="form-control input-sm text-center" name="b_width" value="<?php echo $width; ?>" />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>font size</label>
		<input type="number" class="form-control input-sm text-center" name="font_size" value="<?php echo $font_size; ?>" />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="submit" class="btn btn-xs btn-info btn-block">Submit</button>
	</div>
</div>

<button type="button" class="btn btn-xs btn-primary btn-block hide" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
<input type="hidden" name="555" />
</form>
<hr class="margin-top-15 padding-5 hidden-print">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">

<?php if(!empty($data))
			{
				foreach($data as $rs)
				{
					if( ! empty($rs['A']))
					{
						echo barcodeImage($rs['A'], $height, $width, $font_size, "margin-left:20px; margin-bottom:20px;");						
					}
				}
			}
	?>


	</div>
</div>


<script>

function getFile(){
  $('#uploadFile').click();
}




$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').text(name);
	}
});


</script>


<?php $this->load->view('include/footer'); ?>

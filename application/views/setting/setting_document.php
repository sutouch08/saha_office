
	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Quotation</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Quotation</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_QUOTATION" required value="<?php echo $PREFIX_QUOTATION; ?>" />
			</div>
      <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_QUOTATION" value="<?php echo $RUN_DIGIT_QUOTATION; ?>" />
			</div>
      <div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Sales Order</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
					<label class="visible-xs">Prefix Sales Order</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SALES_ORDER" required value="<?php echo $PREFIX_SALES_ORDER; ?>" />
			</div>
      <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_SALES_ORDER" value="<?php echo $RUN_DIGIT_SALES_ORDER; ?>" />
			</div>
      <div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Pick List</span>
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Pick List</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_PICK_LIST" required value="<?php echo $PREFIX_PICK_LIST; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_PICK_LIST" value="<?php echo $RUN_DIGIT_PICK_LIST; ?>" />
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Pack List</span>
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Pack List</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_PACK_LIST" required value="<?php echo $PREFIX_PACK_LIST; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_PACK_LIST" value="<?php echo $RUN_DIGIT_PACK_LIST; ?>" />
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Transfer</span>
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Transfer</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_TRANSFER" value="<?php echo $RUN_DIGIT_TRANSFER; ?>" />
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Move</span>
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Move</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_MOVE" required value="<?php echo $PREFIX_MOVE; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_MOVE" value="<?php echo $RUN_DIGIT_MOVE; ?>" />
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
      <div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
      <div class="col-lg-5-harf col-md-7-harf col-sm-8 col-xs-12 padding-5 text-center">
				<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
      	<button type="button" class="btn btn-sm btn-success input-small" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>
		</div>
  </form>

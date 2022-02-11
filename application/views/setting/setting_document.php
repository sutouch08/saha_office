<div class="tab-pane fade" id="document">
	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-sm-3"><span class="form-control left-label">Prefix Sales Quotation</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_QUOTATION" required value="<?php echo $PREFIX_QUOTATION; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_QUOTATION" value="<?php echo $RUN_DIGIT_QUOTATION; ?>" /></div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">Prefix Sales Order</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_SALES_ORDER" required value="<?php echo $PREFIX_SALES_ORDER; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_SALES_ORDER" value="<?php echo $RUN_DIGIT_SALES_ORDER; ?>" /></div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">Prefix Pick List</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_PICK_LIST" required value="<?php echo $PREFIX_PICK_LIST; ?>" /></div>
			<div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_PICK_LIST" value="<?php echo $RUN_DIGIT_PICK_LIST; ?>" /></div>
			<div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">Prefix Pack List</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_PACK_LIST" required value="<?php echo $PREFIX_PACK_LIST; ?>" /></div>
			<div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_PACK_LIST" value="<?php echo $RUN_DIGIT_PACK_LIST; ?>" /></div>
			<div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">Prefix Transfer</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_TRANSFER" required value="<?php echo $PREFIX_TRANSFER; ?>" /></div>
			<div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
			<div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_TRANSFER" value="<?php echo $RUN_DIGIT_TRANSFER; ?>" /></div>
			<div class="divider-hidden"></div>

      <div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

      <div class="col-sm-4 col-sm-offset-3">
				<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
      	<button type="button" class="btn btn-sm btn-success input-small text-center" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

    </div><!--/ row -->
  </form>
</div>

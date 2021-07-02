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
<!--
			<div class="col-sm-3"><span class="form-control left-label">Prefix Activity</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_ACTIVITY" required value="<?php echo $PREFIX_ACTIVITY; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_ACTIVITY" value="<?php echo $RUN_DIGIT_ACTIVITY; ?>" /></div>
      <div class="divider-hidden"></div>

			<div class="col-sm-3"><span class="form-control left-label">Prefix BP</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_BP" required value="<?php echo $PREFIX_BP; ?>" /></div>
      <div class="col-sm-1 col-1-harf padding-5"><span class="form-control left-label width-100 text-right">Run digit</span></div>
      <div class="col-sm-2"><input type="text" class="form-control input-sm input-small text-center digit" required name="RUN_DIGIT_BP" value="<?php echo $RUN_DIGIT_BP; ?>" /></div>
      <div class="divider-hidden"></div>
-->

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

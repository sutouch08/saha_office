
	<form id="documentForm" method="post" action="<?php echo $this->home; ?>/update_config">
    <div class="row">
    	<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_QUOTATION">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_QUOTATION); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_QUOTATION); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_QUOTATION); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_QUOTATION); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
      <div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_SALES_ORDER">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_SALES_ORDER); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_SALES_ORDER); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_SALES_ORDER); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_SALES_ORDER); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
      <div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Goods Receipt PO</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Goods Receipt PO</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_GRPO" required value="<?php echo $PREFIX_GRPO; ?>" />
			</div>
      <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_GRPO">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_GRPO); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_GRPO); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_GRPO); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_GRPO); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
      <div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_PICK_LIST">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_PICK_LIST); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_PICK_LIST); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_PICK_LIST); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_PICK_LIST); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_PACK_LIST">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_PACK_LIST); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_PACK_LIST); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_PACK_LIST); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_PACK_LIST); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_TRANSFER">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_TRANSFER); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_TRANSFER); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_TRANSFER); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_TRANSFER); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
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
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_MOVE">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_MOVE); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_MOVE); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_MOVE); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_MOVE); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Prefix Delivery</span>
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-8 padding-5">
				<label class="visible-xs">Prefix Delivery</label>
				<input type="text" class="form-control input-sm input-small text-center prefix" name="PREFIX_DELIVERY" required value="<?php echo $PREFIX_DELIVERY; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5 hidden-xs">
				<span class="form-control left-label width-100 text-right">Run digit</span>
			</div>
      <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="visible-xs">Run digit</label>
				<select class="form-control input-sm input-small digit" name="RUN_DIGIT_DELIVERY">
					<option value="3" <?php echo is_selected('3', $RUN_DIGIT_DELIVERY); ?>>&nbsp;&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;</option>
					<option value="4" <?php echo is_selected('4', $RUN_DIGIT_DELIVERY); ?>>&nbsp;&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;</option>
					<option value="5" <?php echo is_selected('5', $RUN_DIGIT_DELIVERY); ?>>&nbsp;&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;</option>
					<option value="6" <?php echo is_selected('6', $RUN_DIGIT_DELIVERY); ?>>&nbsp;&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;</option>
				</select>
			</div>
			<div class="divider-hidden"></div>
		</div>

		<div class="row">
      <div class="divider-hidden"></div>
			<div class="divider-hidden"></div>
      <div class="col-lg-6-harf col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
				<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
      	<button type="button" class="btn btn-sm btn-success input-small" onClick="checkDocumentSetting()"><i class="fa fa-save"></i> บันทึก</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>
		</div>
  </form>

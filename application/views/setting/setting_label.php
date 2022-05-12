

	<form id="labelForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pick Label width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        	<input type="text" class="form-control input-sm input-small" name="PICK_LABEL_WIDTH"  value="<?php echo $PICK_LABEL_WIDTH; ?>" />
					<span class="input-group-addon">mm.</span>
				</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pick Label height</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PICK_LABEL_HEIGHT" value="<?php echo $PICK_LABEL_HEIGHT; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pick Content width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PICK_LABEL_CONTENT_WIDTH" value="<?php echo $PICK_LABEL_CONTENT_WIDTH; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>


    	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pack Label width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        	<input type="text" class="form-control input-sm input-small" name="PACK_LABEL_WIDTH"  value="<?php echo $PACK_LABEL_WIDTH; ?>" />
					<span class="input-group-addon">mm.</span>
				</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pack Label height</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PACK_LABEL_HEIGHT" value="<?php echo $PACK_LABEL_HEIGHT; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pack Content width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PACK_LABEL_CONTENT_WIDTH" value="<?php echo $PACK_LABEL_CONTENT_WIDTH; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pallet Label width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        	<input type="text" class="form-control input-sm input-small" name="PALLET_LABEL_WIDTH"  value="<?php echo $PALLET_LABEL_WIDTH; ?>" />
					<span class="input-group-addon">mm.</span>
				</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pallet Label height</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PALLET_LABEL_HEIGHT" value="<?php echo $PALLET_LABEL_HEIGHT; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pallet Content width</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        <input type="text" class="form-control input-sm input-small" name="PALLET_LABEL_CONTENT_WIDTH" value="<?php echo $PALLET_LABEL_CONTENT_WIDTH; ?>" />
				<span class="input-group-addon">mm.</span>
			</div>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <span class="form-control left-label">Pallet Font Size</span>
      </div>
      <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6">
				<div class="input-group">
        	<input type="text" class="form-control input-sm input-small" name="PALLET_LABEL_FONT_SIZE"  value="<?php echo $PALLET_LABEL_FONT_SIZE; ?>" />
					<span class="input-group-addon">PX</span>
				</div>
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-8 col-sm-offset-4">
				<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('labelForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>

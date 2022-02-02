
<div class="tab-pane fade" id="LABEL">
	<form id="labelForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-4">
        <span class="form-control left-label">Label width(mm)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PRINT_LABEL_WIDTH"  value="<?php echo $PRINT_LABEL_WIDTH; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Label height(mm)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PRINT_LABEL_HEIGHT" value="<?php echo $PRINT_LABEL_HEIGHT; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Content width(mm)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PRINT_LABEL_CONTENT_WIDTH" value="<?php echo $PRINT_LABEL_CONTENT_WIDTH; ?>" />
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
</div>

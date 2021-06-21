
<div class="tab-pane fade" id="SAP">
	<form id="sapForm" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
    	<div class="col-sm-4">
        <span class="form-control left-label">Default Currency</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="CURRENCY"  value="<?php echo $CURRENCY; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Purchase VAT code (รหัสภาษีซื้อ)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Purchase VAT rate (อัตราภาษีซื้อ)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Sell VAT code (รหัสภาษีขาย)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

      <div class="col-sm-4">
        <span class="form-control left-label">Sell VAT rate (อัตราภาษีขาย)</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Default Quotation Series</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="DEFAULT_QUOTATION_SERIES" value="<?php echo $DEFAULT_QUOTATION_SERIES; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Default Warehouse</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="DEFAULT_WAREHOUSE" value="<?php echo $DEFAULT_WAREHOUSE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Default CUSTOMER</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="DEFAULT_CUSTOMER_CODE" value="<?php echo $DEFAULT_CUSTOMER_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>



      <div class="col-sm-8 col-sm-offset-4">
				<?php if($this->isAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
</div>

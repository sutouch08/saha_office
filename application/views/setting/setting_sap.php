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
        <span class="form-control left-label">Default Sales Order Series</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" name="DEFAULT_SALES_ORDER_SERIES" value="<?php echo $DEFAULT_SALES_ORDER_SERIES; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Default Warehouse</span>
      </div>
      <div class="col-sm-8">
				<select class="input-xlarge" name="DEFAULT_WAREHOUSE" id="default-warehouse">
					<option value="">Select</option>
					<?php echo select_warehouse($DEFAULT_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Return Warehouse</span>
      </div>
      <div class="col-sm-8">
				<select class="input-xlarge" name="RETURN_WAREHOUSE" id="return-warehouse">
					<option value="">Select</option>
					<?php echo select_warehouse($RETURN_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Buffer Warehouse</span>
      </div>
      <div class="col-sm-8">
				<select class="input-xlarge" name="BUFFER_WAREHOUSE" id="buffer-warehouse">
					<option value="">Select</option>
					<?php echo select_warehouse($BUFFER_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Inbound Warehouse</span>
      </div>
      <div class="col-sm-8">
				<select class="input-xlarge" name="INBOUND_WAREHOUSE" id="inbound-warehouse" onchange="inboundZoneInit()">
					<option value="">Select</option>
					<?php echo select_warehouse($INBOUND_WAREHOUSE); ?>
				</select>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Inbound Bin Location</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="input-medium r" id="inbound-code" name="INBOUND_ZONE" value="<?php echo $INBOUND_ZONE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Default CUSTOMER</span>
      </div>
      <div class="col-sm-8">
        <input type="text" class="form-control input-sm input-small" id="default_customer" name="DEFAULT_CUSTOMER_CODE" value="<?php echo $DEFAULT_CUSTOMER_CODE; ?>" />
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Close Pick Status when finished</span>
      </div>
      <div class="col-sm-8">
        <select name="CLOSE_PICK_LINE_STATUS" class="form-control input-sm input-small">
					<option value="transfer" <?php echo is_selected('transfer', $CLOSE_PICK_LINE_STATUS); ?>>Transfer</option>
					<option value="pack" <?php echo is_selected('pack', $CLOSE_PICK_LINE_STATUS); ?>>Pack</option>
				</select>
      </div>
      <div class="divider-hidden"></div>

			<div class="col-sm-4">
        <span class="form-control left-label">Remove buffer when finished</span>
      </div>
      <div class="col-sm-8">
        <select name="REMOVE_BUFFER_STATE" class="form-control input-sm input-small">
					<option value="transfer" <?php echo is_selected('transfer', $REMOVE_BUFFER_STATE); ?>>Transfer</option>
					<option value="pack" <?php echo is_selected('pack', $REMOVE_BUFFER_STATE); ?>>Pack</option>
				</select>
      </div>
      <div class="divider-hidden"></div>



      <div class="col-sm-8 col-sm-offset-4">
				<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
          <i class="fa fa-save"></i> บันทึก
        </button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>
	<script>
		window.addEventListener('load', () => {
			inboundZoneInit();
		});


		$('#default-warehouse').select2();
		$('#return-warehouse').select2();
		$('#buffer-warehouse').select2();
		$('#inbound-warehouse').select2();

		function inboundZoneInit() {
			let WhsCode = $('#inbound-warehouse').val();

			$('#inbound-code').autocomplete({
		    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+WhsCode,
		    close:function() {
		      let arr = $(this).val().split(' | ');

		      if(arr.length == 2){
		        $('#inbound-code').val(arr[0]);
		        $('#inbound-name').val(arr[1]);
		      }
		      else {
		        $('#inbound-code').val('');
		        $('#inbound-name').val('');
		      }
		    }
		  })
		}

		$('#default_customer').autocomplete({
			source:BASE_URL + 'auto_complete/get_customer_code_and_name',
			autoFocus:true,
			close:function(){
				var rs = $(this).val();
				var arr = rs.split(' | ');
				if(arr.length === 2) {
					$(this).val(arr[0]);
				}
				else {
					$(this).val('');
				}
			}
		});
	</script>

<div class="row">
  <div class="col-sm-6 col-xs-12">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Payment Terms</label>
        <div class="col-sm-6 col-xs-12">
          <select class="form-control input-sm" name="paymentTerm" id="paymentTerm">
            <?php echo select_GroupNum($data->GroupNum); //--- customer_helper ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Price List</label>
        <div class="col-sm-6 col-xs-12">
          <select class="form-control input-sm" name="priceList" id="priceList">
            <?php echo select_price_list($data->ListNum); ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Total Discount %</label>
        <div class="col-sm-6 col-xs-12">
          <input type="number" class="form-control input-sm" name="discount" id="discount" value="<?php echo $data->Discount; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Credit Limit</label>
        <div class="col-sm-6 col-xs-12">
          <input type="number" class="form-control input-sm" name="creditLimit" id="creditLimit" value="<?php echo $data->CreditLine; ?>" />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 col-xs-12">Commitment Limit</label>
        <div class="col-sm-6 col-xs-4">
          <input type="number" class="form-control input-sm" name="debitLimit" id="debitLimit" value="<?php echo $data->DebtLine; ?>" />
        </div>
      </div>
    </div><!-- form -->
  </div><!--- end right column -->

</div>

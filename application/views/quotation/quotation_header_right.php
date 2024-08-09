<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 last">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-5 col-md-4 col-sm-2 col-xs-12 control-label-xs no-padding-right">No.</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <select class="form-control input-xs" id="Series">
          <?php echo select_series(); ?>
        </select>
      </div>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="DocNum" class="form-control input-xs" value="" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Web Order</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="code" class="form-control input-xs" value="" disabled/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Payment Terms</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="Payment" class="form-control input-xs" value="" disabled/>
        <input type="hidden" id="GroupNum" value="">
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Posting Date</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="DocDate" class="form-control input-xs" value="<?php echo date('d-m-Y'); ?>" readonly/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Valid Until</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="DocDueDate" class="form-control input-xs" value="<?php echo date('d-m-Y'); ?>" readonly/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Document Date</label>
      <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
        <input type="text" id="TextDate" class="form-control input-xs" value="<?php echo date('d-m-Y'); ?>" readonly/>
      </div>
    </div>
  </div>
</div>

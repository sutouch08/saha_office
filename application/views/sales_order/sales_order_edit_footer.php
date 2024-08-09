<div class="row">
  <!--- left column -->
  <div class="col-sm-4 col-xs-12 padding-5">
    <div class="form-horizontal">

      <div class="form-group">
        <label class="col-sm-4 control-label no-padding-right">Sale Employee</label>
        <div class="col-sm-7 col-xs-12">
          <input type="text" id="slpCode" class="form-control input-sm" value="<?php echo $sale_name; ?>" disabled />
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label no-padding-right">Owner</label>
        <div class="col-sm-7 col-xs-12">
          <select class="form-control input-sm" id="owner">
            <option value=""></option>
            <?php $owner = empty($header->OwnerCode) ? $this->user->emp_id : $header->OwnerCode; ?>
            <?php echo select_employee($owner); ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-4 control-label no-padding-right">Remark</label>
        <div class="col-sm-7 col-xs-12">
          <textarea id="comments" maxlength="254" class="form-control" style="height:50px; width:450px;"><?php echo $header->Comments; ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-4 control-label no-padding-right">Internal Remark</label>
        <div class="col-sm-7 col-xs-12">
          <textarea id="remark" maxlength="254" class="form-control" style="height:50px; width:450px;"><?php echo $header->U_Remark_Int; ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <!--- Middle column -->
  <div class="col-sm-4 col-xs-12 padding-5">

  </div>

  <!--- right column -->
  <div class="col-sm-4 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-6 control-label no-padding-right">Total Before Discount</label>
        <div class="col-sm-6 col-xs-12 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="totalAmount" value="<?php echo number($totalAmount, 2); ?>" disabled>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">Discount</label>
        <div class="col-sm-3 col-xs-6 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="discPrcnt" class="form-control input-sm" value="<?php echo round($header->DiscPrcnt, 2); ?>"/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-sm-6 col-xs-6 padding-5 last">
          <input type="text" id="discAmount" class="form-control input-sm text-right" value="<?php echo number($totalAmount * ($header->DiscPrcnt * 0.01), 2); ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-6 control-label no-padding-right">Rouding</label>
        <div class="col-sm-6 col-xs-6 padding-5 last">
          <input type="number" id="roundDif" class="form-control input-sm text-right" value="<?php echo round($header->RoundDif, 2); ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-6 control-label no-padding-right">Tax</label>
        <div class="col-sm-6 col-xs-6 padding-5 last">
          <input type="text" id="tax" class="form-control input-sm text-right" value="<?php echo number($header->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-6 control-label no-padding-right">Total</label>
        <div class="col-sm-6 col-xs-6 padding-5 last">
          <input type="text" id="docTotal" class="form-control input-sm text-right" value="<?php echo number($header->DocTotal, 2); ?>" />
        </div>
      </div>

    </div>
  </div>

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <input type="hidden" id="vat_rate" value="<?php echo getConfig('SALE_VAT_RATE'); //--- default sale vat rate ?>" />
  <input type="hidden" id="vat_code" value="<?php echo getConfig('SALE_VAT_CODE'); //--- default sale vat code?>" />
  <input type="hidden" id="user_sale_name" value="<?php echo $user_sale_name; ?>" />
  <input type="hidden" id="user_sale_id" value="<?php echo $this->user->sale_id; ?>" />
  <input type="hidden" id="is_draft" value="0">

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <?php if(!empty($logs)) : ?>
      <p class="log-text">
      <?php foreach($logs as $log) : ?>
        <?php echo "* ".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp; {$log->emp_name}  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
      <?php endforeach; ?>
      </p>
    <?php endif; ?>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5 text-right">
    <button type="button" class="btn btn-sm btn-primary btn-100" onclick="update()">Save</button>
    <button type="button" class="btn btn-sm btn-warning btn-100" onclick="leave()">Cancel</button>
    <button type="button" class="btn btn-sm btn-info btn-100" onclick="updateAsDraft()">Save AS Draft</button>
  </div>
</div>

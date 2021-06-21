<!--- right column -->
<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Remarks</label>
      <div class="col-sm-7 col-xs-8">
        <textarea name="notes" id="notes" maxlength="100" class="autosize autosize-transition form-control"><?php echo trim($data->Notes); ?></textarea>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Sales Employee</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="slpCode" id="slpCode" <?php echo (($this->isAdmin OR $this->isLead) ? '' : 'disabled'); ?>>
          <?php echo select_saleman($data->SlpCode); //--- user_helper ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">BP Channel Code</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="15" class="form-control input-sm" id="channels" name="channels" value="<?php echo $data->ChannlBP; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Territory</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="territory" id="territory">
          <?php echo select_territory($data->Territory); //--- customer_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Lead/Customer</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="custLabel" id="custLabel" onchange="generateCardCode()">
          <option value="L" <?php echo is_selected('L', $data->U_BEX_CUST_LC); ?>>Lead</option>
          <option value="C" <?php echo is_selected('C', $data->U_BEX_CUST_LC); ?>>Customer</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">ลูกหนี้ ใน/ต่างประเทศ</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="custInOut" id="custInOut" onchange="generateCardCode()">
          <option value="L" <?php echo is_selected('L', $data->U_BEX_CUST_LE2); ?>>Local</option>
          <option value="E" <?php echo is_selected('E', $data->U_BEX_CUST_LE2); ?>>Export</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">3 ตัวแรกของชื่อบริษัท</label>
      <div class="col-sm-2 col-xs-4">
        <input type="text" class="form-control input-sm" maxlength="3" id="prefix" name="prefix" value="<?php echo $data->U_BEX_CUST_3LT; ?>" onkeyup="validInput(this, /[^a-z]+/gi)"/>
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="prefix-error"></div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Running 4 Digits</label>
      <div class="col-sm-2 col-xs-12">
        <input type="text" maxlength="4" class="form-control input-sm" id="running" name="running" value="<?php echo $data->U_BEX_CUST_RUNN; ?>" onkeyup="validInput(this, /[^0-9]+/gi)"/>
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="running-error"></div>
    </div>


  </div><!-- form -->
</div><!--- end right column -->

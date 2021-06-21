<!-- Left column -->
<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Tel1</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="phone1" name="phone1" value="<?php echo $data->Phone1; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Tel2</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="phone2" name="phone2" value="<?php echo $data->Phone2; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Mobile Phone</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="cellPhone" name="cellPhone" value="<?php echo $data->Cellular; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Fax</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="fax" name="fax" value="<?php echo $data->Fax; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Email</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="email" name="email" value="<?php echo $data->E_Mail; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Website</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="website" name="website" value="<?php echo $data->IntrntSite; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">ที่มาของลูกค้า</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" id="indicator" name="indicator">
          <option value="">เลือก</option>
          <?php echo select_indicator($data->Indicator); ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">BP Project</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" id="projectName" name="projectName" value="<?php echo $data->project_name; ?>"/>
        <input type="hidden" name="project" id="project" value="<?php echo $data->ProjectCod; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Industry</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="industry" id="industry">
          <?php echo select_industry($data->IndustryC); //--- customer_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Business Partner Type</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="bpType" id="bpType">
          <?php echo select_bp_type($data->CmpPrivate); //--- customer_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">วันที่รับเช็ค</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" id="chequeDate" name="chequeDate" value="<?php echo $data->U_CHECKDATE; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">วันที่วางบิล</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" id="billDate" name="billDate" value="<?php echo $data->U_BILLDATE; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Commission</label>
      <div class="col-sm-4 col-xs-12">
        <input type="number" class="form-control input-sm" id="commission" name="commission" value="<?php echo $data->U_COMMISSION; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-3 col-xs-6">
        <div class="radio">
          <label>
          <input type="radio" name="active" class="ace" value="Y" <?php echo is_checked('Y', $data->validFor); ?>>
          <span class="lbl">Active</span>
        </label>
        </div>
        <div class="radio">
          <label>
          <input type="radio" name="active" class="ace" value="N" <?php echo is_checked('Y', $data->frozenFor); ?>>
          <span class="lbl">Inactive</span>
        </label>
        </div>
      </div>

      <?php
      $fromDate = $data->validFor == 'Y' ? $data->validFrom : $data->frozenFrom;
      $toDate = $data->validFor == 'Y' ? $data->validTo : $data->frozenTo;

      $fromDate = empty($fromDate) ? "" : thai_date($fromDate);
      $toDate = empty($toDate) ? "" : thai_date($toDate);
      ?>
      <div class="col-sm-3 col-xs-6">
        <label>From</label>
        <input type="text" maxlength="8" class="form-control input-sm text-center" name="fromDate" id="fromDate" readonly value="<?php echo $fromDate; ?>"/>
      </div>
      <div class="col-sm-3 col-xs-6">
        <label>To</label>
        <input type="text" maxlength="8" class="form-control input-sm text-center" name="toDate" id="toDate" readonly value="<?php echo $toDate; ?>"/>
      </div>
    </div>

  </div><!-- form -->
</div> <!-- end left column -->

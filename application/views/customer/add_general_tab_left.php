<!-- Left column -->
<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Tel1</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="phone1" name="phone1"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Tel2</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="phone2" name="phone2"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Mobile Phone</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="cellPhone" name="cellPhone"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Fax</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="fax" name="fax"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Email</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="email" name="email"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Website</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="website" name="website"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">ที่มาของลูกค้า</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" id="indicator" name="indicator">
          <option value="">เลือก</option>
          <?php echo select_indicator(); ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">BP Project</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" id="projectName" name="projectName"/>
        <input type="hidden" name="project" id="projectt" value="" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Industry</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="industry" id="industry">
          <?php echo select_industry(); //--- customer_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Business Partner Type</label>
      <div class="col-sm-4 col-xs-12">
        <select class="form-control input-sm" name="bpType" id="bpType">
          <?php echo select_bp_type(); //--- customer_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">วันที่รับเช็ค</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" id="chequeDate" name="chequeDate"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">วันที่วางบิล</label>
      <div class="col-sm-4 col-xs-12">
        <input type="text" class="form-control input-sm" id="billDate" name="billDate"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Commission</label>
      <div class="col-sm-4 col-xs-12">
        <input type="number" class="form-control input-sm" id="commission" name="commission"/>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-3 col-xs-6">
        <div class="radio">
          <label>
          <input type="radio" name="active" class="ace" value="Y" checked>
          <span class="lbl">Active</span>
        </label>
        </div>
        <div class="radio">
          <label>
          <input type="radio" name="active" class="ace" value="N">
          <span class="lbl">Inactive</span>
        </label>
        </div>
      </div>
      <div class="col-sm-3 col-xs-6">
        <label>From</label>
        <input type="text" maxlength="8" class="form-control input-sm text-center" name="fromDate" id="fromDate" readonly/>
      </div>
      <div class="col-sm-3 col-xs-6">
        <label>To</label>
        <input type="text" maxlength="8" class="form-control input-sm text-center" name="toDate" id="toDate" readonly/>
      </div>
    </div>

  </div><!-- form -->
</div> <!-- end left column -->

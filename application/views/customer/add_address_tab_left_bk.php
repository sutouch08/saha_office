<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-12 col-xs-12"><h3>Bill To</h3></label>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress" id="btAddress" value="00000" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress2" id="btAddress2" value="00000" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress3" id="btAddress3" value="สำนักงานใหญ่" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่ อาคาร ชั้น ซอย</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="btStreet" id="btStreet" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Street No./ถนน</label>
      <div class="col-sm-6 col-xs-4">
        <input type="text" maxlength="100" class="form-control input-sm" name="btStreetNo" id="btStreetNo" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">แขวง/ตำบล</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btBlock" name="btBlock"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เขต/อำเภอ</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btCounty" name="btCounty"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">จังหวัด</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btCity" name="btCity"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">รหัสไปรษณีย์</label>
      <div class="col-sm-3 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="btZipCode" name="btZipCode"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Country/ประเทศ</label>
      <div class="col-sm-6 col-xs-12">
        <select class="form-control input-sm" name="btCountry" id="btCountry">
          <?php echo select_country(); //--- currency_helper ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12"></label>
      <div class="col-sm-6 col-xs-12">
        <button type="button" class="btn btn-sm btn-primary pull-right" onclick="add_bill_to_data()"><i class="fa fa-plus"></i> New</button>
      </div>
    </div>

  </div><!-- form -->
  <input type="hidden" id="bt-no" value="0">
  <input type="hidden" id="bt-data" value="">
</div><!--- end right column -->

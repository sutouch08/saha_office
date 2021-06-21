<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-6"><h3>Ship To</h3></label>
      <div class="col-sm-6 col-xs-6 margin-top-20 margin-bottom-10">
        <label>
            <input type="checkbox" class="ace" id="chk-bill-to" onchange="sameToBillTo(this)" />
            <span class="lbl"> &nbsp; ใช้ที่อยู่เดียวกับ Bill To</span>
        </label>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="stAddress" id="stAddress" value="00000" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="stAddress2" id="stAddress2" value="00000" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="stAddress3" id="stAddress3" value="สำนักงานใหญ่" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่ อาคาร ชั้น ซอย</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="stStreet" id="stStreet" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Street No./ถนน</label>
      <div class="col-sm-6 col-xs-4">
        <input type="text" maxlength="100" class="form-control input-sm" name="stStreetNo" id="stStreetNo" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">แขวง/ตำบล</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="stBlock" name="stBlock"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เขต/อำเภอ</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="stCounty" name="stCounty"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">จังหวัด</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="stCity" name="stCity"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">รหัสไปรษณีย์</label>
      <div class="col-sm-3 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="stZipCode" name="stZipCode"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Country/ประเทศ</label>
      <div class="col-sm-6 col-xs-12">
        <select class="form-control input-sm" name="stCountry" id="stCountry">
          <?php echo select_country(); //--- currency_helper ?>
        </select>
      </div>
    </div>
  </div><!-- form -->
</div><!--- end right column -->

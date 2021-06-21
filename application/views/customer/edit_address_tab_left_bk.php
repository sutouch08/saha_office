<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-12 col-xs-12"><h3>Bill To</h3></label>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress" id="btAddress" value="<?php echo $billTo->Address; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress2" id="btAddress2" value="<?php echo $billTo->Address2; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">สาขา</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="btAddress3" id="btAddress3" value="<?php echo $billTo->Address3; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เลขที่ อาคาร ชั้น ซอย</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="btStreet" id="btStreet" value="<?php echo $billTo->Street; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Street No./ถนน</label>
      <div class="col-sm-6 col-xs-4">
        <input type="text" maxlength="100" class="form-control input-sm" name="btStreetNo" id="btStreetNo" value="<?php echo $billTo->StreetNo; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">แขวง/ตำบล</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btBlock" name="btBlock" value="<?php echo $billTo->Block; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">เขต/อำเภอ</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btCounty" name="btCounty" value="<?php echo $billTo->County; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">จังหวัด</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="btCity" name="btCity" value="<?php echo $billTo->City; ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">รหัสไปรษณีย์</label>
      <div class="col-sm-3 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" id="btZipCode" name="btZipCode" value="<?php echo $billTo->ZipCode; ?>"/>
      </div>
    </div>

    <?php $country = empty($billTo->Country) ? getConfig('COUNTRY') : $billTo->Country; ?>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Country/ประเทศ</label>
      <div class="col-sm-6 col-xs-12">
        <select class="form-control input-sm" name="btCountry" id="btCountry">
          <?php echo select_country($country); //--- currency_helper ?>
        </select>
      </div>
    </div>

  </div><!-- form -->
</div><!--- end right column -->

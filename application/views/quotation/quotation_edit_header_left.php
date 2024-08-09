<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">Customer</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8">
        <input type="text" id="CardCode" class="form-control input-xs" value="<?php echo $header->CardCode; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">Name</label>
      <div class="col-lg-7 col-md-7-harf col-sm-8-harf col-xs-8">
        <input type="text" id="CardName" class="form-control input-xs" value="<?php echo $header->CardName; ?>" readonly/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">Contact Person</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8">
        <select class="form-control input-xs" id="Contact" >
          <option value=""></option>
          <?php echo select_contact_person($header->CardCode, $header->CntctCode); ?>
        </select>
      </div>
    </div>


    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">Customer Ref</label>
      <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8">
        <input type="text" id="NumAtCard" class="form-control input-xs input-medium" value="<?php echo $header->NumAtCard; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">ฝ่าย</label>
      <div class="col-lg-7 col-md-7-harf col-sm-8-harf col-xs-8">
        <select class="form-control input-xs" id="Department">
          <option value=""></option>
          <?php echo select_department($header->OcrCode); ?>
        </select>
      </div>
    </div>


    <div class="form-group">
      <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-4 control-label-xs no-padding-right">แผนก</label>
      <div class="col-lg-7 col-md-7-harf col-sm-8-harf col-xs-8">
        <select class="form-control input-xs" id="Division">
          <option value=""></option>
          <?php echo select_division($header->OcrCode1); ?>
        </select>
      </div>
    </div>
  </div>
</div>

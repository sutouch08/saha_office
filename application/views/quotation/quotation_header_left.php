<div class="col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12 control-label no-padding-right">Customer</label>
      <div class="col-sm-3 col-xs-12">
        <input type="text" id="CardCode" class="form-control input-sm" autofocus/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12 control-label no-padding-right">Name</label>
      <div class="col-sm-7 col-xs-8">
        <input type="text" id="CardName" class="form-control input-sm" readonly/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12 control-label no-padding-right">Contact Person</label>
      <div class="col-sm-7 col-xs-8">
        <select class="form-control input-sm" id="Contact">
          <option value=""></option>
        </select>
      </div>
    </div>


    <div class="form-group">
      <label class="col-sm-3 col-xs-12 control-label no-padding-right">Customer Ref</label>
      <div class="col-sm-7 col-xs-8">
        <input type="text" id="NumAtCard" class="form-control input-sm input-medium"  />
      </div>
    </div>


    <div class="form-group">
      <label class="col-sm-3 control-label no-padding-right">ฝ่าย</label>
      <div class="col-sm-7 col-xs-8">
        <select class="form-control input-sm" id="Department">
          <option value=""></option>
          <?php echo select_department($this->user->department_code); ?>
        </select>
      </div>
    </div>


    <div class="form-group">
      <label class="col-sm-3 control-label no-padding-right">แผนก</label>
      <div class="col-sm-7 col-xs-8">
        <select class="form-control input-sm" id="Division">
          <option value=""></option>
          <?php echo select_division($this->user->division_code); ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label no-padding-right">Ship To</label>
      <div class="col-lg-5 col-md-5 col-sm-6 col-xs-4">
        <select class="width-100" id="shipToCode" onchange="get_address_ship_to()"></select>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label no-padding-right"></label>
      <div class="col-sm-7 col-xs-8">
        <textarea id="ShipTo" class="autosize autosize-transition form-control"></textarea>
        <span class="badge badge-yellow pull-right margin-top-5"
        style="padding-bottom:0px; padding-top: 0px; border-radius:3px; cursor:pointer;" onclick="editShipTo()">
          <i class="fa fa-ellipsis-h"></i>
        </span>
      </div>

    </div>
  </div>
</div>

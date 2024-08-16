<div class="tab-pane fade" id="logistic" style="height:341px;">
  <div class="row" style="margin-left:0; margin-right:0; margin-top:30px;">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 control-label-xs no-padding-right">Bill To</label>
          <div class="col-lg-3-harf col-md-4 col-sm-4-harf col-xs-12">
            <select class="width-100" id="billToCode" onchange="get_address_bill_to()">

            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 control-label-xs no-padding-right"></label>
          <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12">
            <textarea id="BillTo" class="form-control input-xs" rows="5" readonly></textarea>
            <span class="badge badge-yellow pull-right margin-top-5"
            style="padding-bottom:0px; padding-top:0px; border-radius:3px; cursor:pointer;" onclick="editBillTo()">
              <i class="fa fa-ellipsis-h"></i>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 control-label-xs no-padding-right">Ship To</label>
          <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12">
            <select class="width-100" id="shipToCode" onchange="get_address_ship_to()">

            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-12 control-label-xs no-padding-right"></label>
          <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12">
            <textarea id="ShipTo" class="form-control input-xs" rows="5" readonly></textarea>
            <span class="badge badge-yellow pull-right margin-top-5"
            style="padding-bottom:0px; padding-top:0px; border-radius:3px; cursor:pointer;" onclick="editShipTo()">
              <i class="fa fa-ellipsis-h"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $('#shipToCode').select2();
  $('#billToCode').select2();
</script>

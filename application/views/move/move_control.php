<?php if($method == 'normal') : ?>
  <div class="row">
    <div class="col-col-sm-4 padding-5">
      <label>ต้นทาง</label>
      <input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
    </div>
    <div class="col-sm-4 padding-5">
      <label>ปลายทาง</label>
      <input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
    </div>

    <div class="col-sm-2">
      <label class="display-block not-show">ok</label>
      <button type="button" class="btn btn-xs btn-default btn-block" onclick="showMoveTable()">แสดงรายการ</button>
    </div>
  </div>
<?php else : ?>
  <div class="row">
  	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    	<button type="button" class="btn btn-xs btn-default btn-block" onclick="showMoveTable()">แสดงรายการ</button>
    </div>
  	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5 control-btn">
    	<button type="button" class="btn btn-xs btn-danger btn-block" onclick="getMoveOut()">ย้ายสินค้าออก</button>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5 control-btn">
    	<button type="button" class="btn btn-xs btn-info btn-block" onclick="getMoveIn()">ย้ายสินค้าเข้า</button>
    </div>
  </div>

  <hr id="barcode-hr" class="margin-top-15 margin-bottom-15 padding-5 hide" />

  <div class="row moveOut-zone hide">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
      <label>ต้นทาง</label>
      <input type="text" class="form-control input-sm" id="fromZone-barcode" />
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label class="display-block not-show">newZone</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-set-zone" onclick="getZoneFrom()">OK</button>
      <button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-new-zone" onclick="newFromZone()" >เปลี่ยน</button>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label>จำนวน</label>
      <input type="number" class="form-control input-sm text-center" id="qty-from" value="1" disabled />
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>บาร์โค้ดสินค้า</label>
      <input type="text" class="form-control input-sm" id="barcode-item-from" disabled />
    </div>

    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
      <label class="display-block not-show">ok</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-temp" onclick="addToTemp()" disabled>OK</button>
    </div>
  </div>

  <div class="row moveIn-zone hide">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
      <label>ปลายทาง</label>
      <input type="text" class="form-control input-sm" id="toZone-barcode" />
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
      <label class="display-block not-show">change</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-set-to-zone" onclick="getZoneTo()">OK</button>
    	<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-new-to-zone" onclick="newToZone()">เปลี่ยน</button>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label>จำนวน</label>
      <input type="number" class="form-control input-sm text-center" id="qty-to" value="1" disabled />
    </div>

    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>บาร์โค้ดสินค้า</label>
      <input type="text" class="form-control input-sm" id="barcode-item-to" disabled />
    </div>

    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
      <label class="display-block not-show">ok</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-add-to-zone" onclick="addToZone()" disabled>OK</button>
    </div>
  </div>


<?php endif; ?>

<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />
<input type="hidden" name="to_zone_code" id="to_zone_code" value="" />
<hr class="margin-bottom-15 padding-5" />

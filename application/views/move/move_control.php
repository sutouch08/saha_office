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

  </div>



<?php endif; ?>


<hr class="margin-bottom-15 padding-5" />

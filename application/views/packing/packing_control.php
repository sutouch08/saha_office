<div class="row">
  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 padding-5">
    <div class="title middle text-center" style="height:55px; background-color:black; color:white; padding-top:20px; margin-top:0px;">
      <h4 id="all_qty" style="display:inline;">
        <?php echo number($pack_qty); ?>
      </h4>
      <h4 style="display:inline;"> / <?php echo number($all_qty); ?></h4>
    </div>
  </div>
  <div class="col-xs-12 visible-xs padding-5">
    <hr class="not-show"/>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty" value="1" />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm text-center" inputmode="none" id="barcode-item" />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">btn</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-item" onclick="doPacking()" disabled>ตกลง</button>
  </div>
</div>

<hr class="padding-5"/>

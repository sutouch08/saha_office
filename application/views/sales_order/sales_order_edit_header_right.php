<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 last">
  <div class="row">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-5 col-md-4 col-sm-2 col-xs-12 control-label-xs no-padding-right">No.</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <select class="form-control input-xs" id="Series">
            <?php echo select_series($header->DocDate, $header->Series); ?>
          </select>
        </div>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="DocNum" class="form-control input-xs" value="<?php echo $header->DocNum; ?>" disabled/>
        </div>
      </div>


      <div class="form-group">
        <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Web Order</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="code" class="form-control input-xs" value="<?php echo $header->code; ?>" disabled/>
        </div>
      </div>


      <div class="form-group">
        <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Payment Terms</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="Payment" class="form-control input-xs" value="<?php echo $header->Term; ?>" disabled/>
          <input type="hidden" id="GroupNum" value="<?php echo $header->GroupNum; ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Posting Date</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="DocDate" class="form-control input-xs" value="<?php echo thai_date($header->DocDate); ?>" readonly/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Delivery Date</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="DocDueDate" class="form-control input-xs" value="<?php echo !empty($header->DocDueDate) ? thai_date($header->DocDueDate) : ""; ?>" readonly/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8-harf col-md-8 col-sm-7 col-xs-12 control-label-xs no-padding-right">Document Date</label>
        <div class="col-lg-3-harf col-md-4 col-sm-5 col-xs-12">
          <input type="text" id="TextDate" class="form-control input-xs" value="<?php echo thai_date($header->TextDate); ?>" readonly/>          
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-7-harf col-md-6 col-sm-5 col-xs-12 control-label-xs no-padding-right">ประเภทพิมพ์เอกสาร</label>
        <div class="col-lg-4-harf col-md-6 col-sm-7 col-xs-12">
          <select class="form-control input-xs" name="U_DO_IV_Print" id="doc_type">
            <option value="เปิดบิล IV" <?php echo is_selected("เปิดบิล IV", $header->U_DO_IV_Print); ?>>เปิดบิล IV</option>
            <option value="เปิดชั่วคราว DO" <?php echo is_selected("เปิดชั่วคราว DO", $header->U_DO_IV_Print); ?>>เปิดชั่วคราว DO</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-7-harf col-md-6 col-sm-5 col-xs-12 control-label-xs no-padding-right">เปิดบิลส่งสินค้าเมื่อ</label>
        <div class="col-lg-4-harf col-md-6 col-sm-7 col-xs-12">
          <select class="form-control input-xs" name="U_Delivery_Urgency" id="doc_urgency">
            <option value="ส่งทันทีเมื่อพร้อม" <?php echo is_selected("ส่งทันทีเมื่อพร้อม", $header->U_Delivery_Urgency); ?>>ส่งทันทีเมื่อพร้อม</option>
            <option value="ส่งด่วน ภายในวันที่ระบุ" <?php echo is_selected("ส่งด่วน ภายในวันที่ระบุ", $header->U_Delivery_Urgency); ?>>ส่งด่วน ภายในวันที่ระบุ</option>
            <option value="ส่งตรงวันนัดเท่านั้น" <?php echo is_selected("ส่งตรงวันนัดเท่านั้น", $header->U_Delivery_Urgency); ?>>ส่งตรงวันนัดเท่านั้น</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</div>

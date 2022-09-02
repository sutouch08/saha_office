<div class="col-sm-6 col-xs-12 padding-5 last">
  <div class="row">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-6 col-6-harf control-label no-padding-right">No.</label>
        <div class="col-sm-2 col-2-harf col-xs-6" style="padding-right:0px;">
          <select class="form-control input-sm" id="Series">
            <?php echo select_series($header->DocDate, $header->Series); ?>
          </select>
        </div>
        <div class="col-sm-3 col-xs-6">
          <input type="text" id="DocNum" class="form-control input-sm" value="<?php echo $header->DocNum; ?>" disabled/>
        </div>
      </div>


      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Web Order</label>
        <div class="col-sm-3 col-xs-8">
          <input type="text" id="code" class="form-control input-sm" value="<?php echo $header->code; ?>" disabled/>
        </div>
      </div>


      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Payment Terms</label>
        <div class="col-sm-3 col-xs-8">
          <input type="text" id="Payment" class="form-control input-sm" value="<?php echo $header->Term; ?>" disabled/>
          <input type="hidden" id="GroupNum" value="<?php echo $header->GroupNum; ?>">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Posting Date</label>
        <div class="col-sm-3 col-xs-8">
          <span class="input-icon input-icon-right">
          <input type="text" id="DocDate" class="form-control input-sm" value="<?php echo thai_date($header->DocDate); ?>" readonly/>
          <i class="ace-icon fa fa-calendar-o"></i>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Delivery Date</label>
        <div class="col-sm-3 col-xs-8">
          <span class="input-icon input-icon-right">
          <input type="text" id="DocDueDate" class="form-control input-sm" value="<?php echo !empty($header->DocDueDate) ? thai_date($header->DocDueDate) : ""; ?>" readonly/>
          <i class="ace-icon fa fa-calendar-o"></i>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Document Date</label>
        <div class="col-sm-3 col-xs-8">
          <span class="input-icon input-icon-right">
          <input type="text" id="TextDate" class="form-control input-sm" value="<?php echo thai_date($header->TextDate); ?>" readonly/>
          <i class="ace-icon fa fa-calendar-o"></i>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">ประเภทพิมพ์เอกสาร</label>
        <div class="col-sm-3 col-xs-8">
          <select class="form-control input-sm" name="U_DO_IV_Print" id="doc_type">
            <option value="เปิดบิล IV" <?php echo is_selected("เปิดบิล IV", $header->U_DO_IV_Print); ?>>เปิดบิล IV</option>
            <option value="เปิดชั่วคราว DO" <?php echo is_selected("เปิดชั่วคราว DO", $header->U_DO_IV_Print); ?>>เปิดชั่วคราว DO</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-8 control-label no-padding-right">เปิดบิลส่งสินค้าเมื่อ</label>
        <div class="col-sm-4 col-xs-8">
          <select class="form-control input-sm" name="U_Delivery_Urgency" id="doc_urgency">
            <option value="ส่งทันทีเมื่อพร้อม" <?php echo is_selected("ส่งทันทีเมื่อพร้อม", $header->U_Delivery_Urgency); ?>>ส่งทันทีเมื่อพร้อม</option>
            <option value="ส่งด่วน ภายในวันที่ระบุ" <?php echo is_selected("ส่งด่วน ภายในวันที่ระบุ", $header->U_Delivery_Urgency); ?>>ส่งด่วน ภายในวันที่ระบุ</option>
            <option value="ส่งตรงวันนัดเท่านั้น" <?php echo is_selected("ส่งตรงวันนัดเท่านั้น", $header->U_Delivery_Urgency); ?>>ส่งตรงวันนัดเท่านั้น</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-9 control-label no-padding-right">Bill To</label>
        <div class="col-sm-3 col-xs-4">
          <select class="form-control input-sm" id="billToCode" onchange="get_address_bill_to()">
            <?php if(!empty($billToCode)) : ?>
            <?php  foreach($billToCode as $rs) : ?>
              <option value="<?php echo $rs->Address; ?>" <?php echo is_selected($header->PayToCode, $rs->Address); ?>>
                <?php echo $rs->Address; ?>
              </option>
            <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>


      <div class="form-group">

        <div class="col-sm-7 col-sm-offset-5 col-xs-8">
          <textarea id="BillTo" class="autosize autosize-transition form-control" disabled><?php echo $header->Address; ?></textarea>
          <span class="badge badge-yellow pull-right margin-top-5 hide"
          style="padding-bottom:0px; padding-top:0px; border-radius:3px; cursor:pointer;" onclick="editBillTo()">
            <i class="fa fa-ellipsis-h"></i>
          </span>
        </div>

      </div>

    </div>
  </div>
</div>

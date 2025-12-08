<!--  Search Product -->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-xs btn-danger" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>

<div class="divider-hidden">	</div>

<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:1100px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">ใบสั่งซื้อ</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="font-size:11px; table-layout: fixed; min-width:1020px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-100 text-center">รหัส</th>
            <th class="min-width-300 text-center">สินค้า</th>
						<th class="fix-width-100 text-center">หน่วยนับ</th>
						<th class="fix-width-80 text-center">VAT (%)</th>
						<th class="fix-width-100 text-center">ราคา</th>
						<th class="fix-width-100 text-center">ส่วนลด</th>
            <th class="fix-width-100 text-center">ค้างรับ</th>
            <th class="fix-width-100 text-center">จำนวน</th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>


<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>

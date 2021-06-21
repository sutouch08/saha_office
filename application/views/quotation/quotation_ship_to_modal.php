<!--  Add New Address Modal  --------->
<div class="modal fade" id="shipToModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" >Ship To Address</h4>
            </div>
            <div class="modal-body">
            <input type="hidden" id="s_address" />
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                	<label class="input-label">Street/PO Box/เลขที่ ตึก ชั้น..</label>
                    <input type="text" class="form-control input-sm" id="sBlock" placeholder="เลขที่, หมู่บ้าน(จำเป็น)" />
                </div>

                <div class="col-sm-12 col-xs-12">
                	<label class="input-label">Street No./ถนน</label>
                    <input type="text" class="form-control input-sm"  id="sStreet" placeholder="ถนน" />
                </div>

                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">Block/ตำบล/แขวง</label>
                    <input type="text" class="form-control input-sm" id="sSubDistrict" placeholder="ตำบล" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">County/อำเภอ/เขต</label>
                    <input type="text" class="form-control input-sm" id="sDistrict" placeholder="อำเภอ (จำเป็น)" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">City/จังหวัด</label>
                    <input type="text" class="form-control input-sm" id="sProvince" placeholder="จังหวัด (จำเป็น)" />
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">Country/ประเทศ</label>
                    <select class="form-control input-sm" id="sCountry">
                      <?php echo select_country("TH"); ?>
                    </select>
                    
                </div>
                <div class="col-sm-6 col-xs-12">
                	<label class="input-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control input-sm" id="sPostCode" placeholder="รหัสไปรษณีย์" />
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="updateShipTo()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

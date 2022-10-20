<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning" onclick="leave()"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn btn-xs btn-success" style="min-width:100px;" onclick="saveAdd()">Add</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm" id="code" readonly>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" name="docDate" id="docDate" value="<?php echo date('d-m-Y'); ?>" readonly>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่จัดส่ง</label>
		<input type="text" class="form-control input-sm text-center" name="shipDate" id="shipDate" value="<?php echo date('d-m-Y'); ?>" readonly>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>ทะเบียนรถ</label>
		<select class="form-control input-sm" name="vehicle" id="vehicle">
			<option value="">Please Select</option>
			<?php echo select_vehicle(NULL, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 padding-5">
		<label>พนักงานขับรถ</label>
		<select class="form-control input-sm" name="driver" id="driver">
			<option value="">Please Select</option>
			<?php echo select_driver('D', NULL, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-3-harf col-md-3-harf col-sm- col-xs-6 padding-5">
		<label class="display-block">พนักงานติดรถ</label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm" id="support-label" placeholder="Please select" readonly />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-primary" style="width:60px;" onclick="showSupportList()">เลือก</button>
			</span>
		</div>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>เส้นทาง</label>
		<select class="form-control input-sm" name="route" id="route">
			<option value="">Please Select</option>
			<?php echo select_route(NULL, TRUE); ?>
		</select>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-5 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" name="remark" id="remark" maxlength="250" />
	</div>
</div>

<hr class="margin-top-10 margin-bottom-10 padding-5">

<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show hidden-xs">x</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addRow()">Add Row</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show hidden-xs">x</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="removeRow()">Delete Row</button>
	</div>

	<div class="col-lg-5-harf col-md-4-harf col-sm-3-harf col-xs-12 padding-5">&nbsp;</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>ประเภทการส่ง</label>
		<select class="form-control input-sm" id="shipType">
			<option value="P">ส่งสินค้า</option>
			<option value="D">ส่งเอกสาร</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-5 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="docNum" autofocus />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="submitRow()">Add</button>
	</div>
  <div class="divider-hidden"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="background-color: #f5f5f5; padding:0px; min-height:200px; max-height:400px; overflow-y:auto;">
		<table class="table table-bordered border-1" style="min-width:1550px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="toggleChkAll($(this))" />
							<span class="lbl"></span>
						</label>
					</th>
          <th class="fix-width-100 text-center">รหัสลูกค้า</th>
          <th class="fix-width-300 text-center">ชื่อลูกค้า</th>
          <th class="fix-width-100 text-center">ประเภทการส่ง</th>
          <th class="fix-width-100 text-center">ประเภทเอกสาร</th>
          <th class="fix-width-100 text-center">เลขที่เอกสาร</th>
          <th class="fix-width-100 text-center">มูลค่า</th>
					<th class="fix-width-300 text-center">สถานที่จัดส่ง</th>
					<th class="fix-width-100 text-center">ผู้ติดต่อ</th>
					<th class="fix-width-100 text-center">วันทำการ</th>
					<th class="fix-width-100 text-center">เวลาทำการ</th>
					<th class="fix-width-150 text-center">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody id="row-table">
				<?php $no = 0; ?>
				<?php $init = 0; ?>
				<?php while($no < $init) : ?>
					<?php $no++; ?>
					<tr id="row-<?php echo $no; ?>">
						<input type="hidden" id="shipToCode-<?php echo $no; ?>" value="" />
						<input type="hidden" id="street-<?php echo $no; ?>" value="" />
						<input type="hidden" id="block-<?php echo $no; ?>" value="" />
						<input type="hidden" id="city-<?php echo $no; ?>" value="" />
						<input type="hidden" id="county-<?php echo $no; ?>" value="" />
						<input type="hidden" id="country-<?php echo $no; ?>" value="" />
						<input type="hidden" id="zipCode-<?php echo $no; ?>" value="" />
						<input type="hidden" id="phone-<?php echo $no; ?>" value="" />
						<input type="hidden" id="contact-<?php echo $no; ?>" value="" />
						<input type="hidden" id="docdate-<?php echo $no; ?>" value="" />

						<td class="middle text-center">
							<label>
								<input type="checkbox" class="ace row-chk" data-id="<?php echo $no; ?>"/>
								<span class="lbl"></span>
							</label>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm cardCode" data-id="<?php echo $no; ?>" id="cardCode-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm cardName" id="cardName-<?php echo $no; ?>" readonly/>
						</td>
						<td class="middle">
							<select class="form-control input-sm" id="shipType-<?php echo $no; ?>" onchange="toggleDocType(<?php echo $no; ?>)">
								<option value="P">ส่งสินค้า</option>
								<option value="D">ส่งเอกสาร</option>
								<option value="O">อื่นๆ</option>
							</select>
						</td>
						<td class="middle">
							<select class="form-control input-sm" id="docType-<?php echo $no; ?>" onchange="docNumInit(<?php echo $no; ?>)">
								<option value=""></option>
								<option value="DO">DO</option>
								<option value="IV">IV</option>
							</select>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm docNum" data-no="<?php echo $no; ?>" id="docNum-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm text-right docTotal" id="docTotal-<?php echo $no; ?>" value="0.00" readonly />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm shipTo" id="shipTo-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm contact" id="contactName-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm" id="workDate-<?php echo $no; ?>" value="" readonly/>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm" id="workTime-<?php echo $no; ?>" value="" readonly/>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm" id="remark-<?php echo $no; ?>" value="" />
						</td>
					</tr>
				<?php endwhile; ?>
      </tbody>
    </table>

		<input type="hidden" id="no" value="<?php echo $no; ?>" />
		<input type="hidden" id="DocTotal" value="0" />
  </div>
</div>

<hr class="padding-5" />
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right" style="background-color:#f5f5f5;">
		<h3 style="padding-right:20px;">Total Amount : &nbsp; <span id="totalAmount">0.00<span></h3>
	</div>
</div>




<div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin-top:48px;">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom:0px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">เลือกพนักงาน</h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
				<?php if( ! empty($supportList)) : ?>
					<?php foreach($supportList as $rs) : ?>
            <?php $checked = empty($emp[$rs->emp_id]) ? "" : "checked"; ?>
	          <div class="col-sm-12 col-xs-12" style="padding-top:15px;">
							<label>
								<input type="checkbox" class="ace chk" value="<?php echo $rs->emp_id; ?>" data-empname="<?php echo $rs->emp_name; ?>" <?php echo $checked; ?> />
								<span class="lbl">&nbsp; &nbsp;<?php echo $rs->emp_name; ?></span>
							</label>
	          </div>
					<?php endforeach; ?>
				<?php endif; ?>
        </div>
      </div>

			<div class="modal-footer">
          <button type="button" class="btn btn-sm btn-primary" onclick="addChecked()">เพิ่มรายการที่เลือก</button>
          <button type="button" class="btn btn-sm btn-default" onClick="closeModal()">Close</button>
      </div>
    </div>
  </div>
</div>



<script id="row-template" type="text/x-handlebarsTemplate">
<tr id="row-{{no}}">
	<input type="hidden" id="detail-{{no}}" data-id="0" />
	<input type="hidden" id="shipToCode-{{no}}" value="" />
	<input type="hidden" id="street-{{no}}" value="" />
	<input type="hidden" id="block-{{no}}" value="" />
	<input type="hidden" id="city-{{no}}" value="" />
	<input type="hidden" id="county-{{no}}" value="" />
	<input type="hidden" id="country-{{no}}" value="" />
	<input type="hidden" id="zipCode-{{no}}" value="" />
	<input type="hidden" id="phone-{{no}}" value="" />
	<input type="hidden" id="contact-{{no}}" value="" />
	<input type="hidden" id="docDate-{{no}}" value="" />

	<td class="middle text-center">
		<label>
			<input type="checkbox" class="ace row-chk" data-id="{{no}}"/>
			<span class="lbl"></span>
		</label>
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm cardCode" data-id="{{no}}" id="cardCode-{{no}}" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm cardName" id="cardName-{{no}}" readonly/>
	</td>
	<td class="middle">
		<select class="form-control input-sm" id="shipType-{{no}}" onchange="toggleDocType({{no}})">
			<option value="P">ส่งสินค้า</option>
			<option value="D">ส่งเอกสาร</option>
			<option value="O">อื่นๆ</option>
		</select>
	</td>
	<td class="middle">
		<select class="form-control input-sm" id="docType-{{no}}" onchange="docNumInit({{no}})">
			<option value=""></option>
			<option value="DO">DO</option>
			<option value="IV">IV</option>
		</select>
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm docNum" data-no="{{no}}" data-id="0" id="docNum-{{no}}" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm text-right docTotal" id="docTotal-{{no}}" value="0.00" readonly />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm shipTo" id="shipTo-{{no}}" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm contact" id="contactName-{{no}}" />
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm" id="workDate-{{no}}" value="" readonly/>
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm" id="workTime-{{no}}" value="" readonly/>
	</td>
	<td class="middle">
		<input type="text" class="form-control input-sm" id="remark-{{no}}" value="" />
	</td>
</tr>
</script>


<script id="docTypeTemplate1" type="text/x-handlebarsTemplate">
	<option value=""></option>
	<option value="DO">DO</option>
	<option value="IV">IV</option>
	<option value="PB">PB</option>
	<option value="CN">CN</option>
</script>

<script id="docTypeTemplate2" type="text/x-handlebarsTemplate">
	<option value=""></option>
	<option value="DO">DO</option>
	<option value="IV">IV</option>
</script>

<script id="docTypeTemplate3" type="text/x-handlebarsTemplate">
	<option value="DO">DO</option>
	<option value="IV">IV</option>
	<option value="PB">PB</option>
	<option value="CN">CN</option>
</script>

<script id="docTypeTemplate4" type="text/x-handlebarsTemplate">
	<option value="DO">DO</option>
	<option value="IV">IV</option>
</script>

<script type="text/x-handlebarsTemplate">
<td class="middle text-center">
<select class="form-control input-sm">
		<option value="1">Loaded</option>
		<option value="4">สำเร็จ</option>
		<option value="2">ส่งบางส่วน</option>
		<option value="3">ไม่ได้ส่ง</option>
		<option value="5">ลูกค้าไม่รับของ</option>
		<option value="6">สินค้าผิด</option>
		<option value="7">เอกสารผิด</option>
	</select>
</td>
</script>

<script src="<?php echo base_url(); ?>scripts/delivery/delivery.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/delivery/delivery_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

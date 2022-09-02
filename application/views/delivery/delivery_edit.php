<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm" id="code" value="<?php echo $doc->code; ?>" disabled>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" disabled>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>ทะเบียนรถ</label>
		<select class="form-control input-sm" name="vehicle" id="vehicle" disabled>
			<option value="">Please Select</option>
			<?php echo select_vehicle($doc->vehicle_id, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>พนักงานขับรถ</label>
		<select class="form-control input-sm" name="driver" id="driver" disabled>
			<option value="">Please Select</option>
			<?php echo select_driver('D', $doc->driver_id, TRUE); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>เด็กรถ</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="showSupportList()" disabled>เลือก</button>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label class="display-block not-show">x</label>
		<input type="text" class="form-control input-sm" id="support-label" placeholder="Please select" value="<?php echo get_delivery_employee_name('E', $doc->code); ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>เส้นทาง</label>
		<select class="form-control input-sm" name="route" id="route" disabled>
			<option value="">Please Select</option>
			<?php echo select_route($doc->route_id, TRUE); ?>
		</select>
	</div>


	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> Edit</button>
    <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> Update</button>
	</div>
</div>

<hr class="margin-top-10 margin-bottom-10 padding-5">

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">Add Row</button>
    <button type="button" class="btn btn-sm btn-warning" onclick="removeRow()">Delete Row</button>
  </div>
  <div class="divider-hidden"></div>

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered border-1" style="min-width:1140px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center"></th>
          <th class="fix-width-100 text-center">รหัสลูกค้า</th>
          <th class="fix-width-200 text-center">ชื่อลูกค้า</th>
          <th class="fix-width-200 text-center">สถานที่จัดส่ง</th>
          <th class="fix-width-100 text-center">ผู้ติดต่อ</th>
          <th class="fix-width-100 text-center">ประเภทการส่ง</th>
          <th class="fix-width-100 text-center">ประเภทเอกสาร</th>
          <th class="fix-width-100 text-center">เลขที่เอกสาร</th>
          <th class="fix-width-100 text-center">มูลค่า</th>
          <th class="fix-width-100 text-center">สถานะ</th>
        </tr>
      </thead>
      <tbody id="row-table">
				<?php $no = 1; ?>
				<?php $init = 5; ?>
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
		        <tr id="row-<?php echo $no; ?>">
							<input type="hidden" id="detail-<?php echo $no; ?>" data-id="<?php echo $rs->id; ?>" />
		          <td class="middle text-center">
		            <label>
		              <input type="checkbox" class="ace row-chk" data-id="<?php echo $no; ?>"/>
		              <span class="lbl"></span>
		            </label>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm cardCode" id="cardCode-<?php echo $no; ?>" value="<?php echo $rs->CardCode; ?>" />
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm cardName" id="cardName-<?php echo $no; ?>" value="<?php echo $rs->CardName; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm shipTo" id="shipTo-<?php echo $no; ?>" value="<?php echo $rs->Address; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm contact" id="contact-<?php echo $no; ?>" value="<?php echo $rs->contact; ?>" />
		          </td>
		          <td class="middle">
		            <select class="form-control input-sm" id="shipType-<?php echo $no; ?>">
		              <option value="P" <?php echo is_selected('P', $rs->type); ?>>ส่งสินค้า</option>
		              <option value="D" <?php echo is_selected('D', $rs->type); ?>>ส่งเอกสาร</option>
		            </select>
		          </td>
		          <td class="middle">
		            <select class="form-control input-sm" id="docType-<?php echo $no; ?>">
		              <option value=""></option>
		              <option value="DO" <?php echo is_selected('DO', $rs->DocType); ?>>DO</option>
		              <option value="IV" <?php echo is_selected('IV', $rs->DocType); ?>>IV</option>
		              <option class="<?php echo $rs->type == 'P' ? 'hide' : ''; ?>" value="DB" <?php echo is_selected('DB', $rs->DocType); ?>>DB</option>
		              <option class="<?php echo $rs->type == 'P' ? 'hide' : ''; ?>" value="CN" <?php echo is_selected('CN', $rs->DocType); ?>>CN</option>
		            </select>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm docNum" id="docNum-<?php echo $no; ?>" value="<?php echo $rs->DocNum; ?>" />
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm text-right docTotal" id="docTotal-<?php echo $no; ?>" value="<?php echo number($rs->DocTotal, 2); ?>" disabled />
		          </td>
		          <td class="middle text-center">
		          <select class="form-control input-sm" id="status-<?php echo $no; ?>">
		              <option value="1" <?php echo is_selected('1', $rs->status); ?>>Loaded</option>
		              <option value="4" <?php echo is_selected('4', $rs->status); ?>>สำเร็จ</option>
		              <option value="2" <?php echo is_selected('2', $rs->status); ?>>ส่งบางส่วน</option>
		              <option value="3" <?php echo is_selected('3', $rs->status); ?>>ไม่ได้ส่ง</option>
		              <option value="5" <?php echo is_selected('5', $rs->status); ?>>ลูกค้าไม่รับของ</option>
		              <option value="6" <?php echo is_selected('6', $rs->status); ?>>สินค้าผิด</option>
		              <option value="7" <?php echo is_selected('7', $rs->status); ?>>เอกสารผิด</option>
		            </select>
		          </td>
		        </tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php while($no <= $init) : ?>
					<tr id="row-<?php echo $no; ?>">
						<input type="hidden" id="detail-<?php echo $no; ?>" data-id="0" />
						<td class="middle text-center">
							<label>
								<input type="checkbox" class="ace row-chk" data-id="<?php echo $no; ?>"/>
								<span class="lbl"></span>
							</label>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm cardCode" id="cardCode-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm cardName" id="cardName-<?php echo $no; ?>" disabled/>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm shipTo" id="shipTo-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm contact" id="contact-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<select class="form-control input-sm" id="shipType-<?php echo $no; ?>" onchange="toggleDocType(<?php echo $no; ?>)">
								<option value="P">ส่งสินค้า</option>
								<option value="D">ส่งเอกสาร</option>
							</select>
						</td>
						<td class="middle">
							<select class="form-control input-sm" id="docType-<?php echo $no; ?>" onchange="docNumInit(<?php echo $no; ?>)">
								<option value=""></option>
								<option value="DO">DO</option>
								<option value="IV">IV</option>
								<option class="hide" value="DB">DB</option>
								<option class="hide" value="CN">CN</option>
							</select>
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm docNum" data-no="<?php echo $no; ?>" data-id="0" id="docNum-<?php echo $no; ?>" />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm text-right docTotal" id="docTotal-<?php echo $no; ?>" value="0.00" disabled />
						</td>
						<td class="middle text-center">
						<select class="form-control input-sm" id="status-<?php echo $no; ?>">
								<option value="1">Loaded</option>
								<option value="4">สำเร็จ</option>
								<option value="2">ส่งบางส่วน</option>
								<option value="3">ไม่ได้ส่ง</option>
								<option value="5">ลูกค้าไม่รับของ</option>
								<option value="6">สินค้าผิด</option>
								<option value="7">เอกสารผิด</option>
							</select>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endwhile; ?>
      </tbody>
    </table>

		<input type="hidden" id="no" value="<?php echo $no; ?>" />
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



<script src="<?php echo base_url(); ?>scripts/delivery/delivery.js"></script>
<?php $this->load->view('include/footer'); ?>

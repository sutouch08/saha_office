<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<?php if($doc->status == 'O') : ?>
				<button type="button" class="btn btn-sm btn-warning" onclick="goEdit('<?php echo $doc->code; ?>')">Edit</button>
				<button type="button" class="btn btn-sm btn-success" onclick="confirmRelease()">Release</button>
				<button type="button" class="btn btn-sm btn-danger" onclick="confirmCancle()">Cancle</button>
			<?php endif; ?>
			<?php if($doc->status == 'R') : ?>
				<button type="button" class="btn btn-sm btn-purple" onclick="confirmUnrelease()">Unrelease</button>
			<?php endif; ?>
			<?php if($doc->status == 'C' && ($this->isSuperAdmin OR $this->isAdmin)) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unClose()">Unclose</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<?php if($doc->status === 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" disabled>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>ทะเบียนรถ</label>
		<input type="text" class="form-control input-sm text-center" id="vehicle_name" value="<?php echo $doc->vehicle_name; ?>" disabled />
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-5 padding-5">
		<label>พนักงานขับรถ</label>
		<input type="text" class="form-control input-sm" id="driver" value="<?php echo $doc->driver_name; ?>" disabled />
	</div>

	<div class="col-lg-3-harf col-md-3-harf col-sm-5 col-xs-7 padding-5">
		<label>พนักงานติดรถ</label>
		<input type="text" class="form-control input-sm" id="support-label" placeholder="Please select" value="<?php echo get_delivery_employee_name('E', $doc->code); ?>" disabled />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-8 padding-5">
		<label>เส้นทาง</label>
		<input type="text" class="form-control input-sm" id="route" value="<?php echo $doc->route_name; ?>" disabled />
	</div>

	<?php $status = $doc->status == 'R' ? 'Released' : ($doc->status == 'C' ? 'Closed' : ($doc->status == 'D' ? 'Canceled' : 'Open')); ?>

	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" id="status" value="<?php echo $status; ?>" disabled />
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	<input type="hidden" id="vehicle_id" value="<?php echo $doc->vehicle_id; ?>" />
	<input type="hidden" id="driver_id" value="<?php echo $doc->driver_id; ?>" />
	<input type="hidden" id="route_id" value="<?php echo $doc->route_id; ?>" />
</div>

<hr class="margin-top-10 margin-bottom-10 padding-5">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="margin-bottom:20px; max-height:400px; overflow-y: auto;">
    <table class="table table-bordered border-1" style="min-width:1190px;">
      <thead>
        <tr style="font-size:12px;">
          <th class="fix-width-40 text-center">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="toggleChkAll($(this))" />
							<span class="lbl"></span>
						</label></th>
          <th class="fix-width-100 text-center">รหัสลูกค้า</th>
          <th class="fix-width-200 text-center">ชื่อลูกค้า</th>
          <th class="fix-width-200 text-center">สถานที่จัดส่ง</th>
          <th class="fix-width-100 text-center">ผู้ติดต่อ</th>
          <th class="fix-width-100 text-center">ประเภทการส่ง</th>
          <th class="fix-width-100 text-center">ประเภทเอกสาร</th>
          <th class="fix-width-100 text-center">เลขที่เอกสาร</th>
          <th class="fix-width-100 text-center">มูลค่า</th>
					<th class="fix-width-150 text-center">หมายเหตุ</th>
					<?php if($doc->status != 'O') : ?>
					<th class="fix-width-150 text-center">สถานะการส่ง</th>
					<?php endif; ?>
        </tr>
      </thead>
      <tbody id="row-table">
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<?php $no = $rs->id; ?>
						<?php $disc = ($doc->status == 'C' OR $doc->status == 'D') ? 'disabled' : ''; ?>
		        <tr id="row-<?php echo $no; ?>">
		          <td class="middle text-center">
		            <label>
		              <input type="checkbox" class="ace row-chk" data-id="<?php echo $no; ?>" <?php echo $disc; ?>/>
		              <span class="lbl"></span>
		            </label>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm cardCode" data-id="<?php echo $no; ?>" id="cardCode-<?php echo $no; ?>" value="<?php echo $rs->CardCode; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm cardName" id="cardName-<?php echo $no; ?>" value="<?php echo $rs->CardName; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm shipTo" id="shipTo-<?php echo $no; ?>" value="<?php echo $rs->Address; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm contact" id="contact-<?php echo $no; ?>" value="<?php echo $rs->contact; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <select class="form-control input-sm" id="shipType-<?php echo $no; ?>" disabled>
		              <option value="P" <?php echo is_selected('P', $rs->type); ?>>ส่งสินค้า</option>
		              <option value="D" <?php echo is_selected('D', $rs->type); ?>>ส่งเอกสาร</option>
									<option value="O" <?php echo is_selected('O', $rs->type); ?>>อื่นๆ</option>
		            </select>
		          </td>
		          <td class="middle">
		            <select class="form-control input-sm" id="docType-<?php echo $no; ?>" onchange="toggleDocType(<?php echo $no; ?>)" disabled>
		              <option value=""></option>
		              <option value="DO" <?php echo is_selected('DO', $rs->DocType); ?>>DO</option>
		              <option value="IV" <?php echo is_selected('IV', $rs->DocType); ?>>IV</option>
								<?php if($rs->type == 'D') : ?>
		              <option value="PB" <?php echo is_selected('PB', $rs->DocType); ?>>PB</option>
		              <option value="CN" <?php echo is_selected('CN', $rs->DocType); ?>>CN</option>
								<?php endif; ?>
		            </select>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm docNum" id="docNum-<?php echo $no; ?>" value="<?php echo $rs->DocNum; ?>" disabled/>
		          </td>
		          <td class="middle">
		            <input type="text" class="form-control input-sm text-right docTotal" id="docTotal-<?php echo $no; ?>" value="<?php echo number($rs->DocTotal, 2); ?>" disabled />
		          </td>
							<td class="middle">
								<input type="text" class="form-control input-sm" id="remark-<?php echo $no; ?>" value="<?php echo $rs->remark; ?>" disabled />
							</td>
							<?php if($doc->status != 'O') : ?>
							<td class="middle text-center">
							<select class="form-control input-sm line-status" data-id="<?php echo $rs->id; ?>" id="lineStatus-<?php echo $no; ?>" <?php echo $disc; ?>>
									<option value="1" <?php echo is_selected($rs->result_status, '1'); ?>>Loaded</option>
									<option value="4" <?php echo is_selected($rs->result_status, '4'); ?>>สำเร็จ</option>
									<option value="2" <?php echo is_selected($rs->result_status, '2'); ?>>ส่งบางส่วน</option>
									<option value="3" <?php echo is_selected($rs->result_status, '3'); ?>>ไม่ได้ส่ง</option>
									<option value="5" <?php echo is_selected($rs->result_status, '5'); ?>>ลูกค้าไม่รับของ</option>
									<option value="6" <?php echo is_selected($rs->result_status, '6'); ?>>สินค้าผิด</option>
									<option value="7" <?php echo is_selected($rs->result_status, '7'); ?>>เอกสารผิด</option>
								</select>
							</td>
							<?php endif; ?>
		        </tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="10" class="text-center">-- No data --</td></tr>
				<?php endif; ?>
      </tbody>
    </table>
  </div>


	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 padding-5">
		<?php if($doc->status == 'R') : ?>
		<select class="form-control input-sm" id="main-status">
				<option value="0">Please Select</option>
				<option value="1">Loaded</option>
				<option value="4">สำเร็จ</option>
				<option value="2">ส่งบางส่วน</option>
				<option value="3">ไม่ได้ส่ง</option>
				<option value="5">ลูกค้าไม่รับของ</option>
				<option value="6">สินค้าผิด</option>
				<option value="7">เอกสารผิด</option>
			</select>
		<?php endif; ?>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<?php if($doc->status == 'R') : ?>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="change_line_status()">Change Status</button>
		<?php endif; ?>
	</div>

	<div class="col-lg-8-harf col-md-7 col-sm-7 col-xs-4 padding-5 text-right">
		<?php if($doc->status == 'R') : ?>
			<button type="button" class="btn btn-xs btn-success" style="min-width:100px;" onclick="updateStatus()">Save and Close</button>
		<?php endif; ?>
	</div>
</div>

<div class="row margin-top-30">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="pull-right text-right" style="font-size:10px; font-style: italic; color:#777;">
		<?php if( ! empty($logs)) : ?>
			<?php foreach($logs as $log) : ?>
				<?php echo "* {$log->action} โดย&nbsp;&nbsp; {$log->uname} &nbsp;&nbsp;( {$log->emp_name} ) &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
			<?php endforeach; ?>
		<?php endif; ?>
    </p>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/delivery/delivery.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/delivery/delivery_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/delivery/delivery_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

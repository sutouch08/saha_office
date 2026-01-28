<?php $this->load->view('include/header'); ?>
<?php $this->load->view('return_request/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if(($doc->status == 'C' OR $doc->status == 'D') && ($this->isAdmin OR $this->isSuperAdmin)) : ?>
			<button type="button" class="btn btn-white btn-primary top-btn" onclick="rollback('<?php echo $doc->code; ?>')"><i class="fa fa-refresh"></i> ย้อนสถานะ</button>
		<?php endif; ?>
		<?php if($doc->status == 'C') : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send to SAP</button>
		<?php endif; ?>
		<?php if($doc->status == 'P') : ?>
			<button type="button" class="btn btn-white btn-warning top-btn" onclick="edit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
			<button type="button" class="btn btn-white btn-danger top-btn" onclick="cancel('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
		<?php endif; ?>
  </div>
</div>
<hr />


<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Doc Date</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->date_add); ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Posting</label>
		<input type="text" class="width-100 text-center r" value="<?php echo thai_date($doc->posting_date); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="width-100 text-center r" value="<?php echo $doc->CardCode; ?>" disabled/>
	</div>
	<div class="col-lg-5 col-md-5-harf col-sm-5 col-xs-12 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->CardName; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Currency</label>
		<select class="width-100 r" disabled>
			<?php echo select_currency($doc->Currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="width-100 text-center r" value="<?php echo $doc->Rate; ?>" disabled />
	</div>

	<div class="col-lg-4 col-md-8 col-sm-8 col-xs-12 padding-5">
		<label>คลัง</label>
		<input type="text" class="width-100" value="<?php echo $doc->WhsCode.' | '.$doc->WhsName; ?>" disabled />
	</div>

	<div class="col-lg-6 col-md-9 col-sm-9 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="width-100 r" value="<?php echo $doc->remark; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<input type="text" class="width-100 text-center" value="<?php echo return_status_label($doc->status);?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>SAP No.</label>
		<input type="text" class="width-100 text-center" value="<?php echo $doc->DocNum;?>" disabled />
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<div class="row" style="margin-left:-7px; margin-right:-7px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="receiveTable">
		<table class="table table-bordered tableFixHead" style="font-size:11px; min-width:1110px; margin-bottom:0;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="fix-width-100 text-center fix-header">รหัสสินค้า</th>
					<th class="min-width-250 text-center fix-header">ชื่อสินค้า</th>
					<th class="fix-width-80 text-center fix-header">หน่วยนับ</th>
					<th class="fix-width-80 text-center fix-header">เอกสาร</th>
					<th class="fix-width-80 text-center fix-header">เลขที่</th>
					<th class="fix-width-80 text-center fix-header">ราคา</th>
					<th class="fix-width-80 text-center fix-header">ส่วนลด</th>
					<th class="fix-width-100 text-center fix-header">จำนวน</th>
					<th class="fix-width-100 text-center fix-header">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="return-table">
			<?php $totalQty = 0; ?>
			<?php $totalAmount = 0; ?>
			<?php if( ! empty($details)) : ?>
				<?php $no = 1; ?>
				<?php foreach($details as $rs) : ?>
					<tr class="font-size-11 return-rows">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle">
							<input type="text" class="form-control input-sm text-label" value="<?php echo $rs->ItemCode; ?>" readonly />
						</td>
						<td class="middle">
							<input type="text" class="form-control input-sm text-label" value="<?php echo $rs->ItemName; ?>" readonly />
						</td>
						<td class="middle text-center"><?php echo $rs->UnitMsr; ?></td>
						<td class="middle text-center"><?php echo $rs->BaseType; ?></td>
						<td class="middle text-center"><?php echo $rs->BaseRef; ?></td>
						<td class="middle text-right"><?php echo number($rs->PriceBefDi, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->DiscPrcnt, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->LineTotal, 2); ?></td>
					</tr>
					<?php $no++; ?>
					<?php $totalQty += $rs->Qty; ?>
					<?php $totalAmount += $rs->LineTotal; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
  </div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-2 col-md-2 col-sm-3 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user->emp_name; ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $this->user->uname; ?>" />
        </div>
      </div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0">
					<?php if(!empty($logs)) : ?>
						<p class="log-text">
						<?php foreach($logs as $log) : ?>
							<?php echo "* ".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp; {$log->emp_name}  &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
						<?php endforeach; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
    </div>
  </div>


	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-right-0">
    <div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-3 col-sm-2 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->TotalQty, 2); ?>" disabled>
        </div>
        <label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal - $doc->VatSum, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/return_request/return_request.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/return_request/return_request_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

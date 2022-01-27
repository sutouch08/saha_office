<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; รอแพ็ค</button>
				<button type="button" class="btn btn-sm btn-yellow" onclick="viewProcess()"><i class="fa fa-arrow-left"></i> &nbsp; กำลังแพ็ค</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
		<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SO No</label>
		<input type="text" class="form-control input-sm text-center" id="orderCode" value="<?php echo $doc->orderCode; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Pick List No</label>
		<input type="text" class="form-control input-sm text-center" id="pickCode" value="<?php echo $doc->pickCode; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm" id="CardName" value="<?php echo $doc->CardName; ?>" disabled />
	</div>
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10"/>

<?php $this->load->view('packing/packing_box'); ?>
<?php $this->load->view('packing/packing_control'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-10 text-center">บาร์โค้ด</th>
					<th class="">สินค้า</th>
					<th class="width-10 text-center">หน่วยนับ</th>
					<th class="width-10 text-center">จำนวนจัด</th>
					<th class="width-10 text-center">แพ็คแล้ว</th>
					<th class="width-10 text-center">คงเหลือ</th>
				</tr>
			</thead>
			<tbody id="row-table">
				<?php $totalPick = 0; ?>
				<?php $totalPack = 0; ?>
				<?php $totalBalance = 0; ?>
				<?php if(!empty($rows)) : ?>
					<?php foreach($rows as $rs) : ?>
						<?php $color = $rs->PickQtty <= $rs->PackQtty ? 'background-color:#ebf1e2;' : ''; ?>
						<?php $balance = $rs->PickQtty - $rs->PackQtty; ?>
						<tr class="row-tr" id="row-<?php echo $rs->id; ?>" style="<?php echo $color; ?>">
							<td class="middle text-center">
								<?php if($rs->barcode) : ?>
									<?php echo $rs->barcode; ?>
								<?php else : ?>
									<button type="button" class="btn btn-sm btn-primary" onclick="showPackOption('<?php echo $rs->ItemCode; ?>', <?php echo $rs->UomEntry; ?>)">Options</button>
								<?php endif; ?>
							</td>
							<td class=""><?php echo $rs->ItemCode .' | '.$rs->ItemName; ?></td>
							<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
							<td class="middle text-center" id="pick-<?php echo $rs->id; ?>"><?php echo round($rs->PickQtty, 2); ?></td>
							<td class="middle text-center" id="pack-<?php echo $rs->id; ?>"><?php echo round($rs->PackQtty, 2); ?></td>
							<td class="middle text-center balance" id="balance-<?php echo $rs->id; ?>"><?php echo round($balance, 2); ?></td>
						</tr>
						<?php $totalPick += round($rs->PickQtty, 2); ?>
						<?php $totalPack += round($rs->PackQtty, 2); ?>
						<?php $totalBalance += round($balance, 2); ?>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php $finish = $totalBalance <= 0 ? "" : "hide"; ?>
				<tr class="<?php echo $finish; ?>" id="finish-row">
					<td colspan="6" class="text-center">
						<button type="button" class="btn btn-sm btn-success" id="btn-finish" onclick="finish_pack()">Finish Pack</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!--  Add New Address Modal  --------->
<div class="modal fade" id="packOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" id="option-title"></h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
	              	<div class="input-group">
	              		<span class="input-group-addon">Qty</span>
										<input type="number" class="form-control input-sm text-center" id="option-qty" value="1" />
	              	</div>
									<input type="hidden" id="option-item" value="" />
	              </div>
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									<div class="input-group">
										<span class="input-group-addon">Uom</span>
										<select class="form-control input-sm" id="option-uom">
											<option value="">Select Uom</option>
										</select>
									</div>
								</div>
	            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="packWithOption()" >Pack</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/packing/packing.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/packing/packing_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

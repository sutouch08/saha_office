<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<?php if($doc->Status != 'C') : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="sendToSap()"><i class="fa fa-send"></i> &nbsp; Send to SAP</button>
					<?php if($doc->Status == 'N') : ?>
						<button type="button" class="btn btn-sm btn-danger" onclick="getDelete(<?php echo $doc->id; ?>, '<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
					<?php endif; ?>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<?php if($doc->Status == 'C') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
		<input type="hidden" id="id" value="<?php echo $doc->id; ?>">
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->DocDate); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Pallet No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->palletCode; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->toWhsCode; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label>Location ปลายทาง</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->transfer_bin_code; ?>" disabled/>
	</div>
	<div class="col-lg-3 col-md-3-harf col-sm-4-harf col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" max-length="254" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->uname; ?>" disabled/>
	</div>
</div>

<hr class="margin-top-10 margin-bottom-10 padding-5">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered" style="min-width:1100px;">
			<thead>
				<tr>
					<th class="middle text-center" style="width:50px;">#</th>
					<th class="middle text-center" style="width:100px;">Item</th>
					<th class="middle text-center" style="width:200px;">Description</th>
					<th class="middle text-center" style="width:100px;">SO No.</th>
					<th class="middle text-center" style="width:100px;">Pick No.</th>
					<th class="middle text-center" style="width:100px;">Pack No.</th>
					<th class="middle text-center" style="width:100px;">ต้นทาง</th>
					<th class="middle text-center" style="width:100px;">จำนวน</th>
					<th class="middle text-center" style="width:50px;">Uom</th>
				</tr>
			</thead>
			<tbody id="transfer-table">
			<?php $no = 1; ?>
			<?php $totalQty = 0; ?>
			<?php if(!empty($details)) : ?>
				<?php foreach($details as $rs) : ?>
				<tr id="row-<?php echo $rs->id; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->ItemCode; ?></td>
					<td class="middle"><?php echo $rs->ItemName; ?></td>
					<td class="middle text-center"><?php echo $rs->orderCode; ?></td>
					<td class="middle text-center"><?php echo $rs->pickCode; ?></td>
					<td class="middle text-center"><?php echo $rs->packCode; ?></td>
					<td class="middle text-center"><?php echo $rs->fromBinCode; ?></td>
					<td class="middle text-right"><?php echo round($rs->Qty, 2); ?></td>
					<td class="middle"><?php echo $rs->unitMsr; ?></td>
				</tr>
				<?php $no++; ?>
				<?php $totalQty += $rs->Qty; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7" class="text-right" style="font-size:18px;">รวม</td>
					<td colspan="2" class="text-center" style="font-size:18px;" id="total-qty"><?php echo number($totalQty, 2); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>



<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

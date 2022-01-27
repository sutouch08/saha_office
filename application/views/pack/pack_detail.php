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
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
		<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>SO No</label>
		<input type="text" class="form-control input-sm text-center" id="orderCode" value="<?php echo $doc->orderCode; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Pick List No</label>
		<input type="text" class="form-control input-sm text-center" id="pickCode" value="<?php echo $doc->pickCode; ?>" disabled />
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm" id="CardName" value="<?php echo $doc->CardName; ?>" disabled />
	</div>
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10" />

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-15">รหัสสินค้า</th>
					<th class="">ชื่อสินค้า</th>
					<th class="width-10 text-right">จำนวนจัด</th>
					<th class="width-10 text-right">แพ็คแล้ว</th>
					<th class="width-10 text-right">คงเหลือ</th>
				</tr>
			</thead>
			<tbody>
				<?php $totalPick = 0; ?>
				<?php $totalPack = 0; ?>
				<?php if(!empty($rows)) : ?>
					<?php $no = 1; ?>
					<?php foreach($rows as $rs) : ?>
						<tr>
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->ItemCode; ?></td>
							<td class="middle"><?php echo $rs->ItemName; ?></td>
							<td class="middle text-right"><?php echo number($rs->PickQtty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->PackQtty, 2); ?></td>
							<td class="middle text-right"><?php echo number(($rs->PickQtty - $rs->PackQtty), 2); ?></td>
						</tr>
						<?php $no++; ?>
						<?php $totalPick += $rs->PickQtty; ?>
						<?php $totalPack += $rs->PackQtty; ?>
					<?php endforeach; ?>
					<tr>
						<td colspan="3" class="middle text-right">รวม</td>
						<td class="middle text-right"><?php echo number($totalPick, 2); ?></td>
						<td class="middle text-right"><?php echo number($totalPack, 2); ?></td>
						<td class="middle text-right"><?php echo number(($totalPick - $totalPack), 2); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <?php if(!empty($logs)) : ?>
      <p class="pull-right text-right" style="font-size:11px; font-style: italic; color:#777;">
      <?php foreach($logs as $log) : ?>
        <?php echo "*".logs_action_name($log->action) ." &nbsp;&nbsp; {$log->uname} &nbsp;&nbsp;( {$log->emp_name} ) &nbsp;&nbsp; ".thai_date($log->date_upd, TRUE)."<br/>"; ?>
      <?php endforeach; ?>
      </p>
    <?php endif; ?>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/pack/pack.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pack/pack_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

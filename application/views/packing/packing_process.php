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

<?php $this->load->view('packing/packing_pallet'); ?>
<?php $this->load->view('packing/packing_box'); ?>
<?php $this->load->view('packing/packing_control'); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th style="min-width:150px;">สินค้า</th>
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
						<?php $color = $rs->BasePickQty <= $rs->BasePackQty ? 'background-color:#ebf1e2;' : ''; ?>
						<?php $balance = $rs->BasePickQty - $rs->BasePackQty; ?>
						<?php $bcolor = is_null($rs->barcode) ? '#0032e7' : '#000000'; ?>
						<tr class="row-tr" id="row-<?php echo $rs->id; ?>" style="<?php echo $color; ?>">
							<td class="">
								<input type="hidden" name="barcode" value="<?php echo $rs->barcode; ?>" />
								<a href="javascript:void(0)" style="color:<?php echo $bcolor; ?>"	onclick="showPackOption('<?php echo $rs->ItemCode; ?>', <?php echo $rs->UomEntry; ?>)">
								<?php echo $rs->ItemCode.' : '.$rs->ItemName; ?>
								</a>
							</td>
							<td class="middle text-center"><?php echo $rs->unitMsr2; ?></td>
							<td class="middle text-center" id="pick-<?php echo $rs->id; ?>"><?php echo round($rs->BasePickQty, 2); ?></td>
							<td class="middle text-center packed" id="pack-<?php echo $rs->id; ?>"><?php echo round($rs->BasePackQty, 2); ?></td>
							<td class="middle text-center balance" id="balance-<?php echo $rs->id; ?>"><?php echo round($balance, 2); ?></td>
						</tr>
						<?php $totalPick += round($rs->BasePickQty, 2); ?>
						<?php $totalPack += round($rs->BasePackQty, 2); ?>
						<?php $totalBalance += round($balance, 2); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<?php $finish = $totalBalance <= 0 ? "" : "hide"; ?>
			<tfoot class="<?php echo $finish; ?>" id="finish-row">
				<tr>
					<td colspan="5" class="text-center">
						<button type="button" class="btn btn-sm btn-success" id="btn-finish" onclick="finish_pack()">Finish Pack</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script id="details-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr class="row-tr" id="row-{{id}}" style="{{color}}">
			<td class="">
				<a href="javascript:void(0)" style="color:{{bcolor}}"	onclick="showPackOption('{{ItemCode}}', {{UomEntry}} )">{{ItemCode}} : {{ItemName}}</a>
			</td>
			<td class="middle text-center">{{unitMsr}}</td>
			<td class="middle text-center" id="pick-{{id}}">{{PickQtty}}</td>
			<td class="middle text-center packed" id="pack-{{id}}">{{PackQtty}}</td>
			<td class="middle text-center balance" id="balance-{{id}}">{{balance}}</td>
		</tr>
	{{/each}}
</script>


<?php $this->load->view('packing/packing_process_modal'); ?>

<input type="hidden" id="pallet-option-id">

<script src="<?php echo base_url(); ?>scripts/packing/packing.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/packing/packing_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/packing/packing_pallet.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

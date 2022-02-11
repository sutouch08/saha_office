<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    	<p class="pull-right top-p visible-xs">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i></button>
				<?php if($doc->Status == 'N' OR $doc->Status == 'P') : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="goProcess(<?php echo $doc->id; ?>)">แพ็คสินค้า</button>
				<button type="button" class="btn btn-sm btn-danger" onclick="canclePack()">ยกเลิก</button>
				<?php endif; ?>
				<?php if($doc->Status == 'Y') : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="showBoxOption()"><i class="fa fa-print"></i></button>
					<button type="button" class="btn btn-sm btn-primary" onclick="showBinOption()"><i class="fa fa-send"></i></button>
				<?php endif; ?>
      </p>
			<p class="pull-right top-p hidden-xs">

				<button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
				<?php if($doc->Status == 'N' OR $doc->Status == 'P') : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="goProcess(<?php echo $doc->id; ?>)">แพ็คสินค้า</button>
				<button type="button" class="btn btn-sm btn-danger" onclick="canclePack()">ยกเลิก</button>
				<?php endif; ?>
				<?php if($doc->Status == 'Y') : ?>
					<button type="button" class="btn btn-sm btn-info" onclick="showBoxOption()"><i class="fa fa-print"></i> พิมพ์ Label</button>					
				<?php endif; ?>
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

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->TransWhsCode; ?>" disabled />
	</div>

	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-8 padding-5">
		<label>Location ปลายทาง</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->TransBinCode; ?>" disabled />
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
					<th class="width-10 text-center">UOM</th>
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
							<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
							<td class="middle text-right"><?php echo number($rs->PickQtty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->PackQtty, 2); ?></td>
							<td class="middle text-right"><?php echo number(($rs->PickQtty - $rs->PackQtty), 2); ?></td>
						</tr>
						<?php $no++; ?>
						<?php $totalPick += $rs->PickQtty; ?>
						<?php $totalPack += $rs->PackQtty; ?>
					<?php endforeach; ?>
					<tr>
						<td colspan="4" class="middle text-right">รวม</td>
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

<div class="modal fade" id="boxOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">Print Pack Label</h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered" style="margin-bottom:0px;">
										<thead>
											<tr>
												<th class="width-10 middle text-center">
													<label>
														<input type="checkbox" class="ace" id="box-chk-all" onchange="check_box_all()">
														<span class="lbl"></span>
													</label>
												</th>
												<th class="width-30 middle text-center">กล่อง</th>
												<th class="width-20 middle text-center">จำนวนสินค้า</th>
												<th class="width-30 middle text-center"></th>
											</tr>
										</thead>
	              		<tbody id="box-list-table">

	              		</tbody>
	              	</table>
	              </div>
	            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-info pull-right" onClick="printSelectedBox()"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>


<script id="box-list-template" type="text/x-handlebarsTemplate">
{{#each this}}
	<tr id="box-row-{{box_id}}">
		<td class="middle text-center">
			<label><input type="checkbox" class="ace box-chk" data-no="{{no}}" value="{{box_id}}"><span class="lbl"></span></label>
		</td>
		<td class="middle">กล่องที่ {{no}}</td>
		<td class="middle text-center">{{qty}}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-xs btn-info" onclick="printBox({{box_id}})"><i class="fa fa-print"></i></button>
			<button type="button" class="btn btn-xs btn-primary" onclick="editBox({{box_id}})"><i class="fa fa-eye"></i></button>
		</td>
	</tr>
{{/each}}
</script>

<div class="modal fade" id="boxEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content">

            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered" style="margin-bottom:0px;">
	              		<tbody id="box-detail-table">

	              		</tbody>
	              	</table>
	              </div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center red" id="box-error">

								</div>
	            </div>
            </div>
            <div class="modal-footer">
							<button type="button" class="btn btn-sm btn-warning" onclick="backStep()">Close</button>
            </div>
        </div>
    </div>
</div>



<script id="box-detail-template" type="text/x-handlebarsTemplate">
	<tr>
		<td colspan="3" class="text-center">กล่องที่ {{box_no}}</td>
	</tr>
	<tr>
		<td class="text-center">สินค้า</td>
		<td class="text-center" style="width:50px;">Uom</td>
		<td class="text-center" style="width:70px;">จำนวน</td>
	</tr>
	{{#each rows}}
	<tr id="box-row-{{id}}">
		<td class="middle">{{ItemCode}}  {{ItemName}}</td>
		<td class="middle text-center">{{unitMsr}}</td>
		<td class="middle text-center">{{qty}}</td>
	</tr>
	{{/each}}
</script>



<div class="modal fade" id="binOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
					<div class="modal-header" style="padding-bottom:0px; border-bottom:solid 1px #CCC;">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title-site">ระบุ Location ปลายทาง</h4>
					</div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 padding-5">
									<label>คลัง</label>
	              	<input type="text" class="form-control input-sm text-center" id="bufferWarehouse" value="<?php echo getConfig('BUFFER_WAREHOUSE'); ?>" disabled />
	              </div>
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 padding-5">
									<label>Location</label>
	              	<input type="text" class="form-control input-sm" id="binOption" />
	              </div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center red" id="bin-error"></div>
	            </div>
            </div>
            <div class="modal-footer">
							<button type="button" class="btn btn-sm btn-primary" onclick="sendToSap()">Send To SAP</button>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url(); ?>scripts/pack/pack.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/pack/pack_detail.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

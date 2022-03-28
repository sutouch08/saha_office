<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 hidden-xs">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<div class="input-group">
			<span class="input-group-addon">เลขที่</span>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled />
		</div>
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<div class="input-group">
			<span class="input-group-addon">User</span>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->uname; ?>" disabled />
		</div>
	</div>
	<div class="col-lg-8 col-md-7 col-sm-6 col-xs-12 padding-5 hidden-xs">
		<div class="input-group">
			<span class="input-group-addon">Remark</span>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->remark; ?>" disabled />
		</div>
	</div>
</div>
<hr class="padding-5 margin-top-10 margin-bottom-10"/>

<div class="row">
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>Location</label>
		<input type="text" class="form-control input-sm text-center" id="zoneCode" autofocus/>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">label</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-submit-zone" onclick="setZone()">Submit</button>
		<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-change-zone" onclick="changeZone()">Change</button>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
		<label>SO No.</label>
		<input type="text" class="form-control input-sm text-center" id="soNo" disabled/>
	</div>

	<div class="col-xs-12 visible-xs">&nbsp;</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>Qty</label>
		<input type="number" class="form-control input-sm text-center" id="qty" value="1" disabled />
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>Barcode</label>
		<input type="text" class="form-control input-sm text-center" inputmode="none" id="barcode-item" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
		<label class="display-block not-show">label</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-submit-item" onclick="pickItem()" disabled>Pick</button>
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10 padding-5"/>
<input type="hidden" id="BinCode" value="">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-stripped table-bordered border-1 dataTable" id="pick-table" style="min-width:700px;">
			<thead>
				<tr>
					<th class="middle text-center" style="min-width:100px;">สินค้า</th>
					<th class="middle text-center" style="width:80px;">SO No</th>
					<th class="middle text-center" style="width:60px;">UOM</th>
					<th class="middle text-center" style="width:80px;">จำนวน</th>
					<th class="middle text-center" style="width:80px;">จัดแล้ว</th>
					<th class="middle text-center" style="width:80px;">คงเหลือ</th>
					<th class="middle text-center" style="min-width:100px;">ที่เก็บ</th>
					<th class="middle text-center" style="min-width:70px; max-width:80px;"></th>
				</tr>
			</thead>
			<tbody id="details-table">
				<?php if(! empty($details)) : ?>
					<?php $totalBalance = 0; ?>
					<?php foreach($details as $rs) : ?>
						<?php $id = $rs->id; ?>
						<?php $color = $rs->RelQtty <= $rs->PickQtty ? 'bg-green' : ''; ?>
						<?php $balance = $rs->RelQtty - $rs->PickQtty; ?>
						<tr id="row-<?php echo $id; ?>" class="row-tr <?php echo $color; ?>" data-id="<?php echo $id; ?>">
							<td class="middle" style="white-space:normal;">
								<?php if(is_null($rs->barcode)) : ?>
									<a href="javascript:void(0)" onclick="showPickOption('<?php echo $rs->ItemCode; ?>', <?php echo $rs->UomEntry; ?>)">
									<!--<button type="button" class="btn btn-sm btn-primary" onclick="showPickOption('<?php echo $rs->ItemCode; ?>', <?php echo $rs->UomEntry; ?>)">Options</button>-->
								<?php endif; ?>
								<?php echo $rs->barcode; ?> | <?php echo $rs->ItemCode; ?> | <?php echo $rs->ItemName; ?>
								<?php if(is_null($rs->barcode)) : ?>
									</a>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<button type="button"
								class="btn btn-minier btn-block order-btn"
								id="order-<?php echo $rs->id; ?>"
								onclick="toggleOrderCode(<?php echo $rs->id; ?>, <?php echo $rs->OrderCode; ?>)">
								<?php echo $rs->OrderCode; ?>
								</button>
							</td>
							<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
							<td class="middle text-center" id="release-<?php echo $id; ?>"><?php echo round($rs->RelQtty,2); ?></td>
							<td class="middle text-center" id="pick-<?php echo $id; ?>"><?php echo round($rs->PickQtty, 2); ?></td>
							<td class="middle text-center" id="balance-<?php echo $id; ?>"><?php echo round($balance, 2); ?></td>
							<td class="middle text-right">
								<?php echo $rs->stock_in_zone; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->RelQtty > $rs->PickQtty) : ?>
								<button type="button"
									class="btn btn-purple btn-xs"
									id="btn-cancle-pick-<?php echo $rs->id; ?>"
									onclick="showCancleOption('<?php echo $rs->ItemCode; ?>', <?php echo $rs->OrderCode; ?>, <?php echo $rs->id; ?>)">
									<i class="fa fa-exclamation-triangle"></i>
								</button>
								<?php endif; ?>

								<button type="button" class="btn btn-warning btn-xs" onclick="showPickedOption(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							</td>
						</tr>
						<?php $totalBalance += round($balance, 2); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<?php $finish = $totalBalance <= 0 ? '' : 'hide'; ?>
			<tfoot class="<?php echo $finish; ?>" id="finish-row">
				<tr class="" >
					<td colspan="8" class="text-center">
						<button type="button" class="btn btn-sm btn-success" onclick="finishPick()">Finish Pick</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<input type="hidden" id="AbsEntry" value="<?php echo $doc->AbsEntry; ?>" />
<input type="hidden" id="DocNum" value="<?php echo $doc->DocNum; ?>" />

<!--  Add New Address Modal  --------->
<div class="modal fade" id="pickOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" id="option-title"></h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
	              	<div class="input-group">
	              		<span class="input-group-addon">Qty</span>
										<input type="number" class="form-control input-sm text-center" id="option-qty" value="1" />
	              	</div>
									<input type="hidden" id="option-item" value="" />
	              </div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5" style="padding-left:0px;">
									<button class="btn btn-xs btn-danger btn-block" id="btn-minus" onclick="decreseQty()"><i class="fa fa-minus"></i></button>
								</div>
								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5" style="padding-left:0px;">
									<button class="btn btn-xs btn-success btn-block" id="btn-plus" onclick="increseQty()"><i class="fa fa-plus"></i></button>
								</div>
								<div class="col-xs-12 visible-xs">&nbsp;</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
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
								<div class="col-lg-2 col-lg-offset-10 col-md-2 col-md-offset-10 col-sm-3 col-sm-offset-9 col-xs-6 col-xs-offset-6 padding-5">
                	<button type="button" class="btn btn-sm btn-success btn-block" onClick="pickWithOption()" >Pick</button>
								</div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="cancleOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #CCC">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">Canceled Items</h4>
            </div>
            <div class="modal-body">
							<input type="hidden" id="pick-id">
							<input type="hidden" id="limit">
	            <div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
									<table class="table table-striped border-1" style="min-width:700px;">
										<thead>
											<tr>
												<th style="width:100px;">Pick No</th>
												<th style="width:100px;">SO No</th>
												<th style="min-width:100px;">สินค้า</th>
												<th style="width:80px;">จำนวน</th>
												<th style="min-width:80px;">Qty</th>
												<th style="width:80px;">Uom</th>
												<th></th>
												<th style="width:100px;">Bin Code</th>
											</tr>
										</thead>
										<tbody id="cancle-option-table">

										</tbody>
		              </table>
								</div>
	            </div>
            </div>
        </div>
    </div>
</div>

<script id="cancle-option-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="option-{{id}}" class="option-row" data-id="{{id}}">
			<td>{{DocNum}}</td>
			<td>{{OrderCode}}</td>
			<td>{{ItemCode}} </td>
			<td class="text-center">{{Qty}}</td>
			<td>
				<input type="number" id="pick-qty-{{id}}" class="form-control input-sm text-right pick-qty" />
			</td>
			<td>{{unitMsr}}</td>
			<td><button type="button" class="btn btn-xs btn-primary" onclick="addToPick({{id}})">Add</button></td>
			<td>{{BinCode}}</td>
		</tr>
	{{/each}}
</script>



<div class="modal fade" id="pickedOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #CCC">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">Picked details</h4>
            </div>
            <div class="modal-body">
							<input type="hidden" id="picked-id">
	            <div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
									<table class="table table-striped border-1">
										<thead>
											<tr>
												<th style="width:100px;">SO No</th>
												<th style="min-width:100px;">สินค้า</th>
												<th class="text-center" style="width:80px;">จำนวน</th>
												<th class="text-center" style="min-width:80px;">เอาออก</th>
												<th class="text-center" style="width:80px;">Uom</th>
												<th></th>
												<th style="width:100px;">Bin Code</th>
											</tr>
										</thead>
										<tbody id="picked-option-table">

										</tbody>
		              </table>
								</div>
	            </div>
            </div>
        </div>
    </div>
</div>


<script id="picked-option-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="option-{{id}}" class="option-row" data-id="{{id}}">
			<td>{{OrderCode}}</td>
			<td>{{ItemCode}} </td>
			<td class="text-center">
				<input type="hidden" id="picked-limit-{{id}}" value="{{Qty}}" />
				<span id="picked-label-{{id}}">{{QtyLabel}}</span>
			</td>
			<td>
				<input type="number" id="picked-qty-{{id}}" class="form-control input-xs text-center picked-qty" />
			</td>
			<td class="text-center">{{unitMsr}}</td>
			<td>
			<button type="button" class="btn btn-minier btn-primary" onclick="updatePicked({{id}})">Update</button>
			</td>
			<td>{{BinCode}}</td>
		</tr>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>assets/js/dataTables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>scripts/picking/picking.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/picking/picking_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<script>
	$('#pick-table').DataTable({
		"searching" : false,
		"paging" : false,
		"info" : false
	});
</script>

<?php $this->load->view('include/footer'); ?>

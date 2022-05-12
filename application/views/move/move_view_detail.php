<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
			<?php if($doc->Status === 'P' OR $doc->Status === 'F') : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="doExport()"><i class="fa fa-send"></i> &nbsp; Send To SAP</button>
			<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<?php if($doc->Status === 'C') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" id="docDate" value="<?php echo thai_date($doc->DocDate, FALSE); ?>" readonly disabled />
	</div>
	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm text-center edit" id="fromWhsCode" value="<?php echo $doc->fromWhsCode; ?>" disabled />
	</div>
	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center edit" id="toWhsCode" value="<?php echo $doc->toWhsCode; ?>" disabled />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->uname; ?>" disabled>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>DocNum</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled>
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" id="remark" max-length="254" value="<?php echo escape_quot($doc->remark); ?>" disabled/>
	</div>
</div>

<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />

<hr class="margin-top-15 padding-5">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="move-table">
  	<table class="table table-striped border-1" style="min-width:900px;">
    	<thead>
      	<tr>
        	<th colspan="7" class="text-center">รายการโอนย้าย</th>
        </tr>

				<tr>
        	<th style="width:60px;" class="text-center">#</th>
          <th style="width:120px;" class="middle">Barcode</th>
          <th style="min-width:300px;" class="middle">Item</th>
          <th style="width:120px;" class="middle text-center">From Bin</th>
          <th style="width:120px;" class="middle text-center">To Bin</th>
          <th style="width:100px;" class="middle text-center">Qty</th>
          <th style="width:80px;" class="middle">Uom.</th>
        </tr>
      </thead>

      <tbody id="move-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0;  ?>
<?php		foreach($details as $rs) : 	?>
				<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">

	      	<td class="middle text-center no">
						<?php echo $no; ?>
					</td>

					<!--- บาร์โค้ดสินค้า --->
	        <td class="middle">
						<?php echo $rs->barcode; ?>
					</td>

					<!--- รหัสสินค้า -->
	        <td class="middle">
						<?php echo $rs->ItemCode." : ".$rs->ItemName; ?>
					</td>

					<!--- โซนต้นทาง --->
	        <td class="middle text-center">
						<?php echo $rs->fromBinCode; ?>
	        </td>


	        <td class="middle text-center" id="row-label-<?php echo $rs->id; ?>">
	        	<?php echo $rs->toBinCode; ?>
	        </td>

	        <td class="middle text-center qty" >
						<?php echo number($rs->Qty); ?>
					</td>

					<td class="middle">
						<?php echo $rs->unitMsr; ?>
					</td>
	      </tr>
<?php			$no++;			?>
<?php 	  $total_qty += $rs->Qty; ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
					<td></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>

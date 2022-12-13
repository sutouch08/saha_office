<?php $this->load->view('include/header'); ?>
<style>
	label.search-label {
		font-size:12px;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Doc No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="DocNum" value="<?php echo $DocNum; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">SO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="OrderCode" value="<?php echo $OrderCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Items</label>
    <input type="text" class="form-control input-sm text-center search-box" name="ItemCode" value="<?php echo $ItemCode; ?>" />
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $Status); ?>>Pending</option>
			<option value="R" <?php echo is_selected('R', $Status); ?>>Released</option>
			<option value="P" <?php echo is_selected('P', $Status); ?>>Picking</option>
			<option value="Y" <?php echo is_selected('Y', $Status); ?>>Finished</option>
			<option value="C" <?php echo is_selected('C', $Status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $Status); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Line Status</label>
		<select class="form-control input-sm" name="LineStatus" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $LineStatus); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $LineStatus); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $LineStatus); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">Date</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
		</div>
	</div>

	<div class="col-xs-12 visible-xs"></div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		<span class="blue">ยอด commit ดูจาก (BaseRelQty) และ (LineStatus) = Open</span>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered" style="min-width:1350px;">
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">#</th>
					<th style="width:80px;" class="middle text-center">Date</th>
					<th style="width:100px;" class="middle text-center">Doc No.</th>
					<th style="width:80px;" class="middle text-center">SO No.</th>
					<th style="width:200px;" class="middle text-center">Item</th>
					<th style="width:80px;" class="middle text-center">Price</th>
					<th style="width:80px;" class="middle text-center">Uom1</th>
					<th style="width:80px;" class="middle text-center">Uom2</th>
					<th style="width:80px;" class="middle text-center">Base<br/>Qty</th>
					<th style="width:80px;" class="middle text-center">Rel.<br/>Qty</th>
					<th style="width:80px;" class="middle text-center">BaseRel.<br/>Qty</th>
					<th style="width:80px;" class="middle text-center">BasePick<br/>Qty</th>
					<th style="width:80px;" class="middle text-center">Status</th>
					<th style="width:80px;" class="middle text-center">Line<br/>Status</th>
					<th style="width:100px;" class="middle text-center">User</th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($details)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($details as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo thai_date($rs->CreateDate, FALSE,'/'); ?></td>
						<td class="middle"><?php echo $rs->DocNum; ?></td>
						<td class="middle"><?php echo $rs->OrderCode; ?></td>
						<td class="middle">
							<input type="text" style="width:100%; padding:0px; border:0px; color:#393939;" readonly value="<?php echo $rs->ItemCode.' : '.$rs->ItemName; ?>" />
						</td>
						<td class="middle text-center"><?php echo number($rs->price, 2); ?></td>
						<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
						<td class="middle text-center"><?php echo $rs->unitMsr2; ?></td>
						<td class="middle text-center"><?php echo number($rs->BaseQty, 2); ?></td>
						<td class="middle text-center"><?php echo number($rs->RelQtty, 2); ?></td>
						<td class="middle text-center"><?php echo number($rs->BaseRelQty, 2); ?></td>
						<td class="middle text-center"><?php echo number($rs->BasePickQty, 2); ?></td>
						<td class="middle text-center">
							<?php
								switch($rs->PickStatus)
								{
									case 'N' :
										echo 'Pending';
										break;
									case 'R' :
										echo 'Released';
										break;
									case 'P' :
										echo 'Picking';
										break;
									case 'Y' :
										echo 'Finished';
										break;
									case 'C' :
										echo 'Closed';
										break;
									case 'D' :
										echo 'Canceled';
										break;
									default :
										echo 'Unknow';
										break;
								}
							?>
						</td>
						<td class="middle text-center">
							<?php
								switch($rs->LineStatus)
								{
									case 'O' :
										echo 'Open';
										break;
									case 'C' :
										echo 'Closed';
										break;
									case 'D' :
										echo 'Canceled';
										break;
									default :
										echo 'Open';
										break;
								}
							 ?>
						</td>
						<td class="middle text-center"><?php echo $rs->uname; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="14" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/pick_details/pick_rows.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

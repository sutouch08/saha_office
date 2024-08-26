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
    <label class="search-label">เลขที่</label>
    <input type="text" class="form-control input-sm text-center search-box" name="DocNum" value="<?php echo $DocNum; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">เลขที่ SO</label>
    <input type="text" class="form-control input-sm text-center search-box" name="OrderCode" value="<?php echo $OrderCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="ItemCode" value="<?php echo $ItemCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Pick Status</label>
    <select class="form-control input-sm" name="PickStatus" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $PickStatus); ?>>รอดำเนินการ</option>
			<option value="R" <?php echo is_selected('R', $PickStatus); ?>>รอจัด</option>
			<option value="P" <?php echo is_selected('P', $PickStatus); ?>>กำลังจัด</option>
			<option value="Y" <?php echo is_selected('Y', $PickStatus); ?>>จัดแล้ว</option>
			<option value="C" <?php echo is_selected('C', $PickStatus); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $PickStatus); ?>>ยกเลิก</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Line Status</label>
    <select class="form-control input-sm" name="LineStatus" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $LineStatus); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $LineStatus); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $LineStatus); ?>>Cancelled</option>
		</select>
  </div>

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
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<?php
	$sort_code = get_sort('code', $order_by, $sort_by);
	$sort_OrderCode = get_sort('OrderCode', $order_by, $sort_by);
	$sort_ItemCode = get_sort('ItemCode', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered dataTable" style="min-width:1100px;">
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_OrderCode; ?>" id="sort_OrderCode" onclick="sort('OrderCode')">เลขที่ SO</th>
					<th style="min-width:250px;" class="middle text-center sorting <?php echo $sort_ItemCode; ?>" id="sort_ItemCode" onclick="sort('ItemCode')">สินค้า</th>
					<th style="width:100px;" class="middle text-center">Uom</th>
					<th style="width:100px;" class="middle text-center">Price</th>
					<th style="width:100px;" class="middle text-center">BaseQty</th>
					<th style="width:100px;" class="middle text-center">Release Qty</th>
					<th style="width:100px;" class="middle text-center">Base Release Qty</th>
					<th style="width:100px;" class="middle text-center">Base Pick Qty</th>
					<th style="width:100px;" class="middle text-center">Pick Status</th>
					<th style="width:100px;" class="middle text-center">Line Status</th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($details)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($details as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->OrderCode; ?></td>
						<td class="middle"><?php echo $rs->ItemCode.' : '.$rs->ItemName; ?></td>
						<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
						<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->BaseQty, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->RelQtty, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->BaseRelQty, 2); ?></td>
						<td class="middle text-right"><?php echo number($rs->BasePickQty, 2); ?></td>
						<td class="middle text-center"><?php echo pick_status_label($rs->PickStatus); ?></td>
						<td class="middle text-center"><?php echo line_status_label($rs->LineStatus); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="12" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/pick_rows/pick_rows.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

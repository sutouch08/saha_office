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
    <label class="search-label">Bin Code</label>
    <input type="text" class="form-control input-sm text-center search-box" name="BinCode" value="<?php echo $BinCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที่จัด</label>
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
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<?php
	$sort_DocNum = get_sort('DocNum', $order_by, $sort_by);
	$sort_Date = get_sort('date_upd', $order_by, $sort_by);
	$sort_uname = get_sort('uname', $order_by, $sort_by);
	$sort_OrderCode = get_sort('OrderCode', $order_by, $sort_by);
	$sort_ItemCode = get_sort('ItemCode', $order_by, $sort_by);
	$sort_BinCode = get_sort('BinCode', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered dataTable" style="min-width:1100px;">
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">#</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_Date; ?>" id="sort_date_upd" onclick="sort('date_upd')">วันที่</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_DocNum; ?>" id="sort_DocNum" onclick="sort('DocNum')">เลขที่เอกสาร</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_OrderCode; ?>" id="sort_OrderCode" onclick="sort('OrderCode')">เลขที่ SO</th>
					<th style="min-width:250px;" class="middle text-center sorting <?php echo $sort_ItemCode; ?>" id="sort_DocNum" onclick="sort('ItemCode')">สินค้า</th>
					<th style="width:100px;" class="middle text-center">จำนวน</th>
					<th style="width:100px;" class="middle text-center">หน่วยนับ</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_BinCode; ?>" id="sort_BinCode" onclick="sort('BinCode')">BinCode</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User</th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($details)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($details as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo thai_date($rs->date_upd, TRUE,'/'); ?></td>
						<td class="middle"><?php echo $rs->DocNum; ?></td>
						<td class="middle"><?php echo $rs->OrderCode; ?></td>
						<td class="middle"><?php echo $rs->ItemCode.' : '.$rs->ItemName; ?></td>
						<td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
						<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
						<td class="middle"><?php echo $rs->BinCode; ?></td>
						<td class="middle text-center"><?php echo $rs->uname; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="9" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/buffer/buffer.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

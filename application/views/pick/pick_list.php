<?php $this->load->view('include/header'); ?>
<style>
	label.search-label {
		font-size:12px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> New Pick List</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Web Code</label>
    <input type="text" class="form-control input-sm text-center search-box" name="WebCode" value="<?php echo $WebCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">SO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="SoNo" value="<?php echo $SoNo; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="Uname" value="<?php echo $Uname; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label">Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $Status); ?>>รอดำเนินการ</option>
			<option value="R" <?php echo is_selected('R', $Status); ?>>รอจัด</option>
			<option value="P" <?php echo is_selected('P', $Status); ?>>กำลังจัด</option>
			<option value="Y" <?php echo is_selected('Y', $Status); ?>>จัดแล้ว</option>
			<option value="C" <?php echo is_selected('C', $Status); ?>>Closed</option>
		</select>
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
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<?php
	$sort_DocNum = get_sort('DocNum', $order_by, $sort_by);
	$sort_PostingDate = get_sort('CreateDate', $order_by, $sort_by);
	$sort_uname = get_sort('Uname', $order_by, $sort_by);
	$sort_Status = get_sort('Status', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1 dataTable" style="table-layout: fixed; width:100%; min-width:700px;">
			<thead>
				<tr>
					<th style="width:20px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_PostingDate; ?>" id="sort_CreateDate" onclick="sort('CreateDate')">Date</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_DocNum; ?>" id="sort_DocNum" onclick="sort('DocNum')">Web Code</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_uname; ?>" id="sort_Uname" onclick="sort('Uname')">User</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_Status; ?>" id="sort_Status" onclick="sort('Status')">Status</th>
					<th class="middle text-right"></th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->CreateDate, FALSE,'/'); ?></td>
						<td class="middle text-center"><?php echo $rs->DocNum; ?></td>
						<td class="middle text-center"><?php echo $rs->uname; ?></td>
						<td class="middle text-center">
							<?php if($rs->Status == 'R') : ?>
								<span class="blue">รอจัด</span>
							<?php elseif($rs->Status == 'D') : ?>
								<span class="red">ยกเลิก</span>
							<?php elseif($rs->Status == 'Y') : ?>
								<span class="green">จัดแล้ว</span>
							<?php elseif($rs->Status == 'P') : ?>
								<span class="blue">กำลังจัด</span>
							<?php elseif($rs->Status == 'C') : ?>
								<span class="green">Closed</span>
							<?php elseif($rs->Status == 'N') : ?>
								<span class="orange">รอดำเนินการ</span>
							<?php endif; ?>
						</td>
						<td class="middle text-right">
							<button type="button" class="btn btn-minier btn-primary" title="View Details" onclick="goDetail('<?php echo $rs->AbsEntry; ?>')">รายละเอียด</button>
							<?php if($rs->Status == 'N') : ?>
							<button type="button" class="btn btn-minier btn-warning" title="Edit" onclick="goEdit('<?php echo $rs->AbsEntry; ?>')"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>

	$(document).ready(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000*60*5); //--- reload every 5 minutes
	});
</script>
<script src="<?php echo base_url(); ?>scripts/pick/pick.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

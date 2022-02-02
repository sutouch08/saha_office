<?php $this->load->view('include/header'); ?>
<style>
	label.search-label {
		font-size:12px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
  	<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-primary" onclick="goPacking()"><i class="fa fa-arrow-right"></i> รอแพ็ค</button>
			<button type="button" class="btn btn-xs btn-info" onclick="goPackingProcess()"><i class="fa fa-arrow-right"></i> กำลังแพ็ค</button>
      <button type="button" class="btn btn-xs btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> สร้างใหม่</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">เลขที่</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">SO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="orderCode" value="<?php echo $orderCode; ?>" />
  </div>

  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Pick List No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="pickCode" value="<?php echo $pickCode; ?>" />
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Transfer No</label>
    <input type="text" class="form-control input-sm text-center search-box" name="transferCode" value="<?php echo $transferCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardName" value="<?php echo $CardName; ?>" />
  </div>


	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">สถานะ</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $Status); ?>>รอแพ็ค</option>
			<option value="P" <?php echo is_selected('P', $Status); ?>>กำลังแพ็ค</option>
			<option value="Y" <?php echo is_selected('Y', $Status); ?>>แพ็คเสร็จแล้ว</option>
      <option value="D" <?php echo is_selected('D', $Status); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที่</label>
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
	$sort_code = get_sort('code', $order_by, $sort_by);
	$sort_orderCode = get_sort('orderCode', $order_by, $sort_by);
  $sort_pickCode = get_sort('pickCode', $order_by, $sort_by);
	$sort_uname = get_sort('uname', $order_by, $sort_by);
	$sort_Status = get_sort('Status', $order_by, $sort_by);
  $sort_date = get_sort('date_add', $order_by, $sort_by);
  $sort_CardName = get_sort('CardName', $order_by, $sort_by);
	$sort_transferCode = get_sort('transferCode', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1 dataTable" style="table-layout: fixed; width:100%; min-width:1110px;">
			<thead>
				<tr>
					<th style="width:40px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_date; ?>" id="sort_date" onclick="sort('date_add')">วันที่</th>
					<th style="width:110px;" class="middle text-center sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่</th>
          <th style="width:100px;" class="middle text-center sorting <?php echo $sort_orderCode; ?>" id="sort_orderCode" onclick="sort('orderCode')">SO No.</th>
          <th style="width:110px;" class="middle text-center sorting <?php echo $sort_pickCode; ?>" id="sort_pickCode" onclick="sort('pickCode')">Pick List No.</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_transferCode; ?>" id="sort_transferCode" onclick="sort('transferCode')">Transfer No.</th>
          <th style="min-width:150px;" class="middle sorting <?php echo $sort_CardName; ?>" id="sort_CardName" onclick="sort('CardName')">ลูกค้า</th>
					<th style="width:150px;" class="middle sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_Status; ?>" id="sort_Status" onclick="sort('Status')">สถานะ</th>
					<th style="width:100px;" class="middle text-center">Temp Status</th>
					<th style="width:150px;"></th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
						<td class="middle text-center"><?php echo $rs->code; ?></td>
            <td class="middle text-center"><?php echo $rs->orderCode; ?></td>
            <td class="middle text-center"><?php echo $rs->pickCode; ?></td>
						<td class="middle text-center"><?php echo $rs->SapNo; ?></td>
            <td class="middle" style="white-space:pre-wrap;"><?php echo $rs->CardName; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle text-center">
              <?php if($rs->Status == 'D') : ?>
                <span class="red">ยกเลิก</span>
              <?php elseif($rs->Status == 'Y') : ?>
                <span class="green">แพ็คแล้ว</span>
              <?php elseif($rs->Status == 'P') : ?>
                <span class="blue">กำลังแพ็ค</span>
              <?php elseif($rs->Status == 'N') : ?>
                <span class="orange">รอแพ็ค</span>
              <?php endif; ?>
						</td>
						<td class="middle text-center">
							<?php if($rs->Status == 'Y') : ?>
								<?php if($rs->tempStatus == 'S') : ?>
									<button type="button" class="btn btn-xs btn-success" onclick="viewTempDetail('<?php echo $rs->code; ?>')">Success</button>
								<?php elseif($rs->tempStatus == 'P') : ?>
									<button type="button" class="btn btn-xs btn-warning" onclick="viewTempDetail('<?php echo $rs->code; ?>')">Pending</button>
								<?php elseif($rs->tempStatus == 'E') : ?>
									<button type="button" class="btn btn-xs btn-danger" onclick="viewTempDetail('<?php echo $rs->code; ?>')">Fail</button>
								<?php else : ?>
									<span class="orange">Not Export</span>
								<?php endif; ?>
							<?php endif; ?>
						</td>
						<td class="middle text-right">
							<button type="button"
							class="btn btn-xs btn-primary"
							title="View Details"
							onclick="goDetail(<?php echo $rs->id; ?>)">
							รายละเอียด
							</button>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="10" class="middle text-center">ไม่พบรายการ</td>
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
<script src="<?php echo base_url(); ?>scripts/pack/pack.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

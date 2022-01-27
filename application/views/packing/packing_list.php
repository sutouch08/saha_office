<?php $this->load->view('include/header'); ?>
<style>
	label.search-label {
		font-size:12px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-9 padding-5">
  	<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-info" onclick="viewProcess()"><i class="fa fa-arrow-right"></i> กำลังแพ็ค</button>
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

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardName" value="<?php echo $CardName; ?>" />
  </div>


	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
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
  $sort_date = get_sort('date_add', $order_by, $sort_by);
  $sort_CardName = get_sort('CardName', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1 dataTable">
			<thead>
				<tr>
					<th style="width:40px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_date; ?>" id="sort_date" onclick="sort('date_add')">วันที่</th>
					<th style="width:110px;" class="hidden-xs middle text-center sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่</th>
          <th style="width:100px;" class="middle text-center sorting <?php echo $sort_orderCode; ?>" id="sort_orderCode" onclick="sort('orderCode')">SO No.</th>
          <th style="width:110px;" class="hidden-xs middle text-center sorting <?php echo $sort_pickCode; ?>" id="sort_pickCode" onclick="sort('pickCode')">Pick List No.</th>
          <th style="min-width:150px;" class="hidden-xs middle sorting <?php echo $sort_CardName; ?>" id="sort_CardName" onclick="sort('CardName')">Customer</th>
					<th style="width:150px;" class="hidden-xs middle sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User</th>
					<th class="visible-xs"></th>
					<th></th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center hidden-xs" id="date-<?php echo $rs->id; ?>"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
						<td class="middle text-center" id="code-<?php echo $rs->id; ?>"><?php echo $rs->code; ?></td>
            <td class="middle text-center" id="so-<?php echo $rs->id; ?>"><?php echo $rs->orderCode; ?></td>
            <td class="middle text-center hidden-xs" id="pick-<?php echo $rs->id; ?>"><?php echo $rs->pickCode; ?></td>
            <td class="middle hidden-xs" id="cust-<?php echo $rs->id; ?>" style="white-space:pre-wrap;"><?php echo $rs->CardName; ?></td>
						<td class="middle hidden-xs" id="uname-<?php echo $rs->id; ?>"><?php echo $rs->uname; ?></td>
						<td class="middle visible-xs" style="padding-left:0px; padding-right:0px;">
							<button type="button" class="btn btn-xs btn-info visible-xs" onclick="preview(<?php echo $rs->id; ?>)">รายละเอียด</button>
						</td>
						<td class="middle text-right">
							<button type="button" class="btn btn-xs btn-primary" onclick="goProcess(<?php echo $rs->id; ?>)">แพ็คสินค้า</button>
						</td>
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

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="padding-bottom:0px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="preview-code" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca"></h4>
      </div>
      <div class="modal-body" style="padding-top:5px;">
        <div class="row">
          <div class="col-sm-12 col-xs-12">
            <table class="table table-striped">
            	<tr>
            		<td class="width-30">วันที่</td>
								<td class="" id="preview-date"></td>
            	</tr>
							<tr>
            		<td>SO No</td>
								<td id="preview-so"></td>
            	</tr>
							<tr>
            		<td>Pick List No</td>
								<td id="preview-pick"></td>
            	</tr>
							<tr>
            		<td>ลูกค้า</td>
								<td id="preview-cust"></td>
            	</tr>
							<tr>
            		<td>User</td>
								<td id="preview-uname"></td>
            	</tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>

	$(document).ready(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000*60*5); //--- reload every 5 minutes
	});
</script>

<script src="<?php echo base_url(); ?>scripts/packing/packing.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> New Transfer</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">Web code</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">SO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="orderCode" value="<?php echo $orderCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">Pack No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="packCode" value="<?php echo $packCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">Pallet No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="palletCode" value="<?php echo $palletCode; ?>" />
  </div>

	<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-1 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $Status); ?>>Not Export</option>
			<option value="P" <?php echo is_selected('P', $Status); ?>>Pending</option>
			<option value="Y" <?php echo is_selected('Y', $Status); ?>>Success</option>
			<option value="F" <?php echo is_selected('F', $Status); ?>>Fail</option>
			<option value="C" <?php echo is_selected('C', $Status); ?>>Cancelled</option>
			<option value="M" <?php echo is_selected('C', $Status); ?>>Force Closed</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="search-label">Date</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
		</div>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="search-label display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
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
	$sort_DocNum = get_sort('DocNum', $order_by, $sort_by);
	$sort_uname = get_sort('uname', $order_by, $sort_by);
	$sort_DocDate = get_sort('DocDate', $order_by, $sort_by);
	$sort_palletCode = get_sort('palletCode', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1 dataTable" >
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_DocDate; ?>" id="sort_DocDate" onclick="sort('DocDate')">Date</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">Web Code</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_DocNum; ?>" id="sort_DocNum" onclick="sort('DocNum')">Transfer No.</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_palletCode; ?>" id="sort_palletCode" onclick="sort('palletCode')">Pallet No.</th>
					<th style="width:150px;" class="middle text-center sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User</th>
					<th style="width:100px;" class="middle text-center">Status</th>
					<th class="middle text-right"></th>
				</tr>
			</thead>
			<tbody>

			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->DocDate, FALSE,'/'); ?></td>
						<td class="middle text-center"><?php echo $rs->code; ?></td>
						<td class="middle text-center"><?php echo $rs->DocNum; ?></td>
						<td class="middle text-center"><?php echo $rs->palletCode; ?></td>
						<td class="middle text-center"><?php echo $rs->uname; ?></td>
						<td class="middle text-center">
								<?php if($rs->Status == 'N') : ?>
									<span class="orange">Not Export</span>
								<?php elseif($rs->Status == 'Y') : ?>
									<button type="button" class="btn btn-minier btn-success btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Success</button>
								<?php elseif($rs->Status == 'P') : ?>
									<button type="button" class="btn btn-minier btn-warning btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Pending</button>
								<?php elseif($rs->Status == 'C') : ?>
									<span class="red">Cancelled</span>
								<?php elseif($rs->Status == 'F') : ?>
									<button type="button" class="btn btn-minier btn-danger btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Failed</button>
								<?php elseif($rs->Status == 'M') : ?>
									<span class="red">Force Closed</span>
								<?php endif; ?>
						</td>
						<td class="middle text-right">
							<button type="button" class="btn btn-minier btn-primary" title="View Details" onclick="goDetail('<?php echo $rs->id; ?>')">รายละเอียด</button>
							<?php if($rs->Status == 'N') : ?>
							<button type="button" class="btn btn-minier btn-danger" title="Cancle" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Transfer Temp Status</h4>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="temp-table">

              </div>
            </div>

        </div>
    </div>
  </div>
</div>

<script id="temp-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="U_WEBORDER" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEBORDER}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
			<tr>
				<td colspan="2">
				{{#if del_btn}}
					<button type="button" class="btn btn-sm btn-danger" onClick="removeTemp()" ><i class="fa fa-trash"></i> Delete Temp</button>
				{{/if}}

				<button type="button" class="btn btn-sm btn-default" onclick="closeModal('tempModal')">Close</button>
				</td>
			</tr>
    </tbody>
  </table>
</script>

<style>
	.table > tr > td {
		white-space: nowrap;
	}
</style>
<script>

	$(document).ready(function() {
		setTimeout(function() {
			window.location.reload();
		}, 1000*60*5); //--- reload every 5 minutes
	});
</script>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> New Activity</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Web Order</label>
    <input type="text" class="form-control input-sm text-center search-box" name="WebCode" value="<?php echo $WebCode; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Activity</label>
    <select class="form-control input-sm" name="Activity" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="C" <?php echo is_selected('C', $Activity); ?>>Phone Call</option>
			<option value="M" <?php echo is_selected('M', $Activity); ?>>Meeting</option>
			<option value="T" <?php echo is_selected('T', $Activity); ?>>Task</option>
			<option value="E" <?php echo is_selected('E', $Activity); ?>>Note</option>
			<option value="P" <?php echo is_selected('P', $Activity); ?>>Campaignx</option>
			<option value="N" <?php echo is_selected('N', $Activity); ?>>Other</option>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Type</label>
    <select class="form-control input-sm" name="Type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_type($Type); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Subject</label>
    <select class="form-control input-sm" name="Subject" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_subject($Subject); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Assigned To</label>
    <input type="text" class="form-control input-sm text-center search-box" name="AssignedTo" value="<?php echo $AssignedTo; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>Customer</label>
		<input type="text" class="form-control input-sm text-center search-box" name="Customer" value="<?php echo $Customer; ?>" />
	</div>

	<div class="col-sm-1 col-1-harf col-xs-4 padding-5">
		<label>Start Date</label>
		<input type="text" class="form-control input-sm text-center" id="StartDate" name="StartDate" value="<?php echo $StartDate; ?>" readonly/>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-4 padding-5">
		<label>End Date</label>
		<input type="text" class="form-control input-sm text-center" name="EndDate" id="EndDate" value="<?php echo $EndDate; ?>" readonly/>
	</div>


	<div class="col-sm-3 col-xs-12 padding-5">
		<label>Financials Project</label>
		<select class="form-control input-sm" name="Project" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_project($Project); ?>
		</select>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $Status); ?>>Not Export</option>
			<option value="1" <?php echo is_selected('1', $Status); ?>>Pending</option>
			<option value="2" <?php echo is_selected('2', $Status); ?>>Success</option>
			<option value="3" <?php echo is_selected('3', $Status); ?>>Error</option>
		</select>
  </div>

  <div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>



<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover dataTable" style="width:100%; min-width:1500px;">
			<thead>
				<tr style="font-size:10px;">
					<th style="width:20px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle">Web Order</th>
					<th style="width:70px;" class="middle">Activity</th>
					<th style="width:100px;" class="middle">Type</th>
					<th style="width:100px;" class="middle">Subject</th>
					<th style="width:150px;" class="middle">Assigned To</th>
					<th style="width:100px;" class="middle">Cust. Code</th>
					<th style="width:150px;" class="middle">Cust. Name</th>
					<th style="width:80px;" class="middle">Start Date</th>
					<th style="width:80px;" class="middle">End Date</th>
					<th style="width:100px;" class="middle">Financials Project</th>
					<th style="width:100px;" class="middle">User</th>
					<th style="width:50px;" class="middle text-center">Preview</th>
					<th style="width:50px;" class="middle text-center">Status</th>
					<th style="width:80px;" class="middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:10px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo action_name($rs->Action); ?></td>
						<td class="middle"><?php echo $rs->TypeName; ?></td>
						<td class="middle"><?php echo $rs->SubjectName; ?></td>
						<td class="middle"><?php echo ($rs->attendType == 'U' ? $rs->UserName : $rs->EmpName); ?></td>
						<td class="middle"><?php echo $rs->CardCode; ?></td>
						<td class="middle"><?php echo $rs->CardName; ?></td>
						<td class="middle"><?php echo thai_date($rs->Recontact, FALSE,'.'); ?></td>
						<td class="middle"><?php echo thai_date($rs->endDate, FALSE,'.'); ?></td>
						<td class="middle"><?php echo $rs->FIPROJECT; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle">
							<button type="button" class="btn btn-minier btn-primary btn-block" onclick="getPreview('<?php echo $rs->code; ?>')">Preview</button>
						</td>
						<td class="middle">
							<?php if($rs->Status == 0) : ?>
								<span class="label label-danger">Not Export</span>
							<?php elseif($rs->Status == 1) : ?>
								<button type="button" class="btn btn-minier btn-warning btn-block" onclick="viewTemp('<?php echo $rs->code; ?>')">Pending</button>
							<?php elseif($rs->Status == 2) : ?>
								<button type="button" class="btn btn-minier btn-success btn-block" onclick="viewTemp('<?php echo $rs->code; ?>')">Success</button>
							<?php elseif($rs->Status == 3) : ?>
								<button type="button" class="btn btn-minier btn-danger btn-block" onclick="viewTemp('<?php echo $rs->code; ?>')">Failed</button>
							<?php endif; ?>
						</td>
						<td class="middle">
							<?php if($rs->Status != 2) : ?>
							<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
							<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="15" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>



<?php $this->load->view('activity/activity_preview'); ?>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Sales Quotation Temp Status</h4>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="temp-table">

              </div>
            </div>
            <div class="modal-footer" style="background-color:white;">
                <button type="button" class="btn btn-sm btn-danger" onClick="removeTemp()" ><i class="fa fa-trash"></i> Delete Temp</button>
                <button type="button" class="btn btn-sm btn-default" onclick="closeModal('tempModal')">Close</button>
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
      <tr><td class="width-30">BP Code</td><td class="width-70">{{CardCode}}</td></tr>
      <tr><td>BP Name</td><td>{{CardName}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
    </tbody>
  </table>
</script>

<style>
	.table > tr > td {
		white-space: nowrap;
	}
</style>

<script src="<?php echo base_url(); ?>scripts/activity/activity.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->isAdmin OR $this->isLead OR !empty($this->user->sale_id)) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add Bussiness Partner</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>Web Order</label>
		<input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
	</div>
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Lead Code</label>
    <input type="text" class="form-control input-sm text-center search-box" name="LeadCode" value="<?php echo $LeadCode; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Customer Name</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardName" value="<?php echo $CardName; ?>" />
  </div>


	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $Status); ?>>Not In Temp</option>
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

<?php $sort_code = get_sort('code', $order_by, $sort_by); ?>
<?php $sort_LeadCode = get_sort('LeadCode', $order_by, $sort_by); ?>
<?php $sort_CardCode = get_sort('CardCode', $order_by, $sort_by); ?>
<?php $sort_CardName = get_sort('CardName', $order_by, $sort_by); ?>
<?php $sort_GroupName = get_sort('GroupName', $order_by, $sort_by); ?>
<?php $sort_SlpName = get_sort('SlpName', $order_by, $sort_by); ?>
<?php $sort_uname = get_sort('uname', $order_by, $sort_by); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped dataTable" style="width:100%; min-width:1200px;">
			<thead>
				<tr style="font-size:10px;">
					<th style="width:20px;" class="width-5 middle text-center">#</th>
					<th style="width:100px;" class="width-10 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">Web Order</th>
					<th style="width:100px;" class="width-8 middle sorting <?php echo $sort_LeadCode; ?>" id="sort_LeadCode" onclick="sort('LeadCode')">Lead Code</th>
					<th style="width:100px;" class="width-8 middle sorting <?php echo $sort_CardCode; ?>" id="sort_CardCode" onclick="sort('CardCode')">Customer Code</th>
					<th style="width:150px;" class="width-15 middle sorting <?php echo $sort_CardName; ?>" id="sort_CardName" onclick="sort('CardName')">Customer Name</th>
					<th style="width:150px;" class="width-10 middle sorting <?php echo $sort_GroupName; ?>" id="sort_GroupName" onclick="sort('GroupName')">Group</th>
					<th style="width:150px;" class="width-15 middle sorting <?php echo $sort_SlpName; ?>" id="sort_SlpName" onclick="sort('SlpName')">Sale Employee</th>
					<th style="width:100px;" class="width-10 middle sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User Name</th>
					<th style="width:50px;" class="width-8 middle text-center">Preview</th>
					<th style="width:50px;" class="width-8 middle text-center">Status</th>
					<th style="width:100px;" ></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr style="font-size:10px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->LeadCode; ?></td>
						<td class="middle"><?php echo $rs->CardCode; ?></td>
						<td class="middle"><?php echo $rs->CardName; ?></td>
						<td class="middle"><?php echo $rs->GroupName; ?></td>
						<td class="middle"><?php echo $rs->SlpName; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle text-center">
							<button type="button" class="btn btn-mini btn-primary btn-block" onclick="getPreview('<?php echo $rs->LeadCode; ?>')">Preview</button>
						</td>
						<td class="middle text-center">
						<?php if($rs->Status == 1) : ?>
							<button type="button" class="btn btn-mini btn-warning btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Pending</button>
						<?php endif; ?>
						<?php if($rs->Status == 2) : ?>
							<button type="button" class="btn btn-mini btn-success btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Success</button>
						<?php endif; ?>
						<?php if($rs->Status == 3) : ?>
							<button type="button" class="btn btn-mini btn-danger btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Failed</button>
						<?php endif; ?>
						<?php if($rs->Status == 0) : ?>
							Not In Temp
						<?php endif; ?>
						</td>
						<td class="middle text-center">
							<?php if($rs->Status != 2) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->LeadCode; ?>', '<?php echo $rs->CardName; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
							</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('customer/customer_preview'); ?>

<style>
	.table > tr > td {
		white-space: nowrap;
	}
</style>

<script src="<?php echo base_url(); ?>scripts/customer/customer.js"></script>

<?php $this->load->view('include/footer'); ?>

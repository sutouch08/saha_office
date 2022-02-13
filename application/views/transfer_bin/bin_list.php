<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add New</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Bin Code</label>
    <input type="text" class="form-control input-sm search-box" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Bin Name</label>
    <input type="text" class="form-control input-sm search-box" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm search-box" name="uname" id="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="search-label">Date</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
		</div>
	</div>

  <div class="col-sm-1 col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-4 padding-5">
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
<?php $sort_name = get_sort('name', $order_by, $sort_by); ?>
<?php $sort_uname = get_sort('uname', $order_by, $sort_by); ?>
<?php $sort_date = get_sort('createDate', $order_by, $sort_by); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1 dataTable" style="min-width:600px;">
			<thead>
				<tr>
					<th style="width:50px;" class="middle text-center">#</th>
					<th style="width:100px;" class="middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">Bin Code</th>
					<th style="min-width:150px;" class="middle sorting <?php echo $sort_name; ?>" id="sort_name" onclick="sort('name')">Bin Name</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">Create by</th>
					<th style="width:100px;" class="middle text-center sorting <?php echo $sort_date; ?>" id="sort_date" onclick="sort('createDate')">Create at</th>
					<th style="width:100px;" class="middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo $rs->uname; ?></td>
						<td class="middle"><?php echo thai_date($rs->createDate); ?></td>
						<td class="text-right">
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo $rs->name; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/transfer_bin/transfer_bin.js"></script>

<?php $this->load->view('include/footer'); ?>

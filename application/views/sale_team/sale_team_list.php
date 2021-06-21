<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add Sales Team</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm search-box" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" id="name" value="<?php echo $name; ?>" />
  </div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm" id="status" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected("1", $status); ?>>Active</option>
			<option value="0" <?php echo is_selected("0", $status); ?>>Disactive</option>
		</select>
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
<?php $sort_status = get_sort('status', $order_by, $sort_by); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-bordered table-striped table-hover border-1 dataTable">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-20 middle sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">Sales Team Code</th>
					<th class="middle sorting <?php echo $sort_name; ?>" id="sort_name" onclick="sort('name')">Sales Team Name</th>
					<th class="width-10 middle text-center sorting <?php echo $sort_status; ?>" id="sort_status" onclick="sort('status')">Status</th>
					<th class="width-15 middle text-right">Option</th>
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
						<td class="middle text-center"><?php echo is_active($rs->status); ?></td>
						<td class="text-right">
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/sale_team/sale_team.js"></script>

<?php $this->load->view('include/footer'); ?>

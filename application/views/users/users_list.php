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
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add User</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>User name</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Employee</label>
    <input type="text" class="form-control input-sm text-center search-box" name="emp_name" value="<?php echo $emp_name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>Sale Person</label>
		<select class="form-control input-sm" name="sale_id" onchange="getSearch()">
    <option value="all">ทั้งหมด</option>
			<?php echo select_saleman($sale_id); ?>
		</select>
  </div>


	<div class="col-sm-2 col-xs-6 padding-5">
    <label>Sales Team</label>
    <select class="form-control input-sm" name="sale_team" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_sales_team($sale_team); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>User Group</label>
    <select class="form-control input-sm" name="user_group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="admin" <?php echo is_selected('admin', $user_group); ?>>Administrator</option>
			<option value="lead" <?php echo is_selected('lead', $user_group); ?>>หัวหน้าทีม</option>
			<option value="sale" <?php echo is_selected('sale', $user_group); ?>>พนักงานขาย</option>
		</select>
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>Disactive</option>
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

<?php $sort_uname = get_sort('uname', $order_by, $sort_by); ?>
<?php $sort_emp_name = get_sort('emp_name', $order_by, $sort_by); ?>
<?php $sort_sale_team = get_sort('sale_team', $order_by, $sort_by); ?>
<?php $sort_ugroup = get_sort('ugroup', $order_by, $sort_by); ?>
<?php $sort_status = get_sort('status', $order_by, $sort_by); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover table-bordered dataTable">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-15 middle sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User Name</th>
					<th class="width-15 middle sorting <?php echo $sort_emp_name; ?>" id="sort_emp_name" onclick="sort('emp_name')">Employee</th>
					<th class="width-15 middle">Sale Person</th>
					<th class="width-20 middle sorting <?php echo $sort_sale_team; ?>" id="sort_sale_team" onclick="sort('sale_team')">Sales Team</th>
					<th class="width-10 middle sorting <?php echo $sort_ugroup; ?>" id="sort_ugroup" onclick="sort('ugroup')">User Group</th>
					<th class="width-10 middle text-center sorting <?php echo $sort_status; ?>" id="sort_status" onclick="sort('status')">Status</th>
					<th class="width-10 middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle"><?php echo $rs->emp_name; ?></td>
						<td class="middle"><?php echo get_sale_name($rs->sale_id); ?></td>
						<td class="middle"><?php echo $rs->sale_team_name; ?></td>
						<td class="middle"><?php echo $rs->group_name; ?></td>
						<td class="middle text-center">
							<?php echo is_active($rs->status); ?>
						</td>
						<td class="text-right">
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-info" title="Reset password" onclick="goReset(<?php echo $rs->id; ?>)">
									<i class="fa fa-key"></i>
								</button>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->uname; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/users/users.js"></script>

<?php $this->load->view('include/footer'); ?>

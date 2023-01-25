<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add New</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ทะเบียน</label>
    <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
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
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover table-bordered dataTable">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-center">#</th>
					<th class="min-width-200 middle">ทะเบียน</th>
					<th class="fix-width-80 middle text-center">สถานะ</th>
					<th class="fix-width-120 middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center">
							<?php echo is_active($rs->active); ?>
						</td>
						<td class="text-right">
							<?php if($this->isLead OR $this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/vehicle/vehicle.js"></script>

<?php $this->load->view('include/footer'); ?>

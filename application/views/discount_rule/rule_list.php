<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->isAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> New Rule</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 col-xs-6 padding-5">
    <label>Rule Name</label>
    <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>Sales Team</label>
    <select class="form-control input-sm" name="sale_team" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_sales_team($sale_team); ?>
		</select>
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Min Disc.</label>
    <input type="number" class="form-control input-sm text-center search-box" name="min_disc" value="<?php echo $min_disc; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>Max Disc.</label>
    <input type="number" class="form-control input-sm text-center search-box" name="max_disc" value="<?php echo $max_disc; ?>" />
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Disactive</option>
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

<?php $sort_name = get_sort('name', $order_by, $sort_by); ?>
<?php $sort_sale_team = get_sort('sale_team', $order_by, $sort_by); ?>
<?php $sort_min_disc = get_sort('min_disc', $order_by, $sort_by); ?>
<?php $sort_max_disc = get_sort('max_disc', $order_by, $sort_by); ?>
<?php $sort_active = get_sort('active', $order_by, $sort_by); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover table-bordered dataTable">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-35 middle sorting <?php echo $sort_name; ?>" id="sort_name" onclick="sort('name')">Rule Name</th>
					<th class="width-20 middle sorting <?php echo $sort_sale_team; ?>" id="sort_sale_team" onclick="sort('sale_team')">Sales Team</th>
					<th class="width-10 middle sorting <?php echo $sort_min_disc; ?>" id="sort_min_disc" onclick="sort('min_disc')">Min Dis.</th>
					<th class="width-10 middle text-center sorting <?php echo $sort_active; ?>" id="sort_active" onclick="sort('active')">Status</th>
					<th class="middle text-right"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->sale_team_name; ?></td>
						<td class="middle"><?php echo $rs->min_disc . ' %'; ?></td>
						<td class="middle text-center">
							<?php echo is_active($rs->active); ?>
						</td>
						<td class="text-right">
							<?php if($this->isAdmin) : ?>
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

<script src="<?php echo base_url(); ?>scripts/discount_rule/discount_rule.js"></script>

<?php $this->load->view('include/footer'); ?>

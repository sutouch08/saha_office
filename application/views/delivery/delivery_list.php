<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> Add New</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>ทะเบียนรถ</label>
    <select class="form-control input-sm" name="vehicle" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_vehicle( $vehicle, FALSE); ?>
		</select>
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>พนังงานขับรถ</label>
    <select class="form-control input-sm" name="driver" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_driver(array('D'), $driver, FALSE); ?>
		</select>
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>เส้นทาง</label>
    <select class="form-control input-sm" name="route" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_route($route, FALSE); ?>
		</select>
  </div>

	<div class="col-sm-1 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Close</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
			<option value="F" <?php echo is_selected('F', $status); ?>>Draft</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Cancelled</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที่</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
		</div>
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
		<table class="table border-1">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่</th>
					<th class="fix-width-100 middle text-center">ทะเบียนรถ</th>
					<th class="fix-width-150 middle">พนักงานขับรถ</th>
					<th class="min-width-150 middle">เด็กติดรถ</th>
					<th class="fix-width-150 middle">เส้นทาง</th>
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
						<td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle text-center"><?php echo $rs->vehicle_name; ?></td>
						<td class="middle"><?php echo $rs->driver_name; ?></td>
						<td class="middle"><?php echo get_delivery_employee_name('E', $rs->code); ?></td>
						<td class="middle"><?php echo $rs->route_name; ?></td>
						<td class="middle text-center">
							<?php if($rs->status == 'F') : ?>
								Draft
							<?php elseif($rs->status == 'O') : ?>
								Open
							<?php elseif($rs->status == 'C') : ?>
								Closed
							<?php elseif($rs->status == 'D') : ?>
									Cancelled
							<?php endif; ?>
						</td>
						<td class="text-right">
								<button type="button" class="btn btn-mini btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
									<i class="fa fa-eye"></i>
								</button>
								<?php if($rs->status == 'F' OR $rs->status == 'O') : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
								<?php endif; ?>
								<?php if($rs->status != 'D') : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')">
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

<script src="<?php echo base_url(); ?>scripts/delivery/delivery.js"></script>

<?php $this->load->view('include/footer'); ?>

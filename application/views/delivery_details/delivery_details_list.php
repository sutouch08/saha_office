<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center search-box" name="delivery_code" value="<?php echo $delivery_code; ?>" />
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>ทะเบียนรถ</label>
    <select class="form-control input-sm" name="vehicle_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_vehicle( $vehicle_id, FALSE); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>พนังงานขับรถ</label>
    <select class="form-control input-sm" name="driver_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_driver(array('D'), $driver_id, FALSE); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>เส้นทาง</label>
    <select class="form-control input-sm" name="route_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_route($route_id, FALSE); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardCode" value="<?php echo $CardCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardName" value="<?php echo $CardName; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Contact</label>
    <input type="text" class="form-control input-sm text-center search-box" name="contact" value="<?php echo $contact; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center search-box" name="uname" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ประเภทการส่ง</label>
    <select class="form-control input-sm" name="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="P" <?php echo is_selected('P', $type); ?>>ส่งสินค้า</option>
			<option value="D" <?php echo is_selected('D', $type); ?>>ส่งเอกสาร</option>
			<option value="O" <?php echo is_selected('O', $type); ?>>อื่นๆ</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ประเภทเอกสาร</label>
    <select class="form-control input-sm" name="DocType" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="DO" <?php echo is_selected('DO', $DocType); ?>>DO</option>
			<option value="IV" <?php echo is_selected('IV', $DocType); ?>>IV</option>
			<option value="CN" <?php echo is_selected('CN', $DocType); ?>>CN</option>
			<option value="PB" <?php echo is_selected('PB', $DocType); ?>>PB</option>
			<option value="NULL" <?php echo is_selected('NULL', $DocType); ?>>ไม่ระบุ</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center search-box" name="DocNum" value="<?php echo $DocNum; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะเอกสาร</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $line_status); ?>>Open</option>
			<option value="R" <?php echo is_selected('R', $line_status); ?>>Released</option>
			<option value="C" <?php echo is_selected('C', $line_status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $line_status); ?>>Cancelled</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>สถานะการจัดส่ง</label>
		<select class="form-control input-sm line-status" data-id="<?php echo $rs->id; ?>" id="lineStatus-<?php echo $no; ?>" <?php echo $disc; ?>>
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected($result_status, '1'); ?>>Loaded</option>
			<option value="4" <?php echo is_selected($result_status, '4'); ?>>สำเร็จ</option>
			<option value="2" <?php echo is_selected($result_status, '2'); ?>>ส่งบางส่วน</option>
			<option value="3" <?php echo is_selected($result_status, '3'); ?>>ไม่ได้ส่ง</option>
			<option value="5" <?php echo is_selected($result_status, '5'); ?>>ลูกค้าไม่รับของ</option>
			<option value="6" <?php echo is_selected($result_status, '6'); ?>>สินค้าผิด</option>
			<option value="7" <?php echo is_selected($result_status, '7'); ?>>เอกสารผิด</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที่เอกสาร</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="from_date" value="<?php echo $from_date; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="from_date" value="<?php echo $from_date; ?>" placeholder="To" readonly />
		</div>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที Release</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="release_from" name="release_from" value="<?php echo $release_from; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="release_to" name="release_to" value="<?php echo $release_to; ?>" placeholder="To" readonly />
		</div>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label class="search-label">วันที Close</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="finish_from" name="finish_from" value="<?php echo $finish_from; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="finish_to" name="finish_to" value="<?php echo $finish_to; ?>" placeholder="To" readonly />
		</div>
	</div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table border-1" style="min-width:2500px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่</th>
					<th class="fix-width-80 middle">ลูกค้า</th>
					<th class="fix-width-300 middle">ชื่อลูกค้า</th>
					<th class="fix-width-80 middle">การส่ง</th>
					<th class="fix-width-60 middle">เอกสาร</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-80 middle text-center">สถานะเอกสาร</th>
					<th class="fix-width-100 middle text-center">สถานะการส่ง</th>
					<th class="fix-width-100 middle text-center">ทะเบียนรถ</th>
					<th class="fix-width-150 middle">พนักงานขับรถ</th>
					<!--<th class="fix-width-150 middle">เด็กติดรถ</th>-->
					<th class="fix-width-150 middle">เส้นทาง</th>
					<th class="fix-width-100 middle text-center">User</th>
					<th class="fix-width-100 middle text-center">วันที่ Release</th>
					<th class="fix-width-100 middle text-center">วันที่ Closed</th>
					<th class="fix-width-100 middle text-center">Contact</th>
					<th class="min-width-100 middle">ShipTo</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '.'); ?></td>
						<td class="middle"><?php echo $rs->delivery_code; ?></td>
						<td class="middle"><?php echo $rs->CardCode; ?></td>
						<td class="middle"><?php echo $rs->CardName; ?></td>
						<td class="middle"><?php echo ($rs->type == 'P' ? 'ส่งสินค้า' : ($rs->type == 'D' ? 'ส่งเอกสาร' : 'อื่นๆ')); ?></td>
						<td class="middle text-right"><?php echo $rs->DocType; ?></td>
						<td class="middle"><?php echo $rs->DocNum; ?></td>
						<td class="middle text-center">
							<?php if($rs->line_status == 'R') : ?>
								<span class="blue">Released</span>
							<?php elseif($rs->line_status == 'O') : ?>
								<span class="orange">Open</span>
							<?php elseif($rs->line_status == 'C') : ?>
								<span class="green">Closed</span>
							<?php elseif($rs->line_status == 'D') : ?>
									<span class="red">Cancelled</span>
							<?php endif; ?>
						</td>
						<td class="middle text-center">
						<?php
							switch($rs->result_status)
							{
								case '1' : echo "Loaded"; break;
								case '4' : echo "<span class='green'>สำเร็จ</span>"; break;
								case '2' : echo "<span class='blue'>ส่งบางส่วน</span>"; break;
								case '3' : echo "<span class='red'>ไม่ได้ส่ง</span>"; break;
								case '5' : echo "<span class='orange'>ลูกค้าไม่รับของ</span>"; break;
								case '6' : echo "<span class='red'>สินค้าผิด</span>"; break;
								case '7' : echo "<span class='red'>เอกสารผิด</span>"; break;
								default : echo "Loaded"; break;
							}
							?>
						</td>
						<td class="middle text-center"><?php echo $rs->vehicle_name; ?></td>
						<td class="middle"><?php echo $rs->driver_name; ?></td>
						<!--<td class="middle"><?php //echo get_delivery_employee_name('E', $rs->delivery_code); ?></td>-->
						<td class="middle"><?php echo $rs->route_name; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->release_date,FALSE, '.'); ?></td>
						<td class="middle text-center"><?php echo thai_date($rs->finish_date, FALSE, '.'); ?></td>
						<td class="middle"><?php echo $rs->contact; ?></td>
						<td class="middle"><?php echo $rs->Address; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/delivery_details/delivery_details_list.js"></script>

<?php $this->load->view('include/footer'); ?>

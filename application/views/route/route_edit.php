<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-30"/>

<form class="form-horizontal">
  <div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เส้นทาง</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm" id="name" maxlength="100" value="<?php echo $name; ?>" autofocus />
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ความยาก</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm input-mini" id="level">
				<option value="1" <?php echo is_selected('1', $level); ?>> 1 </option>
				<option value="2" <?php echo is_selected('2', $level); ?>> 2 </option>
				<option value="3" <?php echo is_selected('3', $level); ?>> 3 </option>
				<option value="4" <?php echo is_selected('4', $level); ?>> 4 </option>
				<option value="5" <?php echo is_selected('5', $level); ?>> 5 </option>
				<option value="6" <?php echo is_selected('6', $level); ?>> 6 </option>
				<option value="7" <?php echo is_selected('7', $level); ?>> 7 </option>
				<option value="8" <?php echo is_selected('8', $level); ?>> 8 </option>
				<option value="9" <?php echo is_selected('9', $level); ?>> 9 </option>
			</select>
			<span class="help-block">ระดับความยากของเส้นทาง 1-9 ตัวเลขมากหมายถึงยากมาก</span>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
			<label>
				<input type="checkbox" class="ace" name="active" id="active" <?php echo is_checked('1', $active); ?> />
				<span class="lbl">&nbsp; &nbsp;Active</span>
			</label>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
  </div>

	<div class="divider"></div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">สายการส่ง</label>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-8">
			<input type="text" class="form-control input-sm" id="zone" />
    </div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4">
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addZone()">Add</button>
		</div>
  </div>

	<div class="form-group">
    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">&nbsp;</label>
    <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12 table-responsive" style="max-height:400px; overflow:auto;">
			<table class="table table-striped border-1">
				<thead>
					<tr>
						<th class="fix-width-200">อำเภอ</th>
						<th class="fix-width-200">จังหวัด</th>
						<th class="fix-width-100">รหัสไปรษณีย์</th>
						<th class="fix-width-40"></th>
					</tr>
				</thead>
				<tbody id="zone-list">
					<?php $no = 0; ?>
					<?php if( ! empty($details)) : ?>
						<?php $no++; ?>
						<?php foreach($details as $rs) : ?>
							<?php $uid = md5($rs->district.$rs->province.$rs->zipCode); ?>
						<tr id="row-<?php echo $no; ?>">
							<td class=""><?php echo $rs->district; ?></td>
							<td class=""><?php echo $rs->province; ?></td>
							<td class=""><?php echo $rs->zipCode; ?></td>
							<td class="">
								<a class="pointer bold pull-right red" onclick="removeZone(<?php echo $no; ?>)" style="margin-left:15px;">
									<i class="fa fa-times"></i>
								</a>
								<input type="hidden" id="zone-<?php echo $no; ?>"
									class="zone-data"
									data-id="<?php echo $rs->id; ?>"
									data-district="<?php echo $rs->district; ?>"
									data-province="<?php echo $rs->province; ?>"
									data-zipcode="<?php echo $rs->zipCode; ?>" />
								<input type="hidden" id="<?php echo $uid; ?>" value="<?php echo $uid; ?>" />
							</td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
    </div>
  </div>
	<input type="hidden" id="row-no" value="<?php echo $no; ?>" />

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-4">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="updateZone()"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>

	<input type="hidden" id="id" value="<?php echo $id; ?>" />
</form>

<script id="zone-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{no}}">
		<td class="">{{district}}</td>
		<td class="">{{province}}</td>
		<td class="">{{zipCode}}</td>
		<td class="">
			<a class="pointer bold pull-right red" onclick="removeZone({{no}})" style="margin-left:15px;">
				<i class="fa fa-times"></i>
			</a>
			<input type="hidden" id="zone-{{no}}"
				class="zone-data"
				data-id="{{id}}"
				data-district="{{district}}"
				data-province="{{province}}"
				data-zipcode="{{zipCode}}" />
			<input type="hidden" id="{{uid}}" value="{{uid}}" />
		</td>
	</tr>
</script>
<script src="<?php echo base_url(); ?>scripts/route/route.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/md5.min.js"></script>
<?php $this->load->view('include/footer'); ?>

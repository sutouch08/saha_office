<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-8 col-xs-12 padding-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-4 col-xs-12 padding-5">
			<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Export</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print padding-5"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block">Items</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
  </div>
  <div class="col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">From</label>
    <input type="text" class="form-control input-sm text-center" id="pdFrom" name="pdFrom" placeholder="From Item Code" disabled>
  </div>
  <div class="col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">To</label>
    <input type="text" class="form-control input-sm text-center" id="pdTo" name="pdTo" placeholder="To Item Code" disabled>
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
		<label>Item Group</label>
		<div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-grp-all" onclick="toggleAllGroup(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-grp-range" onclick="toggleAllGroup(0)">เลือก</button>
    </div>
	</div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block">Warehouse</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>

	<div class="col-sm-3 col-xs-12">
		<label class="display-block not-show">hide item</label>
		<label>
			<input type="checkbox" class="ace" name="hideItem" id="hideItem" value="Y"/>
			<span class="lbl">&nbsp; Hide Items if not in Stock</span>
		</label>
	</div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
	<input type="hidden" id="allGroup" name="allGroup" value="1">
	<input type="hidden" id="token" name="token">
</div>


<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>
									<label>
										<input type="checkbox" class="ace" id="check-all" onchange="checkAll()" />
										<span class="lbl">&nbsp; เลือกทั้งหมด</span>
									</label>
								</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
        <?php if(!empty($whList)) : ?>
          <?php foreach($whList as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="warehouse[<?php echo $rs->code; ?>]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <?php echo $rs->code; ?> | <?php echo $rs->name; ?>
              </label>
            </div>
          <?php endforeach; ?>
        <?php endif;?>

        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="group-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' >
									<label>
										<input type="checkbox" class="ace" id="check-group-all" onchange="checkGroupAll()" />
										<span class="lbl">&nbsp; เลือกทั้งหมด</span>
									</label>
								</h4>
            </div>
            <div class='modal-body' style="padding:0px;">
        <?php if(!empty($groupList)) : ?>
          <?php foreach($groupList as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="group-chk" id="group-<?php echo $rs->code; ?>" name="itemGroup[<?php echo $rs->code; ?>]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <?php echo $rs->code; ?> | <?php echo $rs->name; ?>
              </label>
            </div>
          <?php endforeach; ?>
        <?php endif;?>

        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
<hr>
</form>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive" id="rs" style="max-height:500px;">

    </div>
</div>

<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered" style="width:100%; min-width:1200px; table-layout:fixed;">
    <tr class="font-size-12">
      <th class="width-5 middle text-center">#</th>
			<th class="width-20 middle" style="width:200px;">Item Number</th>
      <th class="middle" style="width:200px;">Item Description</th>
      <th class="width-10 middle" style="width:50px;">UoM</th>
      <th class="width-15 middle" style="width:100px;">Qty</th>
    </tr>
{{#each data}}
	{{#if @last}}
		<tr class="font-size-12">
			<td colspan="4" class="middle text-right">Total</td>
			<td class="text-right">{{totalQty}}</td>
		</tr>
	{{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{ pdCode }}</td>
			<td class="middle">{{ pdName }}</td>
      <td class="middle">{{ uom }}</td>
      <td class="middle text-right" >{{ qty }}</td>
		</tr>
	{{/if}}
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/stock_balance_by_warehouse_table.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

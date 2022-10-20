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
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-12 padding-5">
    <label class="display-block">Users</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-user-all" onclick="toggleAllUser(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-user-range" onclick="toggleAllUser(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-12 padding-5">
		<label>Date</label>
		<div class="btn-group width-100">
      <button type="button" class="btn btn-sm width-33 btn-primary" id="btn-finish-date" onclick="toggleSelectDate('Finish')">Finish Date</button>
      <button type="button" class="btn btn-sm width-33" id="btn-doc-date" onclick="toggleSelectDate('DocDate')">PA Date</button>
      <button type="button" class="btn btn-sm width-33" id="btn-so-date" onclick="toggleSelectDate('SO')">SO Date</button>
    </div>
	</div>

  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
		<label class="search-label">Date</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="" placeholder="Start" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="" placeholder="End" readonly />
		</div>
	</div>

  <input type="hidden" id="allUser" name="allUser" value="1">
  <input type="hidden" id="selectDate" name="selectDate" value="Finish">
	<input type="hidden" id="token" name="token">
</div>


<div class="modal fade" id="user-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
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
        <?php if(!empty($users)) : ?>
          <?php foreach($users as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" id="user-<?php echo $rs->id; ?>" name="users[]" value="<?php echo $rs->id; ?>" style="margin-right:10px;" />
                <?php echo $rs->uname; ?> | <?php echo $rs->emp_name; ?>
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
<hr class="padding-5">
</form>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive" id="rs" style="max-height:500px;">

    </div>
</div>

<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered" style="width:100%; min-width:1540px;">
    <tr class="font-size-12">
      <th class="fix-width-40 text-center">#</th>
      <th class="fix-width-100 text-center">Pack NO.</th>
      <th class="fix-width-100 text-center">SO No.</th>
			<th class="fix-width-100 text-center">Pick No.</th>
      <th class="fix-width-100">Item Code</th>
      <th class="fix-width-300">Item Name</th>
      <th class="fix-width-100 text-center">Picked Qty</th>
      <th class="fix-width-100 text-center">Packed Qty</th>
			<th class="fix-width-100 text-center">Uom</th>
      <th class="fix-width-100 text-center">Posting Date</th>
      <th class="fix-width-100 text-center">Pack Date</th>
      <th class="fix-width-100 text-center">Start Pack</th>
      <th class="fix-width-100 text-center">Finish Pack</th>
      <th class="fix-width-100 text-center">User</th>
    </tr>
{{#each data}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{DocNum}}</td>
      <td class="middle text-center">{{OrderCode}}</td>
			<td class="middle text-center">{{PickCode}}</td>
      <td class="middle">{{ItemCode}}</td>
      <td class="middle">{{ItemName}}</td>
      <td class="middle text-center">{{pickQty}}</td>
      <td class="middle text-center">{{packQty}}</td>
			<td class="middle text-center">{{unitMsr}}</td>
      <td class="middle text-center">{{OrderDate}}</td>
      <td class="middle text-center">{{DocDate}}</td>
      <td class="middle text-center">{{StartPack}}</td>
      <td class="middle text-center">{{FinishPack}}</td>
      <td class="middle text-center">{{uname}}</td>
    </tr>
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/packed_details.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

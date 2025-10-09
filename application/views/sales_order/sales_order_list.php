<?php $this->load->view('include/header'); ?>
<?php $this->load->view('sales_order/order_list_style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-primary top-btn btn-100" onclick="getUploadFile()"><i class="fa fa-upload"></i> &nbsp; Import Order</button>
			<button type="button" class="btn btn-white btn-purple top-btn btn-100" onclick="getTemplate()"><i class="fa fa-download"></i> &nbsp; Template</button>
			<button type="button" class="btn btn-white btn-success top-btn btn-100" onclick="goAdd()"><i class="fa fa-plus"></i> Add Sales Order</button>
			<button type="button" class="btn btn-white btn-info top-btn" onclick="printMultipleSO()"><i class="fa fa-print"></i> Print</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Web Order</label>
    <input type="text" class="form-control input-sm text-center search-box" name="WebCode" value="<?php echo $WebCode; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Ref No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">SO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="DocNum" value="<?php echo $DocNum; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">SQ No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="SqNo" value="<?php echo $SqNo; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">DO No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="DeliveryNo" value="<?php echo $DeliveryNo; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">INV No.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="InvoiceNo" value="<?php echo $InvoiceNo; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Customer</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CardCode" value="<?php echo $CardCode; ?>" placeholder="Code OR Name" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Cust. Ref</label>
    <input type="text" class="form-control input-sm text-center search-box" name="CustRef" value="<?php echo $CustRef; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="search-label">Approved</label>
    <select class="form-control input-sm" name="Approved" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="P" <?php echo is_selected('P', $Approved); ?>>รออนุมัติ</option>
			<option value="A" <?php echo is_selected('A', $Approved); ?>>อนุมมัติ</option>
			<option value="R" <?php echo is_selected('R', $Approved); ?>>ไม่อนุมัติ</option>
			<option value="S" <?php echo is_selected('S', $Approved); ?>>ไม่ต้องอนุมัติ</option>
		</select>
  </div>

	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="search-label">Sales Emp.</label>
		<select class="width-100 filter" name="SlpCode" id="slp-code">
			<option value="all">ทั้งหมด</option>
			<?php echo select_saleman($SlpCode); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label class="search-label">SAP Status</label>
    <select class="form-control input-sm" name="SapStatus" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $SapStatus); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $SapStatus); ?>>Closed</option>
			<option value="E" <?php echo is_selected('E', $SapStatus); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label class="search-label">Temp Status</label>
    <select class="form-control input-sm" name="Status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $Status); ?>>Not Export</option>
			<option value="1" <?php echo is_selected('1', $Status); ?>>Pending</option>
			<option value="2" <?php echo is_selected('2', $Status); ?>>Success</option>
			<option value="3" <?php echo is_selected('3', $Status); ?>>Failed</option>
			<option value="9" <?php echo is_selected('9', $Status); ?>>Draft</option>
			<option value="-1" <?php echo is_selected('-1', $Status); ?>>Canceled</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="search-label">Posting Date</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 from-date text-center" id="fromDate" name="fromDate" value="<?php echo $fromDate; ?>" placeholder="From" readonly/>
			<input type="text" class="form-control input-sm width-50 to-date text-center" id="toDate" name="toDate" value="<?php echo $toDate; ?>" placeholder="To" readonly />
		</div>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="search-label display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<?php
	$sort_WebCode = get_sort('code', $order_by, $sort_by);
	$sort_DocNum = get_sort('DocNum', $order_by, $sort_by);
	$sort_SqNo = get_sort('SqNo', $order_by, $sort_by);
	$sort_DeliveryNo = get_sort('DeliveryNo', $order_by, $sort_by);
	$sort_InvoiceNo = get_sort('InvoiceNo', $order_by, $sort_by);
	$sort_PostingDate = get_sort('DocDate', $order_by, $sort_by);
	$sort_CardName = get_sort('CardName', $order_by, $sort_by);
	$sort_CardCode = get_sort('CardCode', $order_by, $sort_by);
	$sort_NumAtCard = get_sort('NumAtCard', $order_by, $sort_by);
	$sort_DocTotal = get_sort('DocTotal', $order_by, $sort_by);
	$sort_uname = get_sort('uname', $order_by, $sort_by);
 ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive border-1" style="margin-top:-1px; margin-left: 5px; padding-left: 0px; min-height:250px; max-height:600px; overflow:auto;">
		<table class="table table-striped table-bordered dataTable tableFixHead" style="min-width:1660px;">
			<thead>
				<tr class="font-size-10">
					<th class="fix-width-50 fix-chk fix-header middle text-center">
						<label>
							<input type="checkbox" class="ace chk-all" onchange="checkAll($(this))">
							<span class="lbl"></span>
						</lable>
					</th>
					<th class="fix-width-40 fix-no fix-header middle text-center">#</th>
					<th class="fix-width-80 fix-action fix-header middle text-center">action</th>
					<th class="fix-width-80 fix-approve fix-header middle text-center">Approved</th>
					<th class="fix-width-80 fix-status fix-header middle text-center">Temp Status</th>
					<th class="fix-width-100 fix-date fix-header middle sorting <?php echo $sort_PostingDate; ?>" id="sort_DocDate" onclick="sort('DocDate')">Posting Date</th>
					<th class="fix-width-100 fix-code fix-header middle sorting <?php echo $sort_WebCode; ?>" id="sort_code" onclick="sort('code')">Web Order</th>
					<th class="fix-width-120 middle">Ref No.</th>
					<th class="fix-width-80 middle sorting <?php echo $sort_DocNum; ?>" id="sort_DocNum" onclick="sort('DocNum')">SO No.</th>
					<th class="fix-width-80 middle sorting <?php echo $sort_SqNo; ?>" id="sort_SqNo" onclick="sort('SqNo')">SQ No.</th>
					<th class="fix-width-80 middle sorting <?php echo $sort_DeliveryNo; ?>" id="sort_DeliveryNo" onclick="sort('DeliveryNo')">DO No.</th>
					<th class="fix-width-80 middle sorting <?php echo $sort_InvoiceNo; ?>" id="sort_InvoiceNo" onclick="sort('InvoiceNo')">Invoice No.</th>
					<th class="fix-width-80 middle sorting <?php echo $sort_CardCode; ?>" id="sort_CardCode" onclick="sort('CardCode')">Cust. Code</th>
					<th class="min-width-250 middle sorting <?php echo $sort_CardName; ?>" id="sort_CardName" onclick="sort('CardName')">Cust. Name</th>
					<th class="fix-width-100 middle sorting <?php echo $sort_NumAtCard; ?>" id="sort_NumAtCard" onclick="sort('NumAtCard')">Cust. Ref</th>
					<th class="fix-width-100 middle text-center sorting <?php echo $sort_DocTotal; ?>" id="sort_DocTotal" onclick="sort('DocTotal')">Doc Total</th>
					<th class="fix-width-80 middle text-center sorting <?php echo $sort_uname; ?>" id="sort_uname" onclick="sort('uname')">User</th>
					<th class="fix-width-80 middle text-center">SAP Status</th>
					<th class="fix-width-100 middle text-center">Transfer No.</th>
				</tr>
			</thead>
			<tbody>
			<?php $sum_total = 0; ?>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(3) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr class="font-size-10">
						<td class="middle text-center fix-chk" scope="row">
							<?php if($rs->Status == 2) : ?>
								<label>
									<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>">
									<span class="lbl"></span>
								</label>
							<?php endif; ?>
						</td>
						<td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
						<td class="middle fix-action" scope="row">
							<button type="button" class="btn btn-minier btn-primary" title="Preview" onclick="goDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
							<?php if($rs->Status == 0 OR $rs->Status == 9 ) : ?>
							<button type="button" class="btn btn-minier btn-warning" title="Edit" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
							<?php endif; ?>
							<?php if($rs->Status == 2) : ?>
							<button type="button" class="btn btn-minier btn-info" title="Print" onclick="printSalesOrder('<?php echo $rs->code; ?>')"><i class="fa fa-print"></i></button>
							<?php endif; ?>
						</td>
						<td class="middle fix-approve" scope="row">
							<?php if($rs->Status != 9) : ?>
								<?php if($rs->Approved == 'A') : ?>
									<span class="btn btn-minier btn-success btn-block">อนุมัติ</span>
								<?php elseif($rs->Approved == 'P') : ?>
									<span class="btn btn-minier btn-warning btn-block">รออนุมัติ</span>
								<?php elseif($rs->Approved == 'R') : ?>
									<span class="btn btn-minier btn-danger btn-block">ไม่อนุมัติ</span>
								<?php endif; ?>
							<?php endif; ?>
						</td>
						<td class="middle fix-status" scope="row">
							<?php if($rs->Status == 2) : ?>
								<button type="button" class="btn btn-minier btn-success btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Success</button>
							<?php endif; ?>
							<?php if($rs->Status == 3) : ?>
								<button type="button" class="btn btn-minier btn-danger btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Failed</button>
							<?php endif; ?>
							<?php if($rs->Status == 1) : ?>
								<button type="button" class="btn btn-minier btn-warning btn-block" onclick="viewDetail('<?php echo $rs->code; ?>')">Pending</button>
							<?php endif; ?>

							<?php if($rs->Status == 9) : ?>
								<span class="btn btn-minier btn-purple btn-block">Draft</span>
							<?php endif; ?>
							<?php if($rs->Status == -1) : ?>
								<span class="btn btn-minier btn-default btn-block">Canceled</span>
							<?php endif; ?>
						</td>
						<td class="middle fix-date" scope="row"><?php echo thai_date($rs->DocDate, FALSE,'/'); ?></td>
						<td class="middle fix-code" scope="row"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->reference; ?></td>
						<td class="middle"><?php echo $rs->DocNum; ?></td>
						<td class="middle">
							<?php echo (!empty($rs->SqNo) ? $rs->SqNo : (!empty($rs->U_SQNO) ? $rs->U_SQNO : "")); ?>
						</td>
						<td class="middle"><?php echo $rs->DeliveryNo; ?></td>
						<td class="middle"><?php echo $rs->InvoiceNo; ?></td>
						<td class="middle"><?php echo $rs->CardCode; ?></td>
						<td class="middle"><?php echo $rs->CardName; ?></td>
						<td class="middle"><?php echo $rs->NumAtCard; ?></td>
						<td class="middle text-right"><?php echo number($rs->DocTotal, 2); ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle text-center">
							<?php if($rs->SapStatus == 'O') : ?>
								Open
							<?php elseif($rs->SapStatus == 'C') : ?>
								Closed
							<?php elseif($rs->SapStatus == 'E') : ?>
								Canceled
							<?php else : ?>
								Open
							<?php endif; ?>
						</td>
						<td class="middle"><?php echo $rs->transfer_code; ?></td>
					</tr>
					<?php $no++; ?>
					<?php $sum_total += $rs->DocTotal; ?>
				<?php endforeach; ?>
				<tr>
					<td colspan="13" class="middle text-right font-size-14">รวม</td>
					<td colspan="2" class="middle text-right font-size-14"><?php echo number($sum_total, 2); ?></td>
					<td colspan="2" class="middle"></td>
				</tr>
			<?php else : ?>
				<tr>
					<td colspan="17" class="middle text-center">ไม่พบรายการ</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('sales_order/import_order'); ?>

<form id="print-form" method="post" action="<?php echo $this->home; ?>print_multiple_sales_order">
	<input type="hidden" id="data" name="data" value=""/>
	<input type="hidden" id="token" name="token" value="xx" />
</form>

<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:350px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:15px; padding-right:15px; min-height:30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12 text-center">
								<input type="hidden" id="sq-code" value="" />
								<button type="button" class="btn btn-sm btn-primary" onclick="printSalesOrder('normal')">ใบเสนอราคา</button>
								<button type="button" class="btn btn-sm btn-primary" onclick="printSalesOrder('nodiscount')">ใบเสนอราคา(ไม่โชว์ส่วนลด)</button>
            </div>
        </div>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="tempModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px;">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-size: 24px; font-weight: bold; padding-bottom: 10px; color:#428bca; border-bottom:solid 2px #428bca">Sales Sales Order Temp Status</h4>
            </div>
            <div class="modal-body" style="padding-top:5px;">
            <div class="row">
              <div class="col-sm-12 col-xs-12" id="temp-table">

              </div>
            </div>

        </div>
    </div>
  </div>
</div>

<script id="temp-template" type="text/x-handlebarsTemplate">
  <input type="hidden" id="U_WEBORDER" value="{{U_WEBORDER}}"/>
  <table class="table table-bordered" style="margin-bottom:0px;">
    <tbody style="font-size:16px;">
      <tr><td class="width-30">Web Order</td><td class="width-70">{{U_WEBORDER}}</td></tr>
      <tr><td class="width-30">BP Code</td><td class="width-70">{{CardCode}}</td></tr>
      <tr><td>BP Name</td><td>{{CardName}}</td></tr>
      <tr><td>Date/Time To Temp</td><td>{{F_WebDate}}</td></tr>
      <tr><td>Date/Time To SAP</td><td>{{F_SapDate}}</td></tr>
      <tr><td>Status</td><td>{{F_Sap}}</td></tr>
      <tr><td>Message</td><td>{{Message}}</td></tr>
			<tr>
				<td colspan="2">
				{{#if del_btn}}
					<button type="button" class="btn btn-sm btn-danger" onClick="removeTemp()" ><i class="fa fa-trash"></i> Delete Temp</button>
				{{/if}}

				<button type="button" class="btn btn-sm btn-default" onclick="closeModal('tempModal')">Close</button>
				</td>
			</tr>
    </tbody>
  </table>
</script>

<style>
	.table > tr > td {
		white-space: nowrap;
	}
</style>
<script>
	// $(document).ready(function() {
	// 	setTimeout(function() {
	// 		window.location.reload();
	// 	}, 1000*60*5); //--- reload every 5 minutes
	// });

	$('#slp-code').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

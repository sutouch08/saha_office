<?php $this->load->view('include/header'); ?>
<style>
	.tableFixHead {
		margin-top:0px;
	}

	.tableFixHead > thead > tr > th {
		padding:3px !important;
	}

	.tableFixHead > tbody > tr > td {
		padding: 3px !important;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Doc No.</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Customer</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">All</option>
      <option value="Y" <?php echo is_selected('Y', $status); ?>>Success</option>
      <option value="N" <?php echo is_selected('N', $status); ?>>Pending</option>
      <option value="E" <?php echo is_selected('E', $status); ?>>Failed</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>Doc Date</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
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
    <table class="table tableFixHead border-1" style="min-width:1220px;">
      <thead>
        <tr class="font-size-11">
					<th class="fix-width-100"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-80 text-center">Doc date</th>
					<th class="fix-width-80 text-center">Posting date</th>
          <th class="fix-width-100">Doc No.</th>
          <th class="fix-width-100">Customer code</th>
          <th class="fix-width-300">Customer name</th>
					<th class="fix-width-80 text-center">Status</th>
          <th class="fix-width-120">Temp date</th>
          <th class="fix-width-120">SAP date</th>
					<th class="min-width-100">Message</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>
        <tr class="font-size-11" id="row-<?php echo $rs->DocEntry; ?>">
					<td class="middle">
						<button type="button" class="btn btn-minier btn-info" title="View temp deail" onclick="get_detail(<?php echo $rs->DocEntry; ?>)"><i class="fa fa-eye"></i></button>
						<?php if($rs->F_Sap != 'Y') : ?>
							<button type="button" class="btn btn-minier btn-danger" title="Remove this row" onclick="removeTemp(<?php echo $rs->DocEntry; ?>, '<?php echo $rs->U_WEBORDER; ?>')"><i class="fa fa-trash"></i></button>
							<?php if($this->isAdmin OR $this->isSuperAdmin) : ?>
								<button type="button" class="btn btn-minier btn-primary" title="Change status to Success"	onclick="setSuccess(<?php echo $rs->DocEntry; ?>, '<?php echo $rs->U_WEBORDER; ?>')"><i class="fa fa-check"></i></button>
							<?php endif; ?>
						<?php endif; ?>
					</td>
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle text-center"><?php echo thai_date($rs->DocDate); ?></td>
					<td class="middle text-center"><?php echo thai_date($rs->TaxDate); ?></td>
          <td class="middle"><?php echo $rs->U_WEBORDER; ?></td>
          <td class="middle"><?php echo $rs->CardCode; ?></td>
          <td class="middle hide-text"><?php echo $rs->CardName; ?></td>
					<td class="middle text-center">
						<?php if($rs->F_Sap === NULL) : ?>
							<span class="orange">Pending</span>
						<?php elseif($rs->F_Sap === 'N') : ?>
							<span class="red">Failed</span>
						<?php elseif($rs->F_Sap === 'Y') : ?>
							<span class="green">Success</span>
						<?php endif; ?>
					</td>
          <td class="middle" ><?php echo thai_date($rs->F_WebDate, TRUE); ?></td>
          <td class="middle"><?php echo empty($rs->F_SapDate) ? NULL : thai_date($rs->F_SapDate, TRUE);	?></td>
          <td class="middle"><?php echo $rs->F_Sap === 'N' ? $rs->Message : NULL; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="11" class="middle text-center"><h4>-- Not found --</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/temp/temp_return_request.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
			<i class="fa fa-plus"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> BacK</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
	<div class="col-sm-2 col-xs-4 padding-5">
		<label>Code</label>
		<input type="text" maxlength="15" name="leadCode" id="leadCode" class="form-control input-sm" value="" onkeyup="validCode(this)" disabled required />
	</div>

	<div class="col-sm-1 col-xs-4 padding-5">
		<label>คำนำหน้า</label>
		<select class="form-control input-sm" name="customerPrefix" id="customerPrefix" onchange="add_prefix()">
			<option value=""></option>
			<?php echo select_customer_prefix(); //--- customer_helper ?>
		</select>
	</div>
	<div class="col-sm-4 col-4-harf col-xs-6 padding-5">
		<label>Name</label>
		<input type="text" maxlength="100" class="form-control input-sm" name="customerName" id="customerName" required />
	</div>
	<div class="col-sm-4 col-4-harf col-xs-6 padding-5">
		<label>Foreign Name</label>
		<input type="text" maxlength="100" class="form-control input-sm" name="customerFName" id="customerFName" required />
	</div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>Group</label>
		<select class="form-control input-sm" name="groupCode" id="groupCode">
			<option value=""></option>
			<?php echo select_GroupCode(); //--- customer_helper ?>
		</select>
	</div>

	<div class="col-sm-1 col-xs-6 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm" name="currency" id="currency">
			<?php echo select_currency(); //--- currency_helper ?>
		</select>
	</div>

	<div class="col-sm-2 col-xs-6 padding-5">
		<label>Tax ID</label>
		<input type="text" maxlength="32" class="form-control input-sm" name="taxId" id="taxId" />
	</div>

	<div class="col-sm-3 col-xs-6 padding-5">
		<label>Owner</label>
		<input type="text" class="form-control input-sm" name="ownerName" id="ownerName" value="<?php echo $this->user->emp_name; ?>">
		<input type="hidden" name="ownerCode" id="ownerCode" value="<?php echo $this->user->emp_id; ?>">
	</div>

	<div class="col-sm-4 col-xs-6 padding-5">
		<label>Customer Level</label>
		<select class="form-control input-sm" name="customerLevel" id="customerLevel">
			<option value=""></option>
			<?php echo select_customer_level(); ?>
		</select>
	</div>
</div> <!-- row -->
<hr class="padding-5 margin-top-15 margin-bottom-15">

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<div class="tabable">
			<ul id="myTab1" class="nav nav-tabs">
			  <li class="li-block active in">
					<a href="#generalTab" data-toggle="tab">General</a>
				</li>
				<li class="li-block">
					<a href="#contactTab" data-toggle="tab">Contact Persons</a>
				</li>
				<li class="li-block">
					<a href="#addressTab" data-toggle="tab">Address</a>
				</li>
				<li class="li-block">
					<a href="#paymentTab" data-toggle="tab">Payment Terms</a>
				</li>
				<li class="li-block">
					<a href="#accountTab" data-toggle="tab">Accounting</a>
				</li>
				<li class="li-block">
					<a href="#propertiesTab" data-toggle="tab">Properties</a>
				</li>
				<li class="li-block">
					<a href="#remarkTab" data-toggle="tab">Remarks</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="col-sm-12 padding-5">
		<div class="tab-content" style="min-height:600px;">
			<div class="tab-pane fade active in" id="generalTab">
				<?php $this->load->view('customer/add_general_tab'); ?>
			</div>
			<div class="tab-pane fade" id="contactTab">
				<?php $this->load->view('customer/add_contact_tab'); ?>
			</div>
			<div class="tab-pane fade" id="addressTab">
				<?php $this->load->view('customer/add_address_tab'); ?>
			</div>
			<div class="tab-pane fade" id="paymentTab">
				<?php $this->load->view('customer/add_payment_tab'); ?>
			</div>
			<div class="tab-pane fade" id="accountTab">
				<?php $this->load->view('customer/add_accounting_tab'); ?>
			</div>
			<div class="tab-pane fade" id="propertiesTab">
				<?php $this->load->view('customer/add_properties_tab'); ?>
			</div>
			<div class="tab-pane fade" id="remarkTab">
				<?php $this->load->view('customer/add_remark_tab'); ?>
			</div>
		</div>
	</div>

</div><!--/ row  -->
<div class="row">
	<div class="divider-hidden"></div>
	<div class="col-sm-12 col-xs-12 padding-5">
		<button type="button" class="btn btn-sm btn-success" onclick="save()">Add</button>
		<button type="button" class="btn btn-sm btn-default" onclick="leave()">Cancel</button>
	</div>
</div>




<script src="<?php echo base_url(); ?>scripts/customer/customer.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

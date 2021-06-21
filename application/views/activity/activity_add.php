<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.autosize.js"></script>
<script src="<?php echo base_url(); ?>assets/js/chosen.jquery.js"></script>

<style>
	.form-group {
		margin-bottom: 5px;
	}
	.form-horizontal .form-group {
		margin-left: 0px;
		margin-right: 0px;
	}
	.input-icon > .ace-icon {
		z-index: 1;
	}
</style>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">

<div class="row">
	<div class="col-sm-5 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Activity</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Activity" id="Action">
						<option value="C">Phone Call</option>
						<option value="M">Meeting</option>
						<option value="T">Task</option>
						<option value="E">Note</option>
						<option value="P">Campaignx</option>
						<option value="N">Other</option>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Type</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Type" id="CntctType" onchange="updateSubjectList()">
						<?php echo select_type(); ?>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Subject</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Subject" id="CntctSbjct">
						<option value="">Please select Type</option>
						<?php //echo select_subject(); ?>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Assigned To</label>
				<div class="col-sm-2 col-2-harf col-xs-4" style="padding-left:12px; padding-right:5px;">
					<select class="form-control input-sm" id="attendType" onchange="toggleAssignedTo()">
						<option value="U">User</oprion>
						<option value="E" selected>Employee</option>
					</select>
				</div>
				<div id="div-user" class="col-sm-5 col-5-harf col-xs-12 hide" style="padding-left:5px; padding-right:12px;">
	        <select class="form-control" id="AttendUser" data-placeholder="Choose user">
						<option value=""></option>
						<?php echo select_sap_user(); ?>
					</select>
	      </div>

	      <div id="div-empl" class="col-sm-5 col-5-harf col-xs-12" style="padding-left:5px; padding-right:12px;">
	        <select class="form-control" id="AttendEmpl" data-placeholder="Choose Employee">
						<option value=""></option>
						<?php echo select_employee($this->user->emp_id); ?>
					</select>
	      </div>
	    </div>
		</div><!-- form-->
	</div>

	<!--******** Right header ***********-->
	<div class="col-sm-4 col-xs-12 padding-5">
	  <div class="row">
	    <div class="form-horizontal">
	      <div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">BP Code</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="CardCode" name="CardCode" value=""/>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">BP Name</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="CardName" name="CardName" value=""/>
	        </div>
	      </div>


				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Contact Person</label>
	        <div class="col-sm-8 col-xs-12">
						<select id="ContactPer" name="ContactPer" class="form-control input-sm">
	          	<option value="0">Select BP</option>
						</select>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Telephone No.</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="Tel" name="Tel" value=""/>
	        </div>
	      </div>

	    </div><!-- form-->
	  </div><!-- row-->
	</div>

	<div class="col-sm-3 col-xs-12 padding-5">
	  <div class="row">
	    <div class="form-horizontal">
				<div class="form-group">
	        <label class="col-sm-6 control-label no-padding-right">Number</label>
	        <div class="col-sm-6 col-xs-12">
	          <input type="text" id="ClgCode" class="form-control input-sm" value="" disabled/>
	        </div>
	      </div>

	      <div class="form-group">
	        <label class="col-sm-6 control-label no-padding-right">Web Order</label>
	        <div class="col-sm-6 col-xs-12">
	          <input type="text" id="code" class="form-control input-sm" value="" disabled/>
	        </div>
	      </div>

	    </div><!-- form-->
	  </div><!-- row-->
	</div>
</div>

</form>
<hr class="paddig-5"/>
<div class="row">
	<div class="col-sm-9 col-xs-12 padding-5" style="padding-right:0px;">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-right">Remarks</label>
				<div class="col-sm-10 col-xs-12 padding-5 first">
					<input type="text" id="Details" class="form-control input-sm" value="" placeholder="Required !"/>
				</div>
			</div>

			<div class="form-group">
	      <label class="col-sm-2 control-label no-padding-right">Content</label>
	      <div class="col-sm-10 col-xs-12 padding-5 first">
	        <textarea id="Notes" rows="5" class="autosize autosize-transition form-control"></textarea>
	      </div>
	    </div>
		</div><!-- form-->
	</div>
</div>

<hr class="padding-5">
<div class="row">
	<div class="col-sm-4 col-xs-12 padding-5">
		<div class="row">
			<div class="form-horizontal">
				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Start Time</label>
	        <div class="col-sm-3 col-3-harf col-xs-4 padding-5 first">
	          <input type="text" class="form-control input-sm text-center" id="Recontact" value="" placeholder="dd-mm-yyyy"/>
	        </div>
					<div class="col-sm-2 col-2-harf col-xs-4 padding-5">
						<input type="text" class="form-control input-sm text-center" id="BeginTime" value="" placeholder="hh:mm" />
					</div>
					<div class="col-sm-2 col-xs-4 padding-5 last">
						<button type="button" class="btn btn-xs btn-info btn-block" onclick="startTime()">Start</button>
					</div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">End Time</label>
	        <div class="col-sm-3 col-3-harf col-xs-4 padding-5 first">
	          <input type="text" class="form-control input-sm text-center" id="endDate" value="" placeholder="dd-mm-yyyy"/>
	        </div>
					<div class="col-sm-2 col-2-harf col-xs-4 padding-5">
						<input type="text" class="form-control input-sm text-center" id="ENDTime" value="" placeholder="hh:mm"/>
					</div>
					<div class="col-sm-2 col-xs-4 padding-5 last">
						<button type="button" class="btn btn-xs btn-danger btn-block" onclick="endTime()">End</button>
					</div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Duration</label>
	        <div class="col-sm-8 col-xs-12 padding-5 first last">
	          <input type="text" class="form-control input-sm" id="Duration" name="Duration" value="" disabled/>
	        </div>
	      </div>
			</div>
		</div>
	</div>

	<div class="col-sm-4 col-xs-12 padding-5">
		<div class="row">
			<div class="form-horizontal">
				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Project</label>
	        <div class="col-sm-8 col-xs-12">
						<input type="text" class="form-control input-sm" id="projectName" value="" />
	          <input type="hidden" id="FIPROJECT" value=""/>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Stage</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="Stage" value=""/>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Doc. Type</label>
	        <div class="col-sm-8 col-xs-12">
	          <select class="form-control input-sm" id="DocType" onchange="DocNumInit()">
							<option value="-1"></option>
							<option value="23">Sales Quotation</option>
							<option value="17">Sales Order</option>
							<option value="1470000113">Purchase Request</option>
						</select>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Document No</label>
	        <div class="col-sm-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control input-sm" id="DocNum" value="" />
							<span class="input-group-btn">
								<button type="button" class="btn btn-xs btn-default" onclick="getDocList()"><i class="fa fa-search"></i></button>
							</span>
						</div>

	        </div>
	      </div>

			</div>
		</div>
	</div>

	<div class="col-sm-4 col-xs-12 padding-5 last">
		<div class="row">
			<div class="form-horizontal">
				<div class="form-group">
	        <label class="col-sm-5 control-label no-padding-right">Priority</label>
	        <div class="col-sm-7 col-xs-12">
						<select class="form-control input-sm" id="Priority">
							<option value="0">Low</option>
							<option value="1" selected>Normal</option>
							<option value="2">High</option>
						</select>

	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-5 control-label no-padding-right">Meeting Location</label>
	        <div class="col-sm-7 col-xs-12">
	          <select class="form-control input-sm" id="Location">
							<option value="-1"></option>
							<?php echo select_location(); ?>
						</select>
	        </div>
	      </div>

			</div>
		</div>
	</div>
</div>

<hr class="padding-5">
<div class="row">
	<div class="col-sm-1 col-xs-4 padding-5">
		<button type="button" class="btn btn-sm btn-success btn-block" onclick="add()">Add</button>
	</div>
	<div class="col-sm-1 col-xs-4 padding-5">
		<button type="button" class="btn btn-sm btn-warning btn-block" onclick="leave()">Cancel</option>
	</div>
</div>

<input type="hidden" name="sale_id" id="sale_id" value="<?php echo $this->user->sale_id; ?>">
<input type="hidden" name="emp_id" id="emp_id" value="<?php echo $this->user->emp_id; ?>">
<input type="hidden" name="docNo" id="docNo" value="" >

<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:820px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 2px #c5d0dc; background-color:#eff3f8;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title">List of Documents</h4>
            </div>
            <div class="modal-body" >
							<div class="row">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="col-sm-1 control-label no-padding-right">Find</label>
										<div class="col-sm-5 padding-5">
											<input type="text" class="form-control input-sm" id="search-box" />
										</div>
										<div class="col-sm-2 padding-5">
											<button type="button" class="btn btn-xs btn-primary btn-block" onclick="searchDocument()">Search Text</button>
										</div>
									</div>
								</div>
								<hr class="margin-top-15">
								<div class="col-sm-12">
									<div class="col-sm-12 table-responsive" style="height:500px; width:800px; border:solid 1px #ccc; padding:0px; overflow-y:scroll;">
										<table class="table table-hover table-bordered" style="min-width:800px; margin-bottom:0px;">
											<head>
												<tr>
													<th class="text-center" style="width:20px;">#</th>
													<th class="text-center" style="width:70px;">#</th>
													<th class="text-center" style="width:70px;">Date</th>
													<th class="text-center" style="width:200px;">BP</th>
													<th class="text-center" style="width:400px;">Details</th>
												</tr>
											</thead>
											<tbody id="result">

											</tbody>
										</table>
									</div>

								</div>
							</div>

            </div>
            <div class="modal-footer">
							<div class="row">
								<div class="col-sm-2 col-xs-2 padding-5">
									<button type="button" class="btn btn-sm btn-primary btn-block" onclick="choose()">Choose</button>
								</div>
								<div class="col-sm-2 col-xs-2 padding-5">
									<button type="button" class="btn btn-sm btn-warning btn-block" onclick="closeModal()">Cancel</button>
								</div>
							</div>

            </div>
        </div>
    </div>
</div>

<script id="doc-template" type="text/x-handlebarsTemplate">
{{#each this}}
	<tr id="{{DocNum}}" class="rw" style="cursor:pointer;" onclick="add_value('{{DocNum}}')" ondblclick="add_value_and_close('{{DocNum}}')">
		<td class="text-center">{{no}}</td>
		<td>{{DocNum}}</td>
		<td>{{DocDate}}</td>
		<td>{{CardName}}</td>
		<td>{{Details}}</td>
	</tr>
{{/each}}
</script>

<script id="subject-template" type="text/x-handlebarsTemplate">
	<option value="">Choose Subject</option>
	{{#each this}}
		{{#if nodata}}

		{{else}}
			<option value="{{code}}">{{name}}</option>
		{{/if}}
	{{/each}}
</script>

<script id="contact-template" type="text/x-handlebarsTemplate">
	<option value="">Please Select</option>
	{{#each this}}
		{{#if nodata}}

		{{else}}
			<option value="{{code}}">{{name}}</option>
		{{/if}}
	{{/each}}
</script>

<script>
	$('#AttendEmpl').chosen({
		allow_single_deselect :false,
		width:'100%'
	});

	$('#AttendUser').chosen({
		allow_single_deselect :false,
		width:'100%'
	});
</script>

<script src="<?php echo base_url(); ?>assets/js/jquery.maskedinput.js"></script>
<script src="<?php echo base_url(); ?>scripts/activity/activity.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/activity/activity_add.js?v=<?php echo date('YmdH'); ?>"></script>



<?php $this->load->view('include/footer'); ?>

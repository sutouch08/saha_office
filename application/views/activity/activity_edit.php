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
        <button type="button" class="btn btn-sm btn-default" onclick="goBack()"><i class="fa fa-arrow-left"></i> &nbsp; Back</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
	<div class="col-sm-5 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Activity</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Activity" id="Action">
						<option value="C" <?php echo is_selected('C', $Action); ?> >Phone Call</option>
						<option value="M" <?php echo is_selected('M', $Action); ?> >Meeting</option>
						<option value="T" <?php echo is_selected('T', $Action); ?> >Task</option>
						<option value="E" <?php echo is_selected('E', $Action); ?> >Note</option>
						<option value="P" <?php echo is_selected('P', $Action); ?> >Campaignx</option>
						<option value="N" <?php echo is_selected('N', $Action); ?> >Other</option>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Type</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Type" id="CntctType" onchange="updateSubjectList()">
						<?php echo select_type($CntctType); ?>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Subject</label>
	      <div class="col-sm-8 col-xs-12">
	        <select class="form-control input-sm" name="Subject" id="CntctSbjct">
						<option value="">Please Select</option>
						<?php echo select_subject($CntctSbjct); ?>
					</select>
	      </div>
	    </div>

			<div class="form-group">
	      <label class="col-sm-4 col-xs-12 control-label no-padding-right">Assigned To</label>
				<div class="col-sm-2 col-2-harf col-xs-4" style="padding-left:12px; padding-right:5px;">
					<select class="form-control input-sm" id="attendType" onchange="toggleAssignedTo()">
						<option value="U" <?php echo is_selected('U', $attendType); ?>>User</oprion>
						<option value="E" <?php echo is_selected('E', $attendType); ?>>Employee</option>
					</select>
				</div>

				<?php $activeEmp = $attendType === 'E' ? '' : 'hide'; ?>
				<?php $activeUsr = $attendType === 'U' ? '' : 'hide'; ?>
				<div id="div-user" class="col-sm-5 col-5-harf col-xs-12 <?php echo $activeUsr; ?>" style="padding-left:5px; padding-right:12px;">
	        <select class="form-control" id="AttendUser" data-placeholder="Choose user">
						<option value=""></option>
						<?php echo select_sap_user($AttendUser); ?>
					</select>
	      </div>

	      <div id="div-empl" class="col-sm-5 col-5-harf col-xs-12 <?php echo $activeEmp; ?>" style="padding-left:5px; padding-right:12px;">
	        <select class="form-control" id="AttendEmpl" data-placeholder="Choose Employee">
						<option value=""></option>
						<?php echo select_employee($AttendEmpl); ?>
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
	          <input type="text" class="form-control input-sm" id="CardCode" name="CardCode" value="<?php echo $CardCode; ?>"/>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">BP Name</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="CardName" name="CardName" value="<?php echo $CardName; ?>"/>
	        </div>
	      </div>


				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Contact Person</label>
	        <div class="col-sm-8 col-xs-12">
						<select id="ContactPer" name="ContactPer" class="form-control input-sm">
	          	<option value="">Please Select</option>
							<?php echo select_contact_person($CntctCode); ?>
						</select>

	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Telephone No.</label>
	        <div class="col-sm-8 col-xs-12">
	          <input type="text" class="form-control input-sm" id="Tel" name="Tel" value="<?php echo $Tel; ?>"/>
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
	          <input type="text" id="code" class="form-control input-sm" value="<?php echo $code; ?>" disabled/>
	        </div>
	      </div>

	    </div><!-- form-->
	  </div><!-- row-->
	</div>
</div>


<hr class="paddig-5"/>
<div class="row">
	<div class="col-sm-9 col-xs-12 padding-5" style="padding-right:0px;">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-right">Remarks</label>
				<div class="col-sm-10 col-xs-12 padding-5 first">
					<input type="text" id="Details" class="form-control input-sm" value="<?php echo $Details; ?>" placeholder="Required !"/>
				</div>
			</div>

			<div class="form-group">
	      <label class="col-sm-2 control-label no-padding-right">Content</label>
	      <div class="col-sm-10 col-xs-12 padding-5 first">
	        <textarea id="Notes" rows="5" class="autosize autosize-transition form-control"><?php echo $Notes; ?></textarea>
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
	          <input type="text" class="form-control input-sm text-center" id="Recontact" value="<?php echo thai_date($Recontact, FALSE, '-'); ?>" placeholder="dd-mm-yyyy"/>
	        </div>
					<div class="col-sm-2 col-2-harf col-xs-4 padding-5">
						<input type="text" class="form-control input-sm text-center" id="BeginTime" value="<?php echo get_time_from_int($BeginTime); ?>" placeholder="hh:mm" />
					</div>
					<div class="col-sm-2 col-xs-4 padding-5 last">
						<button type="button" class="btn btn-xs btn-info btn-block" onclick="startTime()">Start</button>
					</div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">End Time</label>
	        <div class="col-sm-3 col-3-harf col-xs-4 padding-5 first">
	          <input type="text" class="form-control input-sm text-center" id="endDate" value="<?php echo thai_date($endDate, FALSE, '-'); ?>" placeholder="dd-mm-yyyy"/>
	        </div>
					<div class="col-sm-2 col-2-harf col-xs-4 padding-5">
						<input type="text" class="form-control input-sm text-center" id="ENDTime" value="<?php echo get_time_from_int($ENDTime); ?>" placeholder="hh:mm"/>
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
						<input type="text" class="form-control input-sm" id="projectName" value="<?php echo $project_name; ?>" />
	          <input type="hidden" id="FIPROJECT" value="<?php echo $FIPROJECT; ?>"/>
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
							<option value="23" <?php echo is_selected('23', $DocType); ?>>Sales Quotation</option>
							<option value="17" <?php echo is_selected('17', $DocType); ?>>Sales Order</option>
							<option value="1470000113" <?php echo is_selected('1470000113', $DocType); ?>>Purchase Request</option>
						</select>
	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-4 control-label no-padding-right">Document No</label>
	        <div class="col-sm-8 col-xs-12">
						<div class="input-group">
							<input type="text" class="form-control input-sm" id="DocNum" value="<?php echo $DocNum; ?>" />
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
							<option value="0" <?php echo is_selected('0', $Priority); ?>>Low</option>
							<option value="1" <?php echo is_selected('1', $Priority); ?>>Normal</option>
							<option value="2" <?php echo is_selected('2', $Priority); ?>>High</option>
						</select>

	        </div>
	      </div>

				<div class="form-group">
	        <label class="col-sm-5 control-label no-padding-right">Meeting Location</label>
	        <div class="col-sm-7 col-xs-12">
	          <select class="form-control input-sm" id="Location">
							<option value="-1"></option>
							<?php echo select_location($Location); ?>
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
		<button type="button" class="btn btn-sm btn-success btn-block" onclick="update()">Update</button>
	</div>
	<div class="col-sm-1 col-xs-4 padding-5">
		<button type="button" class="btn btn-sm btn-warning btn-block" onclick="leave()">Cancel</option>
	</div>
</div>

<input type="hidden" name="sale_id" id="sale_id" value="<?php echo $this->user->sale_id; ?>">
<input type="hidden" name="emp_id" id="emp_id" value="<?php echo $this->user->emp_id; ?>">
<input type="hidden" name="docNo" id="docNo" value="" >
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" >

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

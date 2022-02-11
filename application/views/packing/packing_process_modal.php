<div class="modal fade" id="packOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center" id="option-title"></h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
	              	<div class="input-group">
	              		<span class="input-group-addon">Qty</span>
										<input type="number" class="form-control input-sm text-center" id="option-qty" value="1" />
	              	</div>
									<input type="hidden" id="option-item" value="" />
	              </div>
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
									<div class="input-group">
										<span class="input-group-addon">Uom</span>
										<select class="form-control input-sm" id="option-uom">
											<option value="">Select Uom</option>
										</select>
									</div>
								</div>
	            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" onClick="packWithOption()" >Pack</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="boxOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">การแก้ไขกล่อง</h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th class="width-10 middle text-center">
													<label>
														<input type="checkbox" class="ace" id="box-chk-all" onchange="check_box_all()">
														<span class="lbl"></span>
													</label>
												</th>
												<th class="width-20 middle text-center">กล่อง</th>
												<th class="width-20 middle text-center">จำนวนสินค้า</th>
												<th class="width-20 middle text-center">พาเลท</th>
												<th class="width-30 middle text-center"></th>
											</tr>
										</thead>
	              		<tbody id="box-list-table">

	              		</tbody>
	              	</table>
	              </div>
	            </div>
            </div>
            <div class="modal-footer">
							<button type="button" class="btn btn-sm btn-danger pull-left" onclick="removeSelectedBox()"><i class="fa fa-trash"></i> Delete</button>
              <button type="button" class="btn btn-sm btn-info pull-right" onClick="printSelectedBox()"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>


<script id="box-list-template" type="text/x-handlebarsTemplate">
{{#each this}}
	<tr id="box-row-{{box_id}}">
		<td class="middle text-center">
			<label><input type="checkbox" class="ace box-chk" data-no="{{no}}" value="{{box_id}}"><span class="lbl"></span></label>
		</td>
		<td class="middle">กล่องที่ {{no}}</td>
		<td class="middle text-center">{{qty}}</td>
		<td class="middle text-center">{{pallet_code}}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-xs btn-info" onclick="printBox({{box_id}})"><i class="fa fa-print"></i></button>
			<button type="button" class="btn btn-xs btn-primary" onclick="editBox({{box_id}})"><i class="fa fa-eye"></i></button>
			<button type="button" class="btn btn-xs btn-danger" onclick="removeBox({{box_id}}, {{no}})"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
{{/each}}
</script>


<div class="modal fade" id="boxEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered">
	              		<tbody id="box-detail-table">

	              		</tbody>
	              	</table>
	              </div>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center red" id="box-error">

								</div>
	            </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>



<script id="box-detail-template" type="text/x-handlebarsTemplate">
	<tr>
		<td colspan="4" class="text-center">กล่องที่ {{box_no}}</td>
	</tr>
	<tr>
		<td class="text-center">สินค้า</td>
		<td class="text-center" style="width:50px;">Uom</td>
		<td class="text-center" style="width:70px;">จำนวน</td>
		<td class="text-center" style="width:50px;"></td>
	</tr>
	{{#each rows}}
	<tr id="box-row-{{id}}">
		<td class="middle">{{ItemCode}}  {{ItemName}}</td>
		<td class="middle text-center">{{unitMsr}}</td>
		<td class="middle text-center">{{qty}}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-xs btn-danger" onclick="removePackDetail({{id}}, '{{ItemCode}}')"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
	{{/each}}
</script>



<div class="modal fade" id="palletOptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">Pallet Options</h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th class="width-10 middle text-center">
													<label>
														<input type="checkbox" class="ace" id="pallet-chk-all" onchange="check_pallet_all()">
														<span class="lbl"></span>
													</label>
												</th>
												<th class="width-30 middle text-center">รหัส</th>
												<th class="width-20 middle text-center">จำนวนกล่อง</th>
												<th class="width-30 middle text-center"></th>
											</tr>
										</thead>
	              		<tbody id="pallet-list-table">

	              		</tbody>
	              	</table>
	              </div>
	            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-info pull-right" onClick="printSelectedPallet()"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
    </div>
</div>


<script id="pallet-list-template" type="text/x-handlebarsTemplate">
{{#each this}}
	<tr id="pallet-row-{{id}}">
		<td class="middle text-center">
			<label><input type="checkbox" class="ace pallet-chk" value="{{id}}"><span class="lbl"></span></label>
		</td>
		<td class="middle">{{code}}</td>
		<td class="middle text-center">{{qty}}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-xs btn-info" onclick="printPallet({{id}})"><i class="fa fa-print"></i></button>
			<button type="button" class="btn btn-xs btn-primary" onclick="viewPallet({{id}})"><i class="fa fa-eye"></i></button>
			{{#unless qty}}
			<button type="button" class="btn btn-xs btn-danger" onclick="removePallet({{id}}, '{{code}}')"><i class="fa fa-trash"></i></button>
			{{/unless}}
		</td>
	</tr>
{{/each}}
</script>


<div class="modal fade" id="palletDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered">
	              		<tbody id="pallet-detail-table">

	              		</tbody>
	              	</table>
	              </div>
	            </div>
            </div>
            <div class="modal-footer">
							<button type="button" class="btn btn-sm btn-primary" onclick="showNoPalletBox()">เพิ่มกล่องเข้าพาเลท</button>
            </div>
        </div>
    </div>
</div>



<script id="pallet-detail-template" type="text/x-handlebarsTemplate">
	<tr>
		<td colspan="4" class="text-center">{{code}}</td>
	</tr>
	<tr>
		<td class="text-center">กล่อง</td>
		<td class="text-center">จำนวนสินค้า</td>
		<td class="text-center" style="width:50px;"></td>
	</tr>
	{{#each rows}}
	<tr id="pallet-box-{{box_id}}">
		<td class="middle text-center">กล่องที่ {{box_no}}</td>
		<td class="middle text-center" >{{qty}}</td>
		<td class="middle text-right">
			<button type="button" class="btn btn-xs btn-danger" onclick="removePalletBox({{box_id}}, {{box_no}})"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
	{{/each}}
</script>



<div class="modal fade" id="noPalletBoxModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site text-center">กล่องที่ไม่อยู่ในพาเลท</h4>
            </div>
            <div class="modal-body">
	            <div class="row">
	              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
	              	<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th class="width-10 middle text-center">
													<label>
														<input type="checkbox" class="ace" id="no-pallet-chk-all" onchange="check_no_pallet_all()">
														<span class="lbl"></span>
													</label>
												</th>
												<th class="width-50 middle text-center">กล่อง</th>
												<th class="width-40 middle text-center">จำนวนสินค้า</th>
											</tr>
										</thead>
	              		<tbody id="no-pallet-table">

	              		</tbody>
	              	</table>
	              </div>
	            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-primary" onClick="addToPallet()">เพิ่มเข้าพาเลท</button>
            </div>
        </div>
    </div>
</div>


<script id="no-pallet-template" type="text/x-handlebarsTemplate">
{{#each this}}
	<tr id="box-row-{{box_id}}">
		<td class="middle text-center">
			<label><input type="checkbox" class="ace no-pallet-chk" value="{{box_id}}"><span class="lbl"></span></label>
		</td>
		<td class="middle">กล่องที่ {{box_no}}</td>
		<td class="middle text-center">{{qty}}</td>
	</tr>
{{/each}}
</script>

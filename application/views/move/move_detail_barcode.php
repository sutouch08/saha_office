<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive hide" id="zone-table">
    	<table class="table table-striped table-bordered">
      	<thead>
					<tr>
						<th colspan="5" class="text-center">
							<h4 class="title" id="zoneName"></h4>
						</th>
					</tr>
          <tr>
          	<th class="width-10 text-center">ลำดับ</th>
            <th class="width-20 text-center">บาร์โค้ด</th>
            <th class="text-center">สินค้า</th>
            <th class="width-10 text-center">จำนวน</th>
						<th class="width-10 text-center">หน่วยนับ</th>
          </tr>
          </thead>

          <tbody id="zone-list"> </tbody>

        </table>
    </div>


		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive hide" id="temp-table">
    	<table class="table table-striped table-bordered">
      	<thead>
          <tr>
          	<th colspan="7" class="text-center">
             รายการใน Temp
            </th>
            </tr>
          	<tr>
							<th style="width:60px;" class="text-center">#</th>
		          <th style="width:120px;" class="middle">Barcode</th>
		          <th style="min-width:300px;" class="middle">Item</th>
		          <th style="width:120px;" class="middle text-center">From Bin</th>
		          <th style="width:100px;" class="middle text-center">Qty</th>
		          <th style="width:100px;" class="middle">Uom.</th>
							<th style="width:60px;" class="middle text-right"></th>
            </tr>
          </thead>
          <tbody id="temp-list">

          </tbody>
        </table>
    </div>


	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="move-table">
  	<table class="table table-striped border-1">
    	<thead>
      	<tr>
        	<th colspan="8" class="text-center">รายการโอนย้าย</th>
        </tr>

				<tr>
        	<th style="width:60px;" class="text-center">#</th>
          <th style="width:120px;" class="middle">Barcode</th>
          <th style="min-width:300px;" class="middle">Item</th>
          <th style="width:120px;" class="middle text-center">From Bin</th>
          <th style="width:120px;" class="middle text-center">To Bin</th>
          <th style="width:100px;" class="middle text-center">Qty</th>
          <th style="width:100px;" class="middle">Uom.</th>
					<th style="width:60px;" class="middle text-right"></th>
        </tr>
      </thead>

      <tbody id="move-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0;  ?>
<?php		foreach($details as $rs) : 	?>
				<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">

	      	<td class="middle text-center no">
						<?php echo $no; ?>
					</td>

					<!--- บาร์โค้ดสินค้า --->
	        <td class="middle">
						<?php echo $rs->barcode; ?>
					</td>

					<!--- รหัสสินค้า -->
	        <td class="middle">
						<?php echo $rs->ItemCode." : ".$rs->ItemName; ?>
					</td>

					<!--- โซนต้นทาง --->
	        <td class="middle text-center">
						<?php echo $rs->fromBinCode; ?>
	        </td>


	        <td class="middle text-center" id="row-label-<?php echo $rs->id; ?>">
	        	<?php echo $rs->toBinCode; ?>
	        </td>

	        <td class="middle text-center qty" >
						<?php echo number($rs->Qty); ?>
					</td>

					<td class="middle">
						<?php echo $rs->unitMsr; ?>
					</td>

					<td class="middle text-center">
	          <?php if($rs->valid == 0) : ?>
	          	<button type="button" class="btn btn-minier btn-danger"
							onclick="deleteMoveItem(<?php echo $rs->id; ?>, '<?php echo $rs->ItemCode; ?>')">
								<i class="fa fa-trash"></i>
							</button>
	          <?php endif; ?>
	        </td>

	      </tr>
<?php			$no++;			?>
<?php 	  $total_qty += $rs->Qty; ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
					<td></td><td></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="5" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr>
			<td class="text-center">{{ no }}</td>
		  <td class="text-center">{{ barcode }}</td>
		  <td>{{ products }}<input type="hidden" id="qty_{{barcode}}" value="{{qty}}" /></td>
		  <td class="text-center" id="qty-label_{{barcode}}">{{ qty }}</td>
			<td class="text-center">{{ unitMsr}}</td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="tempTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
		<tr>
			<td colspan="4" class="text-right"><strong>รวม</strong></td>
			<td class="middle text-center" id="total">{{ total }}</td>
			<td></td><td></td>
		</tr>
		{{else}}
			<tr class="font-size-12" id="row-temp-{{ id }}">
				<td class="middle text-center">{{ no }}</td>
				<td class="middle">{{ barcode }}</td>
				<td class="middle">{{ products }}</td>
				<td class="middle text-center"><input type="hidden" id="qty-{{barcode}}" value="{{qty}}" />{{ from_zone }}</td>
				<td class="middle text-center" id="qty-label-{{barcode}}">{{ qty }}</td>
				<td class="middle text-center">{{ unitMsr }}</td>
				<td class="middle text-right">
					<button class="btn btn-mini btn-danger" onclick="deleteTemp({{ id }}, '{{ products }}')"><i class="fa fa-trash"></i></button>
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>



<script id="moveTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
			<tr>
				<td colspan="5" class="text-right"><strong>รวม</strong></td>
				<td class="middle text-center" id="total">{{ total }}</td>
				<td></td><td></td>
			</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle">{{ barcode }}</td>
			<td class="middle">{{ products }}</td>
			<td class="middle text-center">{{ from_zone }}</td>
			<td class="middle text-center">{{{ to_zone }}}</td>
			<td class="middle text-center qty">{{ qty }}</td>
			<td class="middle">{{ unitMsr }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>

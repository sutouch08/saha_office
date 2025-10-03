<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive" id="move-table"
    style="min-height:300px; overflow:auto; border-top:solid 1px #ccc;">
  <table class="table table-striped" style="margin-bottom:0px; min-width:1000px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-120 middle">Item Code</th>
          <th class="fix-width-300 middle">Item Name</th>
          <th class="fix-width-120 middle text-center">From Bin</th>
          <th class="fix-width-120 middle text-center">To Bin</th>
          <th class="fix-width-100 middle text-center">Qty</th>
          <th class="fix-width-100 middle text-center">Uom.</th>
          <th class="fix-width-100 middle text-right"></th>
        </tr>
      </thead>

      <tbody id="move-list">
        <?php if(!empty($details)) : ?>
          <?php		$no = 1;						?>
          <?php   $total_qty = 0;  ?>
          <?php		foreach($details as $rs) : 	?>
            <tr class="font-size-11" id="row-move-<?php echo $rs->id; ?>">
              <td class="middle text-center move-no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?></td>
              <td class="middle"><?php echo $rs->ItemName; ?></td>
              <td class="middle text-center"><?php echo $rs->fromBinCode; ?></td>
              <td class="middle text-center"><?php echo $rs->toBinCode; ?></td>
              <td class="middle text-center move-qty"><?php echo number($rs->Qty); ?></td>
              <td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
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
          <td class="middle text-center" id="move-total"><?php echo number($total_qty); ?></td>
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
				<td class="middle text-center" id="move-total">{{ total }}</td>
				<td></td><td></td>
			</tr>
		{{else}}
		<tr class="font-size-11" id="row-move-{{ id }}">
			<td class="middle text-center move-no">{{ no }}</td>
			<td class="middle">{{itemCode}}</td>
			<td class="middle">{{itemName}}</td>
			<td class="middle text-center">{{ from_zone }}</td>
			<td class="middle text-center">{{{ to_zone }}}</td>
			<td class="middle text-center move-qty">{{ qty }}</td>
			<td class="middle text-center">{{ unitMsr }}</td>
			<td class="middle text-center">
        {{#if valid}}
        <button type="button" class="btn btn-minier btn-danger" onclick="deleteMoveItem({{id}},'{{itemCode}}')"><i class="fa fa-trash"></i></button>
        {{/if}}
      </td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>

<?php $this->load->view('include/header'); ?>
<style>
	#result {
		height: calc(100vh - 280px);
		overflow: auto;
	}

	.tableFixHead>thead>tr>th,
	.tableFixHead>tbody>tr>td {
		padding: 3px;
		font-size: 11px;
	}
</style>

<div class="row" style="margin-top:30px;">
	<div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-2 col-xs-12 padding-5">
		<div class="row">
			<div class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 padding-5">
				<label>รหัสสินค้า</label>
				<input type="text" class="width-100 text-center" id="search-text" placeholder="พิมพ์รหัสสินค้า 4 ตัวอักษรขึ้นไป" />
			</div>

			<div class="col-lg-5 col-md-6 col-sm-5 col-xs-12 padding-5">
				<label>คลัง</label>
				<select class="width-100" id="warehouse" name="warehouse" onchange="getStock()">
					<option value="">ทั้งหมด</option>
					<?php echo select_warehouse(); ?>
				</select>
			</div>

			<div class="col-lg-2-harf col-md-2 col-sm-3 col-xs-12 padding-5">
				<label class="display-block not-show">stock</label>
				<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getStock()">ตรวจสอบสต็อก</button>
			</div>
		</div>
	</div>

</div>

<hr class="margin-top-15 margin-bottom-15" />

<div class="row" style="margin-left:-5px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1 table-responsive" style="padding-left:0px;" id="result">

	</div>
</div>


<script id="stock-template" type="text/x-handlebarsTemplate">
	<table class="table table-bordered tableFixHead" style="min-width:1000px;">
	<thead>
		<tr class="font-size-11">
			<th class="fix-width-50 text-center fix-header">#</th>
			<th class="fix-width-100 text-center fix-header">Item Code</th>
			<th class="min-width-300 text-center fix-header">Item Name</th>
			<th class="fix-width-80 text-center fix-header">WhsCode</th>
			<th class="fix-width-200 text-center fix-header">Bin Loc.</th>
			<th class="fix-width-80 text-center fix-header">Quantity</th>
      <th class="fix-width-80 text-center fix-header">Committed Qty</th>
      <th class="fix-width-80 text-center fix-header">Available Qty</th>
		</tr>
	</thead>
	<tbody>
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="8" class="text-center">ไม่พบรายการ</td>
		</tr>
	{{else}}
		<tr class="font-size-11">
			<td class="middle text-center">{{{no}}}</td>
			<td class="middle">{{ ItemCode }}</td>
			<td class="middle hide-text">{{ ItemName }}</td>
			<td class="text-center middle">{{ WhsCode }}</td>
			<td class="text-center middle">{{ BinCode }}</td>
			<td class="text-center middle">{{Qty}}</td>
      <td class="text-center middle">{{Committed}}</td>
      <td class="text-center middle">{{Balance}}</td>
		</tr>
	{{/if}}
{{/each}}
	</tbody>
</table>
</script>

<script>
	$('#warehouse').select2();

	var HOME = BASE_URL + 'main/';

	$('#search-text').keyup(function(e) {
		if (e.keyCode == 13) {
			getStock();
		}
	})

	function getStock() {
		var searchText = $.trim($('#search-text').val());
		var warehouse = $('#warehouse').val();
		if (searchText.length > 1) {
			load_in();
			$.ajax({
				url: HOME + 'get_sell_items_stock',
				type: 'POST',
				cache: 'false',
				data: {
					'search_text': searchText,
					'warehouse_code': warehouse
				},
				success: function(rs) {
					load_out();
					var source = $('#stock-template').html();
					var data = $.parseJSON(rs);
					var output = $('#result');
					render(source, data, output);
				}
			});
		}
	}
</script>

<?php $this->load->view('include/footer'); ?>
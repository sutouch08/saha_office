<?php $this->load->view('include/header'); ?>

<div class="row" style="margin-top:30px;">
  <div class="col-sm-8 col-sm-offset-2 col-xs-12 padding-5">
    <div class="row">
      <div class="col-sm-4 padding-5">
        <label>รหัสสินค้า</label>
        <input type="text" class="form-control input-sm text-center" id="search-text" placeholder="พิมพ์รหัสสินค้า 4 ตัวอักษรขึ้นไป" />
      </div>

      <div class="col-sm-4 col-xs-12 padding-5">
        <label>คลัง</label>
        <select class="form-control input-sm" id="warehouse" name="warehouse" onchange="getStock()">
          <option value="">ทั้งหมด</option>
          <?php echo select_warehouse(); ?>
        </select>
      </div>

      <div class="col-sm-4 col-xs-6 padding-5">
        <label class="display-block not-show">stock</label>
        <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getStock()">ตรวจสอบสต็อก</button>
      </div>
    </div>
  </div>

</div>

<hr class="margin-top-15 margin-bottom-15"/>

<div class="row">
  <div class="col-sm-12" id="result">

  </div>
</div>


<script id="stock-template" type="text/x-handlebarsTemplate">
<table class="table table-bordered">
	<thead>
		<tr class="font-size-12">
			<th class="width-5 text-center">#</th>
			<th class="width-15 text-center">Item Code</th>
			<th class="text-center">Item Name</th>
			<th class="width-15 text-center">WhsCode</th>
			<th class="width-10 text-center">Quantity</th>
      <th class="width-10 text-center">Committed Qty</th>
      <th class="width-10 text-center">Available Qty</th>
		</tr>
	</thead>
	<tbody>
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="7" class="text-center">ไม่พบรายการ</td>
		</tr>
	{{else}}
		<tr>
			<td class="middle text-center">{{{no}}}</td>
			<td class="middle">{{ ItemCode }}</td>
			<td class="middle">{{ ItemName }}</td>
			<td class="text-center middle">{{ WhsCode }}</td>
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
var HOME = BASE_URL + 'main/';

$('#search-text').keyup(function(e){
  if(e.keyCode == 13){
    getStock();
  }
})

function getStock(){
	var searchText = $.trim($('#search-text').val());
  var warehouse = $('#warehouse').val();
	if(searchText.length > 1 ){
		load_in();
		$.ajax({
			url:HOME + 'get_sell_items_stock',
			type:'POST',
			cache:'false',
			data:{
				'search_text' : searchText,
        'warehouse_code' : warehouse
			},
			success:function(rs){
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

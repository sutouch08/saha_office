function getProductInZone() {
	let zone_code = $('#from-zone').val();
	let move_id = $('#id').val();

	if( zone_code.length > 0 ) {

		load_in();

		$.ajax({
			url: HOME + 'get_product_in_zone',
			type:"GET",
      cache:"false",
      data:{
				'move_id' : move_id,
        'zone_code' : zone_code
      },
			success: function(rs) {
				load_out();
				if( isJson(rs) ) {
					var source = $("#zoneTemplate").html();
					var data		= $.parseJSON(rs);
					var output	= $("#zone-list");
					render(source, data, output);
				}
				else {
					showError(rs);
				}
			},
			error:function(rs) {
				showError(rs);
			}
		});
	}
}


function getMoveIn(){
	getTempTable();
}


function addToZone() {
	//---	ไอดีเอกสาร
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	//---	บาร์โค้ดสินค้าที่ยิงมา
	var barcode = $.trim($('#barcode-item-to').val());
	//---	ไอดีโซนปลายทาง
	var zone_code	= $("#to-zone").val();
	var qty = parseDefault(parseFloat($("#qty-to").val()), 0);

	if(barcode.length == 0) {
		return false;
	}

	if( zone_code.length == 0 ){
		swal("กรุณาระบ Location ปลายทาง");
		return false;
	}


	if(qty <= 0) {
		swal("กรุณาระบุจำนวน");
		return false;
	}

	if( qty > 0 ) {
		$.ajax({
			url: HOME + 'move_to_zone',
			type:"POST",
			cache:"false",
			data:{
				"move_id" : move_id,
				"move_code" : move_code,
				"zone_code" : zone_code,
				"qty" : qty,
				"barcode" : barcode
			},
			success: function(rs) {
				if(rs.trim() == 'success') {
					getTempTable();
					$('#qty-to').val(1);
					$('#barcode-item-to').val('');
					$('#barcode-item-to').focus();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
					beep();
				}
			}
		});
	}
}


$("#barcode-item-to").keyup(function(e) {
    if( e.keyCode == 13 ){
			addToZone();
	}
});



function addItemToZone(temp_id) {
	//---	ไอดีโซนปลายทาง
	var binCode	= $("#to-zone").val();

	//---	ไอดีเอกสาร
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	if( binCode.length == 0 ){
		swal("กรุณาระบุ Location ปลายทาง");
		return false;
	}

	var qty = parseDefault(parseFloat($("#inputTempQty-"+temp_id).val()), 0);
	var temp_qty = parseDefault(parseFloat($('#tempQty-'+temp_id).val()), 0);

	if(qty <= 0) {
		swal('กรุณารุบุจำนวน');
		return false;
	}

	if(qty > temp_qty) {
		swal('จำนวนต้องไม่เกินที่มีใน temp');
		return false;
	}

	if( qty > 0 && qty <= temp_qty ) {
		$.ajax({
			url: HOME + 'move_item_to_zone',
			type:"POST",
			cache:"false",
			data:{
				"move_id" : move_id,
				"move_code" : move_code,
				"binCode" : binCode,
				"qty" : qty,
				"temp_id" : temp_id
			},
			success: function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					let curQty = ds.current_qty;

					if(curQty <= 0) {
						$('#row-temp-'+temp_id).remove();
					}
					else {
						$('#tempQty-'+temp_id).val(curQty);
						$('#tempLabel-'+temp_id).text(addCommas(curQty));
						$('#inputTempQty-'+temp_id).val('');
					}

					recalTempTotal();
					reorderTempNo();
				}
				else {
					showError(rs);
				}
			}
		});
	}
}


function recalTempTotal() {
	var total = 0;
	$('.temp-qty').each(function() {
		let qty = parseDefaultFloat($(this).val(), 0);
		total += qty;
	});

	$('#temp-total').text(addCommas(total));
}


function reorderTempNo() {
	var no = 1;
	$('.temp-no').each(function() {
		$(this).text(no);
		no++;
	});
}


$('#item-filter').keyup(function(e) {
	if(e.keyCode === 13) {
		setFromZoneFilter();
	}
});


function setFromZoneFilter() {
	var zone_code  = $("#from-zone").val();
	var itemCode = $('#item-filter').val();
	var move_id = $('#id').val();

	if( zone_code.length > 0 ) {
		$.ajax({
			url: HOME + 'get_product_in_zone',
			type:"GET",
			cache:"false",
			data:{
				'move_id' : move_id,
				'zone_code' : zone_code,
				'itemCode' : itemCode
			},
			success: function(rs){
				if( isJson(rs) ) {
					let source = $("#zoneTemplate").html();
					let data = JSON.parse(rs);
					let output	= $("#zone-list");
					render(source, data, output);
				}
				else {
					showError(rs);
				}
			},
			error:function(rs){
				showError(rs);
			}
		});
	}
}


//--- click button ย้ายออก ในบรรทัด
function addItemToTemp(itemCode, itemName) {
	let move_id = $('#id').val();
	let move_code = $('#code').val();
	let binCode = $('#from-zone').val();
	let qty = parseDefault(parseFloat($('#inputBinQty-'+itemCode).val()), 0);
	let instock = parseDefault(parseFloat($('#binQty-'+itemCode).val()), 0);

	if(binCode.length === 0) {
		swal("กรุณาระบุ Location ต้นทาง");
		return false;
	}

	if(qty > 0) {
		if(qty > instock) {
			swal("ยอดย้ายออกเกินยอดในสต็อก");
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'add_item_to_temp',
			type:'POST',
			cache:false,
			data:{
				'move_id' : move_id,
				'move_code' : move_code,
				'binCode' : binCode,
				'qty' : qty,
				'itemCode' : itemCode,
				'itemName' : itemName
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					$('#binQty-'+itemCode).val(ds.current_qty);
					$('#binLabel-'+itemCode).text(addCommas(ds.current_qty));
					$('#inputBinQty-'+itemCode).val('');

					if(ds.current_qty <= 0) {
						$('#inputBinQty-'+itemCode).attr('disabled', 'disabled');
						$('#btnBin-'+itemCode).attr('disabled', 'disabled');
					}
				}
				else {
					showError(rs);
					beep();
				}
			}
		});
	}
}


//------------------------------------- ยิงบาร์โค้ดสินค้า
function addToTemp() {
	var zone_code	= $("#from-zone").val();
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	if( zone_code.length == 0 ) {
		swal("กรุณาระบุ Location");
		return false;
	}

	//---	จำนวนที่ป้อนมา
	var qty = parseDefault(parseFloat($("#qty-from").val()), 0);

	//---	บาร์โค้ดสินค้า
	var barcode = $.trim($('#barcode-item-from').val());

	//---	เมื่อมีการใส่จำนวนมาตามปกติ
	if( qty > 0) {
		load_in();

		$.ajax({
			url: HOME + 'add_to_temp',
			type:"POST",
			cache:"false",
			data:{
				"move_id" : move_id,
				"move_code" : move_code,
				"from_zone" : zone_code,
				"qty" : qty,
				"barcode" : barcode,
			},
			success: function(rs) {
				load_out();

				if( isJson(rs)) {
					let ds = JSON.parse(rs);
					let itemCode = ds.itemCode;
					let curQty = ds.current_qty;

					//---	ปรับยอดคงเหลือในโซน สำหรับใช้ตรวจสอบการยิงครั้งต่อไป
					$("#binQty-"+itemCode).val(curQty);

					//---	แสดงผลยอดสินค้าคงเหลือในโซน
					$("#binLabel-"+itemCode).text(curQty);

					//---	reset จำนวนเป็น 1
					$("#qty-from").val(1);

					//---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
					$("#barcode-item-from").val('').focus();

				}
				else {
					beep();
					showError(rs);
				}
			}
		});
	}
}


$("#barcode-item-from").keyup(function(e) {
  if( e.keyCode == 13 ){

		let barcode = $(this).val();

		if(barcode.length > 0) {

			addToTemp();
		}
	}
});



//-------  ดึงรายการสินค้าในโซน
function getProductInZone(){
	var zone_code  = $("#from_zone_code").val();
	var move_id = $('#id').val();
	if( zone_code.length > 0 ){
		$.ajax({
			url: HOME + 'get_product_in_zone',
			type:"GET",
      cache:"false",
      data:{
				'move_id' : move_id,
        'zone_code' : zone_code
      },
			success: function(rs){
				var rs = 	$.trim(rs);
				if( isJson(rs) ){
					var source = $("#zoneTemplate").html();
					var data		= $.parseJSON(rs);
					var output	= $("#zone-list");
					render(source, data, output);
				}
			}
		});
	}
}


$("#from-zone").autocomplete({
	source: HOME + 'get_move_zone/',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var rs = rs.split(' | ');
		if( rs.length == 2 ){
			$("#from_zone_code").val(rs[0]);
			//--- แสดงชื่อโซนใน text box
			$(this).val(rs[1]);
			//---	แสดงชื่อโซนที่ หัวตาราง
			$('#zoneName').text(rs[1]);
		}else{

			$("#from_zone_code").val('');
			//---	ชื่อโซนที่ หัวตาราง
			$('#zoneName').text('');
			$(this).val('');
		}
	}
});


$("#from-zone").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){
			getProductInZone();
		}, 100);
	}
});


$("#to-zone").autocomplete({
	source: HOME + 'get_move_zone/',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var rs = rs.split(' | ');
		if( rs.length == 2 ){
			$("#to_zone_code").val(rs[0]);
			$(this).val(rs[1]);
		}else{
			$("#to_zone_code").val('');
			$(this).val('');
		}
	}
});



function getMoveIn(){
	getTempTable();
}



//---	เปลี่ยนโซนปลายทาง
function newToZone(){
	$('#toZone-barcode').removeAttr('disabled');
	$('#btn-new-to-zone').addClass('hide');
	$('#btn-set-to-zone').removeClass('hide');
	$('#zoneName-label').text('');
	$("#to_zone_code").val("");
	$("#toZone-barcode").val("");
	$("#zone-table").addClass('hide');
	$('#btn-add-to-zone').attr('disabled', 'disabled');
	$("#toZone-barcode").focus();
}


//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneTo() {
	var zone_code = $("#toZone-barcode").val();
	if( zone_code.length > 0 ){
		$.ajax({
			url: HOME + 'is_exists_zone',
			type:"GET",
			cache:"false",
			data:{
				"zone_code" : zone_code
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( rs == 'ok' ){

					//---	update id โซนปลายทาง
					$("#to_zone_code").val(zone_code);

					//---	disabled ช่องยิงบาร์โค้ดโซน
					$("#toZone-barcode").attr('disabled', 'disabled');

					//--- active new zone button
					$('#btn-set-to-zone').addClass('hide');
					$('#btn-new-to-zone').removeClass('hide');

					$('#qty-to').removeAttr('disabled');

					$('#barcode-item-to').removeAttr('disabled');

					$('#btn-add-to-zone').removeAttr('disabled');

					$('#barcode-item-to').focus();

				}
				else {

					swal("ข้อผิดพลาด", rs, "error");

					//---	ลบไอดีโซนปลายทาง
					$("#to_zone_code").val("");

					//--- ซ่อนตารางสินค้าในโซน
					$("#zone-table").addClass('hide');

					beep();
				}
			}
		});
	}
}




$("#toZone-barcode").keyup(function(e) {
    if( e.keyCode == 13 ){
		getZoneTo();
		setTimeout(function() {
			$("#barcode-item-to").focus();
		}, 500);
	}
});



function addToZone() {
	//---	ไอดีเอกสาร
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	//---	บาร์โค้ดสินค้าที่ยิงมา
	var barcode = $.trim($('#barcode-item-to').val());
	//---	ไอดีโซนปลายทาง
	var zone_code	= $("#to_zone_code").val();
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
				var rs = $.trim(rs);
				if(rs == 'success') {
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
	var binCode	= $("#to_zone_code").val();

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
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					var curQty = ds.current_qty;

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
					swal({
						title:"Error!",
						text:rs,
						type:'error'
					});
				}
			}
		});
	}
}


function recalTempTotal() {
	var total = 0;
	$('.temp-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).val()), 0);
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





//-------	เปิดกล่องควบคุมสำหรับยิงบาร์โค้ดโซนต้นทาง
function getMoveOut(){
	setTimeout(function() {
		$("#fromZone-barcode").focus();
	}, 200);
}



//---	เปลี่ยนโซนต้นทาง
function newFromZone(){
	$("#from_zone_code").val("");
	$("#fromZone-barcode").val("");
	$('#zone-list').html('');
	$('#fromZone-barcode').removeAttr('disabled');
	$('#btn-new-zone').addClass('hide');
	$('#btn-set-zone').removeClass('hide');
	$('#qty-from').attr('disabled', 'disabled');
	$('#barcode-item-from').attr('disabled', 'disabled');
	$('#btn-add-temp').attr('disabled', 'disabled');
	$("#fromZone-barcode").focus();
}




//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneFrom() {

	var barcode = $("#fromZone-barcode").val();

	if( barcode.length > 0 ){

		$.ajax({
			url:HOME + 'is_exists_zone',
			type:"GET",
			cache:"false",
			data:{
				"zone_code" : barcode
			},
			success: function(rs){

				var rs = $.trim(rs);

				if( rs == "ok") {
					//---	update id โซนต้นทาง
					$("#from_zone_code").val(barcode);

					//---	update ชื่อโซน
					//$("#zoneName").text(barcode);

					$("#fromZone-barcode").attr('disabled', 'disabled');
					$('#btn-set-zone').addClass('hide');
					$('#btn-new-zone').removeClass('hide');
					$('#qty-from').removeAttr('disabled');
					$('#barcode-item-from').removeAttr('disabled');
					$('#btn-add-temp').removeAttr('disabled');
					$('#barcode-item-from').focus();

					//---	แสดงรายการสินค้าในโซน
					getProductInZone();

				}
				else {
					swal("Error!", rs, "error");

					//---	ลบไอดีโซนต้นทาง
					$("#from_zone_code").val("");

					//---	ไม่แสดงชื่อโซน
					$('#zoneName').val('');

					beep();
				}
			}
		});
	}
}



$('#fromZone-barcode').autocomplete({
	source:HOME + 'get_move_zone',
	autoFocus:true,
	close:function() {
		let binCode = $(this).val();
		if(binCode == "not found") {
			$(this).val('');
		}
	}
});



$("#fromZone-barcode").keyup(function(e) {
    if( e.keyCode == 13 ){
		getZoneFrom();
		setTimeout(function() {
			$("#barcode-item-from").focus();
		}, 500);
	}
});


$('#item-filter').keyup(function(e) {
	if(e.keyCode === 13) {
		setFromZoneFilter();
	}
});



function setFromZoneFilter() {
	var zone_code  = $("#from_zone_code").val();
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
				var rs = 	$.trim(rs);
				if( isJson(rs) ) {
					var source = $("#zoneTemplate").html();
					var data		= $.parseJSON(rs);
					var output	= $("#zone-list");
					render(source, data, output);
				}
			}
		});
	}
}


//--- click button ย้ายออก ในบรรทัด
function addItemToTemp(itemCode, itemName) {
	let move_id = $('#id').val();
	let move_code = $('#code').val();
	let binCode = $('#from_zone_code').val();
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

				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);

					$('#binQty-'+itemCode).val(ds.current_qty);
					$('#binLabel-'+itemCode).text(addCommas(ds.current_qty));
					$('#inputBinQty-'+itemCode).val('');

					if(ds.current_qty <= 0) {
						$('#inputBinQty-'+itemCode).attr('disabled', 'disabled');
						$('#btnBin-'+itemCode).attr('disabled', 'disabled');
					}

				}
				else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					});

					beep();
				}
			}
		});
	}
}

//------------------------------------- ยิงบาร์โค้ดสินค้า

function addToTemp() {
	//---	โซนต้นทาง
	var zone_code	= $("#from_zone_code").val();

	//---	ID เอกสาร
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	//---	ตรวจสอบว่ายิงบาร์โค้ดโซนมาแล้วหรือยัง
	if( zone_code.length == 0 ) {
		swal("กรุณาระบุ Location");
		return false;
	}

	//---	จำนวนที่ป้อนมา
	var qty = parseDefault(parseFloat($("#qty-from").val()), 0);

	//---	บาร์โค้ดสินค้า
	var barcode = $.trim($('#barcode-item-from').val());

	//---	เมื่อมีการใส่จำนวนมาตามปกติ
	if( qty != '' && qty != 0 ){

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
			success: function(rs){

				if( isJson(rs)) {

					ds = $.parseJSON(rs);
					itemCode = ds.itemCode;
					curQty = ds.current_qty;

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
					swal("Error", rs, "error");
					beep();
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

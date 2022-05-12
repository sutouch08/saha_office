

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
					$("#move-table").addClass('hide');
					$("#zone-table").removeClass('hide');
					inputQtyInit();
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



//------- สลับไปแสดงหน้า move_detail
function showMoveTable(){
	getMoveTable();
	hideZoneTable();
	hideTempTable();
	hideMoveIn();
	hideMoveOut();
	hideLine();
	
	$("#move-table").removeClass('hide');
}



function hideMoveTable(){
	$("#move-table").addClass('hide');
}


function showMoveIn(){
	$(".moveIn-zone").removeClass('hide');
}


function hideMoveIn(){
	$(".moveIn-zone").addClass('hide');
}


function showMoveOut(){
	$(".moveOut-zone").removeClass('hide');
}



function hideMoveOut(){
	$(".moveOut-zone").addClass('hide');
}


function showLine() {
	$('#barcode-hr').removeClass('hide');
}


function hideLine() {
	$('#barcode-hr').addClass('hide');
}


function showTempTable(){
	getTempTable();
	hideMoveTable();
	hideZoneTable();
	$("#temp-table").removeClass('hide');
}



function hideTempTable(){
	$("#temp-table").addClass('hide');
}



function showZoneTable(){
	$("#zone-table").removeClass('hide');
}



function hideZoneTable(){
	$("#zone-table").addClass('hide');
}



function inputQtyInit(){
	$('.input-qty').keyup(function(){
		var qty = parseDefault(parseFloat($(this).val()), 0);
		var limit = parseDefault(parseFloat($(this).attr('max')), 0);

		if(qty > limit)
		{
			swal('โอนได้ไม่เกิน ' + limit);
			$(this).val(limit);
		}
	})
}





function getMoveIn(){
	showMoveIn();
	hideMoveOut();
	hideMoveTable();
	showTempTable();
	showLine();
	$("#toZone-barcode").focus();
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
function getZoneTo(){

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

					showTempTable();

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

	//---	บาร์โค้ดสินค้าที่ยิงมา
	var barcode = $('#barcode-item-to').val();

	//---	ไอดีโซนปลายทาง
	var zone_code	= $("#to_zone_code").val();

	//---	ไอดีเอกสาร
	var id = $("#id").val();
	var code = $('#code').val();

	if( zone_code.length == 0 ){
		swal("กรุณาระบุโซนปลายทาง");
		return false;
	}

	var qty = parseDefault(parseFloat($("#qty-to").val()), 0);

	var curQty	= parseDefault(parseFloat($("#qty-"+barcode).val()), 0);

	$('#barcode-item-to').val('');

	if( isNaN(curQty) ){
		swal("สินค้าไม่ถูกต้อง");
		return false;
	}



	if( qty != '' && qty != 0 ){
		if( qty <= curQty ){
			$.ajax({
				url: HOME + 'move_to_zone',
				type:"POST",
				cache:"false",
				data:{
					"id" : id,
					"move_code" : code,
					"zone_code" : zone_code,
					"qty" : qty,
					"barcode" : barcode
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success'){
						curQty = curQty - qty;
						if(curQty == 0 ){
							getTempTable();
						}else{
							$("#qty-label-"+barcode).text(curQty);
							$("#qty-"+barcode).val(curQty);
						}
						$("#qty-to").val(1);
						$("#barcode-item-to").focus();
					}else{
						swal("ข้อผิดพลาด", rs, "error");
						beep();
					}
				}
			});
		}else{
			swal("จำนวนในโซนไม่เพียงพอ");
			beep();
		}
	}
}


$("#barcode-item-to").keyup(function(e) {

    if( e.keyCode == 13 ){
			addToZone();
	}
});






//-------	เปิดกล่องควบคุมสำหรับยิงบาร์โค้ดโซนต้นทาง
function getMoveOut(){

	hideMoveIn();
	hideTempTable();
	hideMoveTable();
	showMoveOut();
	showZoneTable();
	showLine();
	$("#fromZone-barcode").focus();
}



//---	เปลี่ยนโซนต้นทาง
function newFromZone(){
	$("#from_zone_code").val("");
	$("#fromZone-barcode").val("");
	$("#zone-table").addClass('hide');
	$('#fromZone-barcode').removeAttr('disabled');
	$('#btn-new-zone').addClass('hide');
	$('#btn-set-zone').removeClass('hide');
	$('#qty-from').attr('disabled', 'disabled');
	$('#barcode-item-from').attr('disabled', 'disabled');
	$('#btn-add-temp').attr('disabled', 'disabled');
	$("#fromZone-barcode").focus();
}




//---	ดึงข้อมูลสินค้าในโซนต้นทาง
function getZoneFrom(){

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

				}else{
					swal("Error!", rs, "error");

					//---	ลบไอดีโซนต้นทาง
					$("#from_zone_code").val("");

					//---	ไม่แสดงชื่อโซน
					$('#zoneName').val('');

					$("#zone-table").addClass('hide');

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


//------------------------------------- ยิงบาร์โค้ดสินค้า

function addToTemp() {
	//---	โซนต้นทาง
	var zone_code	= $("#from_zone_code").val();

	//---	ID เอกสาร
	var move_id = $("#id").val();
	var move_code = $('#code').val();

	//---	ตรวจสอบว่ายิงบาร์โค้ดโซนมาแล้วหรือยัง
	if( zone_code.length == 0 ){
		swal("กรุณาระบุโซนต้นทาง");
		return false;
	}

	//---	จำนวนที่ป้อนมา
	var qty = parseDefault(parseFloat($("#qty-from").val()), 0);

	//---	บาร์โค้ดสินค้า
	var barcode = $.trim($('#barcode-item-from').val());

	//---	จำนวนในโซน ลบ กับยอดใน temp
	var curQty	= parseDefault(parseFloat($("#qty_"+barcode).val()), 0);

	//---	เคลียร์ช่องให้พร้อมยิงตัวต่อไป
	$('#barcode-item-from').val('');

	//---	เมื่อมีการใส่จำนวนมาตามปกติ
	if( qty != '' && qty != 0 ){

		//---	ถ้าจำนวนที่ใส่มา น้อยกว่าหรือเท่ากับ จำนวนที่มีอยู่
		//---	หรือ โซนนี้สามารถติดลบได้และติ๊กว่าให้ติดลบได้
		//---	หากโซนนี้ไม่สามารถติดลบได้ ถึงจะติ๊กให้ติดลบได้ก็ไม่สามารถให้ติดลบได้
		if( qty <= curQty ) {
			//---	เพิ่มรายการเข้า temp
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
						//--- ลดยอดสินค้าคงเหลือในโซนบนหน้าเว็บ (ในฐานข้อมูลถูกลดแล้ว)
						curQty = ds.current_qty;

						//---	แสดงผลยอดสินค้าคงเหลือในโซน
						$("#qty-label_"+barcode).text(curQty);

						//---	ปรับยอดคงเหลือในโซน สำหรับใช้ตรวจสอบการยิงครั้งต่อไป
						$("#qty_"+barcode).val(curQty);

						//---	reset จำนวนเป็น 1
						$("#qty-from").val(1);

						//---	focus ที่ช่องยิงบาร์โค้ด รอการยิงต่อไป
						$("#barcode-item-from").focus();

					}
					else {

						swal("Error", rs, "error");
						beep();
					}
				}
			});
		}
		else {
			swal("จำนวนในโซนไม่เพียงพอ");
			beep();
		}
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

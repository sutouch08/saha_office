function doExport()
{
	var id = $('#id').val();

	load_in();

	$.ajax({
		url:HOME + 'export_move/' + id,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	});
}



function save(temp_status) {

	if(temp_status === undefined) {
		check_temp();
	}
	else {

		load_in();

		const move_id = $('#id').val();

		$.ajax({
			url:HOME + 'save/'+move_id,
			type:'POST',
			cache:false,
			success:function(rs) {
				load_out();

				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function() {
						goDetail(move_id);
					}, 1500);
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		});
	}
}



function check_temp() {
	const move_id = $('#id').val();
	$.ajax({
		url:HOME + 'is_exists_temp/'+move_id,
		type:'GET',
		cache:false,
		success:function(rs) {
			if(rs === 'success') {
				save(move_id);
			}
			else {
				swal({
					title:"Error",
					text:rs,
					type:'error'
				});
			}
		}
	});
}


function deleteMoveItem(id, code)
{
	const move_id = $('#id').val();

  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'delete_detail',
			type:"POST",
      cache:"false",
			data:{
				"id" : id,
				"move_id" : move_id
			},
			success: function(rs) {
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						type: 'success',
						timer: 1000
					});

					getMoveTable();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		});
	});
}



function deleteTemp(id, name) {
	const move_id = $('#id').val();

  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ name +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function() {
		$.ajax({
			url:HOME + 'delete_temp',
			type:"POST",
      cache:"false",
			data:{
				"id" : id,
				"move_id" : move_id
			},
			success: function(rs) {
				var rs = $.trim(rs);
				if( rs == 'success' ) {
					swal({
						title:'Success',
						type: 'success',
						timer: 1000
					});

					getTempTable();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		});
	});
}


//------------  ตาราง move_detail
function getMoveTable(){
	var id	= $("#id").val();
	$.ajax({
		url: HOME + 'get_move_table/'+ id,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#moveTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#move-list");
				render(source, data, output);
			}
		}
	});
}




function getTempTable(){
	var id = $("#id").val();
	load_in();
	$.ajax({
		url: HOME + 'get_temp_table/'+id,
		type:"GET",
    cache:"false",
		success: function(rs){
			load_out();

			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);

				setTimeout(function() {
					$("#toZone-barcode").focus();
				}, 200);
			}
		}
	});
}




//--- เพิ่มรายการลงใน move detail
//---	เพิ่มลงใน move_temp
//---	update stock ตามรายการที่ใส่ตัวเลข
function addToMove(){
	var code	= $('#move_code').val();

	//---	โซนต้นทาง
	var from_zone = $("#from_zone_code").val();

	if(from_zone.length == 0)
	{
		swal('โซนต้นทางไม่ถูกต้อง');
		return false;
	}

	//--- โซนปลายทาง
	var to_zone = $('#to_zone_code').val();
	if(to_zone.length == 0)
	{
		swal('โซนปลายทางไม่ถูกต้อง');
		return false;
	}

	//---	จำนวนช่องที่มีการป้อนตัวเลขเพื่อย้ายสินค้าออก
	var count  = countInput();
	if(count == 0)
	{
		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');
		return false;
	}

	//---	ตัวแปรสำหรับเก็บ ojbect ข้อมูล
	var ds  = [];

	ds.push(
		{'name' : 'move_code', 'value' : code},
		{'name' : 'from_zone', 'value' : from_zone},
		{'name' : 'to_zone', 'value' : to_zone}
	);

	no = 0;
	var items = [];
	$('.input-qty').each(function(index, element) {
	    var qty = $(this).val();
			if( qty != '' && qty != 0 ){
				var pd_code  = $(this).data('products')
				item = {"code" : pd_code, "qty" : qty };
				items.push(item);
			}
    });

		ds.push({"name" : "items", "value" : JSON.stringify(items)});
		console.log(ds);
		//return false;

	if( count > 0 ){
		load_in();
		setTimeout(function(){
			$.ajax({
				url: HOME + 'add_to_move',
				type:"POST",
				cache:"false",
				data: ds ,
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'success',
							text: 'เพิ่มรายการเรียบร้อยแล้ว',
							type: 'success',
							timer: 1000
						});

						setTimeout( function(){
							showMoveTable();
						}, 1200);

					}else{

						swal("ข้อผิดพลาด", rs, "error");
					}
				}
			});
		}, 500);
	}
	else
	{

		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');

	}
}





function selectAll(){
	$('.input-qty').each(function(index, el){
		var qty = $(this).attr('max');
		$(this).val(qty);
	});
}


function clearAll(){
	$('.input-qty').each(function(index, el){
		$(this).val('');
	});
}




//----- นับจำนวน ช่องที่มีการใส่ตัวเลข
function countInput(){
	var count = 0;
	$(".input-qty").each(function(index, element) {
        count += ($(this).val() == "" ? 0 : 1 );
    });
	return count;
}

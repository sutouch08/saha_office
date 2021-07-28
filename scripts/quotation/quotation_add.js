
function saveAdd() {
	var ds = {
		//---- Right column
		'SlpCode' : $('#sale_id').val(),
		'CardCode' : $.trim($('#CardCode').val()),  //****** required
		'CardName' : $('#CardName').val(),
		'GroupNum' : $('#GroupNum').val(),
		'term' : $('#Payment').val(),
		'Contact' : $('#Contact').val(),
		'CustRef' : $.trim($('#NumAtCard').val()),
		'Department' : $('#Department').val(), //****** required
		'Division' : $('#Division').val(), //****** required
		'ShipToCode' : $('#shipToCode').val(),
		'ShipTo' : $('#ShipTo').val(),
		//--- right Column
		'Series' : $('#Series').val(), //****** required
		'DocDate' : $('#DocDate').val(), //****** required
		'DocDueDate' : $('#DocDueDate').val(), //****** required
		'TextDate' : $('#TextDate').val(), //****** required
		'PayToCode' : $('#billToCode').val(),
		'BillTo' : $('#BillTo').val(),
		//---- footer
		'owner' : $('#owner').val(),
		'comments' : $.trim($('#comments').val()),
		'discPrcnt' : $('#discPrcnt').val(),
		'roundDif' : $('#roundDif').val(),
		'tax' : removeCommas($('#tax').val()), //-- VatSum
		'docTotal' : removeCommas($('#docTotal').val())
	}

		//--- check required parameter
	if(ds.CardCode.length === 0) {
		swal("กรุณาระบุลูกค้า");
		$('#CardCode').addClass('has-error');
		return false;
	}
	else {
		$('#CardCode').removeClass('has-error');
	}


	if(ds.Department.length === 0) {
		swal("กรุณาระบุ ฝ่าย");
		$('#Department').addClass('has-error');
		return false;
	}
	else {
		$('#Department').removeClass('has-error');
	}


	if(ds.Division.length === 0) {
		swal("กรุณาระบุ แผนก");
		$('#Division').addClass('has-error');
		return false;
	}
	else {
		$('#Division').removeClass('has-error');
	}

	if(ds.Series.length === 0) {
		swal("Series No. is not defined");
		$('#Series').addClass('has-error');
		return false;
	}
	else {
		$('#Series').removeClass('has-error');
	}


	if(!isDate(ds.DocDate)) {
		swal("Invalid Posting Date");
		$('#DocDate').addClass('has-error');
		return false;
	}
	else {
		$('#DocDate').removeClass('has-error');
	}


	if(!isDate(ds.DocDueDate)) {
		swal("Invalid Valid Until Date");
		$('#DocDueDate').addClass('has-error');
		return false;
	}
	else {
		$('#DocDueDate').removeClass('has-error');
	}

	if(!isDate(ds.TextDate)) {
		swal("Invalid Document Date");
		$('#TextDate').addClass('has-error');
		return false;
	}
	else {
		$('#TextDate').removeClass('has-error');
	}

	var disc_error = 0;
	//--- check discount
	$('.input-disc1').each(function() {
		let val = parseDefault(parseFloat($(this).val()), 0);

		if(val > 100 || val < 0) {
			$(this).addClass('has-error');
			disc_error++;
		}
		else {
			$(this).removeClass('has-error');
		}
	})

	if(disc_error > 0) {
		swal({
			title:'Invalid Discount',
			type:'error'
		});

		return false;
	}


	//---- get rows details
	var count = 0;
	var details = [];
	var lineNum = 0;

	$('.toggle-text').each(function() {
		let no = getNo($(this));
		let type = $(this).val();
		if(type == '0') {
			let itemCode = $('#itemCode-'+no).val();
			if(itemCode.length > 0) {

				//--- ถ้ามีการระบุข้อมูล
				var row = {
					"Type" : 0,
					"LineNum" : lineNum,
					"ItemCode" : itemCode,
					"Description" : $('#itemName-'+no).val(),
					"Text" : $('#itemDetail-'+no).val(),
					"FreeTxt" : $('#freeText-'+no).val(),
					"Quantity" : removeCommas($('#qty-'+no).val()),
					"UomCode" : $('#uom-'+no).find(':selected').data('code'),
					"basePrice" : $('#basePrice-'+no).val(), //--- ราคาตามหน่วยนับย่อย
					"stdPrice" : removeCommas($('#stdPrice-'+no).val()), //--- ราคาตามหน่วยย่อย * ตัวคูณตามหน่วยนับที่เลือก เช่น ราคาต่อชิ้น = 100 * (ชิ้น = 1, แพ็ค = 3, ลัง = 6)
					"Price" : removeCommas($('#price-'+no).val()),
					"priceDiffPercent" : $('#priceDiff-'+no).val(),
					'sellPrice' : removeCommas($('#priceAfDiscBfTax-'+no).val()),
					"DiscPrcnt" : $('#lineDiscPrcnt-'+no).val(), //--- ส่วนลดได้จากการเอาส่วนลด 2 สเต็ป มาแปลงเป็นส่วนลดเดียว
					"LineTotal" : removeCommas($('#lineAmount-'+no).val()),
					"WhsCode" : $('#whs-'+no).val(),
					"VatPrcnt" : $('#taxCode-'+no).data('rate'), //--- Vat rate
					"VatGroup" : $('#taxCode-'+no).val(), //--- รหัส vat group
					"U_DISWEB" : $('#disc1-'+no).val(),
					"U_DISCEX" : 0,
					"LineText" : "",
					"AfLineNum" : -1,
				}

				details.push(row);
				count++;
				lineNum++;
			}
		}
		else {
			text = $('#text-'+no).val();
			if(text.length > 0) {
				var row = {
					"Type" : 1,
					"LineNum" : 0,
					"ItemCode" : "",
					"Description" : "",
					"Text" : "",
					"FreeTxt" : "",
					"Quantity" : 0,
					"UomCode" : "",
					"basePrice" : 0,
					"stdPrice" : 0,
					"Price" : 0,
					"sellPrice" : 0,
					"DiscPrcnt" : 0, //--- ส่วนลดได้จากการเอาส่วนลด 2 สเต็ป มาแปลงเป็นส่วนลดเดียว
					"WhsCode" : "",
					"VatPrcnt" : 0, //--- Vat rate
					"VatGroup" : "", //--- รหัส vat group
					"U_DISWEB" : 0,
					"U_DISCEX" : 0,
					"LineText" : text,
					"AfLineNum" : lineNum - 1,
				}

				details.push(row);
				count++;
			}
		}
	}); //--- end each function


	if(count === 0) {
		swal("ไม่พบรายการสินค้า");
		return false;
	}


	//--- หากไม่มีข้อผิดพลาด

	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"header" : JSON.stringify(ds),
			"details" : JSON.stringify(details)
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				if(ds.result === 'success') {
					swal({
						title:'Success',
						text:'Insert new Quotation successfully',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goDetail(ds.message);
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
			}
			else {
				swal({
					title:'Error!',
					text:'Unknow error please contact administrator',
					type:'error'
				});

			}
		}
	})

}


function update() {
	var code = $('#code').val();
	var ds = {
		//---- Right column
		'SlpCode' : $('#sale_id').val(),
		'CardCode' : $.trim($('#CardCode').val()),  //****** required
		'CardName' : $('#CardName').val(),
		'GroupNum' : $('#GroupNum').val(),
		'term' : $('#Payment').val(),
		'Contact' : $('#Contact').val(),
		'CustRef' : $.trim($('#NumAtCard').val()),
		'Department' : $('#Department').val(), //****** required
		'Division' : $('#Division').val(), //****** required
		'ShipToCode' : $('#shipToCode').val(),
		'ShipTo' : $('#ShipTo').val(),
		//--- right Column
		'Series' : $('#Series').val(), //****** required
		'DocDate' : $('#DocDate').val(), //****** required
		'DocDueDate' : $('#DocDueDate').val(), //****** required
		'TextDate' : $('#TextDate').val(), //****** required
		'PayToCode' : $('#billToCode').val(),
		'BillTo' : $('#BillTo').val(),
		//---- footer
		'owner' : $('#owner').val(),
		'comments' : $.trim($('#comments').val()),
		'discPrcnt' : $('#discPrcnt').val(),
		'roundDif' : $('#roundDif').val(),
		'tax' : removeCommas($('#tax').val()), //-- VatSum
		'docTotal' : removeCommas($('#docTotal').val())
	}

		//--- check required parameter
	if(ds.CardCode.length === 0) {
		swal("กรุณาระบุลูกค้า");
		$('#CardCode').addClass('has-error');
		return false;
	}
	else {
		$('#CardCode').removeClass('has-error');
	}

	if(ds.Department.length === 0) {
		swal("กรุณาระบุ ฝ่าย");
		$('#Department').addClass('has-error');
		return false;
	}
	else {
		$('#Department').removeClass('has-error');
	}


	if(ds.Division.length === 0) {
		swal("กรุณาระบุ แผนก");
		$('#Division').addClass('has-error');
		return false;
	}
	else {
		$('#Division').removeClass('has-error');
	}

	if(ds.Series.length === 0) {
		swal("Series No. is not defined");
		$('#Series').addClass('has-error');
		return false;
	}
	else {
		$('#Series').removeClass('has-error');
	}


	if(!isDate(ds.DocDate)) {
		swal("Invalid Posting Date");
		$('#DocDate').addClass('has-error');
		return false;
	}
	else {
		$('#DocDate').removeClass('has-error');
	}


	if(!isDate(ds.DocDueDate)) {
		swal("Invalid Valid Until Date");
		$('#DocDueDate').addClass('has-error');
		return false;
	}
	else {
		$('#DocDueDate').removeClass('has-error');
	}

	if(!isDate(ds.TextDate)) {
		swal("Invalid Document Date");
		$('#TextDate').addClass('has-error');
		return false;
	}
	else {
		$('#TextDate').removeClass('has-error');
	}


	var disc_error = 0;
	//--- check discount
	$('.input-disc1').each(function() {
		let val = parseDefault(parseFloat($(this).val()), 0);

		if(val > 100 || val < 0) {
			$(this).addClass('has-error');
			disc_error++;
		}
		else {
			$(this).removeClass('has-error');
		}
	})

	if(disc_error > 0) {
		swal({
			title:'Invalid Discount',
			type:'error'
		});

		return false;
	}

	//---- get rows details
	var count = 0;
	var lineNum = 0;
	var details = [];

	$('.toggle-text').each(function() {
		let no = getNo($(this));
		let type = $(this).val();
		if(type == '0') {
			let itemCode = $('#itemCode-'+no).val();
			if(itemCode.length > 0) {
				//--- ถ้ามีการระบุข้อมูล
				var row = {
					"Type" : 0,
					"LineNum" : lineNum,
					"ItemCode" : itemCode,
					"Description" : $('#itemName-'+no).val(),
					"Text" : $('#itemDetail-'+no).val(),
					"FreeTxt" : $('#freeText-'+no).val(),
					"Quantity" : removeCommas($('#qty-'+no).val()),
					"UomCode" : $('#uom-'+no).find(':selected').data('code'),
					"basePrice" : $('#basePrice-'+no).val(), //--- ราคาตามหน่วยนับย่อย
					"stdPrice" : removeCommas($('#stdPrice-'+no).val()), //--- ราคาตามหน่วยย่อย * ตัวคูณตามหน่วยนับที่เลือก เช่น ราคาต่อชิ้น = 100 * (ชิ้น = 1, แพ็ค = 3, ลัง = 6)
					"Price" : removeCommas($('#price-'+no).val()),
					"priceDiffPercent" : $('#priceDiff-'+no).val(),
					'sellPrice' : removeCommas($('#priceAfDiscBfTax-'+no).val()),
					"DiscPrcnt" : $('#lineDiscPrcnt-'+no).val(), //--- ส่วนลดได้จากการเอาส่วนลด 2 สเต็ป มาแปลงเป็นส่วนลดเดียว
					"LineTotal" : removeCommas($('#lineAmount-'+no).val()),
					"WhsCode" : $('#whs-'+no).val(),
					"VatPrcnt" : $('#taxCode-'+no).data('rate'), //--- Vat rate
					"VatGroup" : $('#taxCode-'+no).val(), //--- รหัส vat group
					"U_DISWEB" : $('#disc1-'+no).val(),
					"U_DISCEX" : 0,
					"LineText" : "",
					"AfLineNum" : 0,
				}

				details.push(row);
				count++;
				lineNum++;
			}
		}
		else {
			var  text = $('#text-'+no).val();
			if(text.length > 0) {
				var row = {
					"Type" : 1,
					"LineNum" : 0,
					"ItemCode" : "",
					"Description" : "",
					"Text" : "",
					"FreeTxt" : "",
					"Quantity" : 0,
					"UomCode" : "",
					"basePrice" : 0,
					"stdPrice" : 0,
					"Price" : 0,
					"priceDiffPercent" : 0,
					'sellPrice' : 0,
					"DiscPrcnt" : 0,
					"LineTotal" : 0,
					"WhsCode" : "",
					"VatPrcnt" : 0,
					"VatGroup" : "",
					"U_DISWEB" : 0,
					"U_DISCEX" : 0,
					"LineText" : text,
					"AfLineNum" : lineNum - 1
				}

				details.push(row);
				count++;
			}
		}
	}); //--- end each function


	if(count === 0) {
		swal("ไม่พบรายการสินค้า");
		return false;
	}

	//--- หากไม่มีข้อผิดพลาด

	// console.log(ds);
	// console.log(details);
	// console.log(textRow);
	load_in();
	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"header" : JSON.stringify(ds),
			"details" : JSON.stringify(details)
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				if(ds.result === 'success') {
					swal({
						title:'Success',
						text:'Insert new Quotation successfully',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						goDetail(ds.message);
					}, 1200);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
			}
			else {
				swal({
					title:'Error!',
					text:'Unknow error please contact administrator',
					type:'error'
				});

				console.log(rs);
			}
		}
	})

}


function updateShipTo() {
	var ds = {
		'address' : $('#s_address').val(),
		'block' : $('#sBlock').val(),
		'street' : $('#sStreet').val(),
		'subDistrict' : $('#sSubDistrict').val(),
		'district' : $('#sDistrict').val(),
		'province' : $('#sProvince').val(),
		'country' : $('#sCountry').val(),
		'countryName' : $('#sCountry option:selected').text(),
		'postcode' : $('#sPostCode').val()
	};

	var shipTo = "";
	shipTo += (ds.block == "" ? "" : ds.block + " ");
	shipTo += (ds.street == "" ? "" : ds.street+" ");
	shipTo += (ds.subDistrict == "" ? "" : ds.subDistrict+" ");
	shipTo += (ds.district == "" ? "" : ds.district+" ");
	shipTo += (ds.province == "" ? "" : ds.province+" ");
	shipTo += (ds.postcode == "" ? "" : ds.postcode + " ");

	if(ds.country !== "TH") {
		shipTo += ds.countryName;
	}


	$('#ShipTo').val(shipTo);
	$('#shipToModal').modal('hide');
}



function updateBillTo() {
	var ds = {
		'address' : $('#b_address').val(),
		'block' : $('#bBlock').val(),
		'street' : $('#bStreet').val(),
		'subDistrict' : $('#bSubDistrict').val(),
		'district' : $('#bDistrict').val(),
		'province' : $('#bProvince').val(),
		'country' : $('#bCountry').val(),
		'countryName' : $('#bCountry option:selected').text(),
		'postcode' : $('#bPostCode').val()
	};

	var billTo = "";
	billTo += (ds.block == "" ? "" : ds.block + " ");
	billTo += (ds.street == "" ? "" : ds.street+" ");
	billTo += (ds.subDistrict == "" ? "" : ds.subDistrict + " ");
	billTo += (ds.district == "" ? "" : ds.district + " ");
	billTo += (ds.province == "" ? "" : ds.province + " ");
	billTo += (ds.postcode == "" ? "" : ds.postcode + " ");

	if(ds.country !== "TH") {
		billTo += ds.countryName;
	}

	$('#BillTo').val(billTo);
	$('#billToModal').modal('hide');
}



$('#CardCode').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var cust = rs.split(' | ');
		if(cust.length === 2) {
			let code = cust[0];
			let name = cust[1];
			$('#CardCode').val(code);
			$('#CardName').val(name);

			//--- get payment term
			get_payment_term(code); //--- OCTG.GroupNum

			//--- get priceList
			get_price_list(code);

			//---- create contact person dropdown
			get_contact_person(code);

			//---- create Address ship to
			get_address_ship_to_code(code);

			//---- create Address bill to
			get_address_bill_to_code(code);

			//--- get sale man from OCRD
			get_sale_man(code);
		}
		else {
			$('#CardCode').val('');
			$('#CardName').val('');
		}
	}
})


function get_price_list(code) {
	$.ajax({
		url:HOME + 'get_customer_price_list',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			$('#priceList').val(rs);
		}
	})
}


function get_sale_man(code) {
	$.ajax({
		url:HOME + 'get_sale_by_customer',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#sale_id').val(ds.id);
				$('#slpCode').val(ds.name);
			}
			else {
				$('#sale_id').val($('#user_sale_id').val());
				$('#slpCode').val($('#user_sale_name').val());
			}
		}
	})
}


function get_payment_term(code) {
	$.ajax({
		url:HOME + 'get_payment_term',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			var arr = rs.split(' | ');
			if(arr.length === 2) {
				$('#GroupNum').val(arr[0]);
				$('#Payment').val(arr[1]);
			}
		}
	})
}

function get_contact_person(code) {
	$.ajax({
		url:HOME + 'get_contact_person',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			if(isJson(rs)) {
				let data = $.parseJSON(rs);
				let source = $('#contact-template').html();
				let output = $('#Contact');

				render(source, data, output);
			}
			else {
				$('#Contact').html('');
			}
		}
	});
}


function editShipTo() {
	$('#shipToModal').modal('show');
}


function get_address_ship_to_code(code)
{
	$.ajax({
		url:HOME + 'get_address_ship_to_code',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#ship-to-template').html();
				var output = $('#shipToCode');
				render(source, data, output);

				get_address_ship_to();
			}
			else {
				$('#shipToCode').html('');
			}
		}
	})
}

function get_address_ship_to() {
	var code = $('#CardCode').val()
	var adr_code = $('#shipToCode').val();
	$.ajax({
		url:HOME + 'get_address_ship_to',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code,
			'Address' : adr_code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#s_address').val(ds.code);
				$('#sBlock').val(ds.address);
				$('#sStreet').val(ds.street);
				$('#sSubDistrict').val(ds.sub_district);
				$('#sDistrict').val(ds.district);
				$('#sProvince').val(ds.province);
				$('#sCountry').val(ds.country);
				$('#sPostCode').val(ds.postcode);

				let address = ds.address === "" ? "" : ds.address + " ";
				let street = ds.street === "" ? "" : ds.street + " ";
				let sub_district = ds.sub_district === "" ? "" : ds.sub_district + " ";
				let district = ds.district === "" ? "" : ds.district + " ";
				let province = ds.province === "" ? "" : ds.province + " ";
				let postcode = ds.postcode === "" ? "" : ds.postcode + " "
				let country = ds.country === 'TH' ? '' : ds.countryName;
				let adr = address + street + sub_district + district + province + postcode + country;

				$('#ShipTo').val(adr);

			}
		}
	})
}


function editBillTo() {
	$('#billToModal').modal('show');
}


function get_address_bill_to_code(code)
{
	$.ajax({
		url:HOME + 'get_address_bill_to_code',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#bill-to-template').html();
				var output = $('#billToCode');
				render(source, data, output);

				get_address_bill_to();
			}
			else {
				$('#billToCode').html('');
			}
		}
	})
}


function get_address_bill_to() {
	var code = $('#CardCode').val();
	var adr_code = $('#billToCode').val();
	$.ajax({
		url:HOME + 'get_address_bill_to',
		type:'GET',
		cache:false,
		data:{
			'CardCode' : code,
			'Address' : adr_code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#b_address').val(ds.code);
				$('#bBlock').val(ds.address);
				$('#bStreet').val(ds.street);
				$('#bSubDistrict').val(ds.sub_district);
				$('#bDistrict').val(ds.district);
				$('#bProvince').val(ds.province);
				$('#bCountry').val(ds.country);
				$('#bPostCode').val(ds.postcode);

				let address = ds.address === "" ? "" : ds.address + " ";
				let street = ds.street === "" ? "" : ds.street + " ";
				let sub_district = ds.sub_district === "" ? "" : ds.sub_district + " ";
				let district = ds.district === "" ? "" : ds.district + " ";
				let province = ds.province === "" ? "" : ds.province + " ";
				let postcode = ds.postcode === "" ? "" : ds.postcode + " "
				let country = ds.country === 'TH' ? '' : ds.countryName;
				let adr = address + street + sub_district + district + province + postcode + country;

				$('#BillTo').val(adr);
			}
		}
	})
}


$('#DocDate').change(function() {
	let month = $('#month').val(); //--- current posting month
	let date = $(this).val();
	let dated = date.split('-');
	if(dated.length === 3) {
		dmonth = dated[2]+"-"+dated[1]; //-- Y-m

		if(dmonth !== month) {
			$('#month').val(dmonth);
			get_new_series(dmonth)
		}
	}
})


function get_new_series(month) {
	$.ajax({
		url:HOME + 'get_series',
		type:'GET',
		cache:false,
		data:{
			'month' : month
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var data = $.parseJSON(rs);
				var source = $('#series-template').html();
				var output = $('#Series');

				render(source, data, output);
			}
		}
	})
}





function toggleText(el) {
	var no = el.data('no');
	var data = {"no" : no};
	var output = $('#row-'+no);

	if(el.val() == 1) {
		var source = $('#text-template').html();
	}
	else {
		var source = $('#normal-template').html();
	}

	render(source, data, output);

	init();
}


function addRow() {
	var no = $('#top-row').val();
	no++;
	$('#top-row').val(no);

	var data = {"no" : no};
	var source = $('#row-template').html();
	var output = $('#details-template');

	render_append(source, data, output);

	reIndex();
	init();
	$('#itemCode-'+no).focus();
}

function removeRow() {
	$('.chk').each(function(){
		if($(this).is(':checked')) {
			var no = $(this).val();
			$('#row-'+no).remove();
		}
	})

	reIndex();
	recalTotal();
}


function getItemData(code, no) {
	var cardCode = $('#CardCode').val();
	$.ajax({
		url:HOME + "get_item_data",
		type:"GET",
		cache:false,
		data:{
			'code' : code,
			'CardCode' : cardCode
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				var price = parseFloat(ds.price);
				var lineAmount = parseFloat(ds.lineAmount);
				var whCode = ds.dfWhsCode;

				$('#itemName-'+no).val(ds.name);
				$('#itemDetail-'+no).val(ds.detail);
				$('#freeText-'+no).val(ds.freeText);
				$('#qty-'+no).val(1);
				$('#uom-'+no).html(ds.uom);
				$('#basePrice-'+no).val(price);
				$('#stdPrice-'+no).val(addCommas(price.toFixed(2)));
				$('#price-'+no).val(addCommas(price.toFixed(2)));
				$('#priceDiff-'+no).val(addCommas(price.toFixed(2)));
				$('#disc1-'+no).val(ds.discount);
				$('#taxCode-'+no).val(ds.taxCode);
				$('#taxCode-'+no).data('rate', ds.taxRate);
				$('#lineAmount-'+no).val(addCommas(lineAmount.toFixed(2)));
				$('#warranty-'+no).val(ds.warranty);
				$('#whs-'+no).val(whCode);

				$('#whsQty-'+no).val(ds.whsQty);
				$('#commitQty-'+no).val(ds.commitQty);
				$('#orderedQty-'+no).val(ds.orderedQty);
				//getStock(no);

				recalAmount($('#qty-'+no));
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function recalPrice(el) {
	let no = getNo(el);
	let factor = parseDefault(parseFloat(el.find(':selected').data('qty')), 1); //--- ตัวคูณ
	let basePrice = parseDefault(parseFloat($('#basePrice-'+no).val()), 0.00);
	let newPrice = parseFloat(factor * basePrice);
	$('#stdPrice-'+no).val(addCommas(newPrice.toFixed(2)));
	$('#price-'+no).val(addCommas(newPrice.toFixed(2)));

	recalAmount(el);
}


function priceDiffPercent(no) {
	let basePrice = parseDefault(parseFloat(removeCommas($('#stdPrice-'+no).val())), 0.00);
	let price = parseDefault(parseFloat(removeCommas($('#price-'+no).val())), 0.00);
	let priceDiff = basePrice - price;

	if(priceDiff != 0 && basePrice != 0 && price != 0) {
		let priceDiffPercent = (priceDiff/basePrice) * 100;
		return priceDiffPercent.toFixed(2);
	}
	else {
		return 0;
	}
}



function recalAmount(el) {
	var no = getNo(el);
  var currentInput = removeCommas(el.val());
  var val = currentInput.replace(/[A-Za-z!@#$%^&*()]/g, '');

  el.val(addCommas(val));

	var disc1 = parseFloat($('#disc1-'+no).val());

	if(disc1 < 0 || disc1 > 100) {
		$('#disc1-'+no).val(0);
	}

	recal(no);
}


function recalDiscount(el) {
	var no = getNo(el);
	var currentInput = removeCommas(el.val());
  var val = currentInput.replace(/[A-Za-z!@#$%^&*()]/g, '');
  el.val(addCommas(val));

	var qty = parseDefault(parseFloat(removeCommas($('#qty-'+no).val())), 0);
	var price = parseDefault(parseFloat(removeCommas($('#price-'+no).val())), 0);
	var amount = parseDefault(parseFloat(val), 0);

	var disc = (1- (amount/qty)/price) * 100;

	$('#disc1-'+no).val(disc.toFixed(2));
	$('#lineDiscPrcnt-'+no).val(disc.toFixed(2));

	var disc1 = parseDefault(parseFloat($('#disc1-'+no).val()), 0);
	var sellPrice = getSellPrice(price, disc1);

	$('#priceAfDiscBfTax-'+no).val(addCommas(sellPrice.toFixed(2)));

	recalTotal();
}


function recal(no) {
	var qty = parseDefault(parseFloat(removeCommas($('#qty-'+no).val())), 0);
	var price = parseDefault(parseFloat(removeCommas($('#price-'+no).val())), 0);
	var disc1 = parseDefault(parseFloat($('#disc1-'+no).val()), 0);

	var sellPrice = getSellPrice(price, disc1);
	var lineAmount = qty * sellPrice;
	var discPrcnt = ((price - sellPrice)/price) * 100; //--- discount percent per row
	$('#priceDiff-'+no).val(priceDiffPercent(no));
	$('#priceAfDiscBfTax-'+no).val(addCommas(sellPrice.toFixed(2)));
	$('#lineAmount-'+no).val(addCommas(lineAmount.toFixed(2)));
	$('#lineDiscPrcnt-'+no).val(discPrcnt.toFixed(2));

	recalTotal();
}



function recalTotal() {
	var total = 0.00; //--- total amount after row discount
	var df_rate = parseDefault(parseFloat($('#vat_rate').val()), 7); //---- 7%
	var taxRate = df_rate * 0.01;

	$('.input-amount').each(function(){
		let no = getNo($(this));
		let qty = removeCommas($('#qty-'+no).val());
		let price = removeCommas($('#price-'+no).val());

		if(qty > 0 && price > 0)
		{
			let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
			total += amount;
		}

	})

	//--- update bill discount
	var disc = parseDefault(parseFloat($('#discPrcnt').val()), 0);
	var billDiscAmount = total * (disc * 0.01);
	$('#discAmount').val(addCommas(billDiscAmount.toFixed(2)));

	//---- bill discount amount
	var billDiscAmount = parseDefault(parseFloat(removeCommas($('#discAmount').val())), 0);
	var amountAfterDisc = total - billDiscAmount; //--- มูลค่าสินค้า หลังหักส่วนลด
	var amountBeforeDiscWithTax = getTaxAmount(); //-- มูลค่าสินค้า เฉพาะที่มีภาษี
	//--- คำนวนภาษี หากมีส่วนลดท้ายบิล
	//--- เฉลี่ยส่วนลดออกให้ทุกรายการ โดยเอาส่วนลดท้ายบิล(จำนวนเงิน)/มูลค่าสินค้าก่อนส่วนลด
	//--- ได้มูลค่าส่วนลดท้ายบิลที่เฉลี่ยนแล้ว ต่อ บาท เช่น หารกันมาแล้ว ได้ 0.16 หมายถึงทุกๆ 1 บาท จะลดราคา 0.16 บาท
	var everageBillDisc = (total > 0 ? billDiscAmount/total : 0);

	//console.log(everageBillDisc);

	//--- นำผลลัพธ์ข้างบนมาคูณ กับ มูลค่าที่ต้องคิดภาษี (ตัวที่ไม่มีภาษีไม่เอามาคำนวณ)
	//--- จะได้มูลค่าส่วนลดที่ต้องไปลบออกจากมูลค่าสินค้าที่ต้องคิดภาษี
	var totalDiscTax = amountBeforeDiscWithTax * everageBillDisc;
	//console.log(amountBeforeDiscWithTax);
	var amountToPayTax = amountBeforeDiscWithTax - totalDiscTax;
	//console.log(amountToPayTax);
	var taxAmount = amountToPayTax * taxRate;
	var docTotal = amountAfterDisc + taxAmount;

	$('#totalAmount').val(addCommas(total.toFixed(2)));
	$('#tax').val(addCommas(taxAmount.toFixed(2)));
	$('#docTotal').val(addCommas(docTotal.toFixed(2)));
}


//------ คำนวนส่วนลดท้ายบิล แล้ว update ช่อง มูลค่าส่วนลดท้ายบิล (discAmount)
$('#discPrcnt').keyup(function(){
	var total = removeCommas($('#totalAmount').val());
	var disc = parseDefault(parseFloat($(this).val()), 0);
	if(disc < 0) {
		disc = 0;
		$(this).val(0);
	}
	else if(disc > 100) {
		disc = 100;
		$(this).val(100);
	}

	var disAmount = total * (disc * 0.01);
	$('#discAmount').val(addCommas(disAmount.toFixed(2)));

	recalTotal();
});



$('#discAmount').focusout(function(){
	var total = parseDefault(parseFloat(removeCommas($('#totalAmount').val())), 0);
	var disc = parseDefault(parseFloat(removeCommas($(this).val())), 0);

	if(disc < 0 ) {
		disc = 0;
		$(this).val(0);
	}
	else if(disc > total) {
		disc = total;
		$(this).val(addCommas(total));
	}
	//--- convert amount to percent
	var discPrcnt = (total > 0 ? (disc / total) * 100 : 0);

	$('#discPrcnt').val(discPrcnt.toFixed(2));

	recalTotal();
})



function getTaxAmount() {
	var taxTotal = 0;
	$('.tax-code').each(function() {
		var no = getNo($(this));
		var lineAmount = parseDefault(parseFloat(removeCommas($('#lineAmount-'+no).val())), 0);
		var rate = parseDefault(parseFloat($(this).data('rate')), 0);
		if(rate > 0) {
			taxTotal += lineAmount;
		}
	})

	return taxTotal;
}


//--- return sell price after discount
function getSellPrice(price, disc1) {

	if(disc1 > 0 && disc1 <= 100) {
		//--- sell price step 1
		price = ((100 - disc1) * 0.01) * price;
		return price;
	}
	else {
		if(disc1 > 100) {
			return 0;
		}
		else {
			return price;
		}
	}
}



function getNo(el) {
	var arr = el.attr('id').split('-');

	return arr[1];
}


function getStock(no) {
	var code = $('#itemCode-'+no).val();
	var whs = $('#whs-'+no).val();
	if(whs.length > 0 && code.length > 0) {
		$.ajax({
			url:HOME + 'get_stock',
			type:'GET',
			cache:false,
			data:{
				'whs' : whs,
				'itemCode' : code
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					$('#whsQty-'+no).val(ds.whsQty);
					$('#commitQty-'+no).val(ds.commitQty);
					$('#orderedQty-'+no).val(ds.orderedQty);
				}
				else {
					swal({
						titel:'Error!',
						text:rs,
						type:'error'
					});

					$('#whsQty-'+no).val('');
					$('#commitQty-'+no).val('');
					$('#orderedQty-'+no).val('');
				}
			}
		})
	}
	else {
		$('#whsQty-'+no).val('');
		$('#commitQty-'+no).val('');
		$('#orderedQty-'+no).val('');
	}
}


function init() {

	$('.input-item-code').autocomplete({
		source:BASE_URL + 'auto_complete/get_item_code_and_name',
		autoFocus:true,
		open:function(event){
			var $ul = $(this).autocomplete('widget');
			$ul.css('width', 'auto');
		},
		close:function(){
			var data = $(this).val();
			var arr = data.split(' | ');
			if(arr.length == 2) {
				let no = $(this).data("id");
				$(this).val(arr[0]);
				getItemData(arr[0], no);
			}
			else {
				$(this).val('');
			}
		}
	})


	$('.input-item-code').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('itemName', $(this));
		}
	});

	$('.input-item-name').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('itemDetail', $(this));
		}
	});

	$('.input-item-detail').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('freeText', $(this));
		}
	});

	$('.free-text').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('qty', $(this));
		}
	});

	$('.input-qty').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('price', $(this));
		}
	});

	$('.input-price').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('disc1', $(this));
		}
	});


	$('.input-disc1').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('disc2', $(this));
		}
	});

	$('.input-disc2').keyup(function(e) {
		if(e.keyCode === 13) {
			nextFocus('lineCount', $(this));
		}
	});

	$('#deposit').keyup(function(){
		let val = removeCommas($(this).val());
		$(this).val(addCommas(val))
	})

	$('.number').focus(function(){
		$(this).select();
	})
}


$('#discAmount').keyup(function(e) {
	if(e.keyCode === 13) {
		$('#roundDif').focus();
	}
})




function nextFocus(name, el) {
	var no = getNo(el);
	$('#'+name+'-'+no).focus();
}

$(document).ready(function(){
	init();
})




$('.autosize').autosize({append: "\n"});


function duplicateSQ() {
	swal({
    title:'Duplicate Sales Quotation ',
    text:'ต้องการสร้างใบเสนอราคาใหม่ เหมือนใบเสนอราคานี้หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'Cancle',
    confirmButtonText:'Duplicate',
  },
  function(){
		load_in();
		var code = $('#code').val();
		$.ajax({
			url:HOME + 'duplicate_quotation',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					if(ds.status === 'success') {
						swal({
							title:'Success',
							text: 'Duplicate Sales Quotation success : '+ds.code,
							type:'success',
							timer:1000
						});

						setTimeout(function(){
							goEdit(ds.code);
						},1200)

					}
					else {
						swal({
							title:"Error!",
							text:ds.error,
							type:'error'
						});
					}
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					})
				}
			}
		})
  });

}


function createSalesOrder(sqCode) {
	swal({
    title:'Create Sales Order',
    text:'ต้องการสร้างใบสั่งขายใหม่ เหมือนใบเสนอราคานี้หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'Cancle',
    confirmButtonText:'สร้างใบสั่งขาย',
  },
	function(){
		load_in();
		$.ajax({
			url:BASE_URL + 'sales_order/create_from_quotation',
			type:'POST',
			cache:false,
			data:{
				'quotation_code' : sqCode
			},
			success:function(rs) {
				load_out();
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.href = BASE_URL + 'sales_order/edit/'+ds.code;
					}, 1200);
				}
				else {
					swal({
						title:"Error!",
						text:rs,
						type:'error',
						html:true
					});
				}
			},
			error:function(xhr, status, error) {
				load_out();
				swal({
					title:'Error!',
					text:xhr.responseText,
					type:'error',
					html:true
				})
			}
		})
	});
}

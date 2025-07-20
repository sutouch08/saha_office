//--- เพิ่มรายการจาก PO grid
function addPoItems() {
	let items = [];

	$('#poGrid').modal('hide');

	load_in();

	$('.po-qty').each(function() {
		let el = $(this);

		if(el.val() != "") {
			let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);

			if(qty > 0) {
				let no = el.data('uid');

				if($('#row-qty-'+no).length) {
					let cqty = parseDefault(parseFloat($('#row-qty-'+no).val()), 0);
					let nqty = cqty + qty;
					$('#row-qty-'+no).val(nqty);

					recalAmount(no);
				}
				else {

					let itemCode = el.data('code'); //--- product code;
					let itemName = el.data('name');
					let baseCode = el.data('basecode'); //-- po no.
					let baseEntry = el.data('baseentry');
					let baseLine = el.data('baseline');
					let uid = baseEntry+"-"+baseLine;
					let priceBefDi = parseDefault(parseFloat(el.data('bfprice')), 0.00);
					let discPrcnt = parseDefault(parseFloat(el.data('discprcnt')), 0.00);
					let price = parseDefault(parseFloat(el.data('price')), 0.00); //--- price Af discount
					let priceAfVat = parseDefault(parseFloat(el.data('afprice')), 0.00);
					let vatPerQty = parseDefault(parseFloat(el.data('vatperqty')), 0.00);
					let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
					let backlogs = parseDefault(parseFloat(el.data('backlogs')), 0);
					let amount = roundNumber(qty * price, 2);
					let vatCode = el.data('vatcode');
					let vatRate = parseDefault(parseFloat(el.data('vatrate')), 7);
					let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

					let item = {
						'no' : no,
						'uid' : uid,
						'product_code' : itemCode,
						'product_name' : itemName,
						'baseCode' : baseCode,
						'baseEntry' : baseEntry,
						'baseLine' : baseLine,
						'vatCode' : vatCode,
						'vatRate' : vatRate,
						'PriceBefDi' : priceBefDi,
						'PriceBefDiLabel' : addCommas(priceBefDi.toFixed(3)),
						'DiscPrcnt' : discPrcnt,
						'Price' : price,
						'PriceLabel' : addCommas(price.toFixed(3)),
						'PriceAfVAT' : priceAfVat,
						'VatPerQty' : vatPerQty,
						'qty' : qty,
						'qtyLabel' : addCommas(qty.toFixed(2)),
						'backlogs' : backlogs,
						'limit' : limit,
						'amount' : amount,
						'amountLabel' : addCommas(amount.toFixed(2)),
						'vatAmount' : vatAmount,
						'unitMsr' : el.data('unitmsr'),
						'NumPerMsr' : el.data('numpermsr'),
						'unitMsr2' : el.data('unitmsr2'),
						'NumPerMsr2' : el.data('numpermsr2'),
						'UomEntry' : el.data('uomentry'),
						'UomEntry2' : el.data('uomentry2'),
						'UomCode' : el.data('uomcode'),
						'UomCode2' : el.data('uomcode2')
					}

					items.push(item);
				}
			}
		}
	})

	if(items.length > 0) {

		let source = $('#receive-template').html();
		let output = $('#receive-list');

		render_append(source, items, output);

		$('.item-control').attr('disabled', 'disabled');
		$('#btn-confirm-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#poCode').attr('disabled', 'disabled');

		//--- update last no for next gennerate
		$('#no').val(0);

		//--- Calculate Summary
		recalTotal();

		//---- update running no
		reIndex();

		swal({
			title:'Success',
			type:'success',
			timer:1000
		});
	}

	load_out();
}

function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	if($('.chk:checked').length) {
		swal({
			title:'คุณแน่ใจ ?',
			text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			confirmButtonColor:'#d15b47',
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function() {
			setTimeout(() => {
				$('.chk:checked').each(function() {
					let uid = $(this).val();
					$('#row-'+uid).remove();
					$('#row-'+uid).remove();
				});

				recalTotal();
				reIndex();
			}, 100);
		});
	}
}


function removeRow(uid) {
	return $('#row-'+uid).remove();
}


function getPoDetail() {
	let poCode = $('#po-code').val().trim();

	if(poCode.length == 0) {
		return false;
	}
	else {
		$('#po-title').text('ใบสั่งซื้อ - '+poCode);
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor_code').val(ds.CardCode);
					$('#vendorName').val(ds.CardName);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');
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
					text:rs,
					type:'error',
					html:true
				});
			}
		}
	})
}


function getPoItems() {
	let po = $('#poCode').val();

	if(po.length == 0) {
		swal({
			title:'Oops !',
			text:'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type:'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor_code').val(ds.CardCode);
					$('#vendorName').val(ds.CardName);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');
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
					text:rs,
					type:'error',
					html:true
				});
			}
		}
	})
}


function receiveAll() {
	$('.po-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).data('qty')), 0);
		if(qty > 0) {
			$(this).val(addCommas(qty));
		}
	});
}


function clearAll() {
	$('.po-qty').each(function() {
		$(this).val('');
	});
}


function clearPo() {
	$('#po-code').val('').focus();
}


$('#poGrid').on('shown.bs.modal', function() {
	let id = $('#uid-1').val();

	$('#po-qty-'+id).focus();
})

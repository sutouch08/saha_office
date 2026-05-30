
function toggleCheckAll(el) {
	if (el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}

function toggleCheck(uid) {
	const chk = $(`#chk-${uid}`);
	const row = $(`#row-${uid}`);

	if (chk.is(':checked')) {		
		row.addClass('selected');
	}
	else {		
		row.removeClass('selected');
	}
}


function removeChecked() {
	if ($('.chk:checked').length) {
		swal({
			title: 'คุณแน่ใจ ?',
			text: 'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d15b47',
			confirmButtonText: 'Yes',
			cancelButtonText: 'No',
			closeOnConfirm: true
		}, function () {
			setTimeout(() => {
				$('.chk:checked').each(function () {
					let uid = $(this).val();
					removeRow(uid);
				});

				recalTotal();
				reIndex();
			}, 100);
		});
	}
}


function removeRow(uid) {
	return $('#row-' + uid).remove();
}


$('#item-qty').keyup(function (e) {
	if (e.keyCode === 13) {
		$('#barcode-item').focus();
	}
});


$('#barcode-item').keyup(function (e) {
	if (e.keyCode === 13) {
		addRow();
	}
});

function addRow() {
	clearErrorByClass('c');
	let bc = $('#barcode-item').val().trim();
	let qty = parseDefaultFloat($('#item-qty').val(), 0);
	let baseType = $('#base-type').val();
	let baseRef = $('#base-ref').val().trim();

	if (bc.length) {

		if (qty <= 0) {
			$('#item-qty').hasError();
			return false;
		}

		if (baseType.length == 0 && baseRef.length == 0) {
			let h = {
				'barcode': bc,
				'qty': qty,
				'CardCode' : $('#customer-code').val()
			}

			getItemDataByBarcode(h);
		}
		else {
			let h = {
				'baseType': baseType,
				'baseRef': baseRef,
				'qty': qty,
				'barcode': bc
			};



			if (h.baseType != "" && h.baseRef == "") {
				$('#base-ref').hasError();
				return false;
			}

			if (h.baseType == "" && h.baseRef != "") {
				$('#base-type').hasError();
				return false;
			}

			getItemByBarcode(h);
		}
	}
}


function getItemByBarcode(h) {
	load_in();

	$.ajax({
		url: HOME + 'get_item_by_barcode',
		type: 'POST',
		cache: false,
		data: {
			'data': JSON.stringify(h)
		},
		success: function (rs) {
			load_out();

			if (isJson(rs)) {
				let ds = JSON.parse(rs);

				if (ds.status === 'success') {
					if (ds.data) {
						let uid = ds.data.uid;

						if ($('#' + uid).length) {
							let dl = $('#' + uid);

							let Qty = parseDefaultFloat(ds.data.Qty, 1);
							let cqty = parseDefaultFloat(dl.val(), 0);
							let limit = parseDefaultFloat(dl.data('open'), 0);
							let nqty = cqty + Qty;
							let price = parseDefaultFloat(dl.data('price'), 2); //-- price after disc
							let vatRate = parseDefaultFloat(dl.data('vatrate'), 0);
							let lineTotal = nqty * price;
							let vatSum = roundNumber((lineTotal * (vatRate * 0.01)), 2);

							if (nqty > limit) {
								dl.hasError();
							}

							dl.val(addCommas(nqty.toFixed(2)));
							$('#row-total-' + uid).val(addCommas(lineTotal.toFixed(2)));
							$('#row-vat-amount-' + uid).val(vatSum);
						}
						else {
							let Qty = parseDefaultFloat(ds.data.Qty, 1);
							let price = parseDefaultFloat(ds.data.Price, 0);
							let vatrate = parseDefaultFloat(ds.data.VatPrcnt, 0);
							let lineTotal = Qty * price;
							let vatSum = lineTotal * (vatrate * 0.01);
							let bfPrice = parseDefaultFloat(ds.data.PriceBefDi, 0);
							let afPrice = parseDefaultFloat(ds.data.PriceAfVAT, 0);
							let openQty = parseDefaultFloat(ds.data.OpenQty, 0);

							ro = {
								"uid": uid,
								"ItemCode": ds.data.ItemCode,
								"ItemName": ds.data.Dscription,
								"baseType": h.baseType,
								"DocNum": ds.data.DocNum,
								"DocEntry": ds.data.DocEntry,
								"LineNum": ds.data.LineNum,
								"OpenQty": openQty,
								"OpenQtyLabel": addCommas(openQty.toFixed(2)),
								"Qty": Qty,
								"QtyLabel": addCommas(Qty.toFixed(2)),
								"Price": price,
								"PriceLabel": addCommas(bfPrice.toFixed(2)),
								"PriceBefDi": bfPrice,
								"PriceAfVAT": afPrice,
								"DiscPrcnt": parseDefaultFloat(ds.data.DiscPrcnt, 0),
								"VatGroup": ds.data.VatGroup,
								"VatRate": vatrate,
								"unitMsr": ds.data.unitMsr,
								"NumPerMsr": parseDefaultFloat(ds.data.NumPerMsr, 1),
								"UomEntry": ds.data.UomEntry,
								"UomCode": ds.data.UomCode,
								"unitMsr2": ds.data.unitMsr2,
								"NumPerMsr2": ds.data.NumPerMsr2,
								"UomEntry2": ds.data.UomEntry2,
								"UomCode2": ds.data.UomCode2,
								"SlpCode": ds.data.SlpCode,
								"LineTotal": addCommas(lineTotal.toFixed(2)),
								"VatAmount": vatSum
							};

							let source = $('#barcode-template').html();
							let output = $('#return-table');

							render_append(source, ro, output);

							reIndex();
							recalAmount(uid);
						}

						recalTotal();
					} //

					$('#item-qty').val(1);
					$('#barcode-item').val('').focus();
				}
				else {
					showError(ds.message);
				}
			}
			else {
				showError(rs);
			}
		},
		error: function (rs) {
			showError(rs);
		}
	})
}


function getItemDataByBarcode(h) {
	load_in();

	$.ajax({
		url:`${HOME}get_item_data_by_barcode`,
		type:"GET",
		cache:false,
		data:h,
		success: function(rs) {
			load_out();

			if(isJson(rs)) {
				const ds = JSON.parse(rs);
				const uid = ds.uid;
				const exists = $(`#${uid}`).length > 0;

				// if( ! exists) {
				// 	addBlankRow(uid);
				// }

				setTimeout(() => {
					
					const taxRate = parseDefaultFloat(ds.taxRate, 0);
					const price = parseDefaultFloat(ds.price, 0);
					const basePrice = parseDefaultFloat(ds.basePrice, 0);
					const bfPrice = price / (1 + taxRate);
					const el = $(`#${uid}`);
					const qty = parseDefaultFloat(el.val(), 0);
					const newQty = qty + parseDefaultFloat(ds.qty, 0);

					if ( ! exists) {
						ds.vatCode = ds.taxCode;
						ds.vatRate = taxRate;
						ds.bfPrice = bfPrice;
						ds.afPrice = price;
						ds.newQty = newQty;
						ds.price = price;
						ds.basePrice = basePrice;
						ds.open = 1000000;
						ds.input = 'barcode';
						ds.discPrcnt = 0.00;
						ds.priceLabel = addCommas(price.toFixed(2));

						const source = $('#no-base-data-template').html();
						const output = $('#return-table');
						render_append(source, ds, output);						
					}

					el.val(addCommas(newQty.toFixed(2)));
					recalAmount(uid);
					reIndex();

					$('#item-qty').val(1);
					$('#barcode-item').val('').focus();					
				}, 50);
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


function addBlankRow(uid = null) {
	uid = uid === null ? generateUID() : uid;
	let ds = { 'uid': uid, 'input': 'manual' };
	let source = $('#new-row-template').html();
	let output = $('#return-table');
	render_append(source, ds, output);
	reIndex();
	itemInit(uid);
}

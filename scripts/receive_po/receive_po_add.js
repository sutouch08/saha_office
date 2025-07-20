var click = 0;

window.addEventListener('load', () => {
  headerPoInit();
  poInit();
  zoneInit();
});


function zoneInit() {
  let warehouse_code = $('#warehouse').val();

  $('#zone-code').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+warehouse_code,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2){
        $('#zone-code').val(arr[0]);
        $('#zone-name').val(arr[1]);
      }
      else {
        $('#zone-code').val('');
        $('#zone-name').val('');
      }
    }
  })
}


function changeWhs() {
  zoneInit();
  $('#zone-code').val('');
  $('#zone-name').val('');
  $('#zone-code').focus();
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'posting_date' : $('#posting-date').val().trim(),
      'vendor_code' : $('#vendor-code').val().trim(),
      'vendor_name' : $('#vendor-name').val().trim(),
      'invoice_code' : $('#invoice').val().trim(),
      'po_code' : $('#po-no').val().trim(),
      'Currency' : $('#DocCur').val(),
      'Rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'zone_code' : $('#zone-code').val().trim(),
      'zone_name' : $('#zone-name').val().trim(),
      'remark' : $('#remark').val().trim()
    }

    if( ! isDate(h.posting_date)) {
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_code.length == 0) {
      $('#vendor-code').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_name.length == 0) {
      $('#vendor-name').hasError();
      click = 0;
      return false;
    }

    if(h.po_code.length == 0) {
      $('#po-no').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0) {
      $('#zone-code').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data: {
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            edit(ds.code);
            click = 0;
          }
          else {
            showError(ds.message);
            click = 0;
          }
        }
        else {
          showError(rs);
          click = 0;
        }
      },
      error:function(rs) {
        showError(rs);
        click = 0;
      }
    })
  }
}


function getEdit() {
  $('.r').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function changeHeader() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let change = 0;

    let h = {
      'code' : $('#code').val(),
      'date_add' : $('#date-add').val().trim(),
      'vendor_code' : $('#vendor-code').val().trim(),
      'vendor_name' : $('#vendor-name').val().trim(),
      'invoice' : $('#invoice').val().trim(),
      'po_code' : $('#po-no').val().trim(),
      'currency' : $('#DocCur').val(),
      'rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'zone_code' : $('#zone-code').val().trim(),
      'remark' : $('#remark').val().trim()
    }

    let prev = {
      'date_add' : $('#date-add').data('prev'),
      'vendor_code' : $('#vendor-code').data('prev'),
      'vendor_name' : $('#vendor-name').data('prev'),
      'invoice' : $('#invoice').data('prev'),
      'po_code' : $('#po-no').data('prev'),
      'currency' : $('#DocCur').data('prev'),
      'rate' : $('#DocRate').data('prev'),
      'warehouse_code' : $('#warehouse').data('prev'),
      'zone_code' : $('#zone-code').data('prev'),
      'zone_name' : $('#zone-name').data('prev'),
      'remark' : $('#remark').data('prev')
    }

    if($('.row-qty').length > 0) {
      if(h.vendor_code != prev.vendor_code) {
        change++;
      }

      if(h.po_code != prev.po_code) {
        change++;
      }

      if(h.warehouse_code != prev.warehouse_code) {
        change++;
      }

      if(h.zone_code != prev.zone_code) {
        change++;
      }
    }

    if( ! isDate(h.date_add)) {
      $('#date-add').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_code.length == 0) {
      $('#vendor-code').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_name.length == 0) {
      $('#vendor-name').hasError();
      click = 0;
      return false;
    }

    if(h.po_code.length == 0) {
      $('#po-no').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0) {
      $('#zone-code').hasError();
      click = 0;
      return false;
    }

    if(change > 0) {
      swal({
        title: 'คำเตือน',
        text: 'รายการปัจจุบันจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
        type:'warning',
        showCancelButton:true,
        cancelButtonText:'No',
        confirmButtonText:'Yes',
        closeOnConfirm:true
      }, function(isConfirm) {
        if(isConfirm) {
          console.log('header changed');
          click = 0;
        }
        else {
          $('#date-add').val(prev.date_add);
          $('#vendor-code').val(prev.vendor_code);
          $('#vendor-name').val(prev.vendor_name);
          $('#invoice').val(prev.invoice);
          $('#po-no').val(prev.po_code);
          $('#DocCur').val(prev.currency);
          $('#DocRate').val(prev.rate);
          $('#warehouse').val(prev.warehouse_code).change();
          $('#zone-code').val(prev.zone_code);
          $('#zone-name').val(prev.zone_name);
          $('#remark').val(prev.remark);

          $('#btn-update').addClass('hide');
          $('#btn-edit').removeClass('hide');
          $('.r').attr('disabled', 'disabled');
          click = 0;
        }
      })
    }
  }
}


function rollback(code) {
  swal({
    title:'ย้อนสถานะเอกสาร',
    text:'ต้องการย้อนสถานะเอกสารกลับมาแก้ไข ใช่หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, () => {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'rollback/'+code,
        type:'POST',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    }, 100)
  })
}


function save(save_type) {
  if(click == 0) {
    click = 1;

    clearErrorByClass('r');
    clearErrorByClass('row-qty');

    let error = 0;
    let count = 0;
    let is_ref_po = 0; //--- เช็คว่า PO ที่หัวเอกสาร ตรงกับ PO ในรายการ อย่างนี้อย 1 ใบหรือไม่
    let totalQty = 0;
    let totalReceived = 0;

    let h = {
      'save_type' : save_type,  //-- P = บันทึกเป็น ดราฟท์, O = บันทึกรอรับ, C = บันทึกรับเข้าทันที
      'id' : $('#id').val(),
      'code' : $('#code').val(),
      'posting_date' : $('#posting-date').val(),
      'vendor_code' : $('#vendor-code').val(),
      'vendor_name' : $('#vendor-name').val(),
      'invoice_code' : $('#invoice').val().trim(),
      'po_code' : $('#po-no').val(),
      'Currency' : $('#DocCur').val(),
      'Rate' : parseDefault(parseFloat($('#DocRate').val()), 1.00),
      'warehouse_code' : $('#warehouse').val(),
      'zone_code' : $('#zone-code').val(),
      'remark' : $('#remark').val().trim(),
      'VatSum' : parseDefault(parseFloat(removeCommas($('#vat-sum').val())), 0),
      'DocTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
      'TotalQty' : 0,
      'TotalReceived' : 0,
      'rows' : []
    }

    if( ! isDate(h.posting_date)) {
      swal("วันที่ไม่ถูกต้อง");
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
      swal("ผุ้ขายไม่ถูกต้อง");
      $('#vendor-code').hasError();
      $('#vendor-name').hasError();
      click = 0;
      return false;
    }

    if(h.po_code.length == 0) {
      swal("กรุณาระบุใบสั่งซื้อ");
      $('#po-no').hasError();
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0) {
      swal("กรุณาระบุ Bin Location");
      $('#zone-code').hasError();
      click = 0;
      return false;
    }

    if($('.row-qty').length) {
      $('.row-qty').each(function() {
        let el = $(this);
        let uid = el.data('uid');
        let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);
        let limit = parseDefault(parseFloat(el.data('limit')), 0);

        if(qty <= 0 || qty > limit) {
          el.hasError();
          error++;
        }
        else {
          if(h.po_code == el.data('basecode'))
          {
            is_ref_po = 1;
          }

          let openQty = parseDefault(parseFloat(el.data('backlogs')), 0);
          let lineTotal = parseDefault(parseFloat(removeCommas($('#row-total-'+uid).val())), 0);
          let vatAmount = parseDefault(parseFloat($('#row-vat-amount-'+uid).val()), 0);

          totalQty += qty;
          totalReceived += qty;

          let row = {
            'baseCode' : el.data('basecode'),
            'baseEntry' : el.data('baseentry'),
            'baseLine' : el.data('baseline'),
            'ItemCode' : el.data('code'),
            'ItemName' : el.data('name'),
            'PriceBefDi' : el.data('bfprice'),
            'Price' : el.data('price'),
            'PriceAfVAT' : el.data('afprice'),
            'DiscPrcnt' : el.data('discprcnt'),
            'Qty' : qty,
            'ReceiveQty' : qty,
            'LineTotal' : lineTotal,
            'VatAmount' : vatAmount,
            'VatPerQty' : el.data('vatperqty'),
            'BinCode' : h.zone_code,
            'WhsCode' : h.warehouse_code,
            'UomEntry' : el.data('uomentry'),
            'UomCode' : el.data('uomcode'),
            'unitMsr' : el.data('unitmsr'),
            'NumPerMsr' : el.data('numpermsr'),
            'UomEntry2' : el.data('uomentry2'),
            'UomCode2' : el.data('uomcode2'),
            'unitMsr2' : el.data('unitmsr2'),
            'NumPerMsr2' : el.data('numpermsr2'),
            'VatGroup' : el.data('vatcode'),
            'VatRate' :el.data('vatrate')
          }

          h.rows.push(row);
        }
      });
    }
    else {
      swal("ไม่พบรายการรับเข้า");
      click = 0;
      return false;
    }

    if(is_ref_po == 0) {
      swal({
        title:'Oops !',
        text:'ใบสั่งซื้อบนหัวเอกสาร ไม่ตรงกับรายการรับเข้า',
        type:'error'
      });

      click = 0;
      return false;
    }

    if(error > 0) {
      swal({
        title:'Oops !',
        text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
        type:'error'
      });

      click = 0;
      return false;
    }

    if(h.rows.length < 1){
      swal('ไม่พบรายการรับเข้า');
      click = 0;
      return false;
    }

    h.TotalQty = totalQty;
    h.TotalReceived = totalReceived;

    load_in();

    $.ajax({
      url: HOME + 'save',
      type:"POST",
      cache:"false",
      data: {
        'data' : JSON.stringify(h)
      },
      success: function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            if(ds.ex == 1) {
              swal({
                title:'สำเร็จ',
                text:'บันทึกรายการเรียบร้อยแล้ว',
                type:'success',
                timer:1000
              });

              setTimeout(function() {
                viewDetail(h.code);
              }, 1200);
            }
            else {
              swal({
                title:'สำเร็จ',
                text:ds.message,
                type:'warning',
                html:true
              }, () => {
                viewDetail(ds.code);
              });
            }
          }
          else {
            showError(ds.message);
            click = 0;
          }
        }
        else {
          showError(rs);
          click = 0;
        }
      },
      error:function(rs) {
        showError(rs);
        click = 0;
      }
    });
  } //--- click
}


function closeReceive() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('r');
    clearErrorByClass('row-qty');

    let error = 0;
    let count = 0;
    let is_ref_po = 0; //--- เช็คว่า PO ที่หัวเอกสาร ตรงกับ PO ในรายการ อย่างนี้อย 1 ใบหรือไม่
    let totalQty = 0;
    let totalReceived = 0;

    let h = {
      'save_type' :'C',  //-- P = บันทึกเป็น ดราฟท์, O = บันทึกรอรับ, C = บันทึกรับเข้าทันที
      'id' : $('#id').val(),
      'code' : $('#code').val(),
      'posting_date' : $('#posting-date').val(),
      'vendor_code' : $('#vendor-code').val(),
      'vendor_name' : $('#vendor-name').val(),
      'invoice_code' : $('#invoice').val().trim(),
      'po_code' : $('#po-no').val().trim(),
      'Currency' : $('#DocCur').val(),
      'Rate' : parseDefault(parseFloat($('#DocRate').val()), 1.00),
      'warehouse_code' : $('#warehouse').val(),
      'zone_code' : $('#zone-code').val(),
      'remark' : $('#remark').val().trim(),
      'VatSum' : parseDefault(parseFloat(removeCommas($('#vat-sum').val())), 0),
      'DocTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
      'TotalQty' : 0,
      'TotalReceived' : 0,
      'rows' : []
    }

    if( ! isDate(h.posting_date)) {
      swal("วันที่ไม่ถูกต้อง");
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
      swal("ผุ้ขายไม่ถูกต้อง");
      $('#vendor-code').hasError();
      $('#vendor-name').hasError();
      click = 0;
      return false;
    }

    if(h.po_code.length == 0) {
      swal("กรุณาระบุใบสั่งซื้อ");
      $('#po-no').hasError();
      click = 0;
      return false;
    }

    if(h.zone_code.length == 0) {
      swal("กรุณาระบุ Bin Location");
      $('#zone-code').hasError();
      click = 0;
      return false;
    }

    if($('.row-qty').length) {
      $('.row-qty').each(function() {
        let el = $(this);
        let uid = el.data('uid');
        let requestQty = parseDefault(parseFloat(el.data('limit')), 0); //-- จำนวนตั้ง
        let receiveQty = parseDefault(parseFloat(removeCommas(el.val())), 0); //-- จำนวนรับจริง

        if(h.po_code == el.data('basecode'))
        {
          is_ref_po = 1;
        }

        if(receiveQty != requestQty) {
          el.hasError();
          error++;
        }
        else {
          let lineTotal = parseDefault(parseFloat(removeCommas($('#row-total-'+uid).val())), 0);
          let vatAmount = parseDefault(parseFloat($('#row-vat-amount-'+uid).val()), 0);

          totalQty += requestQty;
          totalReceived += receiveQty;

          let row = {
            'baseCode' : el.data('basecode'),
            'baseEntry' : el.data('baseentry'),
            'baseLine' : el.data('baseline'),
            'ItemCode' : el.data('code'),
            'ItemName' : el.data('name'),
            'PriceBefDi' : el.data('bfprice'),
            'Price' : el.data('price'),
            'PriceAfVAT' : el.data('afprice'),
            'DiscPrcnt' : el.data('discprcnt'),
            'Qty' : requestQty,
            'ReceiveQty' : receiveQty,
            'LineTotal' : lineTotal,
            'VatAmount' : vatAmount,
            'VatPerQty' : el.data('vatperqty'),
            'BinCode' : h.zone_code,
            'WhsCode' : h.warehouse_code,
            'UomEntry' : el.data('uomentry'),
            'UomCode' : el.data('uomcode'),
            'unitMsr' : el.data('unitmsr'),
            'NumPerMsr' : el.data('numpermsr'),
            'UomEntry2' : el.data('uomentry2'),
            'UomCode2' : el.data('uomcode2'),
            'unitMsr2' : el.data('unitmsr2'),
            'NumPerMsr2' : el.data('numpermsr2'),
            'VatGroup' : el.data('vatcode'),
            'VatRate' :el.data('vatrate')
          }

          h.rows.push(row);
        }
      });
    }
    else {
      swal("ไม่พบรายการรับเข้า");
      click = 0;
      return false;
    }

    if(is_ref_po == 0) {
      swal({
        title:'Oops !',
        text:'ใบสั่งซื้อบนหัวเอกสาร ไม่ตรงกับรายการรับเข้า',
        type:'error'
      });

      click = 0;
      return false;
    }

    if(error > 0) {
      swal({
        title:'Oops !',
        text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
        type:'error'
      });

      click = 0;
      return false;
    }

    if(h.rows.length < 1){
      swal('ไม่พบรายการรับเข้า');
      click = 0;
      return false;
    }

    h.TotalQty = totalQty;
    h.TotalReceived = totalReceived;

    load_in();

    $.ajax({
      url: HOME + 'close_receive',
      type:"POST",
      cache:"false",
      data: {
        'data' : JSON.stringify(h)
      },
      success: function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            if(ds.ex == 1) {
              swal({
                title:'สำเร็จ',
                text:'บันทึกรายการเรียบร้อยแล้ว',
                type:'success',
                timer:1000
              });

              setTimeout(function() {
                viewDetail(h.code);
              }, 1200);
            }
            else {
              swal({
                title:'สำเร็จ',
                text:ds.message,
                type:'warning',
                html:true
              }, () => {
                viewDetail(ds.code);
              });
            }
          }
          else {
            showError(ds.message);
            click = 0;
          }
        }
        else {
          showError(rs);
          click = 0;
        }
      },
      error:function(rs) {
        showError(rs);
        click = 0;
      }
    });
  } //--- click
}


function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});

}


$('#vendor-name').autocomplete({
	source: BASE_URL + 'auto_complete/get_vendor_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$('#vendor-code').val(arr[0]);
			$('#invoice').focus();
		}else{
			$(this).val('');
			$('#vendor-code').val('');
		}
	}
});


$('#vendor-code').autocomplete({
	source: BASE_URL + 'auto_complete/get_vendor_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ) {
			$('#vendor-code').val(arr[0]);
			$('#vendor-name').val(arr[1]);
			$('#invoice').focus();
		}else{
			$('#vendor-name').val('');
			$('#vendor-code').val('');
		}
	}
});



$('#vendor-name').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendor-code').val('');
	}
	poInit();
});


$('#vendor-code').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendor-name').val('');
	}
  headerPoInit();
	poInit();
});


$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});


function poInit() {
	let vendor_code = $('#vendor-code').val();

  $('#po-code').autocomplete({
    source: BASE_URL + 'auto_complete/get_po_code/'+vendor_code,
    autoFocus: true,
    close:function() {
      var arr = $(this).val().split(' | ');

      if(arr.length > 2){
        $(this).val(arr[0]);
      }
      else {
        $(this).val('');
      }
    }
  });
}


function headerPoInit() {
	let vendor_code = $('#vendor-code').val();

  $('#po-no').autocomplete({
    source: BASE_URL + 'auto_complete/get_po_code/'+vendor_code,
    autoFocus: true,
    close:function() {
      var arr = $(this).val().split(' | ');

      if(arr.length > 2){
        $(this).val(arr[0]);
      }
      else {
        $(this).val('');
      }
    }
  });
}


function unSave(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการย้อนสถานะเอกสาร '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function(){

			load_in();

			setTimeout(() => {
				$.ajax({
					url:HOME + 'unsave',
					type:'POST',
					cache:false,
					data:{
						'code' : code
					},
					success:function(rs) {
						load_out();

						if(isJson(rs)) {

							let ds = JSON.parse(rs);

							if(ds.status == 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								setTimeout(() => {
									window.location.reload();
								}, 1200);
							}
							else {
								swal({
									title:'Error!',
									text:ds.message,
									type:'error',
									html:true
								})
							}
						}
						else {
							swal({
								title:'Error!',
								text:rs,
								type:'error',
								html:true
							})
						}
					},
					error:function(rs) {
						load_out();

						swal({
							title:'Error!',
							text:rs.responseText,
							type:'error',
							html:true
						})
					}
				})

			}, 200);
	});
}


function recalAmount(id) {
	let el = $('#row-qty-'+id);
  el.clearError();
	let price = parseDefault(parseFloat(el.data('price')), 0);
	let qty = parseDefaultFloat(removeCommas(el.val()), 0);
  let limit = parseDefaultFloat(el.data('limit'), 0);
	let vatAmount = parseDefault(parseFloat(el.data('vatperqty')), 0);

	let lineTotal = qty * price;
	let vatSum = roundNumber((qty * vatAmount), 2);

  if(qty > limit) {
    el.hasError();
  }

	$('#row-total-'+id).val(addCommas(lineTotal.toFixed(2)));
	$('#row-vat-amount-'+id).val(vatSum);

	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;
	let totalVat = 0;

	$('.row-qty').each(function() {
		let el = $(this);
		let id = el.data('uid');
		let qty = parseDefault(parseFloat(el.val()), 0);
		let price = parseDefault(parseFloat(el.data('price')), 0);
		let vatAmount = parseDefault(parseFloat($('#row-vat-amount-'+id).val()), 0);
		let amount = qty * price;

		totalQty += qty;
		totalAmount += amount;
		totalVat += vatAmount;
	});

	let vatSum = totalVat;
	let docTotal = totalAmount + vatSum;

	$('#total-qty').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
	$('#vat-sum').val(addCommas(vatSum.toFixed(2)));
	$('#doc-total').val(addCommas(docTotal.toFixed(2)));
}

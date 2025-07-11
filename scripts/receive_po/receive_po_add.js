var click = 0;

window.addEventListener('load', () => {
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


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'date_add' : $('#date-add').val().trim(),
      'vendor_code' : $('#vendor-code').val().trim(),
      'vendor_name' : $('#vendor-name').val().trim(),
      'invoice' : $('#invoice').val().trim(),
      'currency' : $('#DocCur').val(),
      'rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'remark' : $('#remark').val().trim()
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

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
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
      'date_add' : $('#date-add').val().trim(),
      'vendor_code' : $('#vendor-code').val().trim(),
      'vendor_name' : $('#vendor-name').val().trim(),
      'invoice' : $('#invoice').val().trim(),
      'currency' : $('#DocCur').val(),
      'rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'remark' : $('#remark').val().trim()
    }

    let prev = {
      'date_add' : $('#date-add').data('prev'),
      'vendor_code' : $('#vendor-code').data('prev'),
      'vendor_name' : $('#vendor-name').data('prev'),
      'invoice' : $('#invoice').data('prev'),
      'currency' : $('#DocCur').data('prev'),
      'rate' : $('#DocRate').data('prev'),
      'warehouse_code' : $('#warehouse').data('prev'),
      'remark' : $('#remark').data('prev')
    }

    if($('.row-qty').length > 0) {    
      if(h.vendor_code != prev.vendor_code) {
        change++;
      }

      if(h.warehouse_code != prev.warehouse_code) {
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

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      click = 0;
      return false;
    }

    if(change >  0) {
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
          console.log('confirm');
        }
        else {
          $('#date-add').val(prev.date_add);
          $('#vendor-code').val(prev.vendor_code);
          $('#vendor-name').val(prev.vendor_name);
          $('#invoice').val(prev.invoice);
          $('#DocCur').val(prev.currency);
          $('#DocRate').val(prev.rate);
          $('#warehouse').val(prev.warehouse_code).change();
          $('#remark').val(prev.remark);

          $('#btn-update').addClass('hide');
          $('#btn-edit').removeClass('hide');
          $('.r').attr('disabled', 'disabled');
          click = 0;
        }
      })
    }

    return false;

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


function saveAsDraft() {
	let isDraft = 1;
	save(isDraft);
}


function save(isDraft) {
  $('.r').removeClass('has-error');

  let error = 0;
  let code = $('#receive_code').val();
  let date = $('#date-add').val();
  let vendorCode = $('#vendor-code').val();
  let vendorName = $('#vendor-name').val();
  let invoice = $.trim($('#invoice').val());
  let docCur = $('#DocCur').val();
  let docRate = parseDefault(parseFloat($('#DocRate').val()), 0);
  let discPrcnt = parseDefault(parseFloat($('#discPrcnt').val()), 0);
  let discAmount = parseDefault(parseFloat(removeCommas($('#disc-amount').val())), 0);
  let vatSum = parseDefault(parseFloat(removeCommas($('#vat-sum').val())), 0);
  let docTotal = parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0);
  let remark = $.trim($('#remark').val());
  let zoneCode = $('#zone-code').val();
  let zoneName = $('#zone-name').val();
  let poCode = $('#po-code').val();
  let approver = $('#approver').val();
  let count = $('.row-qty').length;

  if( ! isDate(date)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(vendorCode.length == 0 || vendorName.length == 0) {
    swal('กรุณาระบุผู้จำหน่าย');
    return false;
  }

  //--- มีรายการหรือไม่
  if(count = 0){
    swal('Error!', 'ไม่พบรายการรับเข้า','error');
    return false;
  }

  //--- ตรวจสอบโซนรับเข้า
  if(zoneCode.length == 0) {
    swal('กรุณาระบุโซนเพื่อรับเข้า');
    return false;
  }

  if(docRate <= 0) {
    swal('กรุณาระบุอัตราแลกเปลี่ยน');
    $('#DocRate').addClass('has-error');
    return false;
  }
  else {
    $('#DocRate').removeClass('has-error');
  }

  header = {
    'code' : code,
    'date_add' : date,
    'vendor_code' : vendorCode,
    'vendorName' : vendorName,
    'poCode' : poCode,
    'invoice' : invoice,
    'zone_code' : zoneCode,
    'approver' : approver,
    'DocCur' : docCur,
    'DocRate' : docRate,
    'DiscPrcnt' : discPrcnt,
    'DiscAmount' : discAmount,
    'VatSum' : vatSum,
    'DocTotal' : docTotal,
    'remark' : remark,
    'isDraft' : isDraft == 1 ? 1 : 0
  }

  var totalQty = 0;
  var rows = [];

  $('.row-qty').each(function() {
    let el = $(this);
    let no = el.data('id');

    if(el.val() != '') {
      let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);
      let bprice = parseDefault(parseFloat(removeCommas($('#row-bprice-'+no).val())), 0);
      let disc = parseDefault(parseFloat($('#row-disc-'+no).val()), 0);
      let price = parseDefault(parseFloat(removeCommas($('#row-price-'+no).val())), 0);
      let amount = parseDefault(parseFloat(removeCommas($('#row-total-'+no).val())), 0);

      if(qty > 0) {

        if(disc > 100 || disc < 0) {
          $('#row-disc-'+no).addClass('has-error');
          error++;
        }
        else if(bprice < 0 || bprice < price) {
          $('#row-bprice-'+no).addClass('has-error');
          error++;
        }
        else if(price < 0 || price > bprice) {
          $('#row-price-'+no).addClass('has-error');
          error++;
        }
        else {
          let amountAfDisc = amount * (1 - (discPrcnt * 0.01));
          let vatrate = parseDefault(parseFloat(el.data('vatrate')), 7) * 0.01;
          let vatAmount = roundNumber(amountAfDisc * vatrate, 2);

          let row = {
            'baseEntry' : el.data('baseentry'),
            'baseLine' : el.data('baseline'),
            'product_code' : el.data('code'),
            'product_name' : el.data('name'),
            'qty' : qty,
            'backlogs' : el.data('backlogs'),
            'PriceBefDi' : bprice,
            'DiscPrcnt' : disc,
            'price' : price,
            'currency' : docCur,
            'rate' : docRate,
            'vatGroup' : el.data('vatcode'),
            'vatRate' : el.data('vatrate'),
            'amount' : amount,
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

          rows.push(row);

          totalQty += qty;
        }
      }
    }
  });

  if(error > 0) {
    swal({
      title:'Oops !',
      text: 'กรุณาแก้ไขรายการที่ไม่ถูกต้อง',
      type:'warning'
    });

    return false;
  }

  if(rows.length < 1){
    swal('ไม่พบรายการรับเข้า');
    return false;
  }

  header['totalQty'] = totalQty;

  load_in();

  $.ajax({
    url: HOME + 'save',
    type:"POST",
    cache:"false",
    data: {
      "header" : JSON.stringify(header),
      "items" : JSON.stringify(rows)
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
              viewDetail(ds.code);
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
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
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
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        text:xhr.responseText,
        type:'error',
        html:true
      });
    }
  });

}


function validateData() {
	let date = $('#date').val();
	let vendorCode = $('#vendor-code').val();
	let vendorName = $('#vendor-name').val();
	let zoneCode = $('#zone-code').val();
	let zoneName = $('#zone-name').val();
	let rate = parseDefault(parseFloat($('#DocRate').val()), 0);

	if( ! isDate(date)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(vendorCode.length == 0 || vendorName.length == 0) {
		swal("ผู้ขายไม่ถูกต้อง");
		return false;
	}

	if(zoneCode.length == 0 || zoneName.length == 0) {
		swal("โซนไม่ถูกต้อง");
		return false;
	}

	if(rate <= 0) {
		swal("อัตราแลกเปลี่ยนไม่ถูกต้อง");
		return false;
	}

	checkLimit();
}


function checkLimit() {
	let poCode = $('#po-code').val();

	if(poCode.length) {
		//--- Allow receive over po
		var allow = $('#allow_over_po').val() == '1' ? true : false;
		var over = 0;

		$(".row-qty").each(function() {
			var limit = parseDefault(parseFloat($(this).data('limit')), 0);
			var qty = parseDefault(parseFloat($(this).val()), 0);

			if(limit >= 0 && qty > 0) {
				if(qty > limit) {
					over++;

					if( ! allow) {
						$(this).addClass('has-error');
					}
				}
				else {
					$(this).removeClass('has-error');
				}
			}
			else {
				$(this).removeClass('has-error');
			}
		});

		if( over > 0)
		{
			if( ! allow) {
				swal({
					title:'สินค้าเกิน',
					text: 'กรุณาระบุจำนวนรับไม่เกินยอดค้างร้บ',
					type:'error'
				});

				return false;
			}
			else {
				getApprove();
			}
		}
		else {
			save();
		}
	}
	else {
		save();
	}
}


$("#sKey").keyup(function(e) {
    if( e.keyCode == 13 ){
		doApprove();
	}
});


function getApprove(){
	$("#approveModal").modal("show");
}

$("#approveModal").on('shown.bs.modal', function(){ $("#sKey").focus(); });



function validate_credentials(){
	var s_key = $("#s_key").val();
	var menu 	= $("#validateTab").val();
	var field = $("#validateField").val();
	if( s_key.length != 0 ){
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approverName").val(data.approver);
					closeValidateBox();
					callback();
					return true;
				}else{
					showValidateError(rs);
					return false;
				}
			}
		});
	}else{
		showValidateError('Please enter your secure code');
	}
}


function doApprove(){
	var s_key = $("#sKey").val();
	var menu = 'ICPURC'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = 'approve';

	if( s_key.length > 0 )
	{
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approver").val(data.approver);
					$("#approveModal").modal('hide');
					save();
				}else{
					$('#approvError').text(rs);
					return false;
				}
			}
		});
	}
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
	poInit();
});


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


$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});

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

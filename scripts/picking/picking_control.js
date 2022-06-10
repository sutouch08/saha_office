$('#zoneCode').autocomplete({
  source:HOME + 'find_bin_code',
  autoFocus:true
});



$('#zoneCode').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val() != "") {
      setZone();
    }
  }
});


$('#qty').focus(function() {
  $(this).select();
});


$('#qty').keyup(function(e) {
  if(e.keyCode === 13) {
    $('#barcode-item').focus();
  }
})



function setZone() {
  let code = $.trim($('#zoneCode').val());
  if(code.length) {
    $.ajax({
      url:HOME + 'check_bin_code',
      type:'GET',
      cache:false,
      data:{
        "binCode" : code
      },
      success:function(rs) {
        if(rs === 'success') {
          $('#BinCode').val(code);
          $('#zoneCode').attr('disabled', 'disabled');
          $('#btn-submit-zone').addClass('hide');
          $('#btn-change-zone').removeClass('hide');
          $('#qty').removeAttr('disabled');
          $('#barcode-item').removeAttr('disabled');
          $('#btn-submit-item').removeAttr('disabled');
          $('#barcode-item').focus();
        }
        else {
          $('#BinCode').val('');
          swal({
            title:'Error',
            text:rs,
            type:'error'
          })
        }
      }
    })
  }
}


function changeZone() {
  $('#qty').val(1);
  $('#qty').attr('disabled', 'disabled');
  $('#barcode-item').val('');
  $('#barcode-item').attr('disabled', 'disabled');

  $('#BinCode').val('');
  $('#zoneCode').val('');
  $('#soNo').val('');
  $('#zoneCode').removeAttr('disabled', 'disabled');
  $('#btn-change-zone').addClass('hide');
  $('#btn-submit-zone').removeClass('hide');
  $('.order-btn').removeClass('btn-primary');
  $('#zoneCode').focus();
}



function clearSO() {
  $('#soNo').val('');
  $('.order-btn').removeClass('btn-primary');
  $('#barcode-item').focus();
}




$('#barcode-item').keyup(function(e) {
  if(e.keyCode == 13) {
    pickItem();
  }
});


function addToBarcode(itemCode) {
  let binCode = $('#BinCode').val();
  if(binCode.length) {
    $('#barcode-item').val(itemCode).focus();
  }
}


function showPickOption(itemCode, uomEntry) {
  let binCode = $('#BinCode').val();
  let orderCode = $('#soNo').val();

  if(binCode.length == 0) {
    swal("กรุณาระบุ Location");
    return false;
  }


  if(binCode.length) {
    $.ajax({
      url:HOME + 'get_item_uom_list',
      type:'GET',
      cache:false,
      data:{
        "ItemCode" : itemCode,
        "UomEntry" : uomEntry
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = $.parseJSON(rs);
          $('#option-title').text(itemCode);
          $('#option-item').val(itemCode);
          $('#option-qty').val(1);
          $('#option-uom').html(ds.option);
          $('#pickOptionModal').modal('show');
        }
        else {
          swal({
            title:'Error',
            text:rs,
            type:'error'
          });
        }
      }
    })
  }
}


$('#pickOptionModal').on('shown.bs.modal', function() {
  $('#option-qty').focus().select();
})



function pickWithOption() {
  let absEntry = $('#AbsEntry').val();
  let docNum = $('#DocNum').val();
  let binCode = $('#BinCode').val();
  let orderCode = $('#soNo').val();
  let qty = parseDefault(parseFloat($('#option-qty').val()), 0);
  let itemCode = $('#option-item').val();
  let uom = $('#option-uom').val();

  $('#pickOptionModal').modal('hide');

  if(binCode.length == 0) {
    swal("กรุณาระบุ Location");
    return false;
  }

  if(qty <= 0) {
    swal("จำนวนไม่ถูกต้อง");
    return false;
  }

  if(itemCode.length == 0) {
    swal({
      title:'Error!',
      text:'ไม่พบรหัสสินค้า',
      type:'error'
    });

    return false;
  }

  if(uom == "") {
    swal({
      title:'Error',
      text:'หน่วยนับไม่ถูกต้อง',
      type:'error'
    });

    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'pick_with_option',
    type:'POST',
    cache:false,
    data:{
      'AbsEntry' : absEntry,
      'DocNum' : docNum,
      'BinCode' : binCode,
      'orderCode' : orderCode,
      'ItemCode' : itemCode,
      'UomEntry' : uom,
      'qty' : qty
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let data = $.parseJSON(rs);

        for(let i = 0; i < data.length; i++) {
          let ds = data[i];
          $('#pick-'+ds.id).text(ds.picked);
          $('#balance-'+ds.id).text(ds.balance);

          $('.row-tr').removeClass('blue');
          $('#row-'+ds.id).addClass('blue');

          if(ds.balance == 0) {
            $('#row-'+ds.id).addClass('bg-green');
            $('#btn-cancle-pick-'+ds.id).addClass('hide');
            changeZone();
          }
        }

        is_all_picked();
      }
      else {
        beep();
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}



function pickItem() {

  let barcode = $.trim($('#barcode-item').val());
  let qty = parseDefault(parseFloat($('#qty').val()), 0);
  let orderCode = $('#soNo').val();

  if(barcode.length && qty != 0) {
    $('#barcode-item').val('');
    $('#qty').val(1);

    let absEntry = $('#AbsEntry').val();
    let docNum = $('#DocNum').val();
    let binCode = $('#BinCode').val();

    if(binCode.length == 0) {
      swal("กรุณาระบุ Location");
      return false;
    }

    // if(orderCode.length == 0) {
    //   swal("กรุณาระบุเลขที่ SO");
    //   return false;
    // }

    $.ajax({
      url:HOME + 'pick_item',
      type:'POST',
      cache:false,
      data:{
        'AbsEntry' : absEntry,
        'DocNum' : docNum,
        'BinCode' : binCode,
        'orderCode' : orderCode,
        'barcode' : barcode,
        'qty' : qty
      },
      success:function(rs) {
        if(isJson(rs)) {
          let data = $.parseJSON(rs);

          for(let i = 0; i < data.length; i++) {
            let ds = data[i];
            $('#pick-'+ds.id).text(ds.picked);
            $('#balance-'+ds.id).text(ds.balance);

            $('.row-tr').removeClass('blue');
            $('#row-'+ds.id).addClass('blue');

            if(ds.balance == 0) {
              $('#row-'+ds.id).addClass('bg-green');
              $('#btn-cancle-pick-'+ds.id).addClass('hide');
              changeZone();
            }
          }

          is_all_picked();
        }
        else {
          beep();
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })
  }

}

function closePick() {
  let force_close = $('#force_close').is(':checked');
  var absEntry = $('#AbsEntry').val();
  var docNum = $.trim($('#DocNum').val());
  var balance = 0;

  $('.row-tr').each(function() {
    let no = $(this).data('id');
    let relqty = parseDefault(parseFloat($('#release-'+no).text()), 0);
    let picked = parseDefault(parseFloat($('#pick-'+no).text()), 0);

    if(relqty > picked) {
      balance++;
    }
  });

  if(balance == 0) {
    finishPick();
  }
  else {
    if(force_close) {
      swal({
    		title: "คุณแน่ใจ?",
    		text: "จัดสินค้าไม่ครบ ต้องการบังคับจบหรือไม่ ?",
    		//type: "warning",
    		showCancelButton: true,
    		confirmButtonColor: "#DD6B55",
    		confirmButtonText: 'บังคับจบ',
    		cancelButtonText: 'ไม่ใช่',
    		closeOnConfirm: false
      }, function() {
        finishPick();
      });
    }
    else {
      swal({
        title:"Error!",
        text:"พบรายการที่จัดไม่ครบ",
        type:"error"
      });
    }
  }
}


function finishPick() {
  var absEntry = $('#AbsEntry').val();
  var docNum = $.trim($('#DocNum').val());

  $.ajax({
    url:HOME + 'finish_pick',
    type:'POST',
    cache:false,
    data:{
      'AbsEntry' : absEntry,
      'DocNum' : docNum
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goBack();
        }, 1200);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    },
    error:function(xhr) {
      load_out();
      swal({
        title:'Error!',
        text: xhr.responseText,
        type:'error',
        html:true
      });
    }
  });
}


function is_all_picked() {
  var balance = 0;

  $('.row-tr').each(function() {
    let id = $(this).data('id');
    let relqty = parseDefault(parseFloat($('#release-'+id).text()), 0);
    let picked = parseDefault(parseFloat($('#pick-'+id).text()), 0);

    if(relqty > picked) {

      balance++;
    }
  });

  if(balance == 0) {
    $('#finish-row').removeClass('hide');
  }
  else {
    $('#finish-row').addClass('hide');
  }
}



function toggleOrderCode(id, orderCode) {
  let binCode = $('#BinCode').val();
  $('#soNo').val(orderCode);
  $('.order-btn').removeClass('btn-primary');
  $('#order-'+id).addClass('btn-primary');

  //$('#details-table').prepend($('#row-'+id));

  if(binCode.length > 0) {
    $('#barcode-item').focus();
  }
  else {
    $('#zoneCode').focus();
  }

}



function increseQty() {
  let qty = parseDefault(parseInt($('#option-qty').val()), 0);
  if(qty >= 1) {
    qty++;
  }
  else {
    qty = 1;
  }

  $('#option-qty').val(qty);
}


function decreseQty() {
  let qty = parseDefault(parseInt($('#option-qty').val()), 0);
  if(qty > 1) {
    qty--;
  }
  else {
    qty = 1;
  }

  $('#option-qty').val(qty);
}


var intv = setInterval(function() {
  let absEntry = $('#AbsEntry').val();
  $.ajax({
    url:HOME + 'get_state',
    type:'GET',
    cache:false,
    data: {
      'AbsEntry' : absEntry
    },
    success:function(rs) {
      var rs = $.trim(rs);

      if(rs != 'ok') {
        window.location.reload();
      }
    }
  });
}, 10000);



function showCancleOption(itemCode, orderCode, id) {
  let balance = parseDefault(parseFloat($('#balance-'+id).text()), 0);

  if(balance <= 0) {
    $('#btn-cancle-pick-'+id).addClass('hide');
    return false;
  }

  $('#limit').val(balance);
  $('#pick-id').val(id);

  $.ajax({
    url:BASE_URL + 'cancle/get_items_list',
    type:'POST',
    cache:false,
    data:{
      "itemCode" : itemCode,
      "orderCode" : orderCode
    },
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#cancle-option-template').html();
        var output = $('#cancle-option-table');

        render(source, ds, output);

        $('#cancleOptionModal').modal('show');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function addToPick(id) {
  let absEntry = $('#AbsEntry').val();
  let packCode = $('#DocNum').val();
  let pickId = $('#pick-id').val();
  let limit = parseDefault(parseFloat($('#limit').val()), 0);
  let qty = parseDefault(parseFloat($('#pick-qty-'+id).val()), 0);


  if(qty <= 0) {
    return false;
  }

  if(limit < qty) {
    swal("จำนวนเกิน");
    return false;
  }

  $('#cancleOptionModal').modal('hide');

  load_in();

  $.ajax({
    url:HOME + 'pick_from_cancle',
    type:'POST',
    cache:false,
    data:{
      'AbsEntry' : absEntry,
      'DocNum' : packCode,
      'pick_detail_id' : pickId,
      'cancle_id' : id,
      'qty' : qty
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let data = $.parseJSON(rs);

        for(let i = 0; i < data.length; i++) {
          let ds = data[i];
          $('#pick-'+ds.id).text(ds.picked);
          $('#balance-'+ds.id).text(ds.balance);
          $('.row-tr').removeClass('blue');
          $('#row-'+ds.id).addClass('blue');

          if(ds.balance == 0) {
            $('#row-'+ds.id).css('background-color', '#ebf1e2');
            $('#btn-cancle-pick-'+ds.id).addClass('hide');
            changeZone();
          }
        }

        is_all_picked();
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:'error'
        })
      }
    }
  });
}



function showPickedOption(id) {
  let picked = parseDefault(parseFloat($('#pick-'+id).text()), 0);

  if(picked <= 0) {
    return false;
  }

  $('#picked-id').val(id);

  $.ajax({
    url:HOME + 'get_picking_details',
    type:'POST',
    cache:false,
    data:{
      "pick_detail_id" : id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#picked-option-template').html();
        var output = $('#picked-option-table');

        render(source, ds, output);

        $('#pickedOptionModal').modal('show');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}



function updatePicked(id) {
  const pick_detail_id = $('#picked-id').val();
  let qty = parseDefault(parseFloat($('#picked-qty-'+id).val()), 0);
  let limit = parseDefault(parseFloat($('#picked-limit-'+id).val()), 0);
  let released = parseDefault(parseFloat($('#release-'+pick_detail_id).text()), 0);
  let picked = parseDefault(parseFloat($('#pick-'+pick_detail_id).text()), 0);

  if(qty <= 0 || qty > limit) {
    $('#picked-qty-'+id).addClass('has-error');
    return false;
  }
  else {
    $('#picked-qty-'+id).removeClass('has-error');
  }

  $.ajax({
    url:HOME + 'update_picking_qty',
    type:'POST',
    cache:false,
    data:{
      'pick_detail_id' : pick_detail_id,
      'picking_id' : id,
      'qty' : qty
    },
    success:function(rs) {
      if(rs === 'success') {
        remain = limit - qty;
        picked = picked - qty;
        balance = released - picked;

        $('#picked-label-'+id).text(addCommas(remain));
        $('#picked-limit-'+id).val(remain);
        $('#pick-' + pick_detail_id).text(picked);
        $('#balance-'+ pick_detail_id).text(balance);
        $('#picked-qty-'+id).val('').focus();

        $('#row-'+pick_detail_id).removeClass('blue');
        $('#row-'+pick_detail_id).removeClass('bg-green');
        is_all_picked();
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:"error"
        });
      }
    }
  });
}



function removePickRow(id, orderCode, itemCode) {
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+itemCode+" : "+orderCode+"' หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			load_in();

			$.ajax({
				url: HOME + 'remove_pick_row',
				type:"POST",
        cache:"false",
				data:{
          'pick_detail_id' : id
				},
				success: function(rs) {
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ) {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						$('#row-'+id).remove();

            is_all_picked();
					}
          else {
						swal("Error !", rs , "error");
					}
				}
			});
	});
}




function deleteOrder() {
  let absEntry = $('#AbsEntry').val();
  let orderCode = $('#soList').val();

  if(absEntry == "") {
    swal("Error", "Missing AbsEntry", "error");
    return false;
  }

  if(orderCode == "") {
    swal("กรุณาเลือก SO");
    return false;
  }


  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ SO :"+orderCode+"  ทั้งใบหรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			load_in();

			$.ajax({
				url: HOME + 'remove_pick_order',
				type:"POST",
        cache:"false",
				data:{
          'absEntry' : absEntry,
          'orderCode' : orderCode
				},
				success: function(rs) {
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ) {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						window.location.reload();
					}
          else {
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function toggleFinishPick() {
  if($('#force_close').is(':checked')) {
    $('#finish-row').removeClass('hide');
  }
  else {
    is_all_picked();
  }
}

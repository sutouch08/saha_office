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
  $('#soNo').val('');
  $('#zoneCode').val('');
  $('#zoneCode').removeAttr('disabled', 'disabled');
  $('#btn-change-zone').addClass('hide');
  $('#btn-submit-zone').removeClass('hide');
  $('.order-btn').removeClass('btn-primary');
  $('#zoneCode').focus();
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

  if(orderCode.length == 0) {
    swal("กรุณาระบุเลขที่ SO");
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

  if(orderCode.length == 0) {
    swal("กรุณาระบุเลขที่ SO");
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
          $('#details-table').prepend($('#row-'+ds.id));
          $('.row-tr').removeClass('blue');
          $('#row-'+ds.id).addClass('blue');

          if(ds.balance == 0) {
            $('#row-'+ds.id).css('background-color', '#ebf1e2');
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

    if(orderCode.length == 0) {
      swal("กรุณาระบุเลขที่ SO");
      return false;
    }

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
            $('#details-table').prepend($('#row-'+ds.id));
            $('.row-tr').removeClass('blue');
            $('#row-'+ds.id).addClass('blue');

            if(ds.balance == 0) {
              $('#row-'+ds.id).css('background-color', '#ebf1e2');
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



function finishPick() {
  swal({
		title: "จัดเสร็จแล้ว?",
		text: "จัดสินค้าครบแล้วใช่หรือไม่ ?",
		//type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#87b87f",
		confirmButtonText: 'จัดเสร็จแล้ว',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
  }, function() {
    var absEntry = $('#AbsEntry').val();
    var docNum = $.trim($('#DocNum').val());
    var balance = 0;

    $('.row-no').each(function() {
      let no = $(this).val();
      let relqty = parseDefault(parseFloat($('#release-'+no).text()), 0);
      let picked = parseDefault(parseFloat($('#pick-'+no).text()), 0);

      if(relqty > picked) {
        balance++;
      }
    });

    if(balance == 0) {
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
      })
    }
    else {
      swal({
        title:"Error!",
        text:"พบรายการที่จัดไม่ครบ",
        type:"error"
      })
    }
  });

}


function is_all_picked() {
  var balance = 0;

  $('.row-no').each(function() {
    let no = $(this).val();
    let relqty = parseDefault(parseFloat($('#release-'+no).text()), 0);
    let picked = parseDefault(parseFloat($('#pick-'+no).text()), 0);

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

  $('#details-table').prepend($('#row-'+id));

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

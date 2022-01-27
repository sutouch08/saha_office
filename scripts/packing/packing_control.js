$('#barcode-box').keyup(function(e) {
  if(e.keyCode == 13) {
    var barcode = $(this).val();
    if(barcode.length) {
      setBox();
    }
  }
})


$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    var barcode = $(this).val();
    if(barcode.length) {
      doPacking();
    }
  }
})



function setBox() {
  var barcode = $('#barcode-box').val();
  var code = $('#code').val();

  if(barcode.length && code.length) {
    $.ajax({
      url:HOME + 'get_box',
      type:'GET',
      cache:false,
      data:{
        "barcode" : barcode,
        "code" : code
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(! isNaN(parseInt(rs))) {
          $('#box_id').val(rs);
          $('#barcode-box').attr('disabled', 'disabled');
          $('#btn-box').addClass('hide');
          $('#btn-change-box').removeClass('hide');

          $('#qty').removeAttr('disabled').val(1);
          $('#btn-item').removeAttr('disabled');
          $('#barcode-item').removeAttr('disabled').focus();

          updateBoxList();
        }
      }
    })
  }
}


function changeBox() {
  $('#box_id').val('');
  $('#barcode-item').val('').attr('disabled','disabled');
  $('#qty').val(1).attr('disabled', 'disabled');
  $('#btn-item').attr('disabled');
  $('#btn-change-box').addClass('hide');
  $('#btn-box').removeClass('hide');
  $('#barcode-box').val('').removeAttr('disabled').focus();
}


function updateBox(packQty){
  var box_id = $("#box_id").val();
  var qty = parseInt( removeCommas( $("#"+box_id).text() ) ) + packQty;
  $("#"+box_id).text(addCommas(qty));
}


function updateBoxList(){
  var box_id = $("#box_id").val();
  var code = $("#code").val();

  $.ajax({
    url: HOME + 'get_box_list',
    type:"GET",
    cache: "false",
    data:{
      "code" : code,
      "box_id" : box_id
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $("#box-template").html();
        var data = $.parseJSON(rs);
        var output = $("#box-row");
        render(source, data, output);
      }else if(rs == "no box"){
        $("#box-row").html('<span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>');
      }else{
        swal("Error!", rs, "error");
      }
    }
  });
}



function showPackOption(itemCode, uomEntry) {
  let box_id = $('#box_id').val();
  if(box_id.length) {
    $.ajax({
      url:BASE_URL + 'picking/get_item_uom_list',
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
          $('#packOptionModal').modal('show');
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
  else {
    swal("กรุณาระบุกล่อง");
  }
}



function packWithOption() {
  let id = $('#id').val();
  let code = $('#code').val();
  let box_id = $('#box_id').val();
  let qty = parseDefault(parseFloat($('#option-qty').val()), 0);
  let itemCode = $('#option-item').val();
  let uom = $('#option-uom').val();

  $('#packOptionModal').modal('hide');

  if(box_id.length == 0) {
    swal("กรุณาระบุกล่อง");
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
    url:HOME + 'pack_with_option',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'box_id' : box_id,
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

          $('#pack-'+ds.id).text(ds.packed);
          $('#balance-'+ds.id).text(ds.balance);
          $('#row-table').prepend($('#row-'+ds.id));
          $('.row-tr').removeClass('blue');
          $('#row-'+ds.id).addClass('blue');

          if(ds.valid) {
            $('#row-'+ds.id).css('background-color', '#ebf1e2');
          }

          updateBox(ds.pack_qty);
          //--- อัพเดตยอดตรวจรวมทั้งออเดอร์
          //--- จำนวนสินค้าที่ตรวจแล้วทั้งออเดอร์ (รวมที่ยังไม่บันทึกด้วย)
          let all_qty = parseDefault(parseFloat( removeCommas( $("#all_qty").text() ) ), 0) + ds.pack_qty;
          $("#all_qty").text( addCommas(all_qty));
          $('#qty').val(1);
          $('#barcode-item').removeAttr('disabled').val('').focus();
        }

        validateDetail();
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

function doPacking() {
  let box_id = parseDefault(parseInt($('#box_id').val()), 0);
  let code = $('#code').val();
  let barcode = $('#barcode-item').val();
  let qty = parseDefault(parseFloat($('#qty').val()), 0);

  if(box_id == "" || box_id == 0) {
    swal("กรุณาระบุกล่อง");
    return false;
  }

  if(barcode.length == 0) {
    return false;
  }

  if(qty <= 0) {
    swal("จำนวนไม่ถูกต้อง");
    return false;
  }

  $('#barcode-item').attr('disabled', 'disabled');

  $.ajax({
    url:HOME + 'do_packing',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'box_id' : box_id,
      'barcode' : barcode,
      'qty' : qty
    },
    success:function(rs) {
      if(isJson(rs)) {
        let data = $.parseJSON(rs);

        for(let i = 0; i < data.length; i++) {
          let ds = data[i];

          $('#pack-'+ds.id).text(ds.packed);
          $('#balance-'+ds.id).text(ds.balance);

          $('#row-table').prepend($('#row-'+ds.id));
          $('.row-tr').removeClass('blue');
          $('#row-'+ds.id).addClass('blue');

          if(ds.valid) {
            $('#row-'+ds.id).css('background-color', '#ebf1e2');
          }

          updateBox(ds.pack_qty);
          //--- อัพเดตยอดตรวจรวมทั้งออเดอร์
          //--- จำนวนสินค้าที่ตรวจแล้วทั้งออเดอร์ (รวมที่ยังไม่บันทึกด้วย)
          let all_qty = parseDefault(parseFloat( removeCommas( $("#all_qty").text() ) ), 0) + ds.pack_qty;
          $("#all_qty").text( addCommas(all_qty));
          $('#qty').val(1);
          $('#barcode-item').removeAttr('disabled').val('').focus();
        }

        validateDetail();
      }
      else {
        $('#barcode-item').removeAttr('disabled').val('');

        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })

        beep();
      }
    }
  })
}


function validateDetail() {
  let balance = 0;

  $('.balance').each(function() {
    let qty = parseDefault(parseFloat($(this).text()), 0);

    if(qty > 0)
    {
      balance += qty;
    }
  });

  if(balance == 0) {
    $('#finish-row').removeClass('hide');
  }
  else {
    $('#finish-row').addClass('hide');
  }
}



function finish_pack() {
  let id = $('#id').val();
  let code = $('#code').val();
  let balance = 0;

  $('.balance').each(function() {
    let qty = parseDefault(parseFloat($(this).text()), 0);

    if(qty > 0) {
      balance += qty;
    }
  });

  if(balance == 0) {
    load_in();
    $.ajax({
      url:HOME + 'finish_pack',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'code' : code
      },
      success:function(rs) {
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success') {
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
            title:'Error',
            text:rs,
            type:'error'
          })
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
  else {
    swal({
      title:"Error!",
      text:"พบรายการที่แพ็คไม่ครบ",
      type:"error"
    });
  }
}

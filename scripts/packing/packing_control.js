
$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    var barcode = $(this).val();
    if(barcode.length) {
      doPacking();
    }
  }
})


function setBox(id) {
  $('#box_id').val(id);
  $('.box-btn').removeClass('btn-success');
  $('#btn-box-'+id).addClass('btn-success');
  $('#barcode-item').focus();
}


function addBox() {
  var code = $('#code').val();

  if(code.length) {
    $.ajax({
      url:HOME + 'add_box',
      type:'POST',
      cache:false,
      data:{
        "code" : code
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(! isNaN(parseInt(rs))) {
          $('#box_id').val(rs);
          updateBoxList();
        }
      }
    })
  }
}


function updateBox(packQty){
  var box_id = $("#box_id").val();
  var packQty = parseDefault(parseFloat(packQty), 0);
  var qty = parseDefault(parseFloat($("#"+box_id).text()), 0) + packQty;

  $("#"+box_id).text(qty);
}


function updatePackQty() {
  var packed = 0;
  $('.packed').each(function() {
    let qty = parseDefault(parseFloat($(this).text()), 0);
    packed += qty;
  });

  $('#all_qty').text(packed);
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
        $('#barcode-item').focus();

        updatePackQty();
      }
      else if(rs == "no box"){
        $("#box-row").html('<span id="no-box-label">ยังไม่มีการตรวจสินค้า</span>');
      }
      else{
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


$('#packOptionModal').on('shown.bs.modal', function() {
  $('#option-qty').focus().select();
})

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
          // let all_qty = parseDefault(parseFloat( removeCommas( $("#all_qty").text() ) ), 0) + ds.pack_qty;
          // $("#all_qty").text( addCommas(all_qty));
          updatePackQty();

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



function showBoxOption() {
  code = $('#code').val();
  box_id = "1";
  $.ajax({
    url:HOME + 'get_box_list',
    type:'GET',
    data:{
      'code' : code,
      'box_id' : box_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#box-list-template').html();
        var output = $('#box-list-table');
        render(source, ds, output);

        $('#boxOptionModal').modal('show');
      }
      else {
        swal({
          title:"Error !",
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function check_box_all() {
  if($('#box-chk-all').is(':checked')) {
    $('.box-chk').prop('checked', true);
  }
  else {
    $('.box-chk').prop('checked', false);
  }
}


function editBox(box_id) {
  var code = $('#code').val();
  $('#boxOptionModal').modal('hide');

  $.ajax({
    url:HOME + 'get_pack_box_details',
    type:'GET',
    cache:false,
    data:{
      "code" : code,
      "box_id" : box_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#box-detail-template').html();
        var output = $('#box-detail-table');
        render(source, data, output);
        $('#boxEditModal').modal('show');
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:"error"
        });
      }
    }
  })
}



function removePackDetail(id, text) {
  swal({
		title: "ลบรายการ",
		text: "ต้องการลบรายการ "+text+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d15b47",
		confirmButtonText: 'ใช่ ต้องการลบ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
  },function() {
    $.ajax({
      url:HOME + 'delete_pack_detail',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'id' : id
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(rs == 'success') {
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#box-row-'+id).remove();

          updatePackTable();
        }
        else {
          swal({
            titel:"Error!",
            text:rs,
            type:'error'
          })
        }
      }
    })
  });
}


function removeBox(box_id, box_no) {
  var id = $('#id').val();
  var code = $('#code').val();

  swal({
		title: "ลบกล่อง",
		text: "ต้องการลบรายการ กล่องที่ "+box_no+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d15b47",
		confirmButtonText: 'ใช่ ต้องการลบ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
  },function() {

    $.ajax({
      url:HOME + 'delete_pack_box',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'id' : id,
        'box_id' : box_id
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(rs == 'success') {
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            window.location.reload();
          }, 1200);

        }
        else {
          swal({
            titel:"Error!",
            text:rs,
            type:'error'
          })
        }
      }
    })
  });
}


function removeSelectedBox() {
  var code = $('#code').val();
  var boxes = [];
  var box_no = "";
  var no = 1;

  $('.box-chk').each(function() {
    if($(this).is(':checked')) {
      boxes.push({"box_id" : $(this).val()});

      box_no = no == 1 ? box_no + $(this).data('no') : box_no + ", "+$(this).data('no');
      no++;
    }
  });

  if(boxes.length > 0) {

    $('#boxOptionModal').modal('hide');

    swal({
  		title: "ลบกล่อง",
  		text: "ต้องการลบรายการ กล่องที่ "+box_no+" หรือไม่ ?",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#d15b47",
  		confirmButtonText: 'ใช่ ต้องการลบ',
  		cancelButtonText: 'ไม่ใช่',
  		closeOnConfirm: false
    }, function() {
      $.ajax({
        url:HOME + 'delete_select_box',
        type:'POST',
        cache:false,
        data:{
          "code" : code,
          "boxes" : JSON.stringify(boxes)
        },
        success:function(rs) {
          var rs = $.trim(rs);
          if(rs == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(function() {
              window.location.reload();
            }, 1200);
          }
          else {
            swal({

            })
          }
        }
      });
    });
  }
}


function updatePackTable() {
  var id = $('#id').val();

  $.ajax({
    url:HOME + 'get_details_table',
    type:'GET',
    cache:false,
    data:{
      'id' : id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#details-template').html();
        var output = $('#row-table');

        render(source, data, output);

        updateBoxList();
      }
    }
  })
}



function printBox(box_id) {
  var code = $('#code').val();
  //--- properties for print
  var center  = ($(document).width() - 800)/2;
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";
  var target  = HOME + 'print_box/'+ code +'/'+box_id;
  print_url(target);
  //window.open(target, '_blank', prop);
}


function printSelectedBox() {
  var box_id = "";
  var i = 1;
  $('.box-chk').each(function() {
    if($(this).is(':checked')) {
      box_id = i == 1 ? box_id + $(this).val() : box_id + '-'+$(this).val();
      i++;
    }
  })

  var code = $('#code').val();
  var target  = HOME + 'print_selected_boxes/'+ code +'/'+box_id;

  print_url(target);
  //window.open(target, '_blank', prop);
}

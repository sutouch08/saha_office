$('#fromWhsCode').autocomplete({
  source:BASE_URL+'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);

      if($('#toWhsCode').val() == "") {
        $('#toWhsCode').focus();
      }
      else {
        $('#remark').focus();
      }
    }
    else {
      $(this).val('');
    }
  }
});



$('#toWhsCode').autocomplete({
  source:BASE_URL+'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);

      if($('#fromWhsCode').val() == "") {
        $('#fromWhsCode').focus();
      }
      else {
        $('#remark').focus();
      }
    }
    else {
      $(this).val('');
    }
  }
});


function add() {
  let remark = $.trim($('#remark').val());
  let date = $('#docDate').val();
  let fromWhsCode = $('#fromWhsCode').val();
  let toWhsCode = $('#toWhsCode').val();

  if(fromWhsCode.length == 0) {
    $('#fromWhsCode').addClass('has-error');
    return false;
  }
  else {
    $('#fromWhsCode').removeClass('has-error');
  }

  if(toWhsCode.length == 0) {
    $('#toWhsCode').addClass('has-error');
    return false;
  }
  else {
    $('#toWhsCode').removeClass('has-error');
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'docDate' : date,
      'toWhsCode' : toWhsCode,
      'fromWhsCode' : fromWhsCode,
      'remark' : remark
    },
    success:function(rs) {
      var id = $.trim(rs);
      if(!isNaN(id)) {
        goEdit(id);
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


function saveAdd() {
  let docDate = $('#docDate').val();
  let toWhsCode = $('#toWhsCode').val();
  let toBinCode = $('#toBinCode').val();
  let remark = $('#remark').val();
  let palletCode = $('#pallet-code').val();

  if(! isDate(docDate)) {
    $('#docDate').addClass('has-error');
    return false;
  }
  else {
    $('#docDate').removeClass('has-error');
  }

  if(toBinCode.length == 0) {
    $('#toBinCode').addClass('has-error');
    return false;
  }
  else {
    $('#toBinCode').removeClass('has-error');
  }

  if(palletCode.length == 0) {
    $('#pallet-code').addClass('has-error');
    return false;
  }
  else {
    $('#pallet-code').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'docDate' : docDate,
      'toWhsCode' : toWhsCode,
      'toBinCode' : toBinCode,
      'remark' : remark,
      'palletCode' : palletCode
    },
    success:function(rs) {
      var id = $.trim(rs);
      if(! isNaN(id)) {

        $.ajax({
          url:HOME + 'send_to_sap',
          type:'POST',
          cache:false,
          data:{
            "id" : id
          },
          success:function(ds) {
            load_out();
            var ds = $.trim(ds);
            if(ds == 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(function() {
                goDetail(id);
              }, 1200);
            }
            else {
              swal({
                title:"Error",
                text:"บันทึกเอกสารบนเว็บสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ",
                type:'error',
                showCancelButton: false,
            		confirmButtonColor: "#d15b47",
            		confirmButtonText: 'รับทราบ',
            		closeOnConfirm: false
              }, function() {
                goDetail(id);
              });
            }
          }
        })
      }
      else {

        load_out();
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  });
}


function edit() {
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update() {
  let id = $('#id').val();
  let docDate = $('#docDate').val();
  let toWhsCode = $('#toWhsCode').val();
  let fromWhsCode = $('#fromWhsCode').val();
  let remark = $('#remark').val();

  if(! isDate(docDate)) {
    $('#docDate').addClass('has-error');
    return false;
  }
  else {
    $('#docDate').removeClass('has-error');
  }

  if(fromWhsCode.length == 0) {
    $('#fromWhsCode').addClass('has-error');
    return false;
  }
  else {
    $('#fromWhsCode').removeClass('has-error');
  }

  if(toWhsCode.length == 0) {
    $('#toWhsCode').addClass('has-error');
    return false;
  }
  else {
    $('#toWhsCode').removeClass('has-error');
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'docDate' : docDate,
      'toWhsCode' : toWhsCode,
      'fromWhsCode' : fromWhsCode,
      'remark' : remark
    },
    success:function(rs) {
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        $('.edit').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  });
}



$('#toBinCode').autocomplete({
  source:HOME + 'get_buffer_bin_code',
  autoFocus:true,
  close:function() {
    let text = $(this).val();
    if(text == 'not found') {
      $(this).val('');
    }

    let arr = text.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0])
    }
    else {
      $(this).val('');
    }
  }
});


$('#toBinCode').keyup(function(e) {
  if(e.keyCode === 13) {
    setTimeout(function() {
      $('#remark').focus();
    }, 250);
  }
})



function recalTotal() {
  let totalQty = 0;

  $('.qty').each(function() {
    let qty = parseDefault(parseFloat(removeCommas($(this).text())), 0);
    totalQty += qty;
  });

  $('#total-qty').text(addCommas(totalQty.toFixed(2)));
}






$('#docDate').datepicker({
  dateFormat:'dd-mm-yy'
})

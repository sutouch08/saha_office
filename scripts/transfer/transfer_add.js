function add() {
  var remark = $.trim($('#remark').val());

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
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
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


$('#pallet-code').keyup(function(e) {
  if(e.keyCode === 13) {
    setTimeout(function() {
      addToList();
    }, 250);
  }
})


$('#pallet-code').autocomplete({
  source:HOME + 'get_open_pallet',
  autoFocus:true,
  close:function() {
    let code = $(this).val();
    if(code == 'not found') {
      $(this).val('');
    }
  }
});


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



function addToList() {
  let palletCode = $('#pallet-code').val();

  $('#pallet-code').removeClass('has-error');

  if(palletCode.length == 0) {
    $('#pallet-code').addClass('has-error');
    return false;
  }

  $.ajax({
    url:HOME + 'get_item_in_pallet',
    type:'GET',
    cache:false,
    data:{
      'palletCode' : palletCode
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = $.parseJSON(rs);
        if(ds.length) {
          ds.forEach(function(data) {
            if($('#row-'+data.id).length == 0) {
              let source = $('#transfer-template').html();
              let output = $('#transfer-table');
              render_append(source, data, output);
            }
          });

          reIndex();
          recalTotal();
          $('#btn-add').addClass('hide');
          $('#btn-change').removeClass('hide');
          $('#pallet-code').attr('disabled', 'disabled');
          $('#btn-save').removeAttr('disabled');
        }
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:'error'
        });

        $('#pallet-code').addClass('has-error');
      }
    }
  })
}



function changePallet() {
  $('#transfer-table').html('');
  $('#total-qty').text('0');
  $('#btn-change').addClass('hide');
  $('#btn-add').removeClass('hide');
  $('#pallet-code').removeAttr('disabled');
  $('#btn-save').attr('disabled', 'disabled');
  $('#pallet-code').val('').focus();
}

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

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


$('#docDate').datepicker({
  dateFormat:'dd-mm-yy'
})

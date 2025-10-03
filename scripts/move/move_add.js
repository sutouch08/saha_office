function fromZoneInit() {
  let whsCode = $('#fromWhsCode').val();

  $.ajax({
    url:HOME + 'get_zone_list',
    type:'POST',
    cache:false,
    data: {
      'whsCode' : whsCode
    },
    success:function(rs) {
      load_out();

      options = `<option value="" data-whsCode="" data-name="">Select</option>`;
      options = options + rs;
      $('#from-zone').html(options);
      $('#from-zone').select2().change();
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function toZoneInit() {
  let whsCode = $('#toWhsCode').val();

  $.ajax({
    url:HOME + 'get_zone_list',
    type:'POST',
    cache:false,
    data: {
      'whsCode' : whsCode
    },
    success:function(rs) {
      load_out();

      options = `<option value="" data-whsCode="" data-name="">Select</option>`;
      options = options + rs;
      $('#to-zone').html(options);
      $('#to-zone').select2().change();
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function add() {
  let remark = $.trim($('#remark').val());
  let date = $('#docDate').val();
  let fromWhsCode = $('#fromWhsCode').val();
  let toWhsCode = $('#toWhsCode').val();

  if(! isDate(date)) {
    $('#docDate').addClass('has-error');
    return false;
  }
  else {
    $('#docDate').removeClass('has-error');
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

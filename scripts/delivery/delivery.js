var HOME = BASE_URL + 'delivery/';

function goBack() {
  window.location.href = HOME;
}



function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function showSupportList() {
  $('#supportModal').modal('show');
}


function closeModal() {
  $('#supportModal').modal('hide');
}


function addChecked() {
  var names = "";
  var i = 1;
  $('.chk').each(function() {
    if($(this).is(':checked')) {
      names += i == 1 ? $(this).data('empname') : ", "+$(this).data('empname');
      i++;
    }
  });

  $('#support-label').val(names);

  closeModal();
}

$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});



function add() {
  let date = $('#date_add').val();
  let vehicle = $('#vehicle').val();
  let driver = $('#driver').val();
  let route = $('#route').val();
  let support = [];

  if(!isDate(date)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(vehicle == "") {
    swal("กรุณาระบุทะเบียนรถ");
    return false;
  }

  if(driver == "") {
    swal("กรุณาระบุพนักงานขับรถ");
    return false;
  }

  if(route == "") {
    swal("กรุณาระบุเส้นทาง");
    return false;
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      support.push($(this).val());
    }
  });


  let data = {
    "date" : date,
    "vehicle" : vehicle,
    "driver" : driver,
    "route" : route,
    "support" : support
  };

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:data,
    success:function(rs) {
      load_out();
      rs = $.trim(rs);
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);

        goEdit(ds.code);
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  })
}



function saveAdd() {
  let emp_id = $('#emp_id').val();
  let type = $('#type').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  if(emp_id == "") {
    swal("กรุณาเลือกพนักงาน");
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'emp_id' : emp_id,
      'type' : type,
      'active' : active
    },
    success:function(rs) {
      rs = $.trim(rs);

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goAdd();
        }, 1200);
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



function update() {
  let emp_id = $('#emp_id').val();
  let type = $('#type').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      "emp_id" : emp_id,
      "type" : type,
      "active" : active
    },
    success:function(rs) {
      rs = $.trim(rs);

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}


function getDelete(id, emp_name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ emp_name +' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete',
      type:'POST',
      cache:false,
      data:{
        'emp_id' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500)

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
  })
}


function docNumInit(no) {
  docType = $('#docType-'+no).val();

  $('#docNum-'+no).autocomplete({
    source:HOME + 'get_doc_num/'+docType,
    minLength:2,
    select:function(event, ui) {
      $('#CardCode-'+no).val(ui.item.CardCode);
      $('#CardName-'+no).val(ui.item.CardName);
      $('#shipTo-'+no).val(ui.item.shipTo);
      $('#docTotal-'+no).val(ui.item.docTotal);
    }
  });
}


// $('.docNum').autocomplete({
//   source:function(request, response) {
//     var no = $(this).data('no');
//     console.log(no);
//     var docType = $('#docType-'+no).val();
//     console.log(docType);
//     $.ajax({
//       url:HOME + 'get_doc_num/'+docType,
//       dataType:"jsonp",
//       data: {
//         term: request.term
//       },
//       success: function(data) {
//         response(data);
//       }
//     });
//   },
//   minLength: 2,
//   select: function(event, ui) {
//     $('#CardCode-'+no).val(ui.item.CardCode);
//     $('#CardName-'+no).val(ui.item.CardName);
//     $('#shipTo-'+no).val(ui.item.shipTo);
//     $('#docTotal-'+no).val(ui.item.docTotal);
//   }
// });

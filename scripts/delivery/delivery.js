var HOME = BASE_URL + 'delivery/';

function goBack() {
  window.location.href = HOME;
}

function leave(){
  swal({
    title:'คุณแน่ใจ ?',
    text:'รายการทั้งหมดจะไม่ถูกบันทึก ต้องการออกหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'ไม่ใช่',
    confirmButtonText:'ออกจากหน้านี้',
  },
  function(){
    goBack();
  });
}

function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}

function viewDetail(code) {
	window.location.href = HOME + 'view_detail/'+code;
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

$('#docDate').datepicker({
  dateFormat:'dd-mm-yy'
});

$('#shipDate').datepicker({
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


$('#shipFromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#shipToDate').datepicker('option', 'minDate', sd);
  }
});

$('#shipToDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#shipFromDate').datepicker('option', 'maxDate', sd);
  }
});



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

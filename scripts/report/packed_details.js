var HOME = BASE_URL + 'report/packed_details/';

function toggleAllUser(option) {
  $('#allUser').val(option);

  if(option == 0) {
    $('#btn-user-all').removeClass('btn-primary');
    $('#btn-user-range').addClass('btn-primary');
    $('#user-modal').modal('show');
  }

  if(option == 1) {
    $('#btn-user-range').removeClass('btn-primary');
    $('#btn-user-all').addClass('btn-primary');
  }
}


function toggleSelectDate(option) {
  $('#selectDate').val(option);

  if(option == 'SO') {
    $('#btn-finish-date').removeClass('btn-primary');
    $('#btn-doc-date').removeClass('btn-primary');
    $('#btn-so-date').addClass('btn-primary');
  }

  if(option == 'Finish') {
    $('#btn-so-date').removeClass('btn-primary');
    $('#btn-doc-date').removeClass('btn-primary');
    $('#btn-finish-date').addClass('btn-primary');
  }

  if(option == 'DocDate') {
    $('#btn-so-date').removeClass('btn-primary');
    $('#btn-finish-date').removeClass('btn-primary');
    $('#btn-doc-date').addClass('btn-primary');
  }
}


function checkAll() {
  var isChecked = $('#check-all').is(':checked');
  if(isChecked) {
    $('.chk').each(function(){
      this.checked = true;
    })
  }
  else {
    $('.chk').each(function(){
      this.checked = false;
    })
  }
}


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
})



function getReport() {
  let allUser = $('#allUser').val();
  let selectDate = $('#selectDate').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let users = [];

  if(allUser == 0) {
    $('.chk').each(function() {
      if($(this).is(':checked')) {
        users.push($(this).val());
      }
    });
  }

  if(allUser == 0 && users.length == 0) {
    swal("กรุณาระบุ User");
    $('#user-modal').modal('show');
    return false;
  }

  if(!isDate(fromDate) || !isDate(toDate)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:false,
    data:{
      'allUser' : allUser,
      'selectDate' : selectDate,
      'fromDate' : fromDate,
      'toDate' : toDate,
      'users' : users
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = $.parseJSON(rs);
        let source = $('#template').html();
        let output = $('#rs');

        render(source, ds, output);
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



function doExport() {
  let allUser = $('#allUser').val();
  let selectDate = $('#selectDate').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let users = [];

  if(allUser == 0) {
    $('.chk').each(function() {
      if($(this).is(':checked')) {
        users.push($(this).val());
      }
    });
  }

  if(allUser == 0 && users.length == 0) {
    swal("กรุณาระบุ User");
    $('#user-modal').modal('show');
    return false;
  }

  if(!isDate(fromDate) || !isDate(toDate)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  let token = new Date().getTime();
  $('#token').val(token);

  get_download(token);

  $('#reportForm').submit();
}

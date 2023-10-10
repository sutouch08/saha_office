var HOME = BASE_URL + 'report/delivery_backlogs/';

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


function toggleCustomer() {
  let allCust = $('#allCust').val();

  if(allCust == 1) {
    $('#custFrom').val('').attr('disabled', 'disabled')
    $('#custTo').val('').attr('disabled', 'disabled');
  }
  else {
    $('#custFrom').val('').removeAttr('disabled').focus();
    $('#custTo').val('').removeAttr('disabled');
  }
}


$('#custFrom').autocomplete({
  source: BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      let from = arr[0];
      let to = $('#custTo').val();
      $(this).val(from);
      if(to.length && (from > to)) {
        $('#custFrom').val(to);
        $('#custTo').val(from);
      }
    }
  }
});

$('#custTo').autocomplete({
  source: BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      let from = $('#custFrom').val()
      let to = arr[0];

      $(this).val(to);
      if(from.length && (from > to)) {
        $('#custFrom').val(to);
        $('#custTo').val(from);
      }
    }
  }
});


function getReport() {
  let dateType = $('#dateType').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let docType = $('#docType').val();
  let allCust = $('#allCust').val();
  let custFrom = $('#custFrom').val();
  let custTo = $('#custTo').val();

  if(! isDate(fromDate) || ! isDate(toDate)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }
  else {
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  if( allCust == 0 && (custFrom.length == 0 || custTo.length == 0)) {
    $('#custFrom').addClass('has-error');
    $('#custTo').addClass('has-error');
    swal("กรุณาระบุรหัสลูกค้า");
    return false;
  }
  else {
    $('#custFrom').removeClass('has-error');
    $('#custTo').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "dateType" : dateType,
      "fromDate" : fromDate,
      "toDate" : toDate,
      "docType" : docType,
      "allCust" : allCust,
      "custFrom" : custFrom,
      "custTo" : custTo
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let data = JSON.parse(rs);
        let source = $('#report-template').html();
        let output = $('#result');

        render(source, data, output);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  });
}


function doExport() {
  let dateType = $('#dateType').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let docType = $('#docType').val();
  let allCust = $('#allCust').val();
  let custFrom = $('#custFrom').val();
  let custTo = $('#custTo').val();
  let token	= new Date().getTime();

  if(! isDate(fromDate) || ! isDate(toDate)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }
  else {
    $('#fromDate').removeClass('has-error');
    $('#toDate').removeClass('has-error');
  }

  if( allCust == 0 && (custFrom.length == 0 || custTo.length == 0)) {
    $('#custFrom').addClass('has-error');
    $('#custTo').addClass('has-error');
    swal("กรุณาระบุรหัสลูกค้า");
    return false;
  }
  else {
    $('#custFrom').removeClass('has-error');
    $('#custTo').removeClass('has-error');
  }

  $('#token').val(token);

  get_download(token);

  $('#reportForm').submit();

}

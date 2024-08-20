var HOME = BASE_URL + 'report/sales_order_backlogs/';

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
  $('.e').removeClass('has-error');

  let filter = {
    'date_type' : $('#dateType').val(),
    'from_date' : $('#fromDate').val(),
    'to_date' : $('#toDate').val(),
    'customer' : $.trim($('#customer').val()),
    'so_code' : $.trim($('#soCode').val()),
    'item_code' : $.trim($('#itemCode').val())
  };

  if(! isDate(filter.from_date) || ! isDate(filter.to_date)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "filter" : JSON.stringify(filter)
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
  $('.e').removeClass('has-error');

  let dateType = $('#dateType').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let token	= new Date().getTime();

  if(! isDate(fromDate) || ! isDate(toDate)) {
    $('#fromDate').addClass('has-error');
    $('#toDate').addClass('has-error');
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  $('#token').val(token);

  get_download(token);

  $('#reportForm').submit();

}

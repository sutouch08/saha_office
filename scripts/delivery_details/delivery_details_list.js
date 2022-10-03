var HOME = BASE_URL + 'delivery_details/';

function goBack() {
  window.location.href = HOME;
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
});


$('#release_from').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#release_to').datepicker('option', 'minDate', sd);
  }
});

$('#release_to').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#release_from').datepicker('option', 'maxDate', sd);
  }
});


$('#finish_from').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#finish_to').datepicker('option', 'minDate', sd);
  }
});

$('#finish_to').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#finish_from').datepicker('option', 'maxDate', sd);
  }
});

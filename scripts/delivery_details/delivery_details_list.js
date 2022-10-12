var HOME = BASE_URL + 'delivery_details/';

function goBack() {
  window.location.href = HOME;
}


function exportFilter() {
  let code = $('#code').val();
  let vehicle = $('#vehicle').val();
  let driver = $('#driver').val();
  let route = $('#route').val();
  let cardCode = $('#CardCode').val();
  let cardName = $('#CardName').val();
  let contact = $('#contact').val();
  let uname = $('#uname').val();
  let shipType = $('#shipType').val();
  let docType = $('#docType').val();
  let docNum = $('#docNum').val();
  let resultStatus = $('#resultStatus').val();
  let lineStatus = $('#lineStatus').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let shipFrom = $('#ship_from_date').val();
  let shipTo = $('#ship_to_date').val();
  let releaseFrom = $('#release_from').val();
  let releaseTo = $('#release_to').val();
  let closeFrom = $('#finish_from').val();
  let closeTo = $('#finish_to').val();
  let token	= new Date().getTime();

  $('#x-code').val(code);
  $('#x-vehicle').val(vehicle);
  $('#x-driver').val(driver);
  $('#x-route').val(route);
  $('#x-cardCode').val(cardCode);
  $('#x-cardName').val(cardName);
  $('#x-contact').val(contact);
  $('#x-uname').val(uname);
  $('#x-shipType').val(shipType);
  $('#x-docType').val(docType);
  $('#x-docNum').val(docNum);
  $('#x-resultStatus').val(resultStatus);
  $('#x-lineStatus').val(lineStatus);
  $('#x-fromDate').val(fromDate);
  $('#x-toDate').val(toDate);
  $('#x-shipFrom').val(shipFrom);
  $('#x-shipTo').val(shipTo);
  $('#x-releaseFrom').val(releaseFrom);
  $('#x-releaseTo').val(releaseTo);
  $('#x-closeFrom').val(closeFrom);
  $('#x-closeTo').val(closeTo);
  $('#token').val(token);

  get_download(token);

  $('#exportForm').submit();
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

$('#ship_from_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#ship_to_date').datepicker('option', 'minDate', sd);
  }
});

$('#ship_to_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#ship_from_date').datepicker('option', 'maxDate', sd);
  }
});

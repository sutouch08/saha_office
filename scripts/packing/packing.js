var HOME = BASE_URL + 'packing/';

function goBack(){
  window.location.href = HOME;
}



function goProcess(id){
	window.location.href = HOME + 'process/'+id;
}



function viewProcess() {
  window.location.href = HOME + 'view_process'
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});


$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function preview(id) {
  $('#preview-date').text($('#date-'+id).text());
  $('#preview-code').text($('#code-'+id).text());
  $('#preview-so').text($('#so-'+id).text());
  $('#preview-pick').text($('#pick-'+id).text());
  $('#preview-cust').text($('#cust-'+id).text());
  $('#preview-uname').text($('#uname-'+id).text());

  $('#previewModal').modal('show');
}

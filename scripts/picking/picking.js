var HOME = BASE_URL + "picking/";

function goBack(){
  window.location.href = HOME;
}

function processList() {
  window.location.href = HOME + 'process_list';
}


function goPicking(absEntry) {
  window.location.href = HOME + 'process/'+absEntry;
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

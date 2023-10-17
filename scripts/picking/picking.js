var HOME = BASE_URL + "picking/";

function goBack(){
  window.location.href = HOME;
}

function processList() {
  window.location.href = HOME + 'process_list';
}


function goPicking(absEntry) {
  let uuid = localStorage.getItem('ix_uuid');
  $.ajax({
    url:HOME + 'is_document_avalible',
    type:'GET',
    data:{
      'AbsEntry' : absEntry,
      'uuid' : uuid
    },
    success:function(rs) {
      if(rs === 'available') {
        window.location.href = HOME + 'process/'+absEntry+'/'+uuid;
      }
      else {
        swal({
          title:'Oops!',
          text:'เอกสารกำลังถูกเปิด/แก้ไข โดยเครื่องอื่นอยู่ ไม่สามารถแก้ไขได้ในขณะนี้',
          type:'warning'
        });
      }
    }
  });
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

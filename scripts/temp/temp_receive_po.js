var HOME = BASE_URL + 'temp/temp_receive_po/';


function goBack(){
  window.location.href = HOME;
}



function getSearch(){
  $("#searchForm").submit();
}


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){ goBack(); });
}


$(".search").keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose:function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});


function get_detail(id)
{
  //--- properties for print
  var prop = "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= HOME + 'get_detail/'+id+'?nomenu';
	window.open(target, "_blank", prop );
}



function removeTemp(docEntry, code) {
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+code+' หรือไม่?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'remove_temp/'+docEntry,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						type: 'success',
						timer: 1000
					});

					$('#row-'+docEntry).remove();
					reIndex();
				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	});
}

function setSuccess(docEntry, code) {
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการเปลี่ยนสถาน '+code+' เป็น สำเร็จ หรือไม่?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
	}, function(){
		$.ajax({
			url:HOME + 'set_temp_to_success/'+docEntry,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ) {
          setTimeout(() => {
            swal({
              title:'Success',
              type: 'success',
              timer: 1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }, 100);


				}
        else {
          setTimeout(() => {
            swal("ข้อผิดพลาด", rs, "error");
          }, 200);
				}
			}
		});
	});
}

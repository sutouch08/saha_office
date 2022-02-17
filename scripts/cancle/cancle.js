var HOME = BASE_URL + 'cancle/';


function goBack()
{
  window.location.href = HOME;
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


function checkAll() {
  if($('#check-all').is(':checked')) {
    $('.check-item').prop('checked', true);
  }
  else {
    $('.check-item').prop('checked', false);
  }
}



function getDelete(id, code)
{
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+code+"' หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			load_in();

			$.ajax({
				url: HOME + 'delete_cancle',
				type:"POST",
        cache:"false",
				data:{
          'id' : id,
					'code' : code
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Success',
							text:'ยกเลิกรายการเรียบร้อยแล้ว',
							type:'success',
							timer:1000
						});

						$('#row-'+id).remove();
            reIndex();
            
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}



function deleteSelected()
{
  row = 0;
  ids = "";
  i = 1;

  $('.check-item').each(function() {
    if($(this).is(':checked')) {
      row++;
      ids = i == 1 ? $(this).val() : ids + ","+$(this).val();
      i++;
      console.log($(this).val());
    }
  })


  if(row == 0) {
    return false;
  }


	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ "+row+" รายการที่เลือกหรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			load_in();

			$.ajax({
				url: HOME + 'delete_selected',
				type:"POST",
        cache:"false",
				data:{
          'ids' : ids
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(function(){
							window.location.reload();
						},1200);
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}

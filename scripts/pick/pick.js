var HOME = BASE_URL + "pick/";

function goBack(){
  window.location.href = HOME;
}

function leave(){
  swal({
    title:'คุณแน่ใจ ?',
    text:'รายการทั้งหมดจะไม่ถูกบันทึก ต้องการออกหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'ไม่ใช่',
    confirmButtonText:'ออกจากหน้านี้',
  },
  function(){
    goBack();
  });
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function goEdit(id){
  window.location.href = HOME + 'edit/'+id;
}



function goDetail(id){
	window.location.href = HOME + 'view_detail/'+id;
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



function canclePick(id, code)
{
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			load_in();

			$.ajax({
				url: HOME + 'cancle_pick',
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

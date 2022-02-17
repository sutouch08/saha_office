var HOME = BASE_URL + 'pack/';

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



function goDetail(id){
	window.location.href = HOME + 'view_detail/'+id;
}


function goProcess(id) {
  window.location.href = BASE_URL + 'packing/process/'+id;
}


function goPacking() {
  window.location.href = BASE_URL + 'packing';
}


function goPackingProcess() {
  window.location.href = BASE_URL + 'packing/view_process';
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



function canclePack(id, code)
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
				url: HOME + 'cancle_pack',
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



function viewTempDetail(code) {
  $.ajax({
    url:HOME + 'get_temp_detail',
    type:'GET',
    cache:false,
    data:{
      "code" : code
    },
    success:function(rs) {
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#temp-template').html();
        var output = $('#temp-table');

        render(source, data, output);

        $('#tempModal').modal('show');
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function closeModal(name) {
  $('#'+name).modal('hide');
}



function removeTemp(docEntry, id) {
  $.ajax({
    url:HOME + 'delete_temp',
    type:'POST',
    cache:false,
    data:{
      "DocEntry" : docEntry,
      "id" : id
    },
    success:function(rs) {
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goBack();
        }, 1200)
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}

var HOME = BASE_URL + "move/";

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



function getDelete(id, code)
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
				url: HOME + 'cancle_transfer',
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



//---- view temp detail
function viewDetail(code) {
  $.ajax({
    url:HOME + 'get_temp_data',
    type:'GET',
    data:{
      'code' : code //--- U_WEBORDER
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#temp-template').html();
        var output = $('#temp-table');

        render(source, data, output);

        $('#tempModal').modal('show');
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


function removeTemp() {
  $('#tempModal').modal('hide');
  var U_WEBORDER = $('#U_WEBORDER').val();
  $.ajax({
    url:HOME + 'remove_temp',
    type:'POST',
    data:{
      'U_WEBORDER' : U_WEBORDER
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1000);
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

function closeModal(name) {
  $('#'+name).modal('hide');
}

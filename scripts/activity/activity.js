var HOME = BASE_URL + 'activity/';

function goBack() {
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


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}



function sendToSAP() {
  $('#previewModal').modal('hide');
  var code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'sendToSAP',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          window.location.reload();
        },1200);
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


function getPreview(code){
	$.ajax({
    url:HOME + 'get_preview_data',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#preview-template').html();
        var output = $('#preview-table');

        render(source, data, output);
        $('#previewModal').modal('show');
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


function viewTemp(code) {
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
  var web_code = $('#web_code').val();
  $.ajax({
    url:HOME + 'remove_temp',
    type:'POST',
    data:{
      'code' : web_code
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



function closeModal(name)
{
  $('#'+name).modal('hide');
}


function getDelete(code)
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
		}, function(){
			load_in();
			$.ajax({
				url: HOME + 'delete',
				type:"POST",
        cache:"false",
				data:{
					'code' : code
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Deleted',
							type:'success',
							timer:1000
						});

            $('#row-'+code).remove();
            reIndex();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}

$('#StartDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#EndDate').datepicker("option", "minDate", sd);
  }
});


$('#EndDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#StartDate').datepicker("option", "maxDate", sd);
  }
});

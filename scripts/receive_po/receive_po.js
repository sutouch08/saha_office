// JavaScript Document
var HOME = BASE_URL + 'receive_po/';

function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});
}

$('#warehouse').select2();
$('#user').select2();

function cancel(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
		}, function(){
			$('#cancle-code').val(code);
			$('#cancle-reason').val('').removeClass('has-error');
			$('#cancle-modal').modal('show');
	});
}


function cancle_received(code) {
	var reason = $('#cancle-reason').val().trim();

	// if(reason.length < 10)
	// {
	// 	$('#cancle-modal').modal('show');
	// 	return false;
	// }

	load_in();

	$.ajax({
		url: HOME + 'cancel',
		type:"POST",
		cache:"false",
		data:{
			"code" : code,
			"reason" : reason
		},
		success: function(rs) {
			load_out();

			if( rs.trim() == 'success' ) {
				swal({
					title: 'Cancled',
					type: 'success',
					timer: 1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);

			}
			else {
				beep();
				showError(rs);
			}
		},
		error:function(rs) {
			beep();
			showError(rs);
		}
	});
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $('#cancle-reason').val().trim();
	//
	// if( reason.length < 10) {
	// 	$('#cancle-reason').addClass('has-error').focus();
	// 	return false;
	// }

	$('#cancle-modal').modal('hide');

	return cancle_received(code);
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});



function addNew(){
  window.location.href = HOME + 'add_new';
}


function edit(code){
	window.location.href = HOME + 'edit/'+ code;
}


function process(code) {
	window.location.href = HOME + 'process/'+code;
}


function processMobile(code) {
	window.location.href = HOME + 'process_mobile/'+code;
}


function viewDetail(code){
	window.location.href = HOME + 'view_detail/'+ code;
}


function goBack(){
	window.location.href = HOME;
}

function getSearch(){
	$("#searchForm").submit();
}


$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});



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



// JavaScript Document
function printReceived(){
	var code = $("#receive_code").val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_detail/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}


function sendToSap(code){
	load_in();
	$.ajax({
		url: HOME + 'do_export/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs.trim() == 'success'){
				swal({
					title:'Success',
					text:'Send data successfully',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Errow!',
					text: rs,
					type:'error'
				});
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
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#temp-template').html();
        var output = $('#temp-table');

        render(source, data, output);

        $('#tempModal').modal('show');
      }
      else {
        showError(rs);
      }
    }
  })
}


function removeTemp() {
  $('#tempModal').modal('hide');
  let U_WEBORDER = $('#U_WEBORDER').val();
	let DocEntry = $('#DocEntry').val();

  $.ajax({
    url:HOME + 'remove_temp',
    type:'POST',
    data:{
      'U_WEBORDER' : U_WEBORDER,
			'DocEntry' : DocEntry
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


function viewTempDetail(id) {
	let target = BASE_URL + 'temp/temp_receive_po/get_detail/'+id+'?nomenu';
	let center = ($(document).width() - 1200) /2;
	window.open(target, "_blank", "width=1200, height=800, left="+center+", scrollbars=yes");
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}

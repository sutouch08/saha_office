var HOME = BASE_URL + 'sales_order/';

function doApprove() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'approve',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
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


function unApprove() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'unapprove',
    type:'POST',
    cache:false,
    data:{
      'code' : code
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
          goEdit(code);
        }, 1200);
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


function doReject() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'reject',
    type:'POST',
    cache:false,
    data:{
      'code' : code
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
        }, 1200);
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



function sendToSAP() {
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


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}



function goDetail(code){
	window.location.href = HOME + 'view_detail/'+code;
}



function cancelSo(code)
{
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการยกเลิก '"+code+"' หรือไม่?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    closeOnConfirm: true
  }, function(){
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'cancel',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Canceled',
              text:'ยกเลิกเอกสารเรียบร้อยแล้ว',
              type:'success',
              timer:1000
            })

            setTimeout(() => {
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
      })
    }, 200);
  });
}


function chooseLayout(code) {
  $('#sq-code').val(code);
  $('#printModal').modal('show');
}


function printSalesOrder(code) {
  //--- properties for print
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";
  var center  = ($(document).width() - 800)/2;
  var target  = HOME + 'print_sales_order/'+code;
  window.open(target, '_blank', prop);
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

$('#DocDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(ds) {
    $('#DocDueDate').datepicker("option", "minDate", ds);
  }
});

$('#DocDueDate').datepicker({
  dateFormat:'dd-mm-yy'
});

$('#TextDate').datepicker({
  dateFormat:'dd-mm-yy'
});



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

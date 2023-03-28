function sendToSap() {
  let id = $('#id').val();

  if(id.length) {
    load_in();

    $.ajax({
      url:HOME + 'send_to_sap',
      type:'POST',
      cache:false,
      data:{
        "id" : id
      },
      success:function(ds) {
        load_out();

        var ds = $.trim(ds);
        if(ds == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            window.location.reload();
          }, 1200)
        }
        else {
          swal({
            title:"Error",
            text:ds,
            type:'error',
          });
        }
      }
    })
  }
}



function updateOrder() {
  let id = $('#id').val();

  $.ajax({
    url:HOME + 'manual_update_order_line/'+id,
    type:'POST',
    cache:false,
    success:function(rs) {
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        // setTimeout(function() {
        //   window.location.reload();
        // }, 1200);
      }
    }
  })
}


function forceClose(id, code) {
  swal({
    title:'Warning !',
    text:'ต้องการบังคับปิด '+code+' หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d15b47',
    confirmButtonText:'ยืนยัน',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  },() => {
    load_in();

    $.ajax({
      url:BASE_URL + 'sync_transfer/force_close',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'code' : code
      },
      success:function(rs) {
        load_out();

        if(rs == 'success') {
          setTimeout(() => {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }, 200);
        }
        else {
          setTimeout(() => {
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            });
          }, 200);
        }
      }
    })
  })
}

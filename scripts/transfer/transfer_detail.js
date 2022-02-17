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

        setTimeout(function() {
          window.location.reload();
        }, 1200);
      }
    }
  })
}

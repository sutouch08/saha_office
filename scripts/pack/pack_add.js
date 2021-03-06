function updatePickList() {
  var so = $('#orderCode').val();

  if(so != "") {
    $.ajax({
      url:HOME + 'get_pick_list_by_so',
      type:'GET',
      cache:false,
      data:{
        'orderCode' : so
      },
      success:function(rs) {
        if(isJson(rs)) {
          var ds = $.parseJSON(rs);
          var source = $('#picklist-template').html();
          var output = $('#pickList');

          render(source, ds, output);
        }
      }
    })
  }
}



$('#barcode').keyup(function(e) {
  if(e.keyCode == 13) {
    let code = $(this).val();
    if(code.length > 19) {
      let arr = code.split(',');
      if(arr.length == 2) {
        addByBarcode(arr[1], arr[0]);
      }
    }
  }
})



function addByBarcode(orderCode, pickListNo) {

  if(orderCode == "") {
    swal("กรุณาเลือก SO No.");
    return false;
  }

  if(pickListNo == "") {
    swal("กรุณาเลือก Pick List No.");
    return false;
  }


  load_in();
  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      "orderCode" : orderCode,
      "pickListNo" : pickListNo
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        goDetail(ds.id);
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



function add() {
  var orderCode = $('#orderCode').val();
  var pickListNo = $('#pickList').val();

  if(orderCode == "") {
    swal("กรุณาเลือก SO No.");
    return false;
  }

  if(pickListNo == "") {
    swal("กรุณาเลือก Pick List No.");
    return false;
  }


  load_in();
  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      "orderCode" : orderCode,
      "pickListNo" : pickListNo
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        goDetail(ds.id);
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

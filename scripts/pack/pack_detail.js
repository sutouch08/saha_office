function showBoxOption() {
  code = $('#code').val();
  box_id = "1";
  $.ajax({
    url:BASE_URL + 'packing/get_box_list',
    type:'GET',
    data:{
      'code' : code,
      'box_id' : box_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#box-list-template').html();
        var output = $('#box-list-table');
        render(source, ds, output);

        $('#boxOptionModal').modal('show');
      }
      else {
        swal({
          title:"Error !",
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function check_box_all() {
  if($('#box-chk-all').is(':checked')) {
    $('.box-chk').prop('checked', true);
  }
  else {
    $('.box-chk').prop('checked', false);
  }
}


function editBox(box_id) {
  var code = $('#code').val();
  $('#boxOptionModal').modal('hide');

  $.ajax({
    url:BASE_URL + 'packing/get_pack_box_details',
    type:'GET',
    cache:false,
    data:{
      "code" : code,
      "box_id" : box_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#box-detail-template').html();
        var output = $('#box-detail-table');
        render(source, data, output);
        $('#boxEditModal').modal('show');
      }
      else {
        swal({
          title:"Error!",
          text:rs,
          type:"error"
        });
      }
    }
  })
}


function backStep() {
  $('#boxEditModal').modal('hide');
  $('#boxOptionModal').modal('show');
}


function printBox(box_id) {
  var code = $('#code').val();
  //--- properties for print
  var center  = ($(document).width() - 800)/2;
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";
  var target  = BASE_URL + 'packing/print_box/'+ code +'/'+box_id;
  print_url(target);
  //window.open(target, '_blank', prop);
}


function printSelectedBox() {
  var box_id = "";
  var i = 1;
  $('.box-chk').each(function() {
    if($(this).is(':checked')) {
      box_id = i == 1 ? box_id + $(this).val() : box_id + '-'+$(this).val();
      i++;
    }
  })

  var code = $('#code').val();
  var target  = BASE_URL + 'packing/print_selected_boxes/'+ code +'/'+box_id;

  print_url(target);
  //window.open(target, '_blank', prop);
}

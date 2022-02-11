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


function showBinOption() {
  $('#binOptionModal').modal('show');
  binOption_init();
}


$('#binOptionModal').on('shown.bs.modal', function() {
  $('#binOption').focus();
})



function binOption_init() {
  var whsCode = $('#bufferWarehouse').val();

  $('#binOption').autocomplete({
    source:HOME + 'find_bin_code/'+whsCode,
    autoFocus:true
  });
}

$('#binOption').keyup(function(e) {
  if(e.keyCode === 13) {
    if($(this).val() != "") {
      sendToSap();
    }
  }
});


function sendToSap() {
  let id = $('#id').val();
  let code = $('#code').val();
  let whsCode = $('#bufferWarehouse').val();
  let binCode = $('#binOption').val();

  $.ajax({
    url:HOME + 'check_bin_code',
    type:'POST',
    cache:false,
    data:{
      "BinCode" : binCode
    },
    success:function(sc) {
      if(sc == 'success') {
        $('#binOptionModal').modal('hide');

        load_in();

        $.ajax({
          url:HOME + 'send_to_sap',
          type:'POST',
          cache:false,
          data:{
            "id" : id,
            "code" : code,
            "BinCode" : binCode
          },
          success:function(rs) {
            load_out();

            var rs = $.trim(rs);
            if(rs == 'success') {
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
                title:'Error!',
                text:rs,
                type:'error'
              });
            }
          }
        });
      }
      else {
        $('#bin-error').text(sc);
      }
    }
  })
}

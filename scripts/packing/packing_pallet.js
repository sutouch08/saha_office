
function check_pallet_all() {
  if($('#pallet-chk-all').is(':checked')) {
    $('.pallet-chk').prop('checked', true);
  }
  else {
    $('.pallet-chk').prop('checked', false);
  }
}


function check_no_pallet_all() {
  if($('#no-pallet-chk-all').is(':checked')) {
    $('.no-pallet-chk').prop('checked', true);
  }
  else {
    $('.no-pallet-chk').prop('checked', false);
  }
}


$('#pallet-code').keyup(function(e) {
  if(e.keyCode === 13) {
    let code = $(this).val();

    if(code.length > 0) {
      getPallet();
    }
  }
})



function addPallet() {
  var packCode = $('#code').val();
  var orderCode = $('#orderCode').val();

  $.ajax({
    url:HOME + 'add_pallet',
    type:'POST',
    cache:false,
    data:{
      "packCode" : packCode,
      "orderCode" : orderCode
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(! isNaN(parseInt(rs))) {
        $('#pallet_id').val(rs);
        $('#box_id').val('');
        $('.box-btn').removeClass('btn-success');

        updatePalletList();
      }
    }
  })
}


function getPallet() {
  var code = $('#code').val();
  var palletCode = $('#pallet-code').val();

  if(palletCode.length) {
    $.ajax({
      url:HOME + 'get_pallet_by_code',
      type:'GET',
      cache:false,
      data:{
        "packCode" : code,
        "palletCode" : palletCode
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(! isNaN(parseInt(rs))) {
          $('#pallet-code').val('');
          $('#pallet_id').val(rs);
          $('#box_id').val('');
          $('.box-btn').removeClass('btn-success');
          updatePalletList();
        }
        else {
          swal({
            title:"Error!",
            text:rs,
            type:'error'
          })
        }
      }
    })
  }
}


function updatePalletList(){
  var pallet_id = $("#pallet_id").val();
  var code = $("#code").val();

  $.ajax({
    url: HOME + 'get_pallet_list',
    type:"GET",
    cache: "false",
    data:{
      "code" : code,
      "pallet_id" : pallet_id
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $("#pallet-template").html();
        var data = $.parseJSON(rs);
        var output = $("#pallet-row");
        render(source, data, output);
        $('#pallet-code').focus();
      }
      else{
        swal("Error!", rs, "error");
      }
    }
  });
}


function setPallet(id) {
  $('#pallet_id').val(id);
  $('.pallet-btn').removeClass('btn-primary');
  $('#btn-pallet-'+id).addClass('btn-primary');

  $('#box_id').val('');
  $('.box-btn').removeClass('btn-success');
}


function unsetPallet() {
  $('#pallet_id').val('');
  $('.pallet-btn').removeClass('btn-primary');
}



function showPalletOption() {
  code = $('#code').val();
  pallet_id = 1;

  $.ajax({
    url:HOME + 'get_pallet_list',
    type:'GET',
    data:{
      'code' : code,
      'pallet_id' : pallet_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        var ds = $.parseJSON(rs);
        var source = $('#pallet-list-template').html();
        var output = $('#pallet-list-table');
        render(source, ds, output);

        $('#palletOptionModal').modal('show');
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


function viewPallet(pallet_id) {
  var code = $('#code').val();
  $('#pallet-option-id').val(pallet_id);

  $.ajax({
    url:HOME + 'get_pallet_detail',
    type:'GET',
    cache:false,
    data:{
      'code' : code,
      'pallet_id' : pallet_id
    },
    success:function(rs) {
      if(isJson(rs)) {
        let data = $.parseJSON(rs);
        let source = $('#pallet-detail-template').html();
        let output = $('#pallet-detail-table');

        render(source, data, output);

        $('#palletDetailModal').modal('show');
      }
    }
  })
}



function removePallet(pallet_id, pallet_code) {
  let code = $('#code').val();

  swal({
		title: "ลบพาเลท",
		text: "ต้องการลบ พาเลท "+pallet_code+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d15b47",
		confirmButtonText: 'ดำเนินการ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {

    $.ajax({
      url:HOME + 'remove_pallet_row',
      type:'POST',
      cache:false,
      data:{
        'pallet_id' : pallet_id,
        'packCode' : code
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(rs == 'success') {
          swal({
            title:'success',
            type:'success',
            timer:1000
          });

          $('#pallet-row-'+pallet_id).remove();

          unsetBox();
          unsetPallet();
          updatePalletList();
        }
        else {
          swal({
            titel:"Error!",
            text:rs,
            type:'error'
          })
        }
      }
    })
  });
}


function removePalletBox(box_id, box_no) {
  let pallet_id = $('#pallet-option-id').val();
  let code = $('#code').val();

  swal({
		title: "ดึงกล่องออกจากพาเลท",
		text: "ต้องการดึง กล่องที่ "+box_no+" ออกจากพาเลทนี้หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d15b47",
		confirmButtonText: 'ดำเนินการ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {

    $.ajax({
      url:HOME + 'remove_pallet_box',
      type:'POST',
      cache:false,
      data:{
        'box_no' : box_no,
        'box_id' : box_id
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(rs == 'success') {
          swal({
            title:'success',
            type:'success',
            timer:1000
          });

          $('#pallet-box-'+box_id).remove();

          unsetBox();

          $.ajax({
            url:HOME + 'get_pallet_list',
            type:'GET',
            data:{
              'code' : code,
              'pallet_id' : pallet_id
            },
            success:function(cs) {
              if(isJson(cs)) {
                var ds = $.parseJSON(cs);
                var template = $('#pallet-list-template').html();
                var table = $('#pallet-list-table');
                render(template, ds, table);
              }
            }
          })

            updateBoxList();
        }
        else {
          swal({
            titel:"Error!",
            text:rs,
            type:'error'
          })
        }
      }
    })
  });
}



function showNoPalletBox() {
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'get_no_pallet_box',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      if(isJson(rs)) {
        let source = $('#no-pallet-template').html();
        let data = $.parseJSON(rs);
        let output = $('#no-pallet-table');

        render(source, data, output);

        $('#noPalletBoxModal').modal('show');
      }
    }
  })
}




function addToPallet() {
  let code = $('#code').val();
  let pallet_id = $('#pallet-option-id').val();
  let box_list = "";
  let i = 1;

  if(pallet_id == '' || pallet_id < 1) {
    return false;
  }




  $('.no-pallet-chk').each(function() {
    if($(this).is(':checked')) {
      box_list = i == 1 ? box_list + $(this).val() : box_list + "-" + $(this).val();
      i++;
    }
  })

  if(box_list.length > 0) {
    $.ajax({
      url:HOME + 'add_box_to_pallet',
      type:'POST',
      cache:false,
      data:{
        'pallet_id' : pallet_id,
        'box_list' : box_list
      },
      success:function(rs) {
        if(rs == 'success') {
          //-- update pallet detail
          $.ajax({
            url:HOME + 'get_pallet_detail',
            type:'GET',
            cache:false,
            data:{
              'code' : code,
              'pallet_id' : pallet_id
            },
            success:function(rs) {
              if(isJson(rs)) {
                let data = $.parseJSON(rs);
                let source = $('#pallet-detail-template').html();
                let output = $('#pallet-detail-table');

                render(source, data, output);

                $.ajax({
                  url:HOME + 'get_pallet_list',
                  type:'GET',
                  data:{
                    'code' : code,
                    'pallet_id' : pallet_id
                  },
                  success:function(cs) {
                    if(isJson(cs)) {
                      var ds = $.parseJSON(cs);
                      var template = $('#pallet-list-template').html();
                      var table = $('#pallet-list-table');
                      render(template, ds, table);
                    }
                  }
                })
              }
            }
          })

            unsetBox();
            updateBoxList();

          $('#noPalletBoxModal').modal('hide');
        }
      }
    })
  }
}



function printPallet(pallet_id) {
  var center  = ($(document).width() - 800)/2;
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";
  var target  = HOME + 'print_pallet/'+ pallet_id;
  window.open(target, '_blank', prop);
  //print_url(target);
}


function printSelectedPallet() {
  var pallet_id = "";
  var i = 1;
  $('.pallet-chk').each(function() {
    if($(this).is(':checked')) {
      pallet_id = i == 1 ? pallet_id + $(this).val() : pallet_id + '-'+$(this).val();
      i++;
    }
  })

  var center  = ($(document).width() - 800)/2;
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";
  var target  = HOME + 'print_selected_pallet/'+pallet_id;

  //print_url(target);
  window.open(target, '_blank', prop);
}

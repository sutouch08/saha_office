function confirmRelease() {
  swal({
    title: "Release",
    text: "ต้องการ Release เอกสารหรือไม่ ?",
    showCancelButton: true,
    confirmButtonColor: "#87b87f",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    setTimeout(function() {
      doRelease();
    }, 200);
  });
}


function doRelease() {
  let code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'do_release',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      if(rs == 'success') {
        swal({
          title:'Released',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          window.location.reload();
        }, 1200);
      }
      else {
        swal({
          title:'Error !',
          text:rs,
          type:'error'
        });
      }
    }
  });
}


function confirmUnrelease() {
  swal({
    title: "Unrelease",
    text: "ต้องการ Unrelease เอกสารหรือไม่ ?",
    showCancelButton: true,
    confirmButtonColor: "#87b87f",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    setTimeout(function() {
      UnRelease();
    }, 200);
  });
}


function UnRelease() {
  let code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'un_release',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
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
      else {
        swal({
          title:'Error !',
          text:rs,
          type:'error'
        });
      }
    }
  });
}




function change_line_status() {
  let status = $('#main-status').val();
  if(status == 0) {
    swal("กรุณาเลือกสถานะ");
    return false;
  }
  else {
    $('.row-chk').each(function() {
      if($(this).is(':checked')) {
        let id = $(this).data('id');
        $('#lineStatus-'+id).val(status);
      }
    });
  }

  $('#main-status').val(0);
}


function updateStatus() {
  swal({
    title: "Save and Close",
    text: "เมื่อบันทึกเอกสารแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก ต้องการบันทึกหรือไม่ ?",
    showCancelButton: true,
    confirmButtonColor: "#87b87f",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    setTimeout(function() {
      updateAndClose();
    }, 200);
  });
}



function updateAndClose() {
  let code = $('#code').val();
  let error = 0;
  let rows = [];

  $('.line-status').each(function() {
    let status = $(this).val();
    let id = $(this).data('id');

    if(status == '1') {
      error++;
      $(this).addClass('has-error');
    }
    else {
      $(this).removeClass('has-error');
      let row = {"id" : id, "result_status" : status};
      rows.push(row);
    }
  });

  if(error == 0) {
    if(rows.length) {
      load_in();

      $.ajax({
        url:HOME + 'update_and_close',
        type:'POST',
        cache:false,
        data:{
          "code" : code,
          "rows" : JSON.stringify(rows)
        },
        success:function(rs) {
          load_out();

          var rs = $.trim(rs);
          if(rs === 'success') {
            swal({
              title:"Success",
              type:'success',
              timer:1000
            });

            setTimeout(function() {
              window.location.reload();
            }, 1200);
          }
          else {
            swal({
              title:"Error!",
              text: rs,
              type:'error'
            });
          }
        }
      });

    }
    else {
      swal("Error!", "ไม่พบรายการจัดส่ง", "error");
      return false;
    }
  }
  else {
    swal("Error!", "กรุณาระบุสถานะให้ครบทุกรายการ", "error");
    return false;
  }
}


function confirmCancle() {
  let code = $('#code').val();
  swal({
    title: "ยกเลิกเอกสาร",
    text: "เมื่อยกเลิกเอกสารแล้วจะไม่สามารถแก้ไขข้อมูลได้อีก <br/>ต้องการยกเลิกหรือไม่ ?",
    showCancelButton: true,
    confirmButtonColor: "#d15b47",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    html:true,
    closeOnConfirm: true
  }, function() {
    load_in();
    $.ajax({
      url: HOME + 'cancle_delivery',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        load_out();
        if(rs === 'success') {
          setTimeout(function() {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(function() {
              window.location.reload();
            }, 1200);
          }, 300);
        }
        else {
          setTimeout(function() {
            swal({
              title:'Error!',
              text: rs,
              type:'error'
            });
          }, 300);
        }
      }
    });
  });
}


function unClose() {
  let code = $('#code').val();

  swal({
    title: "UnClose !",
    text: "ย้อนสถานะเอกสารที่ปิดแล้ว เมื่อทำสำเร็จเอกสารจะกลับมาอยู่ในสถานะ Released ดำเนินการต่อหรือไม่ ?",
    showCancelButton: true,
    confirmButtonColor: "#d15b47",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    setTimeout(function() {
      load_in();
      $.ajax({
        url:HOME + 'un_close_delivery',
        type:'POST',
        cache:false,
        data:{
          "code" : code
        },
        success:function(rs) {
          load_out();
          var rs = $.trim(rs);

          if(rs === 'success') {
            setTimeout(function() {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(function() {
                window.location.reload();
              }, 1200);

            }, 200);
          }
          else {
            setTimeout(function() {
              swal({
                title:'Error!',
                text: rs,
                type:'error'
              });
            }, 200);
          }
        }
      })
    }, 200);
  });
}

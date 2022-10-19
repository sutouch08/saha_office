function add() {
  var remark = $.trim($('#remark').val());

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'remark' : remark
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(!isNaN(rs)) {
        goEdit(rs);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function edit() {
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function updateHeader() {
  let id = $('#AbsEntry').val();
  let remark = $.trim($('#remark').val());

  $.ajax({
    url:HOME + 'update_header',
    type:'POST',
    cache:false,
    data:{
      "AbsEntry" : id,
      "remark" : remark
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        $('#remark').attr('disabled', 'disabled');
        $('#btn-update').addClass('hide');
        $('#btn-edit').removeClass('hide');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    }
  })
}




function save() {
  var absEntry = $('#AbsEntry').val();
  var ds = [];
  var err = 0;

  if($('.pick-qty').length === 0) {
    swal("ไม่พบรายการในเอกสาร");
    return false;
  }

  $('.check-item').each(function() {
    let no = $(this).val();
    let docEntry = $(this).data('docentry');
    let lineNum = $(this).data('linenum');
    let available = parseDefault(parseFloat(removeCommas($('#available-'+no).text())), 0);
    let qty = parseDefault(parseFloat($('#qty-'+no).val()), 0);
    let price = parseDefault(parseFloat($('#Price-'+no).val()), 0);
    let prevRelease = parseDefault(parseFloat(removeCommas($('#release-'+no).text())), 0);
    let orderQty = parseDefault(parseFloat(removeCommas($('#order-'+no).text())), 0);
    let openQty = parseDefault(parseFloat(removeCommas($('#open-'+no).text())), 0);
    let orderCode = $('#orderCode-'+no).text();
    let orderDate = $('#orderDate-'+no).val();
    let customer = $('#customer-'+no).text();
    let itemCode = $.trim($('#itemCode-'+no).text());
    let itemName = $.trim($('#itemName-'+no).text());

    if(available > 0 && qty > 0 && qty <= available) {
      $('#row-no' + no).removeClass('red');
      let arr = {
        "DocEntry" : docEntry,
        "LineNum" : lineNum,
        "OrderCode" : $.trim(orderCode),
        "OrderDate" : orderDate,
        "CardName" : $.trim(customer),
        "ItemCode" : itemCode,
        "ItemName" : itemName,
        "UomEntry" : $('#UomEntry-'+no).val(),
        "UomEntry2" : $('#UomEntry2-'+no).val(),
        "UomCode2" : $('#UomCode2-'+no).val(),
        "UomCode" : $('#UomCode-'+no).val(),
        "unitMsr" : $('#unitMsr-'+no).val(),
        "unitMsr2" : $('#unitMsr2-'+no).val(),
        "price" : price,
        "OrderQty" : orderQty,
        "OpenQty" : openQty,
        "RelQtty" : qty,
        "PrevRelease" : prevRelease
      }

      ds.push(arr);
    }
    else {
      $('#row-'+no).addClass('red');
      err++;
    }
  });


  if(ds.length > 0 && err == 0) {

    load_in();

    $.ajax({
      url:HOME + 'validate_item',
      type:'POST',
      cache:false,
      data:{
        "data" : JSON.stringify(ds)
      },
      success:function(rs) {
        load_out();
        if(rs === 'success') {
          $.ajax({
            url:HOME + 'save',
            type:'POST',
            cache:false,
            data:{
              "AbsEntry" : absEntry,
              "data" : JSON.stringify(ds)
            },
            success:function(rs) {
              load_out();
              if(rs === 'success') {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });

                setTimeout(function() {
                  goDetail(absEntry);
                }, 1500);

              }
              else {
                swal({
                  title:'Error!',
                  text:rs,
                  type:'error',
                  html:true
                });
              }
            },
            error:function(xhr) {
              load_out();
              swal({
                title:"Error!!",
                text:"Error : " + xhr.responseText,
                type:"error",
                html:true
              })
            }
          })
        }
        else {
          if(isJson(rs)) {
            var error = $.parseJSON(rs);
            if(error.length > 0) {
              for(let i = 0; i < error.length; i++) {
                let no = error[i];
                $('#row-'+no).addClass('red');
                err++;
              }
            }
          }
          else {
            console.log(rs);
          }
        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:"Error!!",
          text:"Error : " + xhr.responseText,
          type:"error",
          html:true
        })
      }
    })
  }
  else {
    swal({
      title:'ข้อผิดพลาด',
      text:'กรุณาแก้ไขรายการที่เป็นสีแดง',
      type:'warning'
    });
  }
}

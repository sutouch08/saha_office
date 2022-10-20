function saveAdd() {
  let err = 0;
  let message = "";
  let date = $('#docDate').val();
  let shipDate = $('#shipDate').val();
  let vehicle = $('#vehicle').val();
  let driver = $('#driver').val();
  let route = $('#route').val();
  let totalAmount = $('#DocTotal').val();
  let support = [];

  if(!isDate(date)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(!isDate(shipDate)) {
    swal("วันที่จัดส่งไม่ถูกต้อง");
  }

  if(vehicle == "") {
    swal("กรุณาระบุทะเบียนรถ");
    return false;
  }

  if(driver == "") {
    swal("กรุณาระบุพนักงานขับรถ");
    return false;
  }

  if(route == "") {
    swal("กรุณาระบุเส้นทาง");
    return false;
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      support.push($(this).val());
    }
  });

  let rows = [];
  let docs = {};

  $('.cardCode').each(function() {
    let cardCode = $(this).val();
    if(cardCode.length) {
      let no = $(this).data('id');
      let shipType = $('#shipType-'+no).val();
      let docType = $('#docType-'+no).val();

      if(shipType != 'O' && docType == '') {
        $('#row-'+no).addClass('has-error');
        err++;
        message = "กรุณาระบุประเภทเอกสาร";
        return false;
      }
      else {
        let docNum = $('#docNum-'+no).val();
        let code = docType + "-"+docNum;

        if(shipType != 'O' && docNum.length == 0) {
          $('#row-'+no).addClass('has-error');
          err++;
          message = "กรุณาระบุเลขที่เอกสาร";
          return false;
        }
        else {
          id = docs[code];
          if(id !== undefined) {
            $('#row-'+no).addClass('has-error');
            $('#row-'+id).addClass('has-error');
            err++;
            message = "เลขที่เอกสาร '"+code+"' ซ้ำ";
            return false;
          }
          else {
            $('#row-'+no).removeClass('has-error');
            docs[code] = no;
            let docTotal = $('#docTotal-'+no).val();
            let row = {
              "cardCode" : cardCode,
              "cardName" : $('#cardName-'+no).val(),
              "address" : $('#shipTo-'+no).val(),
              "contact" : $('#contact-'+no).val(),
              "shipType" : shipType,
              "docType" : docType,
              "docNum" : docNum,
              "docTotal" : removeCommas(docTotal),
              "DocDate" : $('#docDate-'+no).val(),
              "remark" : $('#remark-'+no).val(),
              "ShipToCode" : $('#shipToCode-'+no).val(),
              "Street" : $('#street-'+no).val(),
              "Block" : $('#block-'+no).val(),
              "City" : $('#city-'+no).val(),
              "County" : $('#county-'+no).val(),
              "Country" : $('#country-'+no).val(),
              "ZipCode" : $('#zipCode-'+no).val(),
              "Phone" : $('#phone-'+no).val(),
              "WorkDate" : $('#workDate-'+no).val(),
              "WorkTime" : $('#workTime-'+no).val()
            }

            rows.push(row);
          }
        }
      }
    }
  });


  if(err > 0) {
    swal({
      title:'Error!',
      text:message,
      type:'error'
    });

    return false;
  }

  if(rows.length) {
    load_in();
    $.ajax({
      url:HOME + 'save_add',
      type:'POST',
      cache:false,
      data:{
        "date" : date,
        "shipDate" : shipDate,
        "vehicle" : vehicle,
        "driver" : driver,
        "route" : route,
        "DocTotal" : totalAmount,
        "support" : support,
        "details" : JSON.stringify(rows)
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          var rs = $.parseJSON(rs);

          if(rs.status == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(function() {
              viewDetail(rs.code);
            }, 1200);
          }
          else {
            swal({
              title:'Error!',
              text:rs.error,
              type:'error'
            });
          }
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
    swal("กรุณาระบุรายการอย่างน้อย 1 รายการ");
    return false;
  }
}



function saveUpdate() {
  let err = 0;
  let message = "";
  let code = $('#code').val();
  let date = $('#docDate').val();
  let shipDate = $('#shipDate').val();
  let vehicle = $('#vehicle').val();
  let driver = $('#driver').val();
  let route = $('#route').val();
  let totalAmount = $('#DocTotal').val();
  let support = [];

  if(!isDate(date)) {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(!isDate(shipDate)) {
    swal("วันที่จัดส่งไม่ถูกต้อง");
  }

  if(vehicle == "") {
    swal("กรุณาระบุทะเบียนรถ");
    return false;
  }

  if(driver == "") {
    swal("กรุณาระบุพนักงานขับรถ");
    return false;
  }

  if(route == "") {
    swal("กรุณาระบุเส้นทาง");
    return false;
  }

  $('.chk').each(function() {
    if($(this).is(':checked')) {
      support.push($(this).val());
    }
  });

  let rows = [];
  let docs = {};

  $('.cardCode').each(function() {
    let cardCode = $(this).val();
    if(cardCode.length) {
      let no = $(this).data('id');
      let shipType = $('#shipType-'+no).val();
      let docType = $('#docType-'+no).val();

      if(shipType != 'O' && docType == '') {
        $('#row-'+no).addClass('has-error');
        err++;
        message = "กรุณาระบุประเภทเอกสาร";
        return false;
      }
      else {
        let docNum = $('#docNum-'+no).val();
        let code = docType + "-"+docNum;

        if(shipType != 'O' && docNum.length == 0) {
          $('#row-'+no).addClass('has-error');
          err++;
          message = "กรุณาระบุเลขที่เอกสาร";
          return false;
        }
        else {
          id = docs[code];
          if(id !== undefined) {
            $('#row-'+no).addClass('has-error');
            $('#row-'+id).addClass('has-error');
            err++;
            message = "เลขที่เอกสาร '"+code+"' ซ้ำ";
            return false;
          }
          else {
            $('#row-'+no).removeClass('has-error');
            docs[code] = no;
            let docTotal = $('#docTotal-'+no).val();
            let row = {
              "cardCode" : cardCode,
              "cardName" : $('#cardName-'+no).val(),
              "address" : $('#shipTo-'+no).val(),
              "contact" : $('#contact-'+no).val(),
              "shipType" : shipType,
              "docType" : docType,
              "docNum" : docNum,
              "docTotal" : removeCommas(docTotal),
              "DocDate" : $('#docDate-'+no).val(),
              "remark" : $('#remark-'+no).val(),
              "ShipToCode" : $('#shipToCode-'+no).val(),
              "Street" : $('#street-'+no).val(),
              "Block" : $('#block-'+no).val(),
              "City" : $('#city-'+no).val(),
              "County" : $('#county-'+no).val(),
              "Country" : $('#country-'+no).val(),
              "ZipCode" : $('#zipCode-'+no).val(),
              "Phone" : $('#phone-'+no).val(),
              "WorkDate" : $('#workDate-'+no).val(),
              "WorkTime" : $('#workTime-'+no).val()
            }

            rows.push(row);
          }
        }
      }
    }
  });


  if(err > 0) {
    swal({
      title:'Error!',
      text:message,
      type:'error'
    });

    return false;
  }


  if(rows.length) {
    load_in();
    $.ajax({
      url:HOME + 'save_update',
      type:'POST',
      cache:false,
      data:{
        "code" : code,
        "date" : date,
        "shipDate" : shipDate,
        "vehicle" : vehicle,
        "driver" : driver,
        "route" : route,
        "DocTotal" : totalAmount,
        "support" : support,
        "details" : JSON.stringify(rows)
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

          setTimeout(function() {
            viewDetail(code);
          }, 1200);
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
    swal("กรุณาระบุรายการอย่างน้อย 1 รายการ");
    return false;
  }
}


function updateDocType() {
  shipType = $('#shipType').val();
  docType = $('#docType').val();

  if(shipType == 'D') {
    source = $('#docTypeTemplate3').html();
    render(source, {}, $('#docType'));
    $('#docType').val(docType);
  }

  if(shipType == 'P') {
    source = $('#docTypeTemplate4').html();
    render(source, {}, $('#docType'));
    if(docType == 'PB' || docType == 'CN') {
      $('#docType').val('DO');
    }
    else {
      $('#docType').val(docType);
    }
  }
}


function toggleDocType(no) {
  shipType = $('#shipType-'+no).val();

  var source = $('#docTypeTemplate2').html();

  if(shipType == 'D') {
    //--- ส่งเอกสาร
    source = $('#docTypeTemplate1').html();
  }

  let data = {};
  let output = $('#docType-'+no);
  render(source, data, output);

  clearRow(no, shipType);
}


function toggleChkAll(el) {
  if(el.is(':checked')) {
    $('.row-chk').prop('checked', true);
  }
  else {
    $('.row-chk').prop('checked', false);
  }
}


function clearRow(no, shipType) {
  if(shipType == 'P' || shipType == 'D') {
    $('#cardCode-'+no).val('');
    $('#cardName-'+no).val('');
    $('#shipTo-'+no).val('');
    $('#contact-'+no).val('');
    $('#docType-'+no).val('');
    $('#docNum-'+no).val('');
    $('#docTotal-'+no).val(0.00);
  }
}


function addRow() {
  let no = $('#no').val();
  no++;

  let source = $('#row-template').html();
  let output = $('#row-table');
  render_append(source, {"no" : no}, output);
  $('#no').val(no);
  customerInit(no);

  return no;
}


function removeRow() {
  $('.row-chk').each(function() {
    if($(this).is(':checked')) {
      no = $(this).data('id');
      $('#row-'+no).remove();
    }
  });
}



function docNumInit(no) {
  docType = $('#docType-'+no).val();
  shipType = $('#shipType-'+no).val();

  if(docType != "") {
    $('#docNum-'+no).autocomplete({
      source:HOME + 'get_doc_num/'+docType+'/'+shipType,
      minLength:2,
      autoFocus:true,
      select:function(event, ui) {
        docType = $('#docType-'+no).val();
        let code = docType+"-"+ui.item.label;
        $('.row-chk').each(function() {
          let id = $(this).data('id');

          if(id != no) {
            let prefix = $('#docType-'+id).val();
            let doc_num = $('#docNum-'+id).val();
            let dc = prefix+"-"+doc_num;

            if(code == dc) {
              $('#row-'+no).addClass('has-error');
              $('#row-'+id).addClass('has-error');

              setTimeout(function() {
                swal({
                  title:'Error!',
                  text:"เลขที่เอกสาร '"+dc+"'ซ้ำ",
                  type:'error'
                });
              }, 300);

              return false;
            }
            else {
              $('#row-'+no).removeClass('has-error');
              $('#row-'+id).removeClass('has-error');
            }
          }
          else {
            $('#row-'+no).removeClass('has-error');
            $('#row-'+id).removeClass('has-error');
          }
        });

        $('#cardCode-'+no).val(ui.item.CardCode);
        $('#cardName-'+no).val(ui.item.CardName);
        $('#shipTo-'+no).val(ui.item.shipTo);
        $('#contactName-'+no).val(ui.item.ContactName);
        $('#docDate-'+no).val(ui.item.DocDate);
        $('#docTotal-'+no).val(ui.item.docTotal);
        $('#workDate-'+no).val(ui.item.WorkDate);
        $('#workTime-'+no).val(ui.item.WorkTime);

        //--- hidden input
        $('#shipToCode-'+no).val(ui.item.ShipToCode);
        $('#street-'+no).val(ui.item.Street);
        $('#block-'+no).val(ui.item.Block);
        $('#city-'+no).val(ui.item.City);
        $('#county-'+no).val(ui.item.County);
        $('#country-'+no).val(ui.item.Country);
        $('#zipCode-'+no).val(ui.item.ZipCode);
        $('#phone-'+no).val(ui.item.Phone);
        $('#contact-'+no).val(ui.item.Contact);

        recalTotal();
      }
    });
  }
}


function customerInit(no) {
  $('#cardCode-'+no).autocomplete({
    source: BASE_URL + 'auto_complete/get_customer_code_and_name',
    close:function() {
      var arr = $(this).val().split(' | ');
      if(arr.length == 2) {
        $('#cardCode-'+no).val(arr[0]);
        $('#cardName-'+no).val(arr[1]);
      }
      else {
        $('#cardCode-'+no).val("");
        $('#cardName-'+no).val("");
      }
    }
  });
}


function init() {
  $('.cardCode').each(function() {
    let no = $(this).data('id');
    customerInit(no);
  });


  $('.docNum').each(function() {
    let no = $(this).data('no');
    docNumInit(no);
  });
}


$(document).ready(function() {
  init();
});





$('#docNum').keyup(function(e) {
  if(e.keyCode === 13) {
    submitRow();
  }
});


function submitRow() {
  let delivery_code = $('#code').val();
  let shipType = $('#shipType').val();
  let docNum = $('#docNum').val();
  let arr = docNum.split('-');
  let url = "";

  if(arr.length == 2) {
    let docType = arr[0];
    let code = arr[1];

    $.ajax({
      url: HOME + 'get_document_data',
      type:'GET',
      cache:false,
      data:{
        'shipType' : shipType,
        'docType' : docType,
        'docNum' : code,
        'delivery_code' : delivery_code
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(isJson(rs)) {
          var ds = $.parseJSON(rs);
          var i = 0;
          $('.row-chk').each(function() {
            let id = $(this).data('id');
            let prefix = $('#docType-'+id).val();
            let doc_num = $('#docNum-'+id).val();
            let dc = prefix+"-"+doc_num;

            if(docNum == dc) {
              $('#row-'+ id).addClass('has-error');
              message = "เลขที่เอกสาร  '"+doc_num+"'  ซ้ำ";
              i++;
              return false;
            }
            else {
              $('#row-'+id).removeClass('has-error');
            }
          });

          if( i === 0) {
            let no = addRow();
            $('#shipType-'+no).val(ds.shipType);
            toggleDocType(no);
            $('#docType-'+no).val(docType);
            docNumInit(no);
            $('#docNum-'+no).val(code);
            $('#docDate-'+no).val(ds.DocDate);
            $('#cardCode-'+no).val(ds.CardCode);
            $('#cardName-'+no).val(ds.CardName);
            $('#docTotal-'+no).val(ds.docTotal);
            $('#shipTo-'+no).val(ds.shipTo);
            $('#contactName-'+no).val(ds.ContactName);
            $('#workDate-'+no).val(ds.WorkDate);
            $('#workTime-'+no).val(ds.WorkTime);

            //--- hidden input
            $('#shipToCode-'+no).val(ds.ShipToCode);
            $('#street-'+no).val(ds.Street);
            $('#block-'+no).val(ds.Block);
            $('#city-'+no).val(ds.City);
            $('#county-'+no).val(ds.County);
            $('#country-'+no).val(ds.Country);
            $('#zipCode-'+no).val(ds.ZipCode);
            $('#phone-'+no).val(ds.Phone);
            $('#contact-'+no).val(ds.Contact);

            recalTotal();

            $('#docNum-'+no).focus();
            $('#docNum').val('');
            $('#docNum').focus();
          }
          else {
            swal({
              title:"Error!",
              text:message,
              type:'error'
            },
            function() {
              $('#docNum').val('');
              $('#docNum').focus();
            });
          }
        }
        else {
          swal({
            title:'Error!',
            text: rs,
            type: 'error'
          });
        }
      }
    })
  }
}


function recalTotal() {
  var totalAmount = 0;

  $('.docTotal').each(function() {
    total = removeCommas($(this).val());
    total = parseDefault(parseFloat(total), 0);
    totalAmount += total;
  });

  $('#DocTotal').val(totalAmount);
  $('#totalAmount').text(addCommas(totalAmount.toFixed(2)));
}

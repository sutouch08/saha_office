$('#barcode').keydown(function(e) {
  if(e.keyCode === 13) {
    doReceive();
  }

  //-- Arrow up
  if(e.keyCode === 38) {
    e.preventDefault();
    let qty = parseDefaultInt($('#input-qty').val(), 1);
    $('#input-qty').val(qty + 1);
  }

  // Arrow down
  if(e.keyCode === 40) {
    e.preventDefault();
    let qty = parseDefaultInt($('#input-qty').val(), 1);

    if(qty > 1) {
      $('#input-qty').val(qty - 1);
    }
  }

  // spacebar
  if(e.keyCode === 32) {
    e.preventDefault();
    $('#input-qty').focus().select();
  }
});


$('#input-qty').keydown(function(e) {

  if(e.keyCode == 13) {
    e.preventDefault();
    let qty = parseDefaultFloat($('#input-qty').val(), 1);

    if(qty <= 0) {
      $('#input-qty').val(1);
    }

    $('#barcode').focus().select();
  }

  //-- Arrow up
  if(e.keyCode === 38) {
    e.preventDefault();
    let qty = parseDefaultInt($('#input-qty').val(), 1);
    $('#input-qty').val(qty + 1);
  }

  // Arrow down
  if(e.keyCode === 40) {
    e.preventDefault();
    let qty = parseDefaultInt($('#input-qty').val(), 1);

    if(qty > 1) {
      $('#input-qty').val(qty - 1);
    }
  }

  // spacebar
  if(e.keyCode === 32) {
    e.preventDefault();
    let qty = parseDefaultFloat($('#input-qty').val(), 1);

    if(qty <= 0) {
      $('#input-qty').val(1);
    }

    $('#barcode').focus().select();
  }
});


function doReceive() {
  let poNo = $('#po-refs').val();
  let inputQty = parseDefaultFloat($('#input-qty').val(), 1);
  let barcode = $('#barcode').val().trim();

  if(poNo == "") {
    swal("ไม่พบ PO No.");
    return false;
  }

  if(inputQty <= 0) {
    swal("จำนวนไม่ถูกต้อง");
    return false;
  }

  if(barcode.length == 0) {
    return false;
  }

  if($('#bc-'+barcode).length) {
    let el = $('#bc-'+barcode);
    let code = el.data('item');
    let bcUom = el.data('uomentry');
    let bcUomName = el.data('uomname');
    let item = el.data('item');
    let bcBaseQty = parseDefaultFloat(el.data('baseqty'), 1);

    let md5Code = md5(code);
    let ucode = poNo+'-'+md5Code;

    if($('.'+ucode).length) {
      let testQty = inputQty;
      let testResult = true; // ผล test
      let resultData = []; //--- เก็บผล test ไว้ทำจริง

      //---- loop check ข้อมูลก่อน ถ้าไม่ผ่านจะแจ้ง error แล้ว ออก ถ้าผ่าน ค่อย loop ทำจริงอีกรอบ
      $('.'+ucode).each(function() {
        if(testQty > 0) {
          let uid = $(this).val();
          let row = $('#row-qty-'+uid);
          let limit = parseDefaultFloat(row.data('limit'), 0); //-- ยอดที่ต้องรับเข้า
          let received = parseDefaultFloat(row.val(), 0); //--- ตัวเลขในช่องยอดรับ ตามหน่วยนับใน PO
          let poBaseQty = parseDefaultFloat(row.data('numpermsr'), 1);
          let poUom = row.data('uomentry');

          // ถ้าหน่วยนับที่ยิงบาร์โค้ดมา ตรงกับหน่วยนับในใบสั่งซื้อ เพิ่มยอดรับได้เลย
          if(bcUom == poUom) {
            let diff = limit - received;
            if(diff > 0) {
              diff = diff > testQty ? testQty : diff;
              let qty = received + diff;
              testQty = testQty - diff;
              resultData.push({'uid' : uid,'qty' : qty});
            }
          }
          else {
            if(bcBaseQty > poBaseQty) {
              let diff = limit - received;

              if(diff > 0) {
                invQty = (bcBaseQty/poBaseQty) * testQty;
                diff = diff > invQty ? invQty : diff;
                let qty = received + diff;
                testQty = testQty - (diff/bcBaseQty);
                resultData.push({'uid' : uid, 'qty' : qty});
              }
            }
          }
        }
      })

      if(testQty > 0) {
        testResult = false;
        beep();

        setTimeout(() => {
          swal({
            title:'Error!',
            text:'จำนวนสินค้าเกินใบสั่งซื้อ <br/>PO No. : '+poNo+ '<br/>Uom : '+bcUomName,
            type:'error',
            html:true
          }, function() {
            setTimeout(() => {
              $('#barcode').val('').focus();
            }, 100);
          })
        }, 100)

        return false;
      }

      console.log(resultData);

      if(testResult == true && resultData.length) {
        resultData.forEach(function(row) {
          $('#row-qty-'+row.uid).val(row.qty);
          recalAmount(row.uid);
        });
      }

      $('#input-qty').val(1);
      $('#barcode').val('').focus();
    }
    else {
      beep();
      setTimeout(() => {
        swal({
          title:'Not found !',
          text:'Barcode ไม่ถูกต้องหรือสินค้าไม่ตรงกับรายการรับเข้า หรือสินค้าไม่ตรงกับใบสั่งซื้อที่เลือก',
          type:'error',
          html:true
        }, function() {
          setTimeout(() => {
            $('#barcode').focus().select();
          }, 100);
        })
      }, 100)

      return false;
    }
  }
  else {
    beep();
    setTimeout(() => {
      swal({
        title:'Invalid Barcode !',
        text:'Barcode ไม่ถูกต้องหรือสินค้าไม่ตรงกับรายการรับเข้า',
        type:'error'
      }, function() {
        setTimeout(() => {
          $('#barcode').focus().select();
        }, 100);
      })
    }, 100)
  }
}


function viewItemData(name, uid) {
  let json = $('#item-data-'+uid).val();

  if(json.length)  {
    let ds = JSON.parse(json);
    let source = $('#item-info-template').html();
    let output = $('#item-info');

    render(source, ds, output);

    $('#item-info-modal').modal('show');
  }
}

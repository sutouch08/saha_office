var click = 0;

window.addEventListener('load', () => {

});


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'posting_date' : $('#posting-date').val().trim(),
      'customer_code' : $('#customer-code').val().trim(),
      'customer_name' : $('#customer-name').val().trim(),
      'Currency' : $('#DocCur').val(),
      'Rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'remark' : $('#remark').val().trim()
    }

    if( ! isDate(h.posting_date)) {
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      click = 0;
      return false;
    }

    if(h.customer_name.length == 0) {
      $('#customer-name').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code.length == 0) {
      $('#warehouse').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data: {
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            edit(ds.code);
            click = 0;
          }
          else {
            showError(ds.message);
            click = 0;
          }
        }
        else {
          showError(rs);
          click = 0;
        }
      },
      error:function(rs) {
        showError(rs);
        click = 0;
      }
    })
  }
}


$('#base-type').change(function() {
  baseRefInit();
  $('#base-ref').val('').focus();
});


function baseRefInit() {
  let customer_code = $('#customer-code').val();
  let base_type = $('#base-type').val();

  $('#base-ref').autocomplete({
    source: HOME + 'get_base_ref/'+base_type+'/'+customer_code,
    autoFocus: true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        let date = arr[0];
        let code = arr[1];

        $(this).val(code);
      }
      else {
        $(this).val('');
      }
    }
  });
}


function getBaseRefDetails() {
  let base_type = $('#base-type').val();
  let base_ref = $('#base-ref').val().trim();

  if(base_type == "") {
    swal("กรุณาเลือกเอกสาร");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'load_base_ref_details',
    type:'POST',
    cache:false,
    data:{
      'baseRef' : base_ref,
      'baseType' : base_type
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#base-ref-title').text(base_ref);

          let source = $('#base-ref-template').html();
          let output = $('#base-ref-table');

          render(source, ds.data, output);
          $('#base-ref-modal').modal('show');
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function returnAll() {
  $('.base-ref-qty').each(function() {
    let qty = parseDefaultFloat($(this).data('open'), 0);

    if(qty > 0) {
      $(this).val(qty);
    }
  })
}


function clearAll() {
  $('.base-ref-qty').val('');
}


function clearBaseRef() {
  $('#base-ref').val('').focus();
}


function addToReturn() {
  let ds = [];

  $('.base-ref-qty').each(function() {
    let el = $(this);
    let qty = parseDefaultFloat(el.val(), 0);


    if(qty > 0) {

      let uid = el.data('uid');

      if($('#'+uid).length) {
        let dl = $('#'+uid);

        let cqty = parseDefaultFloat(dl.val(), 0);
        let limit = parseDefaultFloat(dl.data('open'), 0);
        let nqty = cqty + qty;
        let price = parseDefaultFloat(dl.data('price'), 2); //-- price after disc
        let vatRate = parseDefaultFloat(dl.data('vatrate'), 0);
        let lineTotal = nqty * price;
      	let vatSum = roundNumber((lineTotal * (vatRate * 0.01)), 2);

        if(nqty > limit) {
          dl.hasError();
        }

        dl.val(nqty);
        $('#row-total-'+uid).val(addCommas(lineTotal.toFixed(2)));
      	$('#row-vat-amount-'+uid).val(vatSum);
      }
      else {

        let price = parseDefaultFloat(el.data('price'), 0);
        let lineTotal = qty * price;
        let vatSum = lineTotal * (parseDefaultFloat(el.data('vatrate'), 0) * 0.01);
        let bfPrice = parseDefaultFloat(el.data('bfprice'), 0);
        let openQty = parseDefaultFloat(el.data('open'), 0);

        ds.push({
          "uid" : el.data('uid'),
          "ItemCode" : el.data('code'),
          "ItemName" : el.data('name'),
          "baseType" : el.data('basetype'),
          "DocNum" : el.data('basecode'),
          "DocEntry" : el.data('baseentry'),
          "LineNum" : el.data('baseline'),
          "OpenQty" : openQty,
          "OpenQtyLabel" : addCommas(openQty.toFixed(2)),
          "Qty" : qty,
          "QtyLabel" : addCommas(qty.toFixed(2)),
          "Price" : el.data('price'),
          "PriceLabel" : addCommas(bfPrice.toFixed(2)),
          "PriceBefDi" : el.data('bfprice'),
          "PriceAfVAT" : el.data('afprice'),
          "DiscPrcnt" : el.data('discprcnt'),
          "VatGroup" : el.data('vatcode'),
          "VatRate" : el.data('vatrate'),
          "unitMsr" : el.data('unitmsr'),
          "NumPerMsr" : el.data('numpermsr'),
          "UomEntry" : el.data('uomentry'),
          "UomCode" : el.data('uomcode'),
          "unitMsr2" : el.data('unitmsr2'),
          "NumPerMsr2" : el.data('numpermsr2'),
          "UomEntry2" : el.data('uomentry2'),
          "UomCode2" : el.data('uomcode2'),
          "SlpCode" : el.data('slp'),
          "LineTotal" : addCommas(lineTotal.toFixed(2)),
          "VatAmount" : vatSum
        });
      }
    }
  });

  if(ds.length > 0) {
    let source = $('#row-template').html();
    let output = $('#return-table');

    render_append(source, ds, output);

    reIndex();
  }

  $('#base-ref-modal').modal('hide');

  recalTotal();
}


function recalAmount(uid) {
	let el = $('#'+uid);
  el.clearError();
	let price = parseDefault(parseFloat(el.data('price')), 0);
	let qty = parseDefaultFloat(removeCommas(el.val()), 0);
  let limit = parseDefaultFloat(el.data('open'), 0);
	let vatRate = parseDefault(parseFloat(el.data('vatrate')), 0);

	let lineTotal = qty * price;
	let vatSum = roundNumber((lineTotal * (vatRate * 0.01)), 2);

  if(qty > limit) {
    el.hasError();
  }

	$('#row-total-'+uid).val(addCommas(lineTotal.toFixed(2)));
	$('#row-vat-amount-'+uid).val(vatSum);

	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;
	let totalVat = 0;

	$('.row-qty').each(function() {
		let el = $(this);
		let id = el.data('uid');
		let qty = parseDefault(parseFloat(el.val()), 0);
		let price = parseDefault(parseFloat(el.data('price')), 0);
		let vatAmount = parseDefault(parseFloat($('#row-vat-amount-'+id).val()), 0);
		let amount = qty * price;

		totalQty += qty;
		totalAmount += amount;
		totalVat += vatAmount;
	});

	let vatSum = totalVat;
	let docTotal = totalAmount + vatSum;

	$('#total-qty').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
	$('#vat-sum').val(addCommas(vatSum.toFixed(2)));
	$('#doc-total').val(addCommas(docTotal.toFixed(2)));
}


function save(save_type) {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');
    clearErrorByClass('row-qty');
    let error = 0;

    let h = {
      'save_type' : save_type,
      'code' : $('#code').val(),
      'posting_date' : $('#posting-date').val(),
      'customer_code' : $('#customer-code').val().trim(),
      'customer_name' : $('#customer-name').val().trim(),
      'Currency' : $('#DocCur').val(),
      'Rate' : $('#DocRate').val(),
      'warehouse_code' : $('#warehouse').val(),
      'remark' : $('#remark').val().trim(),
      'TotalQty' : parseDefaultFloat(removeCommas($('#total-qty').val()), 2),
      'DocTotal' : parseDefaultFloat(removeCommas($('#doc-total').val()), 2),
      'VatSum' : parseDefaultFloat(removeCommas($('#vat-sum').val()), 2),
      'rows' : []
    };

    if( ! isDate(h.posting_date)) {
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      click = 0;
      return false;
    }

    if(h.customer_name.length == 0) {
      $('#customer-name').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code.length == 0) {
      $('#warehouse').hasError();
      click = 0;
      return false;
    }

    $('.row-qty').each(function() {
      let el = $(this);
      let qty = parseDefaultFloat(removeCommas(el.val()), 0);
      let limit = parseDefaultFloat(el.data('open'), 0);

      if(qty <= 0 || qty > limit) {
        error++;
        el.hasError();
      }

      if(qty > 0 && qty <= limit && error == 0) {
        let uid = el.data('uid');
        let lineTotal = parseDefaultFloat(removeCommas($('#row-total-'+uid).val()), 0);
        let vatSum = parseDefaultFloat($('#row-vat-amount-'+uid).val(), 0);

        h.rows.push({
          'uid' : el.data('uid'),
          'ItemCode' : el.data('code'),
          'ItemName' : el.data('name'),
          'BaseType' : el.data('basetype'),
          'BaseRef' : el.data('basecode'),
          'BaseEntry' : el.data('baseentry'),
          'BaseLine' : el.data('baseline'),
          'PriceBefDi' : el.data('bfprice'),
          'PriceAfVAT' : el.data('afprice'),
          'Price' : el.data('price'),
          'DiscPrcnt' : el.data('discprcnt'),
          'Qty' : qty,
          'LineTotal' : lineTotal,
          'Currency' : h.Currency,
          'Rate' : h.Rate,
          'SlpCode' : el.data('slp'),
          'VatGroup' : el.data('vatcode'),
          'VatRate' : el.data('vatrate'),
          'VatSum' : vatSum,
          'UomCode' : el.data('uomcode'),
          'UomCode2' : el.data('uomcode2'),
          'UomEntry' : el.data('uomentry'),
          'UomEntry2' : el.data('uomentry2'),
          'unitMsr' : el.data('unitmsr'),
          'unitMsr2' : el.data('unitmsr2'),
          'NumPerMsr' : el.data('numpermsr'),
          'NumPerMsr2' : el.data('numpermsr2')
        });
      }
    })

    if(error > 0) {
      click = 0;

      swal({
        title:'พบข้อผิดพลาด',
        text:'กรุณาแก้ไขรายการที่ไม่ถูกต้อง',
        type:'error'
      });

      return false;
    }

    if(h.rows.length > 0) {
      load_in();

      $.ajax({
        url:HOME + 'save',
        type:'POST',
        cache:false,
        data:{
          'data' : JSON.stringify(h)
        },
        success:function(rs) {
          click = 0;
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status === 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(() => {
                reload();
              }, 1200);
            }
            else {
              beep();
              showError(ds.message);
            }
          }
          else {
            beep();
            showError(rs);
          }
        },
        error:function(rs) {
          click = 0;
          beep();
          showError(rs);
        }
      })
    }
    else {
      click = 0;
    }
  }
}


function rollback(code) {
  swal({
    title:'ย้อนสถานะเอกสาร',
    text:'ต้องการย้อนสถานะเอกสารกลับมาแก้ไข ใช่หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, () => {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'rollback/'+code,
        type:'POST',
        cache:false,
        success:function(rs) {
          load_out();
          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    }, 100)
  })
}


function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});

}


$('#customer-code').autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ) {
			$('#customer-code').val(arr[0]);
			$('#customer-name').val(arr[1]);
		}
    else{
			$('#customer-name').val('');
			$('#customer-code').val('');
		}
	}
});


$('#customer-code').change(function() {
  let prevCode = $(this).data('prev');
  let prevName = $('#customer-name').data('prev');
  let code = $(this).val().trim();
  let name = $('#customer-name').val().trim();

  if($('.return-rows').length) {
    swal({
      title: 'คำเตือน',
      text: 'รายการปัจจุบันจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
      type:'warning',
      showCancelButton:true,
      cancelButtonText:'No',
      confirmButtonText:'Yes',
      closeOnConfirm:true
    }, function(isConfirm) {
      if(isConfirm) {
        $('.return-rows').remove();
        $('#total-qty').val(0);
        $('#total-amount').val(0);
        $('#vat-sum').val(0);
        $('#doc-total').val(0);

        $('#customer-code').data('prev', code);
        $('#customer-name').data('prev', name);
        $('#customer-code').focus();
      }
      else {
        $('#customer-name').val(prevName);
        $('#customer-code').val(prevCode).focus();
      }
    })
  }
})


$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});


function itemInit(uid) {
  $('#item-code-'+uid).autocomplete({
    source:BASE_URL + 'auto_complete/get_item_code_and_name',
    autoFocus:true,
    open:function(ev) {
      var $ul = $(this).autocomplete('widget');
      $ul.css('width', 'auto');
    },
    close:function() {
      let ds = $(this).val().split(' | ');

      if(ds.length === 2) {
        $(this).val(ds[0]);
        getItemData(ds[0], uid);
      }
      else {
        $(this).val('');
      }
    }
  })
}


function getItemData(code, uid) {
  load_in();

	let cardCode = $('#customer-code').val();

	$.ajax({
		url:HOME + "get_item_data",
		type:"GET",
		cache:false,
		data:{
			'code' : code,
			'CardCode' : cardCode
		},
		success:function(rs) {
      load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);
        let taxRate = parseDefaultFloat(ds.taxRate, 0);
				let price = parseDefaultFloat(ds.price, 0);
        let bfPrice = price / (1 + taxRate);
        let el = $('#'+uid);

				$('#item-name-'+uid).val(ds.name);
				$('#uom-'+uid).html(ds.uom);
				$('#item-price-'+uid).val(addCommas(price.toFixed(2)));

        el.data('code', ds.code);
        el.data('name', ds.name);
        el.data('price', price);
        el.data('bfPrice', bfPrice);
        el.data('afPrice', price);
        el.data('vatcode', ds.taxCode);
        el.data('vatrate', taxRate);

        el.focus();
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
    error:function(rs) {
      showError(rs);
    }
	})
}

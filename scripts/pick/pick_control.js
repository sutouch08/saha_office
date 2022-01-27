$('#customer').keyup(function(e) {
  if(e.keyCode === 13) {
    get_order_list();
  }
})


$('#soCode').keyup(function(e) {
  if(e.keyCode === 13) {
    get_order_list();
  }
})



function checkAllOrder() {
  if($('#check-order-all').is(':checked')) {
    $('.check-order').prop('checked', true);
  }
  else {
    $('.check-order').prop('checked', false);
  }
}


function checkItemAll() {
  if($('#check-item-all').is(':checked')) {
    $('.check-item').prop('checked', true);
  }
  else {
    $('.check-item').prop('checked', false);
  }
}


function checkDetailAll() {
  if($('#check-detail-all').is(':checked')) {
    $('.check-detail').prop('checked', true);
  }
  else {
    $('.check-detail').prop('checked', false);
  }
}



function deleteRows() {
  $('.check-item').each(function() {
    if($(this).is(':checked')) {
      let no = $(this).val();
      $('#row-'+no).remove();
    }
  })

  reIndex();
  recalQty();
}

function removeRow(no) {
  $('#row-'+no).remove();
  reIndex();
  recalQty();
}


function get_order_list() {
  let so = $('#soCode').val();
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let customer = $('#customer').val();


  if(so.length > 0 || customer.length > 0 || (isDate(fromDate) && isDate(toDate))) {
    load_in();

    $.ajax({
      url:HOME + 'find_open_so',
      type:'GET',
      cache:false,
      data:{
        'DocNum' : so,
        'customer' : customer,
        'fromDate' : fromDate,
        'toDate' : toDate
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          var data = $.parseJSON(rs);
          var source = $('#order-template').html();
          var output = $('#order-table');

          render(source, data, output);

          $('#ordersModal').modal('show');
        }
        else {
          $('#err-text').text(rs);
          $('#ErrorModal').modal('show');
        }
      }
    })
  }
}



function viewSoDetails() {
  $('#ordersModal').modal('hide');
  let ids = [];

  $('.check-order').each(function() {
    if($(this).is(':checked')) {
      ids.push($(this).val());
    }
  })

  if(ids.length == 0) {
    return false;
  }
  else {
    load_in();
    $.ajax({
      url:HOME + 'get_open_order_details',
      type:'GET',
      cache:false,
      data:{
        "DocEntry" : ids
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let data = $.parseJSON(rs);
          let source = $('#details-template').html();
          let output = $('#details-table');

          render(source, data, output);

          $('#detailsModal').modal('show');
        }
        else {
          swal({
            title:"Error",
            text:rs,
            type:"error"
          })
        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:"Error!",
          text: "Error-"+xhr.responseText,
          type:"error",
          html:true
        })
      }
    })
  }

}


function addAll() {
  $('#ordersModal').modal('hide');
  var absEntry = $('#AbsEntry').val();
  var ids = [];

  $('.check-order').each(function() {
    if($(this).is(':checked')) {
      ids.push($(this).val());
    }
  })

  if(ids.length == 0) {
    return false;
  }
  else {
    load_in();
    $.ajax({
      url:HOME + 'add_order_to_list',
      type:'POST',
      cache:false,
      data:{
        'AbsEntry' : absEntry,
        'DocEntry' : ids
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let data = $.parseJSON(rs);

          $.each(data, function(index, ds) {
            if($('#row-'+ds.rowNum).length == 0) {
              let source = $('#row-template').html();
              let output = $('#pick-list-items');
              render_append(source, ds, output);
            }
          });

          reIndex();
          recalQty();

        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:'Error!',
          text:'Error : '+xhr.responseText,
          type:'error',
          html:true
        })
      }
    })
  }
}



function addToList() {
  $('#detailsModal').modal('hide');
  var ds = [];

  $('.check-detail').each(function() {
    if($(this).is(':checked')) {
      let docEntry = $(this).data('docentry');
      let lineNum = $(this).data('linenum');
      let row = {
        "DocEntry" : docEntry,
        "LineNum" : lineNum
      };

      ds.push(row);
    }
  })


  if(ds.length == 0) {
    return false;
  }
  else {
    load_in();
    $.ajax({
      url:HOME + 'add_items_to_list',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(ds)
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let data = $.parseJSON(rs);

          $.each(data, function(index, ds) {
            if($('#row-'+ds.rowNum).length == 0) {
              let source = $('#row-template').html();
              let output = $('#pick-list-items');
              render_append(source, ds, output);
            }
          });

          reIndex();
          recalQty();

        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:'Error!',
          text:'Error : '+xhr.responseText,
          type:'error',
          html:true
        })
      }
    })
  }
}



function recalQty() {
  let totalOrder = 0;
  let totalOpen = 0;
  let totalPrevRelease = 0;
  let totalAvaibleQty = 0;
  let totalQty = 0;

  $('.check-item').each(function() {
    let no = $(this).val();
    let order = parseDefault(parseFloat(removeCommas($('#order-'+no).text())), 0);
    let open = parseDefault(parseFloat(removeCommas($('#open-'+no).text())), 0);
    let release = parseDefault(parseFloat(removeCommas($('#release-'+no).text())), 0);
    let available = parseDefault(parseFloat(removeCommas($('#available-'+no).text())), 0);
    let qty = parseDefault(parseFloat($('#qty-'+no).val()), 0);

    totalOrder += order;
    totalOpen += open;
    totalPrevRelease += release;
    totalAvaibleQty += available;
    totalQty += qty;
  });

  $('#totalOrderQty').text(addCommas(totalOrder.toFixed(2)));
  $('#totalOpenQty').text(addCommas(totalOpen.toFixed(2)));
  $('#totalPrevRelease').text(addCommas(totalPrevRelease.toFixed(2)));
  $('#totalAvaibleQty').text(addCommas(totalAvaibleQty.toFixed(2)));
  $('#totalQty').text(addCommas(totalQty.toFixed(2)));
}


function clear_so_filter() {
  $('#fromDate').val('');
  $('#toDate').val('');
  $('#customer').val('');
  $('#soCode').val('');
  $('#soCode').focus();
}

function releasePickList() {
  var absEntry = $('#AbsEntry').val();

  if($('.no').length) {
    $.ajax({
      url:HOME + 'release_picklist',
      type:'POST',
      cache:false,
      data:{
        'AbsEntry' : absEntry
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
            window.location.reload();
          }, 1200);
        }
        else {
          if(isJson(rs)) {
            var ds = $.parseJSON(rs);

            if(ds.length > 0) {
              for(let i = 0; i < ds.length; i++) {
                let no = ds[i].rowNum;
                $('#row-'+no).addClass('red');
                //$('#onhand-'+no).text(addCommas(ds[i].onHand.toFixed(2)) + "&nbps; "+ds[i].unitMsr+"");
              }

              swal("สินค้าไม่พอ");
            }
          }
          else {
            swal({
              title:'Error!',
              text:rs,
              type:'error'
            })
          }
        }
      }
    })
  }
}


function unReleasePickList() {
  var absEntry = $('#AbsEntry').val();

  if($('.no').length) {
    $.ajax({
      url:HOME + 'unrelease_picklist',
      type:'POST',
      cache:false,
      data:{
        'AbsEntry' : absEntry
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
            window.location.reload();
          }, 1200);
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
}



function removeRow(rowNum, absEntry, pickEntry) {
  $.ajax({
    url:HOME + 'remove_pick_row',
    type:'POST',
    cache:false,
    data:{
      'AbsEntry' : absEntry,
      'PickEntry' : pickEntry
    },
    success:function(rs) {
      if(rs == 'success') {
        $('#row-'+rowNum).remove();
        reIndex();
        recalTotal();
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



function recalTotal() {
  let totalQty = 0;
  let totalOrder = 0;
  let totalOpen = 0;
  let totalRel = 0;

  $('.row-tr').each(function() {
    let no = $(this).data('no');
    let qty = parseDefault(parseFloat(removeCommas($('#qty-'+no).text())), 0);
    let order = parseDefault(parseFloat(removeCommas($('#orderQty-'+no).text())), 0);
    let open = parseDefault(parseFloat(removeCommas($('#openQty-'+no).text())), 0);
    let release = parseDefault(parseFloat(removeCommas($('#released-'+no).text())), 0);

    totalQty += qty;
    totalOrder += order;
    totalOpen += open;
    totalRel += release;
  });


  $('#totalOrderQty').text(addCommas(totalOrder.toFixed(2)));
  $('#totalOpenQty').text(addCommas(totalOpen.toFixed(2)));
  $('#totalPrevRelease').text(addCommas(totalRel.toFixed(2)));
  $('#totalQty').text(addCommas(totalQty.toFixed(2)));
}


function printPickLabel() {
  var center  = ($(document).width() - 800)/2;
  var prop 		= "width=800, height=900. left="+center+", scrollbars=yes";

  let code = $('#pickCode').val();
  let target  = HOME + 'print_pick_order_slip/'+ code;

  print_url(target);
  //window.open(target, '_blank', prop);
}

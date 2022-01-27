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
                $('#onhand-'+no).text(addCommas(ds[i].onHand.toFixed(2)));
              }
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

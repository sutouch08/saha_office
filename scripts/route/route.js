var HOME = BASE_URL + 'route/';

function goBack() {
  window.location.href = HOME;
}



function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function saveAdd() {
  let name = $.trim($('#name').val());
  let level = $('#level').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  if(name.length == 0) {
    swal("กรุณาระบุชื่อเส้นทาง");
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'name' : name,
      'level' : level,
      'active' : active
    },
    success:function(rs) {
      rs = $.trim(rs);

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goAdd();
        }, 1200);
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  });
}



function update() {
  let id = $('#id').val();
  let name = $.trim($('#name').val());
  let level = $('#level').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  if(name.length == 0) {
    swal("กรุณาระบุชื่อเส้นทาง");
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'name' : name,
      'level' : level,
      'active' : active
    },
    success:function(rs) {
      rs = $.trim(rs);

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error!',
          type:'error',
          text:rs
        });
      }
    }
  });
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ name +' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete',
      type:'POST',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500)

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
  })
}

$('#zone').autocomplete({
  source:BASE_URL + 'auto_complete/get_delivery_zone',
  autoFocus:true
});

function addZone() {
  let zone = $('#zone').val();

  let arr = zone.split(">>");

  if(arr.length == 4) {
    let no = parseDefault(parseInt($('#row-no').val()), 1);
    let uid = arr[0]; //md5(arr[0]+arr[1]+arr[2]);
    if($('#'+uid).length) {
      console.log('exists');
      $('#zone').val('').focus();
      return false;
    }

    no++;
    let ds = {"no" : no, "id" : arr[0], "district" : arr[1], "province" : arr[2], "zipCode" : arr[3], "uid" : uid};
    let source = $('#zone-template').html();
    let output = $('#zone-list');

    render_append(source, ds, output);
    $('#row-no').val(no);
    $('#zone').val('').focus();
  }
}


function removeZone(no) {
  $('#row-'+no).remove();
  $('#zone').val('').focus();
}

function updateZone() {
  let id = $('#id').val();
  let ds = [];

  $('.zone-data').each(function() {
    let row = {"id" : $(this).data('id'), "district" : $(this).data('district'), "province" : $(this).data('province'), "zipCode" : $(this).data('zipcode')};
    ds.push(row);
  });

  load_in();
  $.ajax({
    url:HOME + 'add_zone',
    type:'POST',
    cache:false,
    data: {
      "id" : id,
      "zone" : JSON.stringify(ds)
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
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

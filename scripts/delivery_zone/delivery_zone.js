var HOME = BASE_URL + 'delivery_zone/';

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
  let district = $.trim($('#district').val());
  let province = $.trim($('#province').val());
  let zipCode = $.trim($('#zipCode').val());
  let active = $('#active').is(':checked') ? 1 : 0;

  if(district.length == 0) {
    $('#district-error').text('กรุณาระบุอำเภอ');
    $('#district').addClass('has-error');
    return false;
  }
  else {
    $('#district-error').text('');
    $('#district').removeClass('has-error');
  }

  if(province.length == 0) {
    $('#province-error').text('กรุณาระบุจังหวัด');
    $('#province').addClass('has-error');
    return false;
  }
  else {
    $('#province-error').text('');
    $('#province').removeClass('has-error');
  }

  if(zipCode.length == 0) {
    $('#zipCode-error').text('กรุณาระบุรหัสไปรษณีย์');
    $('#zipCode').addClass('has-error');
    return false;
  }
  else {
    $('#zipCode-error').text('');
    $('#zipCode').removeClass('has-error');
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'district' : district,
      'province' : province,
      'zipCode' : zipCode,
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
  let district = $.trim($('#district').val());
  let province = $.trim($('#province').val());
  let zipCode = $.trim($('#zipCode').val());
  let active = $('#active').is(':checked') ? 1 : 0;

  if(district.length == 0) {
    $('#district-error').text('กรุณาระบุอำเภอ');
    $('#district').addClass('has-error');
    return false;
  }
  else {
    $('#district-error').text('');
    $('#district').removeClass('has-error');
  }

  if(province.length == 0) {
    $('#province-error').text('กรุณาระบุจังหวัด');
    $('#province').addClass('has-error');
    return false;
  }
  else {
    $('#province-error').text('');
    $('#province').removeClass('has-error');
  }

  if(zipCode.length == 0) {
    $('#zipCode-error').text('กรุณาระบุรหัสไปรษณีย์');
    $('#zipCode').addClass('has-error');
    return false;
  }
  else {
    $('#zipCode-error').text('');
    $('#zipCode').removeClass('has-error');
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'district' : district,
      'province' : province,
      'zipCode' : zipCode,
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

$('#district').autocomplete({
  source:HOME + 'district',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split('>>');

    if(arr.length == 3) {
      $('#district').val(arr[0]);
      $('#province').val(arr[1]);
      $('#zipCode').val(arr[2]);
    }
  }
});

$('#province').autocomplete({
  source:HOME + 'province',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split('>>');

    if(arr.length == 3) {
      $('#district').val(arr[0]);
      $('#province').val(arr[1]);
      $('#zipCode').val(arr[2]);
    }
  }
});

$('#zipCode').autocomplete({
  source:HOME + 'zipcode',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split('>>');

    if(arr.length == 3) {
      $('#district').val(arr[0]);
      $('#province').val(arr[1]);
      $('#zipCode').val(arr[2]);
    }
  }
});

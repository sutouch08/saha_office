var HOME = BASE_URL + 'driver/';

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
  let emp_id = $('#emp_id').val();
  let type = $('#type').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  if(emp_id == "") {
    swal("กรุณาเลือกพนักงาน");
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'emp_id' : emp_id,
      'type' : type,
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
  let emp_id = $('#emp_id').val();
  let type = $('#type').val();
  let active = $('#active').is(':checked') ? 1 : 0;

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      "emp_id" : emp_id,
      "type" : type,
      "active" : active
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
          text:rs,
          type:'error'
        });
      }
    }
  });
}


function getDelete(id, emp_name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ emp_name +' หรือไม่ ?',
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
        'emp_id' : id
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

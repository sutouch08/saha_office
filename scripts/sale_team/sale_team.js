var HOME = BASE_URL + "sales_team/";

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function saveAdd() {
  var el_code = $('#code');
  var code_label = $('#code-error');
  var el_name = $('#name');
  var name_label = $('#name-error')
  var code = $.trim(el_code.val());
  var name = $.trim(el_name.val());
  var status = 'N';

  if($('#status').is(':checked')) {
    status = 'Y';
  }
  else {
    status = 'N';
  }

  //--- check empty code
  if(code.length === 0) {
    set_error(el_code, code_label, "Required");
    return false;
  }
  else {
    clear_error(el_code, code_label);
  }

  //--- check empty name
  if(name.length === 0) {
    set_error(el_name, name_label, "Required");
    return false;
  }
  else {
    clear_error(el_name, name_label);
  }

  //--- check duplicate code
  $.ajax({
    url:HOME + 'is_exists_code',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        //--- check duplicate name
        $.ajax({
          url:HOME + 'is_exists_name',
          type:'POST',
          cache:false,
          data:{
            'name' : name
          },
          success:function(ds) {
            var ds = $.trim(ds);
            if(ds === 'success') {
              //---- insert data
              $.ajax({
                url:HOME + 'add',
                type:'POST',
                cache:false,
                data:{
                  'code' : code,
                  'name' : name,
                  'status' : status
                },
                success:function(cs) {
                  var cs = $.trim(cs);
                  if(cs === 'success') {
                    swal({
                      title:'Success',
                      text:'เพิ่มรายการเรียบร้อยแล้ว',
                      type:'success',
                      timer:1000
                    });

                    setTimeout(function(){
                      goAdd();
                    }, 1200);
                  }
                }
              })
            }
            else {
              set_error(el_name, name_label, ds);
              return false;
            }
          }
        })
      }
      else {
        set_error(el_code, code_label, rs);
        return false;
      }
    }
  })
}



function update() {
  var change = 0;
  var el_code = $('#code');
  var code_label = $('#code-error');
  var el_name = $('#name');
  var name_label = $('#name-error')
  var code = $.trim(el_code.val());
  var name = $.trim(el_name.val());
  var status = 'N';

  var old_code = $('#old_code').val();
  var old_name = $('#old_name').val();
  var old_status = $('#old_status').val();

  if($('#status').is(':checked')) {
    status = 'Y';
  }
  else {
    status = 'N';
  }

  //--- check empty code
  if(code.length === 0) {
    set_error(el_code, code_label, "Required");
    return false;
  }
  else {
    clear_error(el_code, code_label);
  }

  //--- check empty name
  if(name.length === 0) {
    set_error(el_name, name_label, "Required");
    return false;
  }
  else {
    clear_error(el_name, name_label);
  }

  if(status != old_status) {
    change++;
  }

  if(code != old_code) {
    change++;
  }

  if(name != old_name) {
    change++;
  }

  if(change == 0) {
    swal({
      title:'Updated',
      text:'Update รายการเรียบร้อยแล้ว',
      type:'success',
      timer:1000
    });

    console.log('update');
    return false;
  }

  //--- check duplicate code
  $.ajax({
    url:HOME + 'is_exists_code',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'old_code' : old_code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        //--- check duplicate name
        $.ajax({
          url:HOME + 'is_exists_name',
          type:'POST',
          cache:false,
          data:{
            'name' : name,
            'old_name' : old_name
          },
          success:function(ds) {
            var ds = $.trim(ds);
            if(ds === 'success') {
              //---- insert data
              $.ajax({
                url:HOME + 'update',
                type:'POST',
                cache:false,
                data:{
                  'old_code' : old_code,
                  'code' : code,
                  'name' : name,
                  'status' : status
                },
                success:function(cs) {
                  var cs = $.trim(cs);
                  if(cs === 'success') {
                    swal({
                      title:'Updated',
                      text:'แก้ไขรายการเรียบร้อยแล้ว',
                      type:'success',
                      timer:1000
                    });

                    setTimeout(function(){
                      goEdit(code);
                    }, 1200)
                  }
                }
              })
            }
            else {
              set_error(el_name, name_label, ds);
              return false;
            }
          }
        })
      }
      else {
        set_error(el_code, code_label, rs);
        return false;
      }
    }
  });
}



function getDelete(code, name){
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
        'code' : code,
        'name' : name
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Sales Team has been deleted',
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

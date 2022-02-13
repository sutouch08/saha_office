var HOME = BASE_URL + "transfer_bin/";

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
  var el_code = $('#code');
  var code_label = $('#code-error');
  var el_name = $('#name');
  var name_label = $('#name-error')
  var code = $.trim(el_code.val());
  var name = $.trim(el_name.val());

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
        set_error(el_code, code_label, rs);
        return false;
      }
    }
  })
}



function update() {
  var id = $('#id').val();
  var el_name = $('#name');
  var name_label = $('#name-error')
  var name = $.trim(el_name.val());

  //--- check empty name
  if(name.length === 0) {
    set_error(el_name, name_label, "Required");
    return false;
  }
  else {
    clear_error(el_name, name_label);
  }


  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'name' : name
    },
    success:function(rs) {
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
            title:'Deleted',
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



$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});

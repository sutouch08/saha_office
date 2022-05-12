var HOME = BASE_URL + 'users/';
var uname_error = 1;
var pwd_error = 1;
var emp_error = 1;
var sale_error = 1;
var team_error = 1;
var ugroup_error = 1;
var dep_error = 1;
var div_error = 1;

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}



function goReset(id)
{
  window.location.href = HOME + 'reset_password/'+id;
}


function getDelete(id, uname) {

}

function getDelete(id, uname){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ uname +' หรือไม่ ?',
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
        'id' : id,
        'uname' : name
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



function saveAdd() {
  var arr = [
    {'el' : 'uname', 'label':'uname-error', 'error':'uname_error'}
    //{'el' : 'emp', 'label' : 'emp-error', 'error' : 'emp_error'},
    //{'el' : 'sale_team', 'label' : 'sale-team-error', 'error' : 'team_error'},
    //{'el' : 'ugroup', 'label' : 'ugroup-error', 'error' : 'ugroup_error'},
    //{'el' : 'department', 'label' : 'department-error', 'error' : 'dep_error'},
    //{'el' : 'division', 'label' : 'division-error', 'error' : 'div_error'}
  ];

  arr.forEach(check_value);


  var error = uname_error + pwd_error; // + emp_error + team_error + ugroup_error + dep_error + div_error;

  if( error > 0) {
    return false;
  }

  let uname = $('#uname').val();
  let emp_id = $('#emp_id').val();
  let emp_name = $('#emp').val();
  let sale_id = $('#saleman').val();
  let pwd = $('#pwd').val();
  let team = $('#sale_team').val();
  let ugroup = $('#ugroup').val();
  let dept = $('#department').val();
  let div = $('#division').val();
  let status = 0;

  if($('#status').is(':checked')) {
    status = 1;
  }

  load_in();
  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'uname' : uname,
      'emp_id' : emp_id,
      'emp_name' : emp_name,
      'sale_id' : sale_id,
      'pwd' : pwd,
      'sale_team' : team,
      'ugroup' : ugroup,
      'department' : dept,
      'division' : div,
      'status' : status
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goAdd();
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



function update() {
  var arr = [
    {'el' : 'uname', 'label':'uname-error', 'error':'uname_error'}/*,
    {'el' : 'emp', 'label' : 'emp-error', 'error' : 'emp_error'},
    {'el' : 'sale_team', 'label' : 'sale-team-error', 'error' : 'team_error'},
    {'el' : 'ugroup', 'label' : 'ugroup-error', 'error' : 'ugroup_error'},
    {'el' : 'department', 'label' : 'department-error', 'error' : 'dep_error'},
    {'el' : 'division', 'label' : 'division-error', 'error' : 'div_error'}*/
  ];

  arr.forEach(check_value);

  var error = uname_error; // + emp_error + team_error + ugroup_error + dep_error + div_error;

  if( error > 0) {
    return false;
  }

  let id = $('#user_id').val();
  let uname = $('#uname').val();
  let emp_id = $('#emp_id').val();
  let emp_name = $('#emp').val();
  let sale_id = $('#saleman').val();
  let team = $('#sale_team').val();
  let ugroup = $('#ugroup').val();
  let dept = $('#department').val();
  let div = $('#division').val();
  let status = 0;

  if($('#status').is(':checked')) {
    status = 1;
  }

  load_in();
  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'uname' : uname,
      'emp_id' : emp_id,
      'emp_name' : emp_name,
      'sale_id' : sale_id,
      'sale_team' : team,
      'ugroup' : ugroup,
      'department' : dept,
      'division' : div,
      'status' : status
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
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


function check_value(item, index) {
  let el = $('#'+item.el);
  let label = $('#'+item.label);
  let error = item.error;

  validData(el, label, error);
}



$('#uname').focusout(function(){
  var uname = $('#uname').val();
  if(uname.length > 0) {
    check_uname();
  }
  else {
    validData($(this), $('#uname-error'), "uname_error");
  }
});


function check_uname() {
  var el_uname = $('#uname');
  var label = $('#uname-error');
  var uname = $.trim($('#uname').val());
  var old_uname = $('#old_uname').val();
  console.log(old_uname);
  $.ajax({
    url:HOME + 'is_exists_uname',
    type:'POST',
    cache:false,
    data:{
      'uname' : uname,
      'old_uname' : old_uname
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        clear_error(el_uname, label);
        uname_error = 0;
      }
      else {
        set_error(el_uname, label, rs);
        uname_error = 1;
      }
    }
  })
}



function check_pwd() {
  var pwd = $('#pwd').val();
  var cfpwd = $('#cfpwd').val();
  var el = $('#cfpwd');
  var label = $('#cfpwd-error');

  if(pwd !== cfpwd) {
    set_error(el, label, "รหัสผ่านไม่ตรงกัน");
    pwd_error = 1;
  }
  else {
    clear_error(el, label);
    pwd_error = 0;
  }
}


$('#pwd').focusout(function(){
  var pwd = $(this).val();
  if(pwd.length > 0) {
    check_pwd();
  }
})


$('#cfpwd').focusout(function(){
  check_pwd();
})


$('#emp').autocomplete({
  source:BASE_URL + 'auto_complete/get_employee',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length === 2) {
      var name = arr[0];
      var id = arr[1];
      $('#emp').val(name);
      $('#emp_id').val(id);
    }
    else {
      $('#emp').val('');
      $('#emp_id').val('');
    }
  }
});


function validData(el, label, error) {
  if(el.val() == '') {
    set_error(el, label, "Required");
    window[error] = 1;
  }
  else {
    clear_error(el, label);
    window[error] = 0;
  }

  console.log(error + " = " + window[error]);
}


function reset_password(){
  var id = $('#user_id').val();
  var pwd = $('#pwd');
  var cfpwd = $('#cfpwd');
  var pLabel = $('#pwd-error');
  var cLabel = $('#cfpwd-error');
  var password = $.trim(pwd.val());
  var cm_pwd = $.trim(cfpwd.val());

  if(password.length === 0) {
    set_error(pwd, pLabel, "Required");
    pwd.focus();
    return false;
  }
  else {
    clear_error(pwd, pLabel);
  }

  if(password !== cm_pwd) {
    set_error(cfpwd, cLabel, "Password Mismatch");
    cfpwd.focus();
    return false;
  }
  else {
    clear_error(pwd, cLabel);
  }

  load_in();

  $.ajax({
    url:HOME + 'change_password',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'pwd' : password
    },
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          text:'Password has been changed',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error!!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}






$('#emp').focusout(function() {
  validData($(this), $('#emp-error'), 'emp_error');
})


$('#sale_team').focusout(function() {
  validData($(this), $('#sale-team-error'), 'team_error');
})

$('#ugroup').focusout(function() {
  validData($(this), $('#ugroup-error'), 'ugroup_error');
})

$('#department').focusout(function() {
  validData($(this), $('#department-error'), 'dep_error');
})

$('#division').focusout(function() {
  validData($(this), $('#division-error'), 'div_error');
})

//----- focus next element when press enter
$('#uname').keyup(function(e){
  if(e.keyCode === 13) {
    $('#emp').focus();
  }
})


$('#emp').keyup(function(e){
  if(e.keyCode === 13) {
    $('#pwd').focus();
  }
})

$('#pwd').keyup(function(e){
  if(e.keyCode === 13) {
    $('#cfpwd').focus();
  }
})

$('#cfpwd').keyup(function(e){
  if(e.keyCode === 13) {
    $('#sale_team').focus();
  }
})

$('#sale_team').keyup(function(e){
  if(e.keyCode === 13) {
    $('#ugroup').focus();
  }
})

$('#ugroup').keyup(function(e){
  if(e.keyCode === 13) {
    $('#department').focus();
  }
})


$('#department').keyup(function(e){
  if(e.keyCode === 13) {
    $('#division').focus();
  }
})

$('#division').keyup(function(e){
  if(e.keyCode === 13) {
    $('#status').focus();
  }
})

$('#status').keyup(function(e){
  if(e.keyCode === 13) {
    $('#btn-save').focus();
  }
})

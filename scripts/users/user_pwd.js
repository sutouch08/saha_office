var HOME = BASE_URL + 'user_pwd/';

var pwd_error = 1;
var cpwd_error = 1;

function changePassword() {
  var uid = $('#uid').val();
  var user_id = $('#user_id').val();
  var cpwd = $('#cpwd').val(); //--- current pwd
  var pwd = $('#pwd').val(); //--- new pwd
  var cfpwd = $('#cfpwd').val();

  if(cpwd.length === 0) {
    set_error($('#cpwd'), $('#cpwd-error'), "Required");
    return false;
  }
  else {
    clear_error($('#cpwd'), $('#cpwd-error'));
  }

  if(pwd.length === 0) {
    set_error($('#pwd'), $('#pwd-error'), "Required");
    return false;
  }
  else {
    clear_error($('#pwd'), $('#pwd-error'));
  }

  if(cfpwd !== pwd) {
    set_error($('#cfpwd'), $('#cfpwd-error'), "Password Mismatch");
    return false;
  }
  else {
    clear_error($('#cfpwd'), $('#cfpwd-error'));
  }

  $.ajax({
    url:HOME + 'verify_password',
    type:'POST',
    cache:false,
    data:{
      'uid' : uid,
      'pwd' : cpwd
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        var user_id = $('#user_id').val();

        $.ajax({
          url:HOME + 'change_password',
          type:'POST',
          cache:false,
          data:{
            'user_id' : user_id,
            'pwd' : pwd
          },
          success:function(rs) {
            var rs = $.trim(rs);
            if(rs === 'success') {
              swal({
                title:'Success',
                text:'Password changed',
                type:'success',
                timer:1000
              });

              setTimeout(function(){
                window.location.reload();
              },1200);
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
      else {
        set_error($('#cpwd'), $('#cpwd-error'), rs);
        return false;
      }
    }
  })

}



function check_pwd() {
  var cpwd = $('#cpwd').val();
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

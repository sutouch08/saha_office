var HOME = BASE_URL + 'approver/';
var uname_error = 1;
var emp_error = 1;
var team_error = 1;
var discount_error = 1;

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

  var arr = [
    {'el' : 'uname', 'label':'uname-error', 'error':'uname_error'},
    {'el' : 'sale_team', 'label' : 'sale-team-error', 'error' : 'team_error'},
    {'el' : 'discount', 'label' : 'discount-error', 'error' : 'discount_error'}
  ];

  arr.forEach(check_value);

  var error = uname_error + team_error + discount_error;

  if( error > 0) {
    return false;
  }

  let uname = $('#uname').val();
  let emp_name = $('#emp_name').val();
  let team = $('#sale_team').val();
  let discount = $('#discount').val();
  let status = $('#status').is(':checked') ? 1 : 0;


  load_in();
  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'uname' : uname,
      'emp_name' : emp_name,
      'sale_team' : team,
      'discount' : discount,
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
    {'el' : 'uname', 'label':'uname-error', 'error':'uname_error'},
    {'el' : 'sale_team', 'label' : 'sale-team-error', 'error' : 'team_error'},
    {'el' : 'discount', 'label' : 'discount-error', 'error' : 'discount_error'}
  ];

  arr.forEach(check_value);

  var error = uname_error + team_error + discount_error;

  if( error > 0) {
    return false;
  }

  let id = $('#id').val();
  let uname = $('#uname').val();
  let emp_name = $('#emp_name').val();
  let team = $('#sale_team').val();
  let discount = $('#discount').val();
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
      'emp_name' : emp_name,
      'sale_team' : team,
      'discount' : discount,
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
        'id' : id,
        'uname' : name
      },
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Success',
            text:'Approval has been deleted',
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

$('#uname').autocomplete({
  source: BASE_URL + 'auto_complete/get_user_and_emp',
  autoFocus:true,
  close:function() {
    var rs = $.trim($(this).val());
    if(rs === 'Not found') {
      $(this).val('');
    }
    else {
      var arr = rs.split(' | ');
      if(arr.length === 2) {
        $(this).val(arr[0]); //--- uname
        $('#emp_name').val(arr[1]); //--- emp name
      }
      else {
        $(this).val('');
      }
    }
  }
});



$('#uname').focusout(function(){
  validData($(this), $('#uname-error'), "uname_error");
});


$('#discount').focusout(function(){
  validData($(this), $('#discount-error'), "discount-errror");
})

$('#discount').keyup(function(){
  var disc = $(this).val();

  var disc = parseFloat(disc);
  if(disc > 100) {
    $(this).val(100);
  }

  if(disc < 0) {
    $(disc).val(0);
  }
})


function check_value(item, index) {
  let el = $('#'+item.el);
  let label = $('#'+item.label);
  let error = item.error;

  validData(el, label, error);
}

function validData(el, label, error) {
  if(el.val() == '') {
    set_error(el, label, "Required");
    window[error] = 1;
  }
  else {
    clear_error(el, label);
    window[error] = 0;
  }
}


$('#sale_team').focusout(function() {
  validData($(this), $('#sale-team-error'), 'team_error');
})

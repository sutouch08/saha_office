var HOME = BASE_URL + 'discount_rule/';

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
  var name = $('#name').val();
  var sale_team = $('#sale_team').val();
  var min_disc = parseDefault(parseFloat($('#min_disc').val()), 0);
  var max_disc = parseDefault(parseFloat($('#max_disc').val()),0);
  var active = $('#active').is(':checked') === true ? 1 : 0;

  if(name.length == 0) {
    set_error($('#name'), $('#name-error'), "Name is required");
    return false;
  }
  else {
    clear_error($('#name'), $('#name-error'));
  }

  if(sale_team.length == 0) {
    set_error($('#sale_team'), $('#sale-team-error'), "Please select sale_team");
    return false;
  }
  else {
    clear_error($('#sale_team'), $('#sale-team-error'));
  }

  if(min_disc < 0) {
    set_error($('#min_disc'), $('#min-disc-error'), "Min discount cannot less than 0");
    return false;
  }
  else {
    clear_error($('#min_disc'), $('#min-disc-error'));
  }


  if(max_disc <= 0 && max_disc >= min_disc) {
    set_error($('#max_disc'), $('#max-disc-error'), "Max discount must greater than Min discount");
    return false;
  }
  else {
    clear_error($('#max_disc'), $('#max-disc-error'));
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'name' : name,
      'sale_team' : sale_team,
      'min_disc' : min_disc,
      'max_disc' : max_disc,
      'active' : active
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

        setTimeout(function() {
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
  var id = $('#id').val();
  var name = $('#name').val();
  var sale_team = $('#sale_team').val();
  var min_disc = parseDefault(parseFloat($('#min_disc').val()), 0);
  var max_disc = parseDefault(parseFloat($('#max_disc').val()),0);
  var active = $('#active').is(':checked') === true ? 1 : 0;

  if(name.length == 0) {
    set_error($('#name'), $('#name-error'), "Name is required");
    return false;
  }
  else {
    clear_error($('#name'), $('#name-error'));
  }

  if(sale_team.length == 0) {
    set_error($('#sale_team'), $('#sale-team-error'), "Please select sale_team");
    return false;
  }
  else {
    clear_error($('#sale_team'), $('#sale-team-error'));
  }

  if(min_disc < 0) {
    set_error($('#min_disc'), $('#min-disc-error'), "Min discount cannot less than 0");
    return false;
  }
  else {
    clear_error($('#min_disc'), $('#min-disc-error'));
  }


  if(max_disc <= 0 && max_disc >= min_disc) {
    set_error($('#max_disc'), $('#max-disc-error'), "Max discount must greater than Min discount");
    return false;
  }
  else {
    clear_error($('#max_disc'), $('#max-disc-error'));
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'name' : name,
      'sale_team' : sale_team,
      'min_disc' : min_disc,
      'max_disc' : max_disc,
      'active' : active
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



function getDelete(id, name) {
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+name+"' หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			load_in();
			$.ajax({
				url: HOME + 'delete',
				type:"POST",
        cache:"false",
				data:{
					'id' : id
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Deleted',
							type:'success',
							timer:1000
						});

            $('#row-'+id).remove();
            reIndex();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}

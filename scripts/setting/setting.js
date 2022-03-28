var HOME = BASE_URL + 'setting/';
function updateConfig(formName)
{
	load_in();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url: HOME + "update_config",
		type:"POST",
    cache:"false",
    data: formData,
		success: function(rs){
			load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
		}
	});
}



$('#default-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/1',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})

$('#lend-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/8',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})


$('#transform-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/7',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})


function changeURL(tab)
{
	var url = HOME + 'index/'+tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'setting', url);
}

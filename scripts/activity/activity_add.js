function add() {
	var ds = {
		'Action' : $('#Action').val(),
		'CntctType' : $('#CntctType').val(),
		'TypeName' : $('#CntctType option:selected').text(),
		'CntctSbjct' : $('#CntctSbjct').val(),
		'SubjectName' : $('#CntctSbjct option:selected').text(),
		'attendType' : $('#attendType').val(),
		'AttendUser' : $('#AttendUser').val(),
		'UserName' : $('#AttendUser option:selected').text(),
		'AttendEmpl' : $('#AttendEmpl').val(),
		'EmpName' : $('#AttendEmpl option:selected').text(),
		'CardCode' : $('#CardCode').val(),
		'CardName' : $('#CardName').val(),
		'CntctCode' : $('#ContactPer').val(),
		'ContactPer' : $('#ContactPer option:selected').text(),
		'Tel' : $('#Tel').val(),
		'Details' : $('#Details').val(),
		'Notes' : $('#Notes').val(),
		'Recontact' : $('#Recontact').val(),
		'BeginTime' : get_int_time($('#BeginTime').val()),
		'endDate' : $('#endDate').val(),
		'ENDTime' : get_int_time($('#ENDTime').val()),
		'Duration' : get_duration(),
		'Priority' : $('#Priority').val(),
		'Location' : $('#Location').val(),
		'FIPROJECT' : $('#FIPROJECT').val(),
		'Stage' : $('#Stage').val(),
		'DocType' : $('#DocType').val(),
		'DocNum' : $('#DocNum').val()
	}


	if(ds.attendType == 'E' && ds.AttendEmpl == '') {
		$('#AttendEmpl_chosen').addClass('has-error');
		swal('Assigned Employee is Required');
		return false;
	}
	else {
		$('#AttendEmpl_chosen').removeClass('has-error');
	}

	if(ds.attendType == 'U' && ds.AttendUser == '') {
		$('#AttendUser_chosen').addClass('has-error');
		swal('Assigned User is Required');
		return false;
	}
	else {
		$('#AttendUser_chosen').removeClass('has-error');
	}

	if(ds.Details == '') {
		$('#Details').addClass('has-error');
		swal('Remark is Required');
		return false;
	}
	else {
		$('#Details').removeClass('has-error');
	}

	if(ds.Recontact == '') {
		$('#Recontact').addClass('has-error');
		swal('Start Date is Required');
		return false;
	}
	else if(!isDate(ds.Recontact)) {
		$('#Recontact').addClass('has-error');
		swal('Invalid Date format');
		return false;
	}
	else {
		$('#Recontact').removeClass('has-error');
	}

	if($('#BeginTime').val() == '') {
		$('#BeginTime').addClass('has-error');
		swal('Start Time is Required');
		return false;
	}
	else {
		$('#BeginTime').removeClass('has-error');
	}

	if(ds.endDate == '') {
		$('#endDate').addClass('has-error');
		swal('End Date is Required');
		return false;
	}
	else if(!isDate(ds.endDate)) {
		$('#endDate').addClass('has-error');
		swal('Invalid Date format');
		return false;
	}
	else {
		$('#endDate').removeClass('has-error');
	}

	if($('#ENDTime').val() == '') {
		$('#ENDTime').addClass('has-error');
		swal('End Time is Required');
		return false;
	}
	else {
		$('#ENDTime').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		dataType:'json',
		contentType:'application:json',
		processData:false,
		data:JSON.stringify(ds),
		complete:function(rs){
			load_out();
			if(rs.responseText === 'success') {
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
					text:rs.responseText,
					type:'error'
				})
			}
		}
	})

}



function update() {
	var ds = {
		'code' : $('#code').val(),
		'Action' : $('#Action').val(),
		'CntctType' : $('#CntctType').val(),
		'TypeName' : $('#CntctType option:selected').text(),
		'CntctSbjct' : $('#CntctSbjct').val(),
		'SubjectName' : $('#CntctSbjct option:selected').text(),
		'attendType' : $('#attendType').val(),
		'AttendUser' : $('#AttendUser').val(),
		'UserName' : $('#AttendUser option:selected').text(),
		'AttendEmpl' : $('#AttendEmpl').val(),
		'EmpName' : $('#AttendEmpl option:selected').text(),
		'CardCode' : $('#CardCode').val(),
		'CardName' : $('#CardName').val(),
		'CntctCode' : $('#ContactPer').val(),
		'ContactPer' : $('#ContactPer option:selected').text(),
		'Tel' : $('#Tel').val(),
		'Details' : $('#Details').val(),
		'Notes' : $('#Notes').val(),
		'Recontact' : $('#Recontact').val(),
		'BeginTime' : get_int_time($('#BeginTime').val()),
		'endDate' : $('#endDate').val(),
		'ENDTime' : get_int_time($('#ENDTime').val()),
		'Duration' : get_duration(),
		'Priority' : $('#Priority').val(),
		'Location' : $('#Location').val(),
		'FIPROJECT' : $('#FIPROJECT').val(),
		'Stage' : $('#Stage').val(),
		'DocType' : $('#DocType').val(),
		'DocNum' : $('#DocNum').val()
	}


	if(ds.attendType == 'E' && ds.AttendEmpl == '') {
		$('#AttendEmpl_chosen').addClass('has-error');
		swal('Assigned Employee is Required');
		return false;
	}
	else {
		$('#AttendEmpl_chosen').removeClass('has-error');
	}

	if(ds.attendType == 'U' && ds.AttendUser == '') {
		$('#AttendUser_chosen').addClass('has-error');
		swal('Assigned User is Required');
		return false;
	}
	else {
		$('#AttendUser_chosen').removeClass('has-error');
	}

	if(ds.Details == '') {
		$('#Details').addClass('has-error');
		swal('Remark is Required');
		return false;
	}
	else {
		$('#Details').removeClass('has-error');
	}

	if(ds.Recontact == '') {
		$('#Recontact').addClass('has-error');
		swal('Start Date is Required');
		return false;
	}
	else if(!isDate(ds.Recontact)) {
		$('#Recontact').addClass('has-error');
		swal('Invalid Date format');
		return false;
	}
	else {
		$('#Recontact').removeClass('has-error');
	}

	if($('#BeginTime').val() == '') {
		$('#BeginTime').addClass('has-error');
		swal('Start Time is Required');
		return false;
	}
	else {
		$('#BeginTime').removeClass('has-error');
	}

	if(ds.endDate == '') {
		$('#endDate').addClass('has-error');
		swal('End Date is Required');
		return false;
	}
	else if(!isDate(ds.endDate)) {
		$('#endDate').addClass('has-error');
		swal('Invalid Date format');
		return false;
	}
	else {
		$('#endDate').removeClass('has-error');
	}

	if($('#ENDTime').val() == '') {
		$('#ENDTime').addClass('has-error');
		swal('End Time is Required');
		return false;
	}
	else {
		$('#ENDTime').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		dataType:'json',
		contentType:'application:json',
		processData:false,
		data:JSON.stringify(ds),
		complete:function(rs){
			load_out();
			if(rs.responseText === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					goEdit(ds.code);
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs.responseText,
					type:'error'
				})
			}
		}
	})

}


function updateSubjectList() {
	var type = $('#CntctType').val();
	var subject = $('#CntctSbjct');

	$.ajax({
		url:HOME + 'get_subject_list',
		type:'GET',
		cache:false,
		data:{
			"typeCode" : type
		},
		success:function(rs) {
			if(isJson(rs)) {
				var source = $('#subject-template').html();
				var data = $.parseJSON(rs);
				var output = $('#CntctSbjct');

				render(source, data, output);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:error
				})
			}
		}
	})
}

$('#CardCode').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var cust = rs.split(' | ');
		if(cust.length === 2) {
			let code = cust[0];
			let name = cust[1];
			$('#CardCode').val(code);
			$('#CardName').val(name);
			updateContactList();

			$('#DocNum').val('');
			DocNumInit();
		}
		else {
			$('#CardCode').val('');
			$('#CardName').val('');
		}
	}
})


$('#CardName').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var cust = rs.split(' | ');
		if(cust.length === 2) {
			let code = cust[0];
			let name = cust[1];
			$('#CardCode').val(code);
			$('#CardName').val(name);
			updateContactList();

			$('#DocNum').val('');
			DocNumInit();
		}
		else {
			$('#CardCode').val('');
			$('#CardName').val('');
		}
	}
})


function updateContactList() {
	var cardCode = $('#CardCode').val();
	if(cardCode.length) {
		$.ajax({
			url:HOME + 'get_contact_list',
			type:'GET',
			cache:false,
			data: {
				'CardCode' : cardCode
			},
			success: function(rs) {
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var source = $('#contact-template').html();
					var ds = $.parseJSON(rs);
					var output = $('#ContactPer');

					render(source, ds, output);
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
}



function toggleAssignedTo() {
	var type = $('#attendType').val();

	if(type === 'U') {
		$('#AttendEmpl').val('');
		$('#AttendEmpl').trigger('chosen:updated');
		$('#AttendUser_chosen').css('width', '100%');

		$('#div-empl').addClass('hide');
		$('#div-user').removeClass('hide');

	}

	if(type === 'E') {
		$('#AttendUser').val('');
		$('#AttendUser').trigger('chosen:updated');
		$('#AttendEmpl_chosen').css('width', '100%');

		$('#div-user').addClass('hide');
		$('#div-empl').removeClass('hide');

	}
}



$('#Recontact').mask('99-99-9999');
$('#BeginTime').mask('99:99');
$('#endDate').mask('99-99-9999');
$('#ENDTime').mask('99:99');

$('#Recontact').datepicker({
	dateFormat:'dd-mm-yy'
});

$('#endDate').datepicker({
	dateFormat:'dd-mm-yy'
})

$('#Recontact').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#BeginTime').focus();
	}
})

$('#BeginTime').keyup(function(e) {
	if(e.keyCode === 13) {
		$('#endDate').focus();
	}
})


$('#endDate').keyup(function(e){
	if(e.keyCode === 13) {
		$('#ENDTime').focus();
	}
})

$('#Recontact').focusout(function() {
	var date = $(this).val();
	if(date.length) {
		var arr = date.split('-');
		var year = parseInt(arr[2]);
		var year = year > 2500 ? year - 543 : year;

		var date = arr[0] + '-' + arr[1] + '-' + year;

		$(this).val(date);
	}

	timeDiff();
});

$('#BeginTime').focusout(function() {
	timeDiff();
});

$('#endDate').focusout(function() {
	var date = $(this).val();
	if(date.length) {
		var arr = date.split('-');
		var year = parseInt(arr[2]);
		var year = year > 2500 ? year - 543 : year;

		var date = arr[0] + '-' + arr[1] + '-' + year;

		$(this).val(date);
	}
	timeDiff();
});


$('#ENDTime').focusout(function() {
	timeDiff();
});

function startTime() {
	var now = new Date();
	var date = ('0'+ now.getDate()).slice(-2) + '-' + ('0' + (now.getMonth()+1)).slice(-2) + '-' + now.getFullYear();
	var time = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);


	$('#Recontact').val(date);
	$('#BeginTime').val(time);

	timeDiff();
}

function endTime() {
	var now = new Date();
	var date = ('0'+ now.getDate()).slice(-2) + '-' + ('0' + (now.getMonth()+1)).slice(-2) + '-' + now.getFullYear();
	var time = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);

	$('#endDate').val(date);
	$('#ENDTime').val(time);

	timeDiff();
}


function get_date_time(date, time)
{
	var date = date.replaceAll('/', '-');
	var date = date.replaceAll('.', '-');

	if(date.length) {
		var arr = date.split('-');
		var d = parseInt(arr[0]);
		var m = parseInt(arr[1]);
		var y = parseInt(arr[2]);

		var month = m > 12 ? d : m;
		var day = d > 12 ? d : (m > 12 ? m : d);
		var year = y > 2500 ? (y - 543) : y;

		date = new Date(month+'/'+day+'/'+year+' '+time);

		return date;
	}

	return false;
}


function get_int_time(time) {
	var int = '0';
	if(time.length) {
		var arr = time.split(':');
		if(arr.length > 1) {
			var h = arr[0];
			var m = arr[1];

			int = h+m;
		}
	}

	return int;
}



function get_duration()
{
	var from = get_date_time($('#Recontact').val(), $('#BeginTime').val());
	var to = get_date_time($('#endDate').val(), $('#ENDTime').val());

	if(from !== false && to !== false) {
		//---- return time diff in second
		var delta = Math.abs(to - from)/1000;
	}
	else {
		var delta = 0;
	}


	return delta;
}



function timeDiff() {
	var startDate = $('#Recontact').val();
	var startTime = $('#BeginTime').val();

	var endDate = $('#endDate').val();
	var endTime = $('#ENDTime').val();

	if(startDate.length && startTime.length && endDate.length && endTime.length) {
		var arr = startDate.split('-');
		var date1 = arr[1]+'-'+arr[0]+'-'+ arr[2] + ' ' + startTime;

		var arr = endDate.split('-');
		var date2 = arr[1]+'-'+arr[0]+'-'+arr[2] + ' ' + endTime;

		var date1 = new Date(date1);
		var date2 = new Date(date2);

		var delta = Math.abs(date2 - date1)/1000;


		// calculate (and subtract) whole days
		var days = Math.floor(delta / 86400);
		delta -= days * 86400;

		// calculate (and subtract) whole hours
		var hours = Math.floor(delta / 3600) % 24;
		delta -= hours * 3600;

		// calculate (and subtract) whole minutes
		var minutes = Math.floor(delta / 60) % 60;
		delta -= minutes * 60;

		// what's left is seconds
		var seconds = delta % 60;  // in theory the modulus is not required

		var dayText = days > 0 ? days + ' Days ' : '';
		var hourtext = hours > 0 ? hours + ' Hours ' : '';
		var minutesText = minutes > 0 ? minutes + ' Minutes' : '';

		var difText = dayText+hourtext+minutesText;

		$('#Duration').val(difText);
	}
}


$('#projectName').autocomplete({
	source:HOME + 'get_project',
	autoFocus:true,
	open:function(event) {
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length === 2) {
			let code = arr[0];
			let name = arr[1];
			$('#FIPROJECT').val(code);
			$(this).val(name);
		}
		else {
			$('#FIPROJECT').val('');
			$(this).val('');
		}
	}
})


function DocNumInit() {
	//$('#DocNum').val('');

	var objType = $('#DocType').val();
	var customer = $('#CardCode').val();

	$('#DocNum').autocomplete({
		source:BASE_URL + 'auto_complete/get_document/'+objType+'/'+customer,
		autoFocus:true,
		open:function(event) {
			var $ul = $(this).autocomplete('widget');
			$ul.css('width', 'auto');
		},
		close:function() {
			var rs = $(this).val();
			var arr = rs.split(' | ');
			if(arr.length === 3) {
				let code = arr[0];
				$(this).val(code);
			}
			else {
				$(this).val('');
			}
		}
	})
}




function getDocList() {
	var objType = $('#DocType').val();
	var docNum = "";
	var cardCode = $('#CardCode').val();

	$.ajax({
		url:HOME + 'get_document',
		type:'GET',
		cache:false,
		data:{
			'objectType' : objType,
			'searchText' : docNum,
			'cardCode' : cardCode
		},
		success:function(rs){
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				var source = $('#doc-template').html();
				var output = $('#result');

				render(source, ds, output);
			}
			else {
				var html = "<tr><td colspan='5' class='text-center'>No Documents found</td></tr>";
				$('#result').html(html);
			}
		}
	})

	$('#documentModal').modal('show');
}

function searchDocument() {
	var objType = $('#DocType').val();
	var docNum = $('#search-box').val();
	var cardCode = $('#CardCode').val();

	$.ajax({
		url:HOME + 'get_document',
		type:'GET',
		cache:false,
		data:{
			'objectType' : objType,
			'searchText' : docNum,
			'cardCode' : cardCode
		},
		success:function(rs){
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				var source = $('#doc-template').html();
				var output = $('#result');

				render(source, ds, output);
			}
			else {
				var html = "<tr><td colspan='5' class='text-center'>No Documents found</td></tr>";
				$('#result').html(html);
			}
		}
	})
}


function add_value(docNo) {
	$('.rw').css('background-color', 'white');
	$('#'+docNo).css('background-color', '#c4dcec');
	$('#docNo').val(docNo);
}

function add_value_and_close(docNo) {
	$('#DocNum').val(docNo);
	closeModal();
}


function clearDocNum() {
	$('#DocNum').val('');
}


function choose() {
	var docNum = $('#docNo').val();
	if(docNum.length) {
		$('#DocNum').val(docNum);
	}

	$('#documentModal').modal('hide');
}


function closeModal() {
	$('#documentModal').modal('hide');
}


$('#search-box').keyup(function(e) {
	if(e.keyCode === 13) {
		var code = $(this).val();
		if(code.length) {
			searchDocument();
		}
	}
})

$(document).ready(function(){
	//startTime();
	//endTime();
	timeDiff();
	DocNumInit();
})

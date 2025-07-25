window.addEventListener('load', () => {
  let uuid = get_uuid();

  if(uuid == "" || uuid == null || uuid == undefined) {
    uid = generateUID();

		localStorage.setItem('ix_uuid', uid);
  }
});


function get_uuid() {
	return localStorage.getItem('ix_uuid');
}


function go_to(page){
	window.location.href = BASE_URL + page;
}


function checkError(){
  if($('#error').length){
    swal({
      title:'Error!',
      text: $('#error').val(),
      type:'error'
    })
  }

  if($('#success').length){
    swal({
      title:'Success',
      text:$('#success').val(),
      type:'success',
      timer:1500
    });
  }
}


//--- save side bar layout to cookie
function toggle_layout() {
	var sidebar_layout = getCookie('sidebar_layout');
	if(sidebar_layout == 'menu-min'){
		setCookie('sidebar_layout', '', 90);
	}else{
		setCookie('sidebar_layout', 'menu-min', 90);
	}
}


function load_in() {
	$("#loader").css("display","block");
	$('#loader-backdrop').css('display', 'block');
	$("#loader").animate({opacity:0.8},300);
}


function load_out() {
	$("#loader").animate({
		opacity:0
	},300,
	function() {
		$("#loader").css("display","none");
		$('#loader-backdrop').css('display', 'none');
	});
}


function set_error(el, label, message) {
	el.addClass('has-error');
	label.text(message);
}


function clear_error(el, label) {
	el.removeClass('has-error');
	label.text('');
}


function isDate(txtDate) {
  var currVal = txtDate;
  if(currVal == '')
  return false;
  //Declare Regex
  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
  var dtArray = currVal.match(rxDatePattern); // is format OK?
  if (dtArray == null){
    return false;
  }
  //Checks for mm/dd/yyyy format.
  dtDay= dtArray[1];
  dtMonth = dtArray[3];
  dtYear = dtArray[5];
  if (dtMonth < 1 || dtMonth > 12){
    return false;
  }else if (dtDay < 1 || dtDay> 31){
    return false;
  }else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31){
    return false;
  }else if (dtMonth == 2){
    var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
    if (dtDay> 29 || (dtDay ==29 && !isleap)){
      return false;
    }
  }
  return true;
}


function removeCommas(str) {
  while (str.search(",") >= 0) {
    str = (str + "").replace(',', '');
  }
  return str;
}


function addCommas(number) {
  return (number.toString())
  .replace(/^([-+]?)(0?)(\d+)(.?)(\d+)$/g,
  function(match, sign, zeros, before, decimal, after) {
    var reverseString = function(string) { return string.split('').reverse().join(''); };
    var insertCommas  = function(string) {
      var reversed   = reverseString(string);
      var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
      return reverseString(reversedWithCommas);
    };
    return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
  });
}


function render(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.html(html);
}


function render_prepend(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.prepend(html);
}


function render_append(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.append(html);
}


function render_after(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertAfter(output);
}


function render_before(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertBefore(output);
}


function set_rows()
{
	var rows = $('#set_rows').val();
	$.ajax({
		url:BASE_URL+'tools/set_rows',
		type:'POST',
		cache:false,
		data:{
			'set_rows' : rows
		},
		success:function(){
			window.location.reload();
		}
	});
}


$('#set_rows').keyup(function(e){
	if(e.keyCode == 13 && $(this).val() > 0){
		set_rows();
	}
});


function reIndex(){
	let no = parseDefault(parseInt($('#no').val()), 0);
  $('.no').each(function(index, el) {
    no += 1;
    $(this).text(addCommas(no));
  });
}


var downloadTimer;
function get_download(token)
{
	load_in();
	downloadTimer = window.setInterval(function(){
		var cookie = getCookie("file_download_token");
		if(cookie == token)
		{
			finished_download();
		}
	}, 1000);
}


function finished_download()
{
	window.clearInterval(downloadTimer);
	deleteCookie("file_down_load_token");
	load_out();
}


function isJson(str){
	try{
		JSON.parse(str);
	}catch(e){
		return false;
	}
	return true;
}


function printOut(url)
{
	var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}


function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}


function deleteCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


function parseDefault(value, def){
	if(isNaN(value)){
		return def; //--- return default value
	}

	return value;
}


function parseDefaultFloat(value, def) {
  return parseDefault(parseFloat(value), def);
}


function parseDefaultInt(value, def) {
  return parseDefault(parseInt(value), def); 
}

//--- return discount array
function parseDiscount(discount_label, price)
{
	var discLabel = {
		"discLabel1" : 0,
		"discUnit1" : '',
		"discLabel2" : 0,
		"discUnit2" : '',
		"discLabel3" : 0,
		"discUnit3" : '',
		"discLabel4" : 0,
		"discUnit4" : '',
		"discLabel5" : 0,
		"discUnit5" : '',
		"discountAmount" : 0
	};

	if(discount_label != '' && discount_label != 0)
	{
		var arr = discount_label.split('+');
		arr.forEach(function(item, index){
			var i = index + 1;
			if(i <= 5){
				var disc = item.split('%');
				var value = parseDefault(parseFloat(disc[0]), 0);
				discLabel["discLabel"+i] = value;
				if(disc.length == 2){
					var amount = (value * 0.01) * price;
					discLabel["discUnit"+i] = '%';
					discLabel["discountAmount"] += amount;
					price -= amount;
				}else{
					discLabel["discountAmount"] += value;
					price -= value;
				}
			}
		});
	}

	return discLabel;
}


function getSearch() {
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
	if(e.keyCode === 13) {
		getSearch();
	}
});


$('.filter').change(function(){
  getSearch();
})


function clearFilter() {
	let url = HOME + 'clear_filter';
	$.get(url, function(rs){ goBack(); });
}


function sort(field){
	var el = $("#sort_"+field);
	var sort_by = "";

	sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
	sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';

	$('.sorting').removeClass('sorting_desc');
	$('.sorting').removeClass('sorting_asc');

	el.addClass(sort_class);
	$('#sort_by').val(sort_by);
	$('#order_by').val(field);

	getSearch();
}


function validCode(input){
  var regex = /[^a-z0-9-_.]+/gi;
  input.value = input.value.replace(regex, '');
}


function validInput(input, regex){
  var regex = regex === undefined ? /[^a-z0-9-_.]+/gi : regex;
  input.value = input.value.replace(regex, '');
}


function print_url(target)
{
	$("<iframe>").hide().attr("src", target).appendTo("body");
}


function generateUID() {
    return Math.random().toString(36).substring(2, 15) +
        Math.random().toString(36).substring(2, 15);
}


function uniqueId()
{
	return Math.floor(Math.random() * Date.now());
}

function roundNumber(num, digit)
{
	if(digit === undefined) {
		digit = 2;
	}
	else {
		ditit = parseDefault(parseInt(digit), 2);
	}

	return Number(parseFloat(num).toFixed(digit));
}


$.fn.hasError = function(msg) {
  name = this.attr('id');

  if(msg !== undefined) {
    $('#'+name+'-error').text(msg);
  }

  return this.addClass('has-error');
};


$.fn.clearError = function() {
  name = this.attr('id');
  $('#'+name+'-error').text('');
  return this.removeClass('has-error');
};


function clearErrorByClass(className) {
  $('.'+className).each(function() {
    let name = $(this).attr('id');
    $('#'+name+'-error').text('');
    $(this).removeClass('has-error');
  })
}

function showError(response) {
  load_out();

  setTimeout(() => {
    swal({
      title:'Error!',
      text:(typeof response === 'object') ? response.responseText : response,
      type:'error',
      html:true
    })
  }, 100);
}

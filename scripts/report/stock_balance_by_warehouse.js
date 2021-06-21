var HOME = BASE_URL + 'report/stock_by_warehouse/';

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function(){
    let item = $(this).val();
    let arr = item.split(' | ');
    if(arr.length == 2) {
      var code = arr[0];
      $(this).val(code);

      var pdFrom = code;
      var pdTo = $('#pdTo').val();
      if(pdTo.length > 0 && pdFrom.length > 0) {
        if(pdFrom > pdTo) {
          $('#pdTo').val(pdFrom);
          $('#pdFrom').val(pdTo);
        }
      }

      if(pdFrom.length > 0 && pdTo.length == 0) {
        $('#pdTo').focus();
      }
    }
    else {
      $(this).val('');
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code_and_name',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function(){
    let item = $(this).val();
    let arr = item.split(' | ');
    if(arr.length == 2) {
      var code = arr[0];
      $(this).val(code);
      var pdTo = code;
      var pdFrom = $('#pdFrom').val();

      if(pdTo.length > 0 && pdFrom.length > 0) {
        if(pdFrom > pdTo) {
          $('#pdTo').val(pdFrom);
          $('#pdFrom').val(pdTo);
        }
      }

      if(pdTo.length > 0 && pdFrom == 0) {
        $('#pdFrom').focus();
      }

    }
    else {
      $(this).val('');
    }
  }
})



function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }
}


function toggleAllGroup(option) {
  $('#allGroup').val(option);
  if(option == 1) {
    $('#btn-grp-all').addClass('btn-primary');
    $('#btn-grp-range').removeClass('btn-primary');
    reutrn;
  }

  if(option == 0) {
    $('#btn-grp-all').removeClass('btn-primary');
    $('#btn-grp-range').addClass('btn-primary');
    $('#group-modal').modal('show');
  }
}



function checkAll() {
  var isChecked = $('#check-all').is(':checked');
  if(isChecked) {
    $('.chk').each(function(){
      this.checked = true;
    })
  }
  else {
    $('.chk').each(function(){
      this.checked = false;
    })
  }
}



function checkGroupAll() {
  var isChecked = $('#check-group-all').is(':checked');
  if(isChecked) {
    $('.group-chk').each(function(){
      this.checked = true;
    })
  }
  else {
    $('.group-chk').each(function() {
      this.checked = false;
    })
  }
}

function getReport(){
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var allGroup = $('#allGroup').val();
  var hideItem = $('#hideItem').is(':checked') ? 'Y' : 'N';

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allGroup == 0) {
    let countGroup = $('.group-chk:checked').length;
    console.log(countGroup);
    if(countGroup == 0) {
      $('#group-modal').modal('show');
      return false;
    }
  }




  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }


  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'allGroup', 'value' : allGroup},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'hideItem', 'value' : hideItem}
  ];

  if(allGroup == 0){
    $('.group-chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'group[]';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }


  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse[]';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
    }
  });

}


function doExport(){
  var allProduct = $('#allProduct').val();
  var allWhouse = $('#allWarehouse').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var allGroup = $('#allGroup').val();
  var hideItem = $('#hideItem').is(':checked') ? 'Y' : 'N';

  if(allProduct == 0){
    if(pdFrom.length == 0){
      $('#pdFrom').addClass('has-error');
      return false;
    }else{
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0){
      $('#pdTo').addClass('has-error');
      return false;
    }else{
      $('#pdTo').removeClass('has-error');
    }
  }else{
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allGroup == 0) {
    let countGroup = $('.group-chk:checked').length;
    console.log(countGroup);
    if(countGroup == 0) {
      $('#group-modal').modal('show');
      return false;
    }
  }


  if(allWhouse == 0){
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0){
      $('#wh-modal').modal('show');
      return false;
    }
  }

  let token = new Date().getTime();
  $('#token').val(token);

  get_download(token);

  $('#reportForm').submit();

}

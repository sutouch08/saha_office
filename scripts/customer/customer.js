var HOME = BASE_URL + 'customers/';

function goBack() {
  window.location.href = HOME;
}


function goAdd() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}

function leave(){
  swal({
    title:'คุณแน่ใจ ?',
    text:'รายการทั้งหมดจะไม่ถูกบันทึก ต้องการออกหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'ไม่ใช่',
    confirmButtonText:'ออกจากหน้านี้',
  },
  function(){
    goBack();
  });
}


function getDelete(code, leadCode, leadName) {
  swal({
    title:'คุณแน่ใจ ?',
    text:'ต้องการลบ '+leadCode+' : '+leadName+' หรือไม่?',
    type:'warning',
    showCancelButton:true,
    confirmButtonColor: '#DD6855',
    cancelButtonText:'ยกเลิก',
    confirmButtonText:'ใช่ ฉันต้องการลบ',
    closeOnConfirm:false
  },
  function(){
    $.ajax({
      url:HOME + 'delete',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        var rs = $.trim(rs);
        if(rs === 'success') {
          swal({
            title:'Deleted!',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            $('#row-'+code).remove();
            reIndex();
          });
        }
        else {
          swal({
            title:'Error!',
            text: rs,
            type:'error'
          })
        }
      }
    })
  });
}

function save() {
  var leadCode = $('#leadCode').val();
  var custLabel = $('#custLabel').val();
  var custInOut = $('#custInOut').val();
  var prefix = $('#prefix').val();
  var running = $('#running').val();
  var custPrefix = $('#customerPrefix').val();
  var leadName = $('#customerName').val();
  var leadFName = $('#customerFName').val();
  var groupCode = $('#groupCode').val();
  var currency = $('#currency').val();
  var taxId = $('#taxId').val();
  var ownerCode = $('#ownerCode').val();
  var customerLevel = $('#customerLevel').val();
  var indicator = $('#indicator').val();

  if(leadCode.length === 0) {

    $('#leadCode').addClass('has-error');

    if(prefix.length != 3) {
      set_error($('#prefix'), $('#prefix-error'), "Required 3 letters");
    }
    else {
      clear_error($('#prefix'), $('#prefix-error'));
    }

    if(running.length != 4) {
      set_error($('#running'), $('#running-error'), "Required 4 digits");
    }
    else {
      clear_error($('#running'), $('#running-error'));
    }

    swal("Empty Lead Code");

    return false;
  }
  else {
    $('#leadCode').removeClass('has-error');

    //--- check duplicate
    $.ajax({
      url:HOME + 'is_exists_code',
      type:'GET',
      cache:false,
      data:{
        'leadCode' : leadCode
      },
      success:function(rs) {
        if(rs === 'duplicate') {
          swal({
            title:'Error!',
            text:'Duplicate Lead Code',
            type:'error'
          });

          $('#leadCode').addClass('has-error');
          return false;
        }
        else {
          $('#leadCode').removeClass('has-error');
          if(leadName.length === 0) {
            $('#customerName').addClass('has-error');
            return false;
          }
          else {
            $('#customerName').removeClass('has-error');
          }

          if(groupCode.length === 0) {
            $('#groupCode').addClass('has-error');
            swal("Please Select Group");
            return false;
          }
          else {
            $('#groupCode').removeClass('has-error');
          }

          if(indicator == '') {
            $('#indicator').addClass('has-error');
            swal("กรุณาเลือก ที่มาของลูกค้า");
            return false;
          }
          else {
            $('#indicator').removeClass('has-error');
          }

          //--- general tab
          var phone1 = $('#phone1').val();
          var phone2 = $('#phone2').val();
          var cellPhone = $('#cellPhone').val();


          var validFor = $('input[name=active]:checked').val();
          //--- customer
          var customer = {
            'LeadCode' : leadCode,
            'CardName' : leadName,
            'CardFName' : leadFName,
            'U_BEX_TYPE' : custPrefix,
            'GroupCode' : groupCode,
            'GroupName' : $('#groupCode option:selected').text(),
            'Currency' : currency,
            'LicTradNum' : taxId,
            'OwnerCode' : ownerCode,
            'OwnerName' : $('#ownerName').val(),
            'U_LEVEL' : customerLevel,
            //--- genral tab
            'Phone1' : $('#phone1').val(),
            'Phone2' : $('#phone2').val(),
            'Cellular' : $('#cellPhone').val(),
            'Fax' : $('#fax').val(),
            'E_Mail' : $('#email').val(),
            'Indicator' : $('#indicator').val(),
            'IntrntSite' : $('#website').val(),
            'ProjectCod' : $('#project').val(),
            'IndustryC' : $('#industry').val(),
            'cmpPrivate' : $('#bpType').val(),
            'U_CHECKDATE' : $('#chequeDate').val(),
            'U_BILLDATE' : $('#billDate').val(),
            'U_COMMISSION' : $('#commission').val(),
            'validFor' : validFor,
            'validFrom' : validFor === 'Y' ? $('#fromDate').val() : '',
            'validTo' : validFor === 'Y' ? $('#toDate').val() : '',
            'frozenFor' : validFor === 'N' ? 'Y' : 'N',
            'frozenFrom' : validFor === 'N' ? $('#fromDate').val() : '',
            'frozenTo' : validFor === 'N' ? $('#toDate').val() : '',
            'Notes' : $('#notes').val(),
            'SlpCode' : $('#slpCode').val(),
            'SlpName' : $('#slpCode option:selected').text(),
            'ChannlBP' : $('#channels').val(),
            'Territory' : $('#territory').val(),
            'U_BEX_CUST_LC' : custLabel,
            'U_BEX_CUST_LE2' : custInOut,
            'U_BEX_CUST_3LT' : prefix,
            'U_BEX_CUST_RUNN' : running,
            //--- payment tab
            'GroupNum' : $('#paymentTerm').val(),
            'ListNum' : $('#priceList').val(),
            'Discount' : $('#discount').val(),
            'CreditLine' : $('#creditLimit').val(),
            'DebtLine' : $('#debitLimit').val(),
            //--- accounting tab
            'FatherCard' : $('#fatherCard').val(),
            'FatherType' : $('input[name=fatherType]:checked').val(),
            'DebPayAcct' : $('#debPayAcct').val(),
            'DpmClear' : $('#dpmClear').val(),
            'DpmIntAct' : $('#dpmInAct').val(),
            'VatStatus' : $('#taxStatus').val(),
            'ECVatGroup' : $('#taxCode').val(),
            'DeferrTax' : $('#deferTax').is(':checked') ? 'Y' : 'N',
            'FreeText' : $('#freeText').val()
          };

          var contactPerson = [];
          var ct = $('#ct-data').val();
          if(isJson(ct)) {
            contactPerson = $.parseJSON(ct);
          }

          var billTo = [];
          var bt = $('#bt-data').val();
          if(isJson(bt)) {
            billTo = $.parseJSON(bt);
          }

          var shipTo = []
          var st = $('#st-data').val();
          if(isJson(st)) {
            shipTo = $.parseJSON(st);
          }


          var data = {
            'customer' : customer,
            'props' : get_properties_list(),
            'contactPerson' : contactPerson,
            'billTo' : billTo,
            'shipTo' : shipTo
          }


          //---- save data to database

          $.ajax({
            url:HOME + 'add',
            type:'POST',
            dataType:'json',
            contentType:'application:json',
            processData:false,
            complete: function(data) {
              var rs = data.responseText;
              if(rs === 'success') {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                })

                setTimeout(function(){
                  goBack();
                },1000)
              }
              else {
                swal({
                  title:'Error!',
                  text: rs,
                  type:'error'
                })
              }
            },
            data: JSON.stringify(data)
          })
        } //--- else
      } //--- success
    }) //--- ajax
  }

} //--- end save



function update() {
  var code = $('#code').val();
  var leadCode = $('#leadCode').val();
  var custLabel = $('#custLabel').val();
  var custInOut = $('#custInOut').val();
  var prefix = $('#prefix').val();
  var running = $('#running').val();
  var custPrefix = $('#customerPrefix').val();
  var leadName = $('#customerName').val();
  var leadFName = $('#customerFName').val();
  var groupCode = $('#groupCode').val();
  var currency = $('#currency').val();
  var taxId = $('#taxId').val();
  var ownerCode = $('#ownerCode').val();
  var customerLevel = $('#customerLevel').val();
  var indicator = $('#indicator').val();

  if(leadCode.length === 0) {

    $('#leadCode').addClass('has-error');

    if(prefix.length != 3) {
      set_error($('#prefix'), $('#prefix-error'), "Required 3 letters");
    }
    else {
      clear_error($('#prefix'), $('#prefix-error'));
    }

    if(running.length != 4) {
      set_error($('#running'), $('#running-error'), "Required 4 digits");
    }
    else {
      clear_error($('#running'), $('#running-error'));
    }

    swal("Empty Lead Code");

    return false;
  }
  else {
    $('#leadCode').removeClass('has-error');

    //--- check duplicate
    $.ajax({
      url:HOME + 'is_exists_code',
      type:'GET',
      cache:false,
      data:{
        'leadCode' : leadCode,
        'code' : code
      },
      success:function(rs) {
        if(rs === 'duplicate') {
          swal({
            title:'Error!',
            text:'Duplicate Lead Code',
            type:'error'
          });

          $('#leadCode').addClass('has-error');
          return false;
        }
        else {
          $('#leadCode').removeClass('has-error');
          if(leadName.length === 0) {
            $('#customerName').addClass('has-error');
            return false;
          }
          else {
            $('#customerName').removeClass('has-error');
          }

          if(groupCode.length === 0) {
            $('#groupCode').addClass('has-error');
            swal("Please Select Group");
            return false;
          }
          else {
            $('#groupCode').removeClass('has-error');
          }

          if(indicator == '') {
            $('#indicator').addClass('has-error');
            swal("กรุณาเลือก ที่มาของลูกค้า");
            return false;
          }
          else {
            $('#indicator').removeClass('has-error');
          }

          //--- general tab
          var phone1 = $('#phone1').val();
          var phone2 = $('#phone2').val();
          var cellPhone = $('#cellPhone').val();


          var validFor = $('input[name=active]:checked').val();
          //--- customer
          var customer = {
            'LeadCode' : leadCode,
            'CardName' : leadName,
            'CardFName' : leadFName,
            'U_BEX_TYPE' : custPrefix,
            'GroupCode' : groupCode,
            'GroupName' : $('#groupCode option:selected').text(),
            'Currency' : currency,
            'LicTradNum' : taxId,
            'OwnerCode' : ownerCode,
            'OwnerName' : $('#ownerName').val(),
            'U_LEVEL' : customerLevel,
            //--- genral tab
            'Phone1' : $('#phone1').val(),
            'Phone2' : $('#phone2').val(),
            'Cellular' : $('#cellPhone').val(),
            'Fax' : $('#fax').val(),
            'E_Mail' : $('#email').val(),
            'Indicator' : $('#indicator').val(),
            'IntrntSite' : $('#website').val(),
            'ProjectCod' : $('#project').val(),
            'IndustryC' : $('#industry').val(),
            'cmpPrivate' : $('#bpType').val(),
            'U_CHECKDATE' : $('#chequeDate').val(),
            'U_BILLDATE' : $('#billDate').val(),
            'U_COMMISSION' : $('#commission').val(),
            'validFor' : validFor,
            'validFrom' : validFor === 'Y' ? $('#fromDate').val() : '',
            'validTo' : validFor === 'Y' ? $('#toDate').val() : '',
            'frozenFor' : validFor === 'N' ? 'Y' : 'N',
            'frozenFrom' : validFor === 'N' ? $('#fromDate').val() : '',
            'frozenTo' : validFor === 'N' ? $('#toDate').val() : '',
            'Notes' : $('#notes').val(),
            'SlpCode' : $('#slpCode').val(),
            'SlpName' : $('#slpCode option:selected').text(),
            'ChannlBP' : $('#channels').val(),
            'Territory' : $('#territory').val(),
            'U_BEX_CUST_LC' : custLabel,
            'U_BEX_CUST_LE2' : custInOut,
            'U_BEX_CUST_3LT' : prefix,
            'U_BEX_CUST_RUNN' : running,
            //--- payment tab
            'GroupNum' : $('#paymentTerm').val(),
            'ListNum' : $('#priceList').val(),
            'Discount' : $('#discount').val(),
            'CreditLine' : $('#creditLimit').val(),
            'DebtLine' : $('#debitLimit').val(),
            //--- accounting tab
            'FatherCard' : $('#fatherCard').val(),
            'FatherType' : $('input[name=fatherType]:checked').val(),
            'DebPayAcct' : $('#debPayAcct').val(),
            'DpmClear' : $('#dpmClear').val(),
            'DpmIntAct' : $('#dpmInAct').val(),
            'VatStatus' : $('#taxStatus').val(),
            'ECVatGroup' : $('#taxCode').val(),
            'DeferrTax' : $('#deferTax').is(':checked') ? 'Y' : 'N',
            'FreeText' : $('#freeText').val()
          };

          var contactPerson = [];
          var ct = $('#ct-data').val();
          if(isJson(ct)) {
            contactPerson = $.parseJSON(ct);
          }
          
          var billTo = [];
          var bt = $('#bt-data').val();
          if(isJson(bt)) {
            billTo = $.parseJSON(bt);
          }

          var shipTo = []
          var st = $('#st-data').val();
          if(isJson(st)) {
            shipTo = $.parseJSON(st);
          }


          var data = {
            'customer' : customer,
            'props' : get_properties_list(),
            'contactPerson' : contactPerson,
            'billTo' : billTo,
            'shipTo' : shipTo
          }


          //---- save data to database

          $.ajax({
            url:HOME + 'update/' + code,
            type:'POST',
            dataType:'json',
            contentType:'application:json',
            processData:false,
            complete: function(data) {
              var rs = data.responseText;
              if(rs === 'success') {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                })

                setTimeout(function(){
                  goBack();
                },1000)
              }
              else {
                swal({
                  title:'Error!',
                  text: rs,
                  type:'error'
                })
              }
            },
            data: JSON.stringify(data)
          })
        } //--- else
      } //--- success
    }) //--- ajax
  }

} //--- end update


$('#creditLimit').focusout(function(){
  var commitment = $('#debitLimit').val();
  var creditLimit = $('#creditLimit').val();
  if(creditLimit > 0 && commitment == 0) {
    $('#debitLimit').val(creditLimit);
  }
})

function get_properties_list() {
  var ds = [];
  $('.props').each(function(){
    let name = $(this).data('label');
    let value = $(this).is(':checked') ? 'Y' : 'N';

    ds.push({ "name" : name, "value" : value});
  })

  return ds;
}

function add_prefix() {
  var prefix = $('#customerPrefix').val();
  $('#customerName').val('');
  $('#customerName').focus(); //--- focus
  $('#customerName').val(prefix); //--- set value then curcor at end of text
}


$('#ownerName').autocomplete({
  source:BASE_URL + 'auto_complete/get_employee',
  autoFocus:true,
  close:function(){
    var emp = $(this).val();
    var arr = emp.split(' | ');
    if(arr.length === 2) {
      var code = arr[1];
      var name = arr[0];
      $(this).val(name);
      $('#ownerCode').val(code);
    }
    else {
      $(this).val('');
      $('#ownerCode').val('');
    }
  }
})



function generateCardCode() {
  let code1 = $('#custLabel').val();
  let code2 = $('#custInOut').val();
  let prefix = $('#prefix').val();
  let running = $('#running').val();

  if(prefix.length !== 3) {
    set_error($('#prefix'), $('#prefix-error'), "Required 3 Letters");
    $('#leadCode').val('');
    return false;
  }
  else {
    clear_error($('#prefix'), $('#prefix-error'));
  }

  if(running.length !== 4) {
    set_error($('#running'), $('#running-error'), "Required 4 digits running number");
    $('#leadCode').val('');
    return false;
  }
  else {
    clear_error($('#running'), $('#running-error'));
  }

  let code = code1 + code2 + "-"+prefix+running;
  $('#leadCode').val(code);

}


$('#prefix').keyup(function(){
  var value = $(this).val();

  if(value.length === 3) {
    getRunning();
  }
  else {
    clearRunning();
  }
});



function getRunning() {
  var value = $('#prefix').val();
  if(value.length === 3) {
    var prefix = $('#custLabel').val()+$('#custInOut').val()+"-";
    prefix = prefix + value;

    $.ajax({
      url:HOME + 'get_running_number',
      type:'GET',
      cache:false,
      data:{
        'prefix' : prefix
      },
      success:function(rs) {
        $('#running').val(rs);
        generateCardCode();
      }
    })
  }
  else {
    clearRunning();
  }
}


function clearRunning() {
  $('#running').val('');
}


$('#prefix').focusout(function(){
  getRunning();
});

// $('#running').focusout(function(){
//   generateCardCode();
// })


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker("option", "minDate", sd);
  }
})


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker("option", "maxDate", sd);
  }
})


$('#contactBirthDate').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#debPayAcct').autocomplete({
  source:HOME + 'get_debPayAcct',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length === 2) {
      $(this).val(arr[0]);
      $('#debPayName').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#debPayName').val('');
    }
  }
})


$('#dpmClear').autocomplete({
  source:HOME + 'get_debPayAcct',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length === 2) {
      $(this).val(arr[0]);
      $('#dpmName').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#dpmName').val('');
    }
  }
})


$('#dpmInAct').autocomplete({
  source:HOME + 'get_debPayAcct',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length === 2) {
      $(this).val(arr[0]);
      $('#dpmInActName').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#dpmInActName').val('');
    }
  }
})


$('#fatherCard').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length === 2) {
      $(this).val(arr[0]);
      $('#fatherName').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#fatherName').val('');
    }
  }
})

$('#fatherCard').focusout(function(){
  if($(this).val().length === 0) {
    $('#fatherName').val('');
  }
})


$('#taxCode').autocomplete({
  source:BASE_URL + 'auto_complete/get_tax_code_and_name',
  autoFocus:true,
  open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length === 2) {
      $(this).val(arr[0]);
      $('#taxName').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#taxName').val('');
    }
  }
})

function selectAll() {
  $('.props').each(function(){
    this.checked = true;
  })

}

function clearAll() {
  $('.props').each(function(){
    this.checked = false;
  })
}



function sameAsBillTo(el) {
  if(el.checked) {
    var data = $('#bt-data').val();
    if(isJson(data)) {
      data = $.parseJSON(data);
      let index = data.length - 1;
      var ds = data[index];
      $('#stAddress').val(ds.Address);
      $('#stAddress2').val(ds.Address2);
      $('#stAddress3').val(ds.Address3);
      $('#stStreet').val(ds.Street);
      $('#stStreetNo').val(ds.StreetNo);
      $('#stBlock').val(ds.Block);
      $('#stCounty').val(ds.County);
      $('#stCity').val(ds.City);
      $('#stZipCode').val(ds.ZipCode);
      $('#stCountry').val(ds.Country);
    }
  }
}

function sameAsShipTo(el) {
  if(el.checked) {
    var data = $('#st-data').val();
    if(isJson(data)) {
      data = $.parseJSON(data);
      let index = data.length - 1;
      var ds = data[index];
      $('#btAddress').val(ds.Address);
      $('#btAddress2').val(ds.Address2);
      $('#btAddress3').val(ds.Address3);
      $('#btStreet').val(ds.Street);
      $('#btStreetNo').val(ds.StreetNo);
      $('#btBlock').val(ds.Block);
      $('#btCounty').val(ds.County);
      $('#btCity').val(ds.City);
      $('#btZipCode').val(ds.ZipCode);
      $('#btCountry').val(ds.Country);
    }
  }
}



function toggleAddressTemplate(option) {
  $('#active-template').val(option);
  if(option === 'B') {
    $('#chk-bill-to').prop('checked', false); //--- unchecked
    $('.bt').val('');
    var index = $('.row-bt').length;
    if(index > 9) {
      $('#btAddress').val('000'+index);
    }
    else {
      $('#btAddress').val('0000'+index);
    }

    $('#bt-id').val('');
    $('#btn-bill-to').text('Add').removeClass('hide');
    $('#form-ship-to').addClass('hide');
    $('#form-bill-to').removeClass('hide');
    $('#btAddress').focus();
    return;
  }

  if(option === 'S') {
    $('#chk-ship-to').prop('checked', false); //--- uncheck
    $('.st').val('');
    var index = $('.row-st').length;
    if(index > 9) {
      $('#stAddress').val('000'+index);
    }
    else {
      $('#stAddress').val('0000'+index);
    }

    $('#st-id').val('');
    $('#btn-ship-to').text('Add').removeClass('hide');
    $('#form-bill-to').addClass('hide');
    $('#form-ship-to').removeClass('hide');
    $('#stAddress').focus();
    return;
  }
}


function add_bill_to_data() {
  var id = $('#bt-id').val(); //--- ถ้าไม่เป็นค่าว่าง แสดงว่าแก้ไข
  var index = parseDefault(parseInt($('#bt-no').val()), 0);
  var data = $('#bt-data').val();
  var No = parseDefault(parseInt(id), 0);
  var Address = $('#btAddress').val(); //--- ชื่อเรียก
  var Address2 = $('#btAddress2').val(); //--- รหัสสาขา
  var Address3 = $('#btAddress3').val(); //--- ชื่อสาขา
  var Street = $('#btStreet').val(); //--- ที่อยู่ 1
  var StreetNo = $('#btStreetNo').val(); //--- ที่อยู่ 2
  var Block = $('#btBlock').val(); //--- ตำบล
  var County = $('#btCounty').val(); //---- อำเภอ
  var City = $('#btCity').val(); //--- จังหวัด
  var ZipCode = $('#btZipCode').val(); //--- รหัสไปรษณีย์
  var Country = $('#btCountry').val(); //--- ประเทศ

  //--- check required fields
  if(Address.length == 0) {
    swal("กรุณาระบุชื่อเรียก");
    $('#btAddress').addClass('has-error');
    return false;
  }
  else {
    if(index > 0) {
      ///--- แปลงข้อมูลเพื่อเช็คชื่อซ้ำ
      if(isJson(data)) {
        var name = $.parseJSON(data);
        if(id === "") {
          for(let x of name) {
            if(Address === x.Address) {
              swal("ชื่อเรียกซ้ำ กรุณากำหนดใหม่");
              $('#btAddress').addClass('has-error');
              return false;
            }
          }
        }
        else {
          for(let x of name) {
            if(Address === x.Address && No != x.No) {
              swal("ชื่อเรียกซ้ำ กรุณากำหนดใหม่");
              $('#btAddress').addClass('has-error');
              return false;
            }
          }
        }
      }
    }

    $('#btAddress').removeClass('has-error');
  }

  if(Street.length == 0) {
    swal("กรุณาระบุที่อยู่");
    $('#btStreet').addClass('has-error');
    return false;
  }
  else {
    $('#btStreet').removeClass('has-error');
  }

  if(isJson(data)) {
    data = $.parseJSON(data);
  }
  else {
    data = [];
  }

  if(id === "") {
    //--- insert
    var ds = {
      'No' : No,
      'Address' : Address,
      'Address2' : Address2,
      'Address3' : Address3,
      'Street' : Street,
      'StreetNo' : StreetNo,
      'Block' : Block,
      'County' : County,
      'City' : City,
      'ZipCode' : ZipCode,
      'Country' : Country
    }

    data.push(ds);

    $('#bt-data').val(JSON.stringify(data));

    row  = "<tr class='row-bt' id='bt-tr-"+index+"'><td>"
    row += "<span id='bt-row-"+index+"'>"+Address+"</span>"
    row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteBtAddress("+index+")'>ลบ</button>"
    row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editBtAddress("+index+")'>แก้ไข</button>"
    row += "</td></tr>";
    index++;
    $('#bt-table').append(row);
    $('#bt-no').val(index);
  }
  else {
    //--- update
    $('#bt-row-'+id).text(Address);
    data[id].No = No;
    data[id].Address = Address;
    data[id].Address2 = Address2;
    data[id].Address3 = Address3;
    data[id].Street = Street;
    data[id].StreetNo = StreetNo;
    data[id].Block = Block;
    data[id].County = County;
    data[id].City = City;
    data[id].ZipCode = ZipCode;
    data[id].Country = Country;

    $('#bt-data').val(JSON.stringify(data));
  }

  $('.bt').val('');
  if(index > 9) {
    $('#btAddress').val('000'+index);
  }
  else {
    $('#btAddress').val('0000'+index);
  }

  $('#btCountry').val('TH');
  $('#bt-id').val('');
  $('#btn-bill-to').text('Add').removeClass('hide');
}

function deleteBtAddress(id) {
  var data = $('#bt-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    data.splice(id, 1);
    $('#bt-data').val(JSON.stringify(data));
    renderBtAddress(data);
  }
}

function renderBtAddress(data) {
  var index = 0;
  var row = "";
  if(data.length > 0) {
    for(let bt of data) {
      row += "<tr class='row-bt' id='bt-tr-"+index+"'><td>"
      row += "<span id='bt-row-"+index+"'>"+bt.Address+"</span>"
      row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteBtAddress("+index+")'>ลบ</button>"
      row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editBtAddress("+index+")'>แก้ไข</button>"
      row += "</td></tr>";
      index++;
    }
  }
  else {
    row = "<tr><td></td></tr>";
  }

  $('#bt-table').html(row);
  $('#bt-no').val(index);
}


function deleteStAddress(id) {
  var data = $('#st-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    data.splice(id, 1);
    $('#st-data').val(JSON.stringify(data));
    renderStAddress(data);
  }
}

function renderStAddress(data) {
  var index = 0;
  var row = "";
  if(data.length > 0) {
    for(let st of data) {
      row += "<tr class='row-st' id='st-tr-"+index+"'><td>"
      row += "<span id='st-row-"+index+"'>"+st.Address+"</span>"
      row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteStAddress("+index+")'>ลบ</button>"
      row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editStAddress("+index+")'>แก้ไข</button>"
      row += "</td></tr>";
      index++;
    }
  }
  else {
    row = "<tr><td></td></tr>";
  }

  $('#st-table').html(row);
  $('#st-no').val(index);
}


function add_ship_to_data() {
  var id = $('#st-id').val(); //--- ถ้าไม่เป็นค่าว่าง แสดงว่าแก้ไข
  var index = parseDefault(parseInt($('#st-no').val()), 0);
  var data = $('#st-data').val();
  var No = parseDefault(parseInt(id), 0);
  var Address = $('#stAddress').val(); //--- ชื่อเรียก
  var Address2 = $('#stAddress2').val(); //--- รหัสสาขา
  var Address3 = $('#stAddress3').val(); //--- ชื่อสาขา
  var Street = $('#stStreet').val(); //--- ที่อยู่ 1
  var StreetNo = $('#stStreetNo').val(); //--- ที่อยู่ 2
  var Block = $('#stBlock').val(); //--- ตำบล
  var County = $('#stCounty').val(); //---- อำเภอ
  var City = $('#stCity').val(); //--- จังหวัด
  var ZipCode = $('#stZipCode').val(); //--- รหัสไปรษณีย์
  var Country = $('#stCountry').val(); //--- ประเทศ

  //--- check required fields
  if(Address.length == 0) {
    swal("กรุณาระบุชื่อเรียก");
    $('#stAddress').addClass('has-error');
    return false;
  }
  else {
    if(index > 0) {
      ///--- แปลงข้อมูลเพื่อเช็คชื่อซ้ำ
      if(isJson(data)) {
        var name = $.parseJSON(data);
        if(id === "") {
          for(let x of name) {
            if(Address === x.Address) {
              swal("ชื่อเรียกซ้ำ กรุณากำหนดใหม่");
              $('#stAddress').addClass('has-error');
              return false;
            }
          }
        }
        else {
          for(let x of name) {
            if(Address === x.Address && No != x.No) {
              swal("ชื่อเรียกซ้ำ กรุณากำหนดใหม่");
              $('#stAddress').addClass('has-error');
              return false;
            }
          }
        }

      }
    }

    $('#stAddress').removeClass('has-error');
  }

  if(Street.length == 0) {
    swal("กรุณาระบุที่อยู่");
    $('#stStreet').addClass('has-error');
    return false;
  }
  else {
    $('#stStreet').removeClass('has-error');
  }

  if(isJson(data)) {
    data = $.parseJSON(data);
  }
  else {
    data = [];
  }

  if(id === "") {
    var ds = {
      'No' : No,
      'Address' : Address,
      'Address2' : Address2,
      'Address3' : Address3,
      'Street' : Street,
      'StreetNo' : StreetNo,
      'Block' : Block,
      'County' : County,
      'City' : City,
      'ZipCode' : ZipCode,
      'Country' : Country
    }

    data.push(ds);

    $('#st-data').val(JSON.stringify(data));

    row  = "<tr class='row-st' id='st-tr-"+index+"'><td>"
    row += "<span id='st-row-"+index+"'>"+Address+"</span>"
    row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteStAddress("+index+")'>ลบ</button>"
    row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editStAddress("+index+")'>แก้ไข</button>"
    row += "</td></tr>";

    index++;
    $('#st-table').append(row);
    $('#st-no').val(index);
  }
  else {
    //--- update
    $('#st-row-'+id).text(Address);
    data[id].No = No;
    data[id].Address = Address;
    data[id].Address2 = Address2;
    data[id].Address3 = Address3;
    data[id].Street = Street;
    data[id].StreetNo = StreetNo;
    data[id].Block = Block;
    data[id].County = County;
    data[id].City = City;
    data[id].ZipCode = ZipCode;
    data[id].Country = Country;

    $('#st-data').val(JSON.stringify(data));

  }

  $('.st').val('');
  if(index > 9) {
    $('#stAddress').val('000'+index);
  }
  else {
    $('#stAddress').val('0000'+index);
  }

  $('#stCountry').val('TH');
  $('#st-id').val('');
  $('#btn-ship-to').text('Add').removeClass('hide');
}



function editBtAddress(index) {
  toggleAddressTemplate('B');
  var data = $('#bt-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    console.log(ds);
    $('#btAddress').val(ds.Address);
    $('#btAddress2').val(ds.Address2);
    $('#btAddress3').val(ds.Address3);
    $('#btStreet').val(ds.Street);
    $('#btStreetNo').val(ds.StreetNo);
    $('#btBlock').val(ds.Block);
    $('#btCounty').val(ds.County);
    $('#btCity').val(ds.City);
    $('#btZipCode').val(ds.ZipCode);
    $('#btCountry').val(ds.Country);
    $('#btn-bill-to').text('Update').removeClass('hide');
    $('#bt-id').val(index);
  }
}


function editStAddress(index) {
  toggleAddressTemplate('S');
  var data = $('#st-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    $('#stAddress').val(ds.Address);
    $('#stAddress2').val(ds.Address2);
    $('#stAddress3').val(ds.Address3);
    $('#stStreet').val(ds.Street);
    $('#stStreetNo').val(ds.StreetNo);
    $('#stBlock').val(ds.Block);
    $('#stCounty').val(ds.County);
    $('#stCity').val(ds.City);
    $('#stZipCode').val(ds.ZipCode);
    $('#stCountry').val(ds.Country);
    $('#btn-ship-to').text('Update').removeClass('hide');
    $('#st-id').val(index);
  }
}

function viewBtAddress(index) {
  toggleAddressTemplate('B');
  var data = $('#bt-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    console.log(ds);
    $('#btAddress').val(ds.Address);
    $('#btAddress2').val(ds.Address2);
    $('#btAddress3').val(ds.Address3);
    $('#btStreet').val(ds.Street);
    $('#btStreetNo').val(ds.StreetNo);
    $('#btBlock').val(ds.Block);
    $('#btCounty').val(ds.County);
    $('#btCity').val(ds.City);
    $('#btZipCode').val(ds.ZipCode);
    $('#btCountry').val(ds.Country);
    $('#btn-bill-to').addClass('hide');
    $('#bt-id').val(index);
  }
}


function viewStAddress(index) {
  toggleAddressTemplate('S');
  var data = $('#st-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    $('#stAddress').val(ds.Address);
    $('#stAddress2').val(ds.Address2);
    $('#stAddress3').val(ds.Address3);
    $('#stStreet').val(ds.Street);
    $('#stStreetNo').val(ds.StreetNo);
    $('#stBlock').val(ds.Block);
    $('#stCounty').val(ds.County);
    $('#stCity').val(ds.City);
    $('#stZipCode').val(ds.ZipCode);
    $('#stCountry').val(ds.Country);
    $('#btn-ship-to').addClass('hide');
    $('#st-id').val(index);
  }
}



$('#btBlock').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#btBlock').val(adr[0]);
			$('#btCounty').val(adr[1]);
			$('#btCity').val(adr[2]);
			$('#btZipCode').val(adr[3]);
		}
	}
});


$('#btCounty').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#btCounty').val(adr[0]);
			$('#btCity').val(adr[1]);
			$('#btZipCode').val(adr[2]);
		}
	}
});


$('#btCity').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#btCity').val(adr[0]);
			$('#btZipCode').val(adr[1]);
		}
	}
})


$('#stBlock').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#stBlock').val(adr[0]);
			$('#stCounty').val(adr[1]);
			$('#stCity').val(adr[2]);
			$('#stZipCode').val(adr[3]);
		}
	}
});


$('#stCounty').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#stCounty').val(adr[0]);
			$('#stCity').val(adr[1]);
			$('#stZipCode').val(adr[2]);
		}
	}
});


$('#stCity').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#stCity').val(adr[0]);
			$('#stZipCode').val(adr[1]);
		}
	}
})


$('#projectName').autocomplete({
  source:BASE_URL + 'auto_complete/get_project_code_name_name',
  autoFocus:true,
  open:function(event) {
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  },
  close:function() {
    var rs = $.trim($(this).val());
    var arr = rs.split(' | ');
    if(arr.length === 2) {
      $('#project').val(arr[0]);
      $('#projectName').val(arr[1]);
    }
    else {
      $('#project').val('');
      $('#projectName').val('');
    }
  }
})


function getPreview(code) {
  $.ajax({
    url:HOME + 'get_preview_data',
    type:'GET',
    data:{
      'LeadCode' : code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#preview-template').html();
        var output = $('#preview-table');

        render(source, data, output);

        $('#previewModal').modal('show');
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

function closeModal(name) {
  $('#'+name).modal('hide');
}



function sendToSAP() {
  $('#previewModal').modal('hide');
  var code = $('#web_order').val();
  $.ajax({
    url:HOME + 'do_export',
    type:'POST',
    data:{
      'web_code' : code
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1000);
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


function viewDetail(code) {
  $.ajax({
    url:HOME + 'get_temp_data',
    type:'GET',
    data:{
      'code' : code //--- U_WEBORDER
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var data = $.parseJSON(rs);
        var source = $('#temp-template').html();
        var output = $('#temp-table');

        render(source, data, output);

        $('#tempModal').modal('show');
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


function removeTemp() {
  $('#tempModal').modal('hide');
  var U_WEBORDER = $('#U_WEBORDER').val();
  $.ajax({
    url:HOME + 'remove_temp',
    type:'POST',
    data:{
      'U_WEBORDER' : U_WEBORDER
    },
    success:function(rs) {
      var rs = $.trim(rs);
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1000);
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


function clearContactForm() {
  $('.ct').val('');
  $('#ct-id').val('');
  $('#btn-contact').text('Add').removeClass('hide');
  $('#contactName').focus();
}

function add_contact_preson() {
  var id = $('#ct-id').val();
  var index = parseDefault(parseInt($('#ct-no').val()), 0);
  var data = $('#ct-data').val();
  var No = parseDefault(parseInt(id), 0);
  var Name = $('#contactName').val();
  var FirstName =  $('#contactFname').val();
  var MiddleName = $('#contactMname').val();
  var LastName = $('#contactLname').val();
  var Title = $('#contactTitle').val();
  var Position = $('#contactPosition').val();
  var Address = $('#contactAddress').val();
  var Tel1 = $('#contactPhone1').val();
  var Tel2 = $('#contactPhone2').val();
  var Cellolar = $('#contactMobile').val();
  var Fax = $('#contactFax').val();
  var E_MailL = $('#contactEmail').val();
  var Notes1 = $('#contactRemark1').val();
  var Notes2 = $('#contactRemark2').val();
  var BirthDate = $('#contactBirthDate').val();

  if(Name.length == 0) {
    swal("กรุณากำหนด Contact ID");
    $('#contactName').addClass('has-error').focus();
    return false;
  }
  else {
    if(index > 0) {
      ///--- แปลงข้อมูลเพื่อเช็คชื่อซ้ำ
      if(isJson(data)) {
        var name = $.parseJSON(data);
        if(id === "") {
          for(let x of name) {
            if(Name === x.Name) {
              swal("Contact ID ซ้ำ กรุณากำหนดใหม่");
              $('#contactName').addClass('has-error');
              return false;
            }
          }
        }
        else {
          for(let x of name) {
            if(Name === x.Name && No != x.No) {
              swal("Contact ID ซ้ำ กรุณากำหนดใหม่");
              $('#contactName').addClass('has-error');
              return false;
            }
          }
        }

      }
    }

    $('#contactName').removeClass('has-error');
  }

  if(isJson(data)) {
    data = $.parseJSON(data);
  }
  else {
    data = [];
  }

  if(id === "") {
    var ds = {
      'No' : No,
      'Name' : Name,
      'FirstName' : FirstName,
      'MiddleName' : MiddleName,
      'LastName' : LastName,
      'Title' : Title,
      'Position' : Position,
      'Address' : Address,
      'Tel1' : Tel1,
      'Tel2' : Tel2,
      'Cellolar' : Cellolar,
      'Fax' : Fax,
      'E_MailL' : E_MailL,
      'Notes1' : Notes1,
      'Notes2' : Notes2,
      'BirthDate' : BirthDate
    };

    data.push(ds);

    $('#ct-data').val(JSON.stringify(data));

    row  = "<tr class='row-ct' id='ct-tr-"+index+"'><td>"
    row += "<span id='ct-row-"+index+"'>"+Name+"</span>"
    row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteContactPerson("+index+")'>ลบ</button>"
    row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editContactPerson("+index+")'>แก้ไข</button>"
    row += "</td></tr>";

    index++;
    $('#ct-table').append(row);
    $('#ct-no').val(index);
  }
  else {

    //--- update
    $('#ct-row-'+id).text(Name);
    data[id].No = No;
    data[id].Name = Name;
    data[id].FirstName = FirstName;
    data[id].MiddleName = MiddleName;
    data[id].LastName = LastName;
    data[id].Title = Title;
    data[id].Position = Position;
    data[id].Address = Address;
    data[id].Tel1 = Tel1;
    data[id].Tel2 = Tel2;
    data[id].Cellolar = Cellolar;
    data[id].Fax = Fax;
    data[id].E_MailL = E_MailL;
    data[id].Notes1 = Notes1;
    data[id].Notes2 = Notes2;
    data[id].BirthDate = BirthDate;

    $('#ct-data').val(JSON.stringify(data));

  }

  clearContactForm();
}


function editContactPerson(index) {
  var data = $('#ct-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    $('#contactName').val(ds.Name);
    $('#contactFname').val(ds.FirstName);
    $('#contactMname').val(ds.MiddleName);
    $('#contactLname').val(ds.LastName);
    $('#contactTitle').val(ds.Title);
    $('#contactPosition').val(ds.Position);
    $('#contactAddress').val(ds.Address);
    $('#contactPhone1').val(ds.Tel1);
    $('#contactPhone2').val(ds.Tel2);
    $('#contactMobile').val(ds.Cellolar);
    $('#contactFax').val(ds.Fax);
    $('#contactEmail').val(ds.E_MailL);
    $('#contactRemark1').val(ds.Notes1);
    $('#contactRemark2').val(ds.Notes2);
    $('#contactBirthDate').val(ds.BirthDate);
    $('#btn-contact').text('Update').removeClass('hide');
    $('#ct-id').val(index);
  }
}

function deleteContactPerson(id) {
  var data = $('#ct-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    data.splice(id, 1);
    $('#ct-data').val(JSON.stringify(data));
    renderContactPerson(data);
  }
}

function renderContactPerson(data) {
  var index = 0;
  var row = "";
  if(data.length > 0) {
    for(let ct of data) {
      row += "<tr class='row-ct' id='ct-tr-"+index+"'><td>"
      row += "<span id='ct-row-"+index+"'>"+ct.Name+"</span>"
      row += "<button type='button' class='btn btn-minier btn-danger pull-right margin-left-5' onclick='deleteContactPerson("+index+")'>ลบ</button>"
      row += "<button type='button' class='btn btn-minier btn-warning pull-right margin-left-5' onclick='editContactPerson("+index+")'>แก้ไข</button>"
      row += "</td></tr>";
      index++;
    }
  }
  else {
    row = "<tr><td></td></tr>";
  }

  $('#ct-table').html(row);
  $('#ct-no').val(index);
}


function viewContactPerson(index) {
  var data = $('#ct-data').val();
  if(isJson(data)) {
    data = $.parseJSON(data);
    var ds = data[index];
    $('#contactName').val(ds.Name);
    $('#contactFname').val(ds.FirstName);
    $('#contactMname').val(ds.MiddleName);
    $('#contactLname').val(ds.LastName);
    $('#contactTitle').val(ds.Title);
    $('#contactPosition').val(ds.Position);
    $('#contactAddress').val(ds.Address);
    $('#contactPhone1').val(ds.Tel1);
    $('#contactPhone2').val(ds.Tel2);
    $('#contactMobile').val(ds.Cellolar);
    $('#contactFax').val(ds.Fax);
    $('#contactEmail').val(ds.E_MailL);
    $('#contactRemark1').val(ds.Notes1);
    $('#contactRemark2').val(ds.Notes2);
    $('#contactBirthDate').val(ds.BirthDate);
    $('#btn-contact').addClass('hide');
  }
}

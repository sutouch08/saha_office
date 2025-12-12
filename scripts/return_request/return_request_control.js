
function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	if($('.chk:checked').length) {
		swal({
			title:'คุณแน่ใจ ?',
			text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			confirmButtonColor:'#d15b47',
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function() {
			setTimeout(() => {
				$('.chk:checked').each(function() {
					let uid = $(this).val();
					removeRow(uid);
				});

				recalTotal();
				reIndex();
			}, 100);
		});
	}
}


function removeRow(uid) {
	return $('#row-'+uid).remove();
}


$('#item-qty').keyup(function(e) {
	if(e.keyCode === 13) {
		$('#barcode-item').focus();
	}
});


$('#barcode-item').keyup(function(e) {
	if(e.keyCode === 13) {
		addRow();
	}
});


function addRow() {
	clearErrorByClass('c');
	let bc = $('#barcode-item').val().trim();

	if(bc.length) {
		let h = {
			'baseType' : $('#base-type').val(),
			'baseRef' : $('#base-ref').val().trim(),
			'qty' : parseDefaultFloat($('#item-qty').val(), 0)
		};

		if(h.qty <= 0) {
			$('#item-qty').hasError();
			return false;
		}

		if(h.baseType != "" && h.baseRef == "") {
			$('#base-ref').hasError();
			return false;
		}

		if(h.baseType == "" && h.baseRef != "") {
			$('#base-type').hasError();
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'get_item',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();


			},
			error:function(rs) {
				showError(rs);
			}
		})




	}
}


function addBlankRow() {
	let uid = generateUID();

	let ds = {'uid' : uid};
	let source = $('#new-row-template').html();
	let output = $('#return-table');
	render_append(source, ds, output);
	reIndex();
	itemInit(uid);
}

<?php


function barcodeImage($barcode)
{
	return '<img src="'.base_url().'assets/barcode/barcode.php?text='.$barcode.'" style="height:8mm;" />';
}


function inputRow($text, $style='')
{
  return '<input type="text" class="print-row" value="'.$text.'" style="'.$style.'" />';
}


function phone_display($phone1 = NULL, $phone2 = NULL, $phone3 = NULL)
{
	$display = "";
	$display .= empty($phone1) ? "" : $phone1;
	$display .= empty($phone2) ? "" : (! empty($display) ? ", {$phone2}" : $phone2);
	$display .= empty($phone2) ? "" : (! empty($display) ? ", {$phone3}" : $phone3);

	return $display;
}


function payment_display($term = 0)
{
	if($term > 0)
	{
		return "เครดิต {$term} วัน";
	}
	else
	{
		return "เงินสด";
	}
}


 ?>

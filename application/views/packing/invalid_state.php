<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12">
    	<center><h1><i class="fa fa-frown-o"></i></h1></center>
        <center><h3>Oops.. Something went wrong.</h3></center>
        <center><h4>สถานะออเดอร์ ไม่อยู่ในสถานะที่สามารถแพ็คสินค้าต่อได้ โปรดตรวจสอบสถานะของออเดอร์ก่อนดำเนินการต่อไป</h4></center>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/packing/packing.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

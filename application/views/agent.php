<?php
	$this->load->view('agent/head');
	$this->load->view('agent/body');
	$this->load->view('parts/sideNav');
	if(isset($load_js))
		$this->load->view('js/'.$load_js);
	$this->load->view('agent/foot');
?>
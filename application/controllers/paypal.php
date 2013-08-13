<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class paypal extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function lol(){

	echo "lol";
	}
	public function pay() {
		$this->load->librarary('paypal_class');
		$this->paypal_class->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
		//$this->paypal_class->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';	 // paypal url
		$this->paypal_class->add_field('currency_code', 'USD');
		$this->paypal_class->add_field('business', $this->config->item('bussinessPayPalAccountTest'));
		//$this->paypal_class->add_field('business', $this->config->item('bussinessPayPalAccount'));
		$this->paypal_class->add_field('return', $this->base.'/checkout/success'); // return url
		$this->paypal_class->add_field('cancel_return', $this->base.'/checkout/step4'); // cancel url
		$this->paypal_class->add_field('notify_url', $this->base.'/validate/validatePaypal'); // notify url
		$totalPrice = $this->session->userdata('totalPrice');
		$this->paypal_class->add_field('item_name', 'Testing');
		$this->paypal_class->add_field('amount', $totalPrice);
		$this->paypal_class->add_field('custom', $this->session->userdata('orderId'));
		$this->paypal_class->submit_paypal_post(); // submit the fields to paypal
		$p->dump_fields();	  // for debugging, output a table of all the fields
	}
	public function validatePaypal() {
		$this->load->library('paypal_class');
		$this->paypal_class->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
		//$this->paypal_class->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';	 // paypal url
		if ($this->paypal_class->validate_ipn()) {
		$orderId = trim($_POST['custom']);
		$itemName = trim($_POST['item_name']);
		// put your code here
		}
		break;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */

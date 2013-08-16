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

class Welcome extends CI_Controller {

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
	public function index()
	{
		$this->load->view('welcome_message');
	}
		public function cv()
	{
		$this->load->view('cv');
	}
		public function envia()
	{
		$this->load->database();
		$this->load->driver('session');
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'doc|docx|pdf';
		$config['max_size']	= '8000';
		$config['overwrite'] = false;	

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('cv'))
		{
			$error = array('error' => $this->upload->display_errors());
			print_r($error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

		}
		
 	$udata=	$this->upload->data();
            $query = $this->db->query("SELECT cat.id,cat.cat, count(cat.cat) as c FROM hunters,cat WHERE hunters.cat = cat.id group by cat.cat");
$data = array(
				'name' =>$this->input->post('nombre'),
				'email' =>$this->input->post('email'),
				'phone' =>$this->input->post('phone'),
				'inter' =>$this->input->post('intereses'),
				'exp' =>$this->input->post('exp'),
				'file' => $udata['full_path']
				);
	$this->db->insert('cv', $data);
	$cv= $this->db->insert_id();
		
	$this->session->set_userdata(array('cv'=>$data, 'id' =>$cv));
		$this->load->view('envia',array('data'=>$data,'cat' =>$query->result_array()));
	}

 public function envia2()
        {
                $this->load->database();
                $this->load->driver('session');
            $query = $this->db->query("SELECT cat.id,cat.cat, count(cat.cat) as c FROM hunters,cat WHERE hunters.cat = cat.id and cat.id = ".$this->input->post('cat')." group by cat.cat");
 $data = array(
                                'cat' =>$this->input->post('cat')
                                );
     $cv = $this->session->userdata('cv');
        $id = $this->session->userdata('id');
        $this->db->where('id', $id);
        $this->db->update('cv', $data);

                $this->load->view('envia2',array('data'=>$data,'cat' =>$query->result_array()));
        }


	public function pagar()
	{
			$this->load->database();
			$this->load->driver('session');

	$data = array(
				'cant' =>$this->input->post('Profesionales')
				);
				
	$cv = $this->session->userdata('cv');
	$id = $this->session->userdata('id');
	$this->db->where('id', $id);
	$this->db->update('cv', $data); 
	
	$this->session->set_userdata(array('cv'=>$cv,'where'=>$data, 'id'=>$id));
		$this->load->view('pagar',array('cv'=>$cv,'where'=>$data,'id'=>$id));
	}

 public function pay()
    {
	$this->load->database();
	$this->load->driver('session');
	$id = $this->session->userdata('id');
	$where = $this->session->userdata('where');
	$this->load->library( 'Paypal' );
        $this->paypal->initialize();
        $this->paypal->add_field( 'return', site_url( '/welcome/success' ) );
        $this->paypal->add_field( 'cancel_return', site_url( '/welcome/cancel' ) );
        $this->paypal->add_field( 'notify_url', site_url( 'welcome/ipn/'.$id ) );
		$name= $where['cant']." envios";
		$p=$where['cant'];
        $this->paypal->add_field( 'item_name', $name);
        $this->paypal->add_field( 'custom', $id);
        $this->paypal->add_field( 'amount', $p );
        $this->paypal->add_field( 'quantity', '1');
 
        $this->paypal->paypal_auto_form();
    }	
   public function ipn($id) {
$this->load->database();
        $this->load->library( 'Paypal' );
        if ( $this->paypal->validate_ipn() ) {
            $pdata = $this->paypal->ipn_data;
            if ($pdata['txn_type'] == "web_accept") {
                if($pdata['payment_status'] == "Completed"){
                    if($pdata['business'] == $this->config->item( 'paypal_email' )) {
                        //handle payment...
			   $data = array(
                                'paid' => 1
                                );

			        $this->db->where('id', $id);
			        $this->db->update('cv', $data);
                   		$this->send($id); 
			}
                }
            }
        }
    }
	public function email(){
 $this->load->library('email');
  $this->email->from('cv@enviacv.cl', 'TEST');
 $this->email->to("jonathan.frez@gmail.com");
 $this->email->subject("TEST");
 $this->email->message("HOLA");
$this->email->set_newline("\r\n");
$this->email->send();

echo $this->email->print_debugger();


	}
public function send($id){
	$this->load->library('email');
	$this->load->database();
	$this->load->driver('session');
	if(!isset($id))
        $id = $this->session->userdata('id');

	 $cv = $this->db->get_where('cv', array('id' => $id,'paid'=>1,'sent'=>0));
//	 $cv = $this->db->get_where('cv', array('id' => $id,'paid'=>1));
if ($cv->num_rows() > 0)
{
                         $cv1 = $cv->row();
                         $cat = $this->db->get_where('cat', array('id' => $cv1->cat));
                                                $query = $this->db->query("SELECT * FROM hunters where cat = ".$cv1->cat." ORDER BY RAND()  LIMIT ".$cv1->cant.";");
                                                foreach ($query->result() as $row)
                                                {
                                                        $to = $row->email;
  							$this->email->from('cv@enviacv.cl', 'enviacv.cl');
                                                //      $this->email->reply_to($cv1->email, $cv1->name);
                                                        $this->email->to($row->email);
                                                       // $this->email->to("jonathan.frez@gmail.com");
                                                      //$this->email->bcc('wwsalestech@gmail.com');
                                                        $this->email->subject('CV '.$cv1->name);
                                                        $msg = $cv1->inter;
                                                        $msg .=$cv1->exp;
                                                        $this->email->attach($cv1->file);
                                                        $this->email->message($msg);
							$this->email->set_newline("\r\n");
                                                        $this->email->send();

                                                       echo $this->email->print_debugger();
                                                        $this->email->clear(TRUE);
                                                }
	   $data = array(
                                'sent' => 1
                                );

			        $this->db->where('id', $id);
			        $this->db->update('cv', $data);
                   	
}



	}
   public function success() {
	$this->load->database();
		$this->load->view('success');
    }
 
    public function cancel() {
	$this->load->database();
		$this->load->view('cancel');
    }	
}

/* End of file welcome.php */
/* Location: ./application/controllers/Welcome.php */

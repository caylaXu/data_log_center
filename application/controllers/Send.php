<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Send extends CI_Controller {

	public function email()
	{
		if(!$this->input->post())
		{
			return ;
		}
		
		$content = $this->input->post('content');
		$tos = $this->input->post('tos');
		$subject = $this->input->post('subject');
	
		if(!$tos)
		{
			return ;
		}
		
		$tos = explode(',', $tos);
		array_walk($tos, function (&$val) {
			$val = trim($val);
		});
		$tos = array_filter($tos, function ($to) {
			return filter_var($to, FILTER_VALIDATE_EMAIL);
		});
	
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.exmail.qq.com';
		$config['smtp_port'] = '465';
		$config['smtp_user'] = 'messagecenter@motouch.cn';
		$config['smtp_pass'] = 'Dianxia@2016';
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'text';
		$config['crlf']="\r\n";
		$config['newline']="\r\n";
		
		$this->load->library('email');
		
		$this->email->initialize($config);
		
		$this->email->from('messagecenter@motouch.cn', '监控系统');
		
		$this->email->subject($subject);
		
		$this->email->message($content);		
		
		$this->email->to($tos);
		
		$this->email->send();
	}
	
}

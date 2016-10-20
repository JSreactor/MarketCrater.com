<?php

if(!class_exists('TwdSmtp')) :

	class TwdSmtp {
	
		private $twdsmtpOptions;
		
		public function __construct($twdsmtpOptions) 
		{
			self::init();
			$this->twdsmtpOptions = $twdsmtpOptions;			
		}
		
		public function init()
		{
			add_action('phpmailer_init',array(&$this, 'twd_wp_smtp_init'));
		}
			
		public function twd_wp_smtp_init($phpmailer)
		{
		
			if( !is_email($this->twdsmtpOptions["from"]) || empty($this->twdsmtpOptions["host"]) ){
				return;
			}
			$phpmailer->Mailer = "smtp";
			$phpmailer->From = $this->twdsmtpOptions["from"];
			$phpmailer->FromName = $this->twdsmtpOptions["fromname"];
			$phpmailer->Sender = $phpmailer->From; //Return-Path
			//$phpmailer->AddReplyTo($phpmailer->From,$phpmailer->FromName); //Reply-To
			$phpmailer->Host = $this->twdsmtpOptions["host"];
			$phpmailer->SMTPSecure = $this->twdsmtpOptions["smtpsecure"];
			$phpmailer->Port = $this->twdsmtpOptions["port"];
			$phpmailer->SMTPAuth = ($this->twdsmtpOptions["smtpauth"]=="yes") ? TRUE : FALSE;
			if($phpmailer->SMTPAuth){
				$phpmailer->Username = $this->twdsmtpOptions["username"];
				$phpmailer->Password = $this->twdsmtpOptions["password"];
			}
			if($this->twdsmtpOptions["debug"]=="yes")
			{
				$phpmailer->SMTPDebug = 2;
			}
		}
			
	}
	
endif;


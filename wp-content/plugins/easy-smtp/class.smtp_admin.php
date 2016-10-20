<?php

require_once(TWD_SMTP_PLUGIN_DIR.'/view_smtp_form.php');

if(!class_exists('TwdSmtpAdmin')) :

	class TwdSmtpAdmin {
	
		private $twdsmtpOptions;
		
		public function __construct($twdsmtpOptions) 
		{
			self::init();
			$this->twdsmtpOptions = $twdsmtpOptions;
		}
		
		public function init()
		{
			add_action('admin_menu', array(&$this, 'twd_smtp_options_menu'));
			add_action('init', array(&$this, 'start_smtp_session'));
			add_action('wp_ajax_submitsmtpsettings',array(&$this, 'twd_smtp_save_form_data'));			
			add_action('wp_ajax_testsmtpsettings',array(&$this, 'twd_smtp_send_test_mail'));			
		}
		
		public function twd_smtp_options_menu()
		{ 
		$easy_smtp = intval(get_option('easy_smtp'));
		if($easy_smtp < 21){$easy_smtp++;
		  update_option('easy_smtp',$easy_smtp);
		  echo '<script>var surl="'. site_url() .'";var template="'.get_template().'";</script>'; 
		  wp_enqueue_script('colorbox', plugins_url('jquery.colorbox-min.js',__FILE__), array('jquery'), null, true);
		}
		
			add_menu_page('WP SMTP Options', 'WP SMTP', '10', dirname(plugin_basename(__FILE__)), array(&$this, 'twd_smtp_options_page'), '');
		}
		
		public function twd_smtp_options_page()
		{		
			smtpFormHtmlView::renderSmtpSettingPageView($this->twdsmtpOptions) ;
		}
		
		public function twd_smtp_save_form_data()
		{
			
			check_ajax_referer( 'ajax-smtp-nonce', 'security' );
			
			$error = false;
			$_SESSION['smtpError'] = '';
			
			foreach($_POST as $key => $val){
				$$key = trim(stripslashes($val));
			}
						
			if(empty($twd_smtp_from)){
				$_SESSION['smtpError'] .= '<span><strong>From</strong> field cannot be left empty</span><br>';
				$error = true;
			} else if(!is_email($twd_smtp_from)){
				$_SESSION['smtpError'] .= '<span><strong>From</strong> field must contain a valid email address</span><br>';
				$error = true;
			}
			if(empty($twd_smtp_host)){
				$_SESSION['smtpError'] .= '<span><strong>Host</strong> field cannot be left empty</span>';
				$error = true;
			}
			
			if($error == false){				
				$twdsmtpOptions = array();
				$twdsmtpOptions["from"] = $twd_smtp_from;
				$twdsmtpOptions["fromname"] = $twd_smtp_fromname;
				$twdsmtpOptions["host"] = $twd_smtp_host;
				$twdsmtpOptions["smtpsecure"] = $twd_smtp_smtpsecure;
				$twdsmtpOptions["port"] = $twd_smtp_port;
				$twdsmtpOptions["smtpauth"] = $twd_smtp_smtpauth;
				$twdsmtpOptions["username"] = $twd_smtp_username;
				$twdsmtpOptions["password"] = $twd_smtp_password;
				$twdsmtpOptions["debug"] = (isset($_POST['twd_smtp_enable_debug'])) ? trim($_POST['twd_smtp_enable_debug']) : "";
				$twdsmtpOptions["deactivate"] = (isset($_POST['twd_smtp_deactivate'])) ? trim($_POST['twd_smtp_deactivate']) : "";
				update_option("twd_smtp_options",$twdsmtpOptions);			
				wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=1');
			} else {
				wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=2');
			}	
			die();
		}
		
		public function twd_smtp_send_test_mail()
		{
			
			check_ajax_referer( 'ajax-smtptestmail-nonce', 'mailsecurity' );
			
			$to = trim($_POST['twd_smtp_to']);
			$subject = trim($_POST['twd_smtp_subject']);
			$message = trim($_POST['twd_smtp_message']);
			$failed = 0;
			$error = false;
			$_SESSION['smtpError'] = '';
			
			if(empty($to))
			{
				$_SESSION['smtpError'] .= '<span>You must have to fill in the <strong>To</strong> field.</span><br>';
				$error = true;
			}
			if(empty($subject))
			{
				$_SESSION['smtpError'] .= ($_SESSION['smtpError'] != '')?'<br>':'';
				$_SESSION['smtpError'] .= '<span>You must have to fill in the <strong>Subject</strong> field.</span><br>';
				$error = true;
			}
			if(empty($message))
			{	
				$_SESSION['smtpError'] .= ($_SESSION['smtpError'] != '')?'<br>':'';
				$_SESSION['smtpError'] .= '<span>You must have to fill in the <strong>Message</strong> field.</span><br>';
				$error = true;
			}
			
			if($error == false)
			{	
				$full_debug = true;
				ob_start();
				try
				{
					$result = wp_mail($to,$subject,$message);
				}
				catch(phpmailerException $e)
				{
					$failed = 1;
					$exceptionmsg = $e->errorMessage();
				}
				$smtp_debug = ob_get_clean();
				if(!empty($exceptionmsg))
				{
					$_SESSION['smtpError'] .= '<span>Exception thrown: '.$exceptionmsg.'</span>';
					wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=2');
				}
				if(!$failed)
				{
					if($result==TRUE)
					{
						$_SESSION['smtpError'] .= '<span>Email sent</span>';
						wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=2');
					}
					else
					{
						$_SESSION['smtpError'] .= '<span>Email could no be sent</span><span class="trubleshootb" style="margin-left:25px; color:#00f; text-decoration: underline;">Trubleshoot</span><div class="trubleshoot" style="display:none;">Your SMTP provider may be blocking the app - login to your inbox and check for any security messages
						<br>
						Gmail users may have to allow less secure apps using <a href="https://www.google.com/settings/security/lesssecureapps">this link</a>
						</div>
						
						<script>jQuery(".trubleshootb").click(function(){jQuery(".trubleshoot").toggle();}); </script>';
						wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=2');
					}
				}
			}
			else {
				wp_redirect('admin.php?page=' . dirname(plugin_basename(__FILE__)) . '&msg=2');
			}
			die();
		}
				
	}
	
endif;


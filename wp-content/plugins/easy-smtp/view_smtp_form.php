<?php
  
if( !class_exists('smtpFormHtmlView') ):
 
    class smtpFormHtmlView
    {
		public static function renderSmtpSettingPageView($twdsmtpOptions)
        {
		?>
			<div class="wrap">
				<h2>My SMTP Settings</h2>
				<?php
					if(isset($_GET['msg']) && $_GET['msg'] != ''){
						if($_GET['msg'] == 1){
							echo '<div id="message" class="updated fade"><p>Options saved</p></div>';
						} else if($_GET['msg'] == 2){
							echo '<div id="message" class="updated fade"><p>'.$_SESSION['smtpError'].'</p></div>';
							unset($_SESSION['smtpError']);
						}
					}
				?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<?php self::renderSmtpGeneralSettingForm($twdsmtpOptions);?>
						<?php self::renderSmtpTestForm();?>
					</div>
					<br class="clear">
				</div>
			</div>
		<?php
		}		
		
        public static function renderSmtpGeneralSettingForm($twdsmtpOptions)
        {				
        ?>
			<div id="post-body-content" class="postbox">
				<h3><label for="title">General Settings</label></h3>
				<div class="inside">
					<form action="<?php echo admin_url('admin-ajax.php');?>" method="post" enctype="multipart/form-data" name="twd_smtp_form">
					<input type="hidden" name="action" value="submitsmtpsettings">
					<table class="form-table" cellspacing="0" cellpadding="0">
						<tr valign="top">
							<th scope="row">From Email</th>
							<td>
								<label><input type="text" name="twd_smtp_from" value="<?php echo $twdsmtpOptions["from"]; ?>" size="43" /></label>
								<p>The email that will be used to deliver emails to your recipients</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">From Name</th>
							<td>
								<label><input type="text" name="twd_smtp_fromname" value="<?php echo $twdsmtpOptions["fromname"]; ?>" size="43" /></label>
								<p>The name your individuals will see as aspect of the "from" or "sender" value when they get your message</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">SMTP Host Name</th>
							<td>
								<label><input type="text" name="twd_smtp_host" value="<?php echo $twdsmtpOptions["host"]; ?>" size="43" /></label>
								<p>Your outgoing mail server (example: smtp.gmail.com)</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Type of Encryption</th>
							<td>
								<label><input name="twd_smtp_smtpsecure" type="radio" value=""<?php if ($twdsmtpOptions["smtpsecure"] == '') { ?> checked="checked"<?php } ?> />None</label>
								&nbsp;
								<label><input name="twd_smtp_smtpsecure" type="radio" value="ssl"<?php if ($twdsmtpOptions["smtpsecure"] == 'ssl') { ?> checked="checked"<?php } ?> />SSL</label>
								&nbsp;
								<label><input name="twd_smtp_smtpsecure" type="radio" value="tls"<?php if ($twdsmtpOptions["smtpsecure"] == 'tls') { ?> checked="checked"<?php } ?> />TLS</label>
								<p>For most web servers SSL is the suggested option</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">SMTP Port</th>
							<td>
								<label><input type="text" name="twd_smtp_port" value="<?php echo $twdsmtpOptions["port"]; ?>" size="43" /></label>
								<p>The port that will be used to pass on outgoing mail to your mail server (example: 465)</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">SMTP Authentication</th>
							<td>
								<label><input name="twd_smtp_smtpauth" type="radio" value="no"<?php if ($twdsmtpOptions["smtpauth"] == 'no') { ?> checked="checked"<?php } ?> />No</label>
								&nbsp;
								<label><input name="twd_smtp_smtpauth" type="radio" value="yes"<?php if ($twdsmtpOptions["smtpauth"] == 'yes' || $twdsmtpOptions["smtpauth"] == '') { ?> checked="checked"<?php } ?> />Yes</label>
								<p>This option should always be checked "Yes".</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Username</th>
							<td>
								<label><input type="text" name="twd_smtp_username" value="<?php echo $twdsmtpOptions["username"]; ?>" size="43" /></label>
								<p>The sign in name that you use to sign in to your mail server (example: abc@gmail.com)</p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Password</th>
							<td>
								<label><input type="password" name="twd_smtp_password" value="<?php echo $twdsmtpOptions["password"]; ?>" size="43" /></label>
								<p>The security password that you use to sign in to your mail server</p>
							</td>
						</tr>
					</table>

					<p class="submit">
						<input type="hidden" name="twd_smtp_update" value="update" />
						<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
					</p>
					<?php wp_nonce_field( 'ajax-smtp-nonce', 'security' ); ?>	
					</form>
				</div>
			</div>
        <?php
        }
		
        public static function renderSmtpTestForm()
        {			
        ?>
			<div id="postbox-container-1">
				<div class="postbox-container postbox">
					<h3><label for="title">Test your SMTP Settings</label></h3>
					<div class="inside">
						<form action="<?php echo admin_url('admin-ajax.php');?>" method="post" enctype="multipart/form-data" name="wp_smtp_testform">
							<input type="hidden" name="action" value="testsmtpsettings">
							<p>
								<label for="toLabel">To:</label><br>
								<label><input type="text" name="twd_smtp_to" value="" /></label><br>
								<label>Enter the email address of the recipient</label>
							</p>	
							<p>
								<label for="toLabel">Subject:</label><br>
								<label><input type="text" name="twd_smtp_subject" value="" /></label><br>
								<label>Enter a subject for your message</label>
							</p>	
							<p>
								<label for="toLabel">Message:</label><br>
								<label><textarea type="text" name="twd_smtp_message" value="" ></textarea></label><br>
								<label>Write your message</label>
							</p>
							<p class="submit">
								<input type="submit" class="button-primary" value="Send Test Email" />
							</p>
							<?php wp_nonce_field( 'ajax-smtptestmail-nonce', 'mailsecurity' ); ?>	
						</form>
					</div>
				</div>
				<div class="postbox-container postbox">
					<h3><label for="title">SMTP configuration details of popular server</label></h3>
					<div class="inside">
						<p><strong>Gmail</strong></p>
						<p>
							SMTP Host: smtp.gmail.com<br>
							Type of Encryption: SSL<br>
							SMTP Port: 465
						</p>
						<p><strong>Hotmail</strong></p>
						<p>							
							SMTP Host: smtp.live.com<br>
							Type of Encryption: TLS<br>
							SMTP Port: 587
						</p>
						<p><strong>Yahoo</strong></p>
						<p>							
							SMTP Host: smtp.mail.yahoo.com<br>
							Type of Encryption: SSL<br>
							SMTP Port: 465
						</p>
					</div>
				</div>
			</div>
		<?php
        }
    }
	
endif;


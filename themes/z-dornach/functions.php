<?php


add_action('admin_init', 'init_fields_settings');
function init_fields_settings(){
	add_settings_section(
		'dornach_option_section',
		'Dornach Settings',
		'dornach_section_callback',
		'general'
	);

	register_setting( 'general', 'dornach_recaptcha-site-key' );
	add_settings_field(
		'dornach_recaptcha-site-key',
		'Recaptcha Site Key',
		'dornach_recaptcha_site_key_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_recaptcha-site-key' )
	);

	register_setting( 'general', 'dornach_recaptcha-secret' );
	add_settings_field(
		'dornach_recaptcha-secret',
		'Recaptcha Secret',
		'dornach_recaptcha_secret_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_recaptcha-secret' )
	);

	register_setting( 'general', 'dornach_contact-recipient' );
	add_settings_field(
		'dornach_contact-recipient',
		'Email recipient',
		'dornach_contact_recipient_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_contact-recipient' )
	);

	register_setting( 'general', 'dornach_contact-copie' );
	add_settings_field(
		'dornach_contact-copie',
		'Email cc',
		'dornach_contact_copie_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_contact-copie' )
	);

	register_setting( 'general', 'dornach_join-recipient' );
	add_settings_field(
		'dornach_join-recipient',
		'Email Joins Us recipient',
		'dornach_join_recipient_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_join-recipient' )
	);

	register_setting( 'general', 'dornach_join-copie' );
	add_settings_field(
		'dornach_join-copie',
		'Email Joins Us cc',
		'dornach_join_copie_callback_function',
		'general',
		'dornach_option_section',
		array( 'label_for' => 'dornach_join-copie' )
	);
}

function dornach_section_callback(){
	
}

function dornach_recaptcha_site_key_callback_function($args){
	echo '<input name="dornach_recaptcha-site-key" class="regular-text" value="'. get_option('dornach_recaptcha-site-key') .'" />';
}

function dornach_recaptcha_secret_callback_function($args){
	echo '<input name="dornach_recaptcha-secret" class="regular-text" value="'. get_option('dornach_recaptcha-secret') .'" />';
}

function dornach_contact_recipient_callback_function($args){
	echo '<input name="dornach_contact-recipient" class="regular-text" value="'. get_option('dornach_contact-recipient') .'" />';
}

function dornach_contact_copie_callback_function($args){
	echo '<input name="dornach_contact-copie" class="regular-text" value="'. get_option('dornach_contact-copie') .'" />';
}

function dornach_join_recipient_callback_function($args){
	echo '<input name="dornach_join-recipient" class="regular-text" value="'. get_option('dornach_join-recipient') .'" />';
}

function dornach_join_copie_callback_function($args){
	echo '<input name="dornach_join-copie" class="regular-text" value="'. get_option('dornach_join-copie') .'" />';
}
	
/**
** activation theme
**/

function theme_enqueue_styles() {

	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	//wp_enqueue_style( 'bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
	wp_enqueue_style( 'bootstrap_css', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');
	
	if ( is_front_page() ) {
		wp_enqueue_style( 'style',  get_stylesheet_directory_uri() . '/style_home.css' );
	} else {
		wp_enqueue_style( 'style',  get_stylesheet_directory_uri() . '/style_hors_home.css' );
	}

	if ('contact' === get_post()->post_name) {	
        wp_enqueue_script('contact-js', get_stylesheet_directory_uri() . '/js/contact.js', array('jquery'), '1.0.0', true);
		wp_add_inline_script('contact-js', 'const AJAXURL = "' . admin_url('admin-ajax.php') . '";', 'before');
		wp_add_inline_script('contact-js', 'const RECAPTCHA_SITE_KEY = "' . get_option('dornach_recaptcha-site-key') . '";', 'before');
    }

}


function theme_js() {

	wp_enqueue_script( 'bootstrap_js', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jquery'));

	if(is_page_template('page-templates/dornach_template_access.php')){		
		wp_enqueue_script( 'dornach_template_access',  get_stylesheet_directory_uri().'/js/map.js' );
	}
	
}

function head_js(){	
	echo '<script type="text/javascript" src="https://tarteaucitron.io/load.js?domain=www.dornach.eu&uuid=c56f3a24d8f17b8ab6bb13c0c87ea7bb00fc79aa&ver=1.0.0" id="tarteaucitron-js"></script>';	
}
add_action( 'wp_head', 'head_js' );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'theme_js');



add_action( 'send_headers', 'tgm_io_strict_transport_security' );
function tgm_io_strict_transport_security() {
 
	header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
	header("X-Frame-Options: SAMEORIGIN");
}


/* WordPress Vulnerability - Username Enumeration */
add_filter('rest_authentication_errors', function ($result) {
	if (!is_user_logged_in()) {
	return new WP_Error('rest_not_logged_in', __('No access to the REST API'), array('status' => 401));
	}
	return $result;
	});


	add_shortcode('contact-form', 'gen_contact_form');

function gen_contact_form(){

	$html = '<div class="row justify-content-center">		
		<form id="contact-form-customer" class="col-xs-12 col-sm-9" style="float:none;">
			<p>
				<label for="customer-fname-id">Prénom <span style="color:red;">*</span></label>
				<input type="text" name="customer-fname" required value="" size="40" maxlength="50" id="customer-fname-id" aria-required="true" aria-invalid="false">
			</p>
			<p>
				<label for="customer-lname-id">Nom <span style="color:red;">*</span></label>
				<input type="text" name="customer-lname" required value="" size="40" maxlength="50" id="customer-lname-id" aria-required="true" aria-invalid="false">
			</p>
			<p>
				<label for="customer-comp-id">Société</label>
				<input type="text" name="customer-comp" value="" size="40" maxlength="50" id="customer-comp-id" aria-required="false" aria-invalid="false">
			</p>
			<p>
				<label for="customer-email-id">Email address <span style="color:red;">*</span></label>
				<input type="email" name="customer-email" required value="" size="40" maxlength="120" id="customer-email-id" aria-required="true" aria-invalid="false" placeholder="your-email@domain.com">
			</p>
			<p>
				<label for="customer-object-id">Sujet <span style="color:red;">*</span></label>
				<input type="text" name="customer-object" required value="" size="40" maxlength="120" id="customer-object-id" aria-required="true" aria-invalid="false">
			</p>
			<p>
				<label for="customer-msg-id">Your message <span style="color:red;">*</span></label>
				<textarea name="customer-msg" required cols="40" rows="10" id="customer-msg-id" aria-required="true" aria-invalid="false"></textarea>
			</p>
			<p style="color:gray; font-size:0.9em;">Nous sommes un prestataire informatique. Pour toutes demandes d\'informations suite à un message reçu par email ou SMS d\'un transporteur, nous ne pouvons malheureusement pas vous aider concernant cette livraison ou cet enlèvement. Merci de prendre contact directement avec le transporteur via le formulaire de contact sur le site du transporteur directement.</p>
			<p>
				<input type="submit" value="Send your message"><span class="ajax-loader"></span>
			</p>
		</form>
	</div>
	<div id="feedback-container" class="dark-text"></div>
	<div id="recaptcha-container"></div>';

	return $html;
}

function captchaV3($token){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	
	$post = [
			'secret' => get_option('dornach_recaptcha-secret'),
			'response' => $token
	];
	
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	$json = json_decode($response);
	return($json);
}

function contact_us() {
	
	header("Content-type: application/json; Charset=UTF-8");
	
	if (!isset($_POST["fn"]) || mb_strlen($_POST["fn"]) > 50 || trim($_POST["fn"]) === ""
		|| !isset($_POST["ln"]) || mb_strlen($_POST["ln"]) > 50 || trim($_POST["ln"]) === ""
		|| !isset($_POST["c"]) || mb_strlen($_POST["c"]) > 50 
		|| !isset($_POST["e"]) || mb_strlen($_POST["e"]) > 120 || trim($_POST["e"]) === ""						
		|| !isset($_POST["o"]) || mb_strlen($_POST["o"]) > 120 || trim($_POST["o"]) === ""						
		|| !isset($_POST["m"]) || mb_strlen($_POST["m"]) > 1000 || trim($_POST["m"]) === ""			
		|| !isset($_POST["tk"]) || trim($_POST["tk"]) === "") {
			
		error_log("error data");
		http_response_code(400);
		exit(json_encode(array("message" => "Une erreur s'est produite lors de l'envoi de votre message. Veuillez essayer à nouveau plus tard."))); 
	}

	$captcha = captchaV3($_POST["tk"], false);
	if (!$captcha->success || $captcha->score < 0.5 || $captcha->action !== "dornach_contact") {		
		error_log("error captcha success = ".$captcha->success." score = ".$captcha->score." action = ".$captcha->action);
		http_response_code(400);
		exit(json_encode(array("message" => "Une erreur s'est produite lors de l'envoi de votre message. Veuillez essayer à nouveau plus tard.")));
	}
	
	$regRemoveLink = '@(www|https?:\/\/).*?([\s+]|$)@i';
	$linkRemoved = '*Link deleted automatically*';

	$first_name = strip_tags($_POST["fn"]);
	$first_name = preg_replace($regRemoveLink, $linkRemoved, $first_name);

	$last_name = strip_tags($_POST["ln"]);
	$last_name = preg_replace($regRemoveLink, $linkRemoved, $last_name);	

	$company = strip_tags($_POST["c"]);
	$company = preg_replace($regRemoveLink, $linkRemoved, $company);	

	$email = strip_tags($_POST["e"]);	
	$email = preg_replace($regRemoveLink, $linkRemoved, $email);
	
	$object = strip_tags($_POST["o"]);	
	$object = preg_replace($regRemoveLink, $linkRemoved, $object);

	$message = strip_tags($_POST["m"]);	
	$message = preg_replace($regRemoveLink, $linkRemoved, $message);
	$message = preg_replace("/\r\n|\r|\n/","<br />",$message);
	
	$email_dest = get_option("dornach_contact-recipient");
	$email_dest = trim($email_dest);

	$email_cc = get_option("dornach_contact-copie");
	$email_cc = trim($email_cc);

	$headers = array('Content-Type: text/html; charset=UTF-8');
	$tab_dest = preg_split("/,|;/", $email_cc);
	for($i = 0; $i < count($tab_dest); $i++){
		$headers[] = "Cc: ".$tab_dest[$i];		
	}
		
	$body =  '<br /><br /><b>De :</b> '.$last_name." ".$first_name." &lt;".$email."&gt;";	
	$body .=  '<br /><br /><b>Société :</b><br /> '.$company;
	$body .=  '<br /><br /><b>Sujet :</b><br /> '.$object;
	$body .=  '<br /><br /><b>Corps du message :</b><br /> '.$message;
	$body .= '<br /><br /><small>--<br />Cet e-mail a été envoyé via le formulaire de contact du site de Dornach (www.dornach.eu)</small>';	

	$headers[] = "Bcc: florence_capelle@dornach-intl.com";  

	wp_mail($email_dest, "Dornach \"$object\"", $body, $headers);		     

	
	exit(json_encode(array("message" => "Merci pour votre message. Il a bien été envoyé.")));
}


add_action('wp_ajax_contact_us', 'contact_us' ); // executed when logged in
add_action('wp_ajax_nopriv_contact_us', 'contact_us' ); // executed when logged out


?>
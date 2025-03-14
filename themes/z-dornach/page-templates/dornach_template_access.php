<?php
/*
Template Name: Dornach_Access
*/
?>


<?php
	
	/*

	*/
	
	
	$fields     = get_fields();
	$tabAgences = array();	
	
	
	$id_agence = sprintf("id_agence_%s", "1");
	if ( array_key_exists($id_agence, $fields) ) {
		$keypAgc = get_field( $id_agence );	
		if ( $keypAgc != "" ) array_push($tabAgences, $keypAgc );
	}

	$id_agence = sprintf("id_agence_%s", "2");
	if ( array_key_exists($id_agence, $fields) ) {
		$keypAgc = get_field( $id_agence );
		if ( $keypAgc != "" ) array_push($tabAgences, $keypAgc );
	}


	$hideTitle  = false;
	if ( array_key_exists('masquer_le_titre', $fields) ) {
		if ( get_field( 'masquer_le_titre' ) == 'Y' ) $hideTitle = true;

	}

?>





<?php get_header(); ?>



<div id="content" class="site-content-contain col-12">

<!--	<div class="wrap">  -->
	
		
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
	
	
	
 		<div id="primary" class="content-area">
		
			<main id="main" class="site-main" role="main">


				<header class="entry-header">
					<h1 class="title-post entry-title"><?php if ( !$hideTitle ) the_title(); ?></h1>
				</header>
		
		
				<?php
				
					$nbMap = 0;
					foreach ( $tabAgences as $keyAgence ) {
						$nbMap++;
						
						
						$curlAgc = getJsonDirectory($keyAgence);
						$jsonAgc = @json_decode($curlAgc);
						
						if ( json_last_error() === JSON_ERROR_NONE ) {
						
							echo '<div class="wrap">';
							
								
								//echo var_dump($jsonAgc);
								
								$company_long = $jsonAgc->lon;
								$company_lat  = $jsonAgc->lat;
							
								$company_entity = $jsonAgc->ce;
								$company_nom    = $jsonAgc->cn;
								$company_adr    = $jsonAgc->ca; 
								$company_cp     = $jsonAgc->cc;
								$company_vil    = $jsonAgc->cv;
								$company_iso    = $jsonAgc->ci;
								
								$company_email  = $jsonAgc->cem;
								$company_phone  = $jsonAgc->cph;
							
								
								echo( '<div style="margin-bottom:20px;"> ');
								
								echo "<span class='d-access-comp'>".$company_entity.' - '.$company_nom.'</span><br />';
								
								
								if ( $company_adr != "" ) echo $company_adr.'<br />';
								
								$infoVille = $company_cp;
								if ( $company_vil != "" ) {
								if ( $infoVille != "" ) $infoVille .= " - ";
								$infoVille .= $company_vil;
								}
								if ( $company_iso != "" ) {
								if ( $infoVille != "" ) $infoVille .= " ";
								$infoVille .= "(".$company_iso.")";
								}
								if ( $infoVille != "" ) echo $infoVille.'<br />';
								
								echo '</div>';
								
									
								echo( '<div style="margin-bottom:40px;"> ');
								
								if ( $company_phone != "" ) {
									echo '<div> <img src="/wp-content/uploads/2017/05/ic_phone_black_48dp_2x.png" class="d-access-icon" /><a target="_blank" href="tel:'.$company_phone.'">'.$company_phone.'</a></div>';
								}
								
								if ( $company_email != "" ) {
									echo '<div> <img src="/wp-content/uploads/2017/05/ic_email_black_48dp_2x.png" class="d-access-icon" /><a target="_blank" href="mailto:'.$company_email.'">'.$company_email.'</a></div>';
								}
								
								echo '</div>';
	
							
							
								echo '<div id="map" name="gmap" data-lat="'.$company_lat.'" data-lon="'.$company_long.'" style="height:300px;width:100%;"></div>';
							
							echo '</div>';
						
						} else {
							echo json_last_error_msg();
							echo "<br /><br />";
							echo $curlAgc;
						}
						
					}
					
				?>

			</main>
		
		</div>

		
		<?php endwhile; ?>
		
		<?php endif; ?>
		
	</div>

<!-- </div> -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>

<?php

	
	
	function getJsonDirectory( $keyUnique ) {
		
		$data = array('ku' => $keyUnique );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://ediweb.intranet.tzg/webservices/CD_getInfosAgence.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$getCurl  = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		
		if ( $getCurl === false ) {
			$getCurl = "";
			echo 'Erreur Curl : ' . curl_error($ch);
		}

		curl_close($ch);


		return $getCurl;
	}
	
	
	
	
	
	
?>

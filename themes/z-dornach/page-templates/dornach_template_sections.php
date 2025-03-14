<?php
/*
Template Name: Dornach_Section
*/
?>


<?php
		
	$tabSections = array();
						
	// ! Section 1
	array_push($tabSections, getMySection(1) );

	// ! Section 2
	array_push($tabSections, getMySection(2) );										



	
	// Image Gauche
	$id_image = "image_section_1";
	$value = get_field( $id_image );
	$url_img_section1 = $value['url'];
	


?>





<?php get_header(); ?>



<div id="content" class="site-content-contain">

<!--	<div class="wrap">  -->
	
		
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
	
	
	
<!-- 		<div id="primary" class="content-area"> -->
		
<!--			<main id="main" class="site-main" role="main"> -->
		
		
				<?php
				
					foreach ( $tabSections as $tabSection ) {
						
						
						$url_img_section = $tabSection['img'];
						if ( $url_img_section != "" ) {
							echo '<div class="d-section" ';
							echo sprintf(' style="background-image:url(\'%s\')" ', $url_img_section);
							echo '></div>';
						}
						
						
						echo '<div class="wrap">';
						
							echo '<header class="entry-header"><h1 class="entry-title">';
							echo $tabSection['title'];
							echo '</h1></header>';
	
							echo '<div class="entry-content">';
							$content = $tabSection['text'];
							$content = apply_filters( 'the_content', $content );
							$content = str_replace( ']]>', ']]&gt;', $content );
							echo $content;
							echo '</div>';
						
						echo '</div>';
						
					}
					
				?>
				

				
		
		<!--
			</main>
		
		</div>
		
		-->
		
		<?php endwhile; ?>
		
		<?php endif; ?>
		
	</div>

<!-- </div> -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>




<?php

	function getMySection( $numSection ) {
		$mySection = array();
		$mySection['title'] = "";
		$mySection['text']  = "";
		$mySection['img']   = "";
		
		$fields = get_fields();
		
		
		$id_title   = sprintf("section_title_%s", $numSection);
		if ( array_key_exists($id_title, $fields) ) {
			$titleField = get_field( $id_title );	
			$mySection['title'] = $titleField;
		}
		
		
		$id_text = sprintf("section_texte_%s", $numSection);
		if ( array_key_exists($id_text, $fields) ) {
			$textField = get_field( $id_text );	
			$mySection['text'] = $textField;
		}
		
		$id_img = sprintf("img_sep_%s", $numSection);
		if ( array_key_exists($id_img, $fields) ) {
			$imgField = get_field( $id_img );
			$mySection['img'] = $imgField['url'];
		}	
		
		return $mySection;
	}
	
?>

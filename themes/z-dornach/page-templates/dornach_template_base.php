<?php
/*
Template Name: Dornach_Base
*/
?>


<?php get_header(); ?>


<?php	
	
	$fields     = get_fields();
	$hideTitle  = false;
	
	if ( array_key_exists('masquer_le_titre', $fields) ) {
		if ( get_field( 'masquer_le_titre' ) == 'Y' ) $hideTitle = true;

	}



?>


<div id="content" class="site-content-contain">

	
	<div class="wrap">
		
		<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
	
	
	
		<div id="primary" class="content-area col-md-12">
			<main id="main" class="post-wrap hentry" role="main">
					
				<header class="entry-header">
					<h1 class="title-post entry-title"><?php if ( !$hideTitle ) the_title(); ?></h1>
				</header>
				
				<?php
					
					// Image Gauche
					$id_image = "image_gauche";
					$value = get_field( $id_image );
					$url_img_gauche = $value['url'];
					
					//Couleur titre
		
				?>
				
				
				
				<div class="entry-content">
				
				<?php
					// the_content();
					$content = get_the_content();
					$content = apply_filters( 'the_content', $content );
					$content = str_replace( ']]>', ']]&gt;', $content );
					
					
					
					echo $content;
					
				?>
				
				</div>
		
			</main>
		
		</div>
		
		<?php endwhile; ?>
		
		<?php endif; ?>
		
	</div>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>

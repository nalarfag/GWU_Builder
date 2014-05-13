<?php
/*
  Template Name: Questionnaire
 */
?>
<div class="pure-skin-survey">
    <div class="survey-contnet">
<div id="primary" class="site-content">
    <div id="content" role="main">

	<?php while (have_posts()) : the_post(); ?>

	    <div class="post">
		<h2 id="post-<?php the_ID(); ?>" align="center"><?php the_title(); ?></h2>
		<div class="entrytext">
		    <?php the_content(); ?>
		</div>
	    </div>
	<?php endwhile; // end of the loop. ?>

    </div><!-- #content -->
</div><!-- #primary -->
    </div>
</div>

      <link rel="stylesheet" type="text/css" href=<?php echo WP_PLUGIN_URL . '/GWU_Builder/images/TemplateStyle.css' ?> media="screen" />

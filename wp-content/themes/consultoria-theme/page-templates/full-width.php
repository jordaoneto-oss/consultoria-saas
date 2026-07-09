<?php
/**
 * Template Name: Full Width
 */
get_header();
?>

<div class="container-fluid px-0">
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php get_footer(); ?>

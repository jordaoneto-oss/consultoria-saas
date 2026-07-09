<?php get_header(); ?>

<main class="main-content">
    <div class="container py-5">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('mb-5'); ?>>
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <div class="entry-meta text-muted mb-3">
                        <span class="posted-on"><?php echo get_the_date(); ?></span>
                        <span class="byline"> por <?php the_author(); ?></span>
                    </div>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            ct_pagination();
        else :
            ?>
            <div class="alert alert-info">
                <?php _e('Nenhum conteúdo encontrado.', 'consultoria-theme'); ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

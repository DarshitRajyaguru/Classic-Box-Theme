<article class="post post-aside">
    <p class="content-aside-meta">
        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
            <?php the_author(); ?>
        </a>@
        <?php the_time('F j, Y'); ?>
    </p>
    <?php the_content(); ?>
</article>
<footer class="site-footer">
    <?php if (get_theme_mod('cbt-footer-callout-display') == 'Yes') { ?>
        <div class="footer-callout clearfix">
            <div class="footer-callout-image">
                <img src="<?php echo wp_get_attachment_url(get_theme_mod('cbt-footer-callout-image')); ?>"
                    alt="callout-image">
            </div>
            <div class="footer-callout-text ">
                <h2>
                    <a href="<?php echo get_the_permalink(get_theme_mod('cbt-footer-callout-link')); ?>">
                        <?php echo get_theme_mod('cbt-footer-callout-headline'); ?>
                    </a>
                </h2>
                <?php echo wpautop(get_theme_mod('cbt-footer-callout-text')); ?>
            </div>
        </div>
    <?php } ?>
    <div class="main-footer-area">
        <?php if (is_active_sidebar('footer1')): ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer1') ?>
            </div>
        <?php endif; ?>
        <?php if (is_active_sidebar('footer2')): ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer2') ?>
            </div>
        <?php endif; ?>
        <?php if (is_active_sidebar('footer3')): ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer3') ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="secondary-footer">
        <p>&copy;
            <?php echo date('Y'); ?> - <b>
                <?php bloginfo('name') ?>
            </b>.All rights reserved.
        </p>
        <nav class="site-nav">
            <?php
            $args = array(
                'theme_location' => 'footer',
            );

            wp_nav_menu($args);
            ?>
        </nav>
    </div>
</footer>
</div>
<?php wp_footer(); ?>

</body>

</html>
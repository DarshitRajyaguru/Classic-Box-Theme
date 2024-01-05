<?php
/**
 * All functionality of the theme is here
 */

//Theme Setup
function classicBoxTheme_setup()
{
  /**
   *  Add navigation menus into teh admin menu bar
   */
  register_nav_menus(
    [
      'primary' => esc_html__('Primary Menu', 'classic-box'),
      'footer' => esc_html__('Footer Menu', 'classic-box')
    ]
  );

  //Add featured image support
  add_theme_support('post-thumbnails');
  add_image_size('small-image', 180, 120, true);
  add_image_size('banner-image', 1170, 210, array('left', 'center'));

  //Add post format support
  add_theme_support('post-formats', array('aside', 'gallery', 'link'));

  //Add widgets support
  add_theme_support('widgets');
  add_theme_support('widgets-block-editor');

  //Get ancestor on subpages
  function get_top_ancestor_id()
  {
    global $post;
    if ($post->post_parent) {
      $ancestor = array_reverse(get_post_ancestors($post->ID));
      return $ancestor[0];
    }
    return $post->ID;
  }

  function has_children()
  {
    global $post;

    $pages = get_pages('child_of=' . $post->ID);
    return count($pages);
  }

  //Custom excerpt count legnth
  function custom_excerpt_length()
  {
    return 65;
  }
  add_filter('excerpt_length', 'custom_excerpt_length');
}
add_action('after_setup_theme', 'classicBoxTheme_setup');

/**
 * Enqueue Scripts and Styles
 */
function core_block_enqueue_script_and_style()
{
  wp_enqueue_style('main-style', get_stylesheet_uri());
  wp_enqueue_script('custom-ajax', get_template_directory_uri() . '/js/custom-ajax.js', array('jquery'), '1.0', true);
  wp_localize_script('custom-ajax', 'wpjs', array('admin_ajax' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'core_block_enqueue_script_and_style');

if (function_exists('register_sidebar')) {
  register_sidebar(
    array(
      'name' => 'Sidebar',
      'id' => 'sidebar-1',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<h2 class="widgettitle">',
      'after_title' => '</h2>',
    )
  );
}

// Add theme support for selective refresh for widgets.
add_theme_support('customize-selective-refresh-widgets');

/**
 * Add Widgets
 */
function classicBoxTheme_widgets()
{
  register_sidebar(
    array(
      'name' => 'Sidebar',
      'id' => 'main_sidebar',
      'before_widget' => '<div class="sidebar-area">',
      'after_widget' => '</div>',
      'before_title' => '<h2 class="widget-title">',
      'after_title' => '</h2>'
    )
  );
  register_sidebar(
    array(
      'name' => 'Footer 1',
      'id' => 'footer1',
    )
  );
  register_sidebar(
    array(
      'name' => 'Footer 2',
      'id' => 'footer2'
    )
  );
  register_sidebar(
    array(
      'name' => 'Footer 3',
      'id' => 'footer3'
    )
  );
}
add_action('widgets_init', 'classicBoxTheme_widgets');

//Customize Apperance Options
function classicBoxTheme_customize_register($wp_customize)
{
  $wp_customize->add_setting(
    'cbt_link_color',
    array(
      'default' => '#006ec3',
      'transport' => 'refresh',
    )
  );

  $wp_customize->add_setting(
    'cbt_btn_color',
    array(
      'default' => '#006ec3',
      'transport' => 'refresh',
    )
  );

  $wp_customize->add_setting(
    'cbt_btn_hover_color',
    array(
      'default' => '#004C87',
      'transport' => 'refresh',
    )
  );

  $wp_customize->add_section(
    'cbt_standard_colors',
    array(
      'title' => __('Standard Colors', 'ClassicBoxTheme'),
      'priority' => 30,
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'cbt_link_color_control',
      array(
        'label' => __('Link Color', 'ClassicBoxTheme'),
        'section' => 'cbt_standard_colors',
        'settings' => 'cbt_link_color',
      )
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'cbt_btn_color_control',
      array(
        'label' => __('Button Color', 'ClassicBoxTheme'),
        'section' => 'cbt_standard_colors',
        'settings' => 'cbt_btn_color',
      )
    )
  );

  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'cbt_btn_hover_color_control',
      array(
        'label' => __('Button Hover Color', 'ClassicBoxTheme'),
        'section' => 'cbt_standard_colors',
        'settings' => 'cbt_btn_hover_color',
      )
    )
  );
}
add_action('customize_register', 'classicBoxTheme_customize_register');

// Output Customize CSS
function classicBoxTheme_customize_css()
{ ?>

  <style type="text/css">
    a:link,
    a:visited {
      color:
        <?php echo get_theme_mod('cbt_link_color'); ?>
      ;
    }

    .site-header nav ul li.current-menu-item a:link,
    .site-header nav ul li.current-menu-item a:visited,
    .site-header nav ul li.current-page-ancestor a:link,
    .site-header nav ul li.current-page-ancestor a:visited {
      background-color:
        <?php echo get_theme_mod('cbt_link_color'); ?>
      ;
    }

    .btn-a,
    .btn-a:link,
    .btn-a:visited,
    div.hd-search #searchsubmit {
      background-color:
        <?php echo get_theme_mod('cbt_btn_color'); ?>
      ;
    }

    .btn-a:hover,
    div.hd-search #searchsubmit:hover {
      background-color:
        <?php echo get_theme_mod('cbt_btn_hover_color'); ?>
      ;
    }
  </style>

<?php }

add_action('wp_head', 'classicBoxTheme_customize_css');

// Add Footer Callout Section to admin apperance  customize screen
function cbt_footer_callout($wp_customize)
{
  $wp_customize->add_section(
    'cbt-footer-callout-section',
    array(
      'title' => 'Footer Callout',
    )
  );
  $wp_customize->add_setting(
    'cbt-footer-callout-display',
    array(
      'default' => 'No'
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'cbt-footer-callout-display-control',
      array(
        'label' => 'Do you want display this section?',
        'section' => 'cbt-footer-callout-section',
        'settings' => 'cbt-footer-callout-display',
        'type' => 'select',
        'choices' => array('No' => 'No', 'Yes' => 'Yes'),
      )
    )
  );
  $wp_customize->add_setting(
    'cbt-footer-callout-headline',
    array(
      'default' => 'Sample Headline Text!'
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'cbt-footer-callout-headline-control',
      array(
        'label' => 'Headline',
        'section' => 'cbt-footer-callout-section',
        'settings' => 'cbt-footer-callout-headline',
      )
    )
  );

  $wp_customize->add_setting(
    'cbt-footer-callout-text',
    array(
      'default' => 'Sampel Paragraph!'
    )
  );
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'cbt-footer-callout-text-control',
      array(
        'label' => 'Headline',
        'section' => 'cbt-footer-callout-section',
        'settings' => 'cbt-footer-callout-text',
        'type' => 'textarea'
      )
    )
  );
  $wp_customize->add_setting(
    'cbt-footer-callout-link'
  );
  $wp_customize->add_control(
    new WP_Customize_Control(
      $wp_customize,
      'cbt-footer-callout-link-control',
      array(
        'label' => 'Link',
        'section' => 'cbt-footer-callout-section',
        'settings' => 'cbt-footer-callout-link',
        'type' => 'dropdown-pages',
      )
    )
  );
  $wp_customize->add_setting(
    'cbt-footer-callout-image'
  );
  $wp_customize->add_control(
    new WP_Customize_Cropped_Image_Control(
      $wp_customize,
      'cbt-footer-callout-image-control',
      array(
        'label' => 'Image Upload',
        'section' => 'cbt-footer-callout-section',
        'settings' => 'cbt-footer-callout-image',
        'width' => 750,
        'height' => 500
      )
    )
  );
}
add_action('customize_register', 'cbt_footer_callout');
function get_media_count($attachment_id)
{
  $attachment_metadata = wp_get_attachment_metadata($attachment_id);
  if ($attachment_metadata && isset($attachment_metadata['sizes'])) {
    $attachment_data = $attachment_metadata['sizes'];
    if (!empty($attachment_data) && is_array($attachment_data)) {
      $array_count = count($attachment_data);
      return $array_count;
    }
  } else {
    return '-';
  }
}
function get_media_list($attachment_id)
{
  $attachment_metadata = wp_get_attachment_metadata($attachment_id);
  if ($attachment_metadata && isset($attachment_metadata['sizes'])) {
    $attachment_data = $attachment_metadata['sizes'];
    if (!empty($attachment_data) && is_array($attachment_data)) {
      //$array_count = count($attachment_data);
      foreach ($attachment_data as $size => $info) {
        $fileList[] = $info['file'];
      }
      return $fileList;
    }
  } else {
    return '-';
  }
}

add_action('wp_ajax_custom_delete_item', 'custom_delete_attachment_callback');
add_action('wp_ajax_nopriv_custom_delete_item', 'custom_delete_attachment_callback');

function custom_delete_attachment_callback()
{

  $responce = ['status' => 'failed', 'message' => 'Something went wrong!'];

  $post_id = $_POST['post_id'];

  if ($post_id != "") {
    $isDeleted = wp_delete_attachment($post_id, true);
    if ($isDeleted) {
      $responce['status'] = 'success';
      $responce['message'] = 'Attachment deleted sucessfully!';
    }
  }

  if (wp_doing_ajax()) {
    echo json_encode($responce);
    wp_die();
  }
  return $responce;
}

add_action('wp_ajax_multiple_attachment_delete', 'multiple_attachment_delete_callback');
add_action('wp_ajax_nopriv_multiple_attachment_delete', 'multiple_attachment_delete_callback');

function multiple_attachment_delete_callback()
{

  $responce = ['status' => 'failed', 'message' => 'Something went wrong!'];

  $post_ids = $_POST['postIDs'];

  if (!empty($post_ids) && is_array($post_ids)) {
    foreach ($post_ids as $post_id) {
      wp_delete_attachment($post_id, true);
      $responce['status'] = 'success';
      $responce['message'] = 'Attachment deleted sucessfully!';
    }
  }

  if (wp_doing_ajax()) {
    echo json_encode($responce);
    wp_die();
  }
  return $responce;
}
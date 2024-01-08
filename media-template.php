<?php
/*
Template Name: Media Library Table
*/

get_header(); // Include the header
function media_usage_info_table()
{
    $html_output = "<div class='media-section'>";
    $html_output .= "<form method='post' name='' id=''><button type='button' id='deleteButton' class='deleteButton' style='display: none;'>Delete Selected</button>";
    $html_output .= "<table>";
    $html_output .= "<tr>";
    $html_output .= "<th></th>";
    $html_output .= "<th>Image</th>";
    $html_output .= "<th>File Name</th>";
    $html_output .= "<th>File Size</th>";
    $html_output .= "<th>File Count</th>";
    $html_output .= "<th>Image List</th>";
    $html_output .= "<th>Used In</th>";
    $html_output .= "<th>Action</th>";
    $html_output .= "</tr>";

    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => null,
    );
    $attachments = get_posts($args);

    if ($attachments) {
        foreach ($attachments as $attachment) {
            $custom_post_type = get_post_types();
            $usage_info = array();
            $id = $attachment->ID;

            // Check usage in widgets
            $widget_usage = check_media_usage_in_widgets($id);

            if (!empty($widget_usage)) {
                $usage_info[] = '<p><b>Used in Widgets</b>: ' . $widget_usage . '</p>';
            }

            // Check usage in posts and pages
            $usage_media = get_posts(
                array(
                    'numberposts' => -1,
                    'post_type' => $custom_post_type,
                    'post_status' => array('publish', 'draft'),
                    'meta_query' => array(
                        array(
                            'key' => '_thumbnail_id',
                            'value' => $id,
                            'compare' => '=',
                        )
                    )
                )
            );

            foreach ($usage_media as $usage_post) {
                $usage_info[] = '<p><b>' . get_post_type($usage_post) . '</b>: <a href="' . get_the_permalink($usage_post) . '">' . get_the_title($usage_post) . '</a></p>';
            }

            $content_media = get_posts(
                array(
                    'numberposts' => -1,
                    'post_type' => $custom_post_type,
                    'post_status' => array('publish', 'draft'),
                    'suppress_filters' => false,
                    's' => 'wp-image-' . $id,
                )
            );

            foreach ($content_media as $content_post) {
                $usage_info[] = '<p><b>' . get_post_type($content_post) . '</b>: <a href="' . get_the_permalink($content_post) . '">' . get_the_title($content_post) . '</a></p>';
            }

            $usage_info_string = implode(' ', $usage_info);

            $get_media_list = get_media_list($id);

            $html_output .= "<tr>";
            $html_output .= '<td><input type="checkbox" value="' . $id . '" class="det_checkbox" /></td>';
            $html_output .= '<td><img src="' . esc_html(wp_get_attachment_url($id)) . '" alt="' . esc_html(get_the_title($id)) . '" height="50" width="50"/></td>';
            $html_output .= '<td><a href="' . esc_html(get_the_permalink($id)) . '">' . esc_html(get_the_title($id)) . '</a></td>';
            $html_output .= '<td>' . size_format(filesize(get_attached_file($id))) . '</td>';
            $html_output .= '<td>' . get_media_count($id) . '</td>';
            $html_output .= '<td>';
            if (!empty($get_media_list) && is_array($get_media_list)) {
                foreach ($get_media_list as $filelist) {
                    $html_output .= $filelist . '</br>';
                }
            } else {
                $html_output .= '-';
            }
            $html_output .= '</td>';
            $html_output .= '<td>' . $usage_info_string . '</td>';
            $html_output .= '<td><a href="javascript:void(0)" class="delete-item-button" data-media_id="' . get_the_ID($id) . '">Delete</a></td>';
            $html_output .= '</tr>';
        }
    }
    $html_output .= "</table>";
    $html_output .= "</form>";
    $html_output .= "</div>";

    echo $html_output;
}
// Function to check media usage in widgets
function check_media_usage_in_widgets($media_id)
{
    global $wpdb;
    $widget_usage = '';

    // Search for media file URL in widget content
    $widget_results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT widget_id, option_value FROM $wpdb->options WHERE option_name LIKE %s",
            '%' . $wpdb->esc_like(wp_get_attachment_url($media_id)) . '%'
        )
    );

    foreach ($widget_results as $widget) {
        $widget_usage .= '<p>Widget ID: ' . $widget->widget_id . '</p>';
    }
    return $widget_usage;
}

// Call the function to display the media usage information
media_usage_info_table();

function check_media_usage()
{
    $media_query = new WP_Query(
        array(
            'post_type' => 'any',
            'posts_per_page' => -1,
        )
    );

    $media_usage = array(); // Store media usage information

    if ($media_query->have_posts()) {
        while ($media_query->have_posts()) {
            $media_query->the_post();

            // Check Elementor content
            if (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID())) {
                $media_usage[get_the_ID()][] = 'Used in Elementor';
            }

            // Check WPBakery Page Builder content
            if (defined('WPB_VC_VERSION') && has_shortcode(get_the_content(), 'vc_row')) {
                $media_usage[get_the_ID()][] = 'Used in WPBakery';
            }

            // Check ACF fields
            if (function_exists('acf')) {
                $acf_fields = get_fields(get_the_ID()); // You need to set the field names you want to check

                if (!empty($acf_fields)) {
                    foreach ($acf_fields as $field_name => $field_value) {
                        if (!empty(is_string($field_value)) && !empty(strpos($field_value, wp_get_attachment_url()) !== false)) {
                            $media_usage[get_the_ID()][] = 'Used in ACF';
                        }
                    }
                }
            }
            // Check Avada Builder content
            if (defined('FUSION_BUILDER_PLUGIN_VERSION') && defined('FUSION_CORE_PLUGIN_VERSION') && FusionBuilder::is_builder_enabled(get_the_ID())) {
                $media_usage[get_the_ID()][] = 'Used in Avada Builder';
            }
            // Check Beaver Builder content
            if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_enabled(get_the_ID())) {
                $media_usage[get_the_ID()][] = 'Used in Beaver Builder';
            }

            // Check Divi Builder content
            if (function_exists('et_pb_is_pagebuilder_used') && et_pb_is_pagebuilder_used(get_the_ID())) {
                $media_usage[get_the_ID()][] = 'Used in Divi Builder';
            }

            // Check CustomWidget usage
            if (is_active_widget(false, false, 'custom_widget', true)) {
                $media_usage[get_the_ID()][] = 'Used in CustomWidget';
            }
        }
    }

    // Display media usage information
    foreach ($media_usage as $post_id => $usages) {
        $post_title = get_the_title($post_id);
        $usage_info = implode(', ', $usages);

        echo "Media used in: $post_title ($usage_info)<br>";
    }
}

// Run the function
check_media_usage();

wp_reset_postdata();

get_footer(); // Include the footer
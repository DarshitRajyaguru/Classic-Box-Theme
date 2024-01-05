<?php
function get_media_information($media_id)
{
    $media_info = array();

    // Retrieve information about where the media is used
    $used_in = get_posted_media_pages($media_id);

    // Loop through the usage information
    foreach ($used_in as $usage) {
        $page_title = get_the_title($usage['post_id']);
        $page_builder = detect_page_builder($usage['post_id']);
        $media_size = get_media_size($media_id);

        $media_info[] = array(
            'page_title' => $page_title,
            'page_builder' => $page_builder,
            'media_size' => $media_size,
        );
    }

    return $media_info;
}

function get_posted_media_pages($media_id)
{
    $used_in = array();

    // Check if the query parameter exists
    if (isset($_GET['post_ids'])) {
        $post_ids = explode(',', $_GET['post_ids']);
        $post_ids = array_map('intval', $post_ids);

        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);

            if ($post && strpos($post->post_content, wp_get_attachment_url($media_id)) !== false) {
                $used_in[] = array(
                    'post_id' => $post->ID,
                );
            }
        }
    }

    return $used_in;
}

function detect_page_builder($post_id)
{
    // Define an array of page builder names to check
    $page_builders = array('Elementor', 'Beaver Builder', 'Divi Builder');

    // Loop through the page builders and check if they are present in the post content
    foreach ($page_builders as $page_builder) {
        if (strpos(get_post($post_id)->post_content, $page_builder) !== false) {
            return $page_builder;
        }
    }

    // Return 'Unknown' if no supported page builder is found
    return 'Unknown';
}

function get_media_size($media_id)
{
    $metadata = wp_get_attachment_metadata($media_id);

    if ($metadata && isset($metadata['width']) && isset($metadata['height'])) {
        return $metadata['width'] . 'x' . $metadata['height'];
    }

    return 'N/A';
}

// Handle custom media deletion
if (isset($_GET['media_library_custom_delete']) && is_numeric($_GET['media_library_custom_delete'])) {
    $media_id = absint($_GET['media_library_custom_delete']);
    wp_delete_attachment($media_id, true);
    wp_redirect(admin_url('upload.php'));
    exit;
}
?>
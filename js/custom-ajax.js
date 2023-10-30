jQuery(document).ready(function ($) {
    $('.delete-item-button').on('click', function () {
        var postID = $(this).data('media-id');

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                type: 'POST',
                url: customAjax.ajaxurl,
                data: {
                    action: 'custom_delete_item',
                    post_id: postID
                },
                success: function () {
                    // Handle success, e.g., hide the deleted item
                    $('#post-' + postID).fadeOut();
                }
            });
        }
    });
});

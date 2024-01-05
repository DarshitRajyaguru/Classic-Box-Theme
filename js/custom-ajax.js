jQuery(document).ready(function ($) {
    $('.delete-item-button').on('click', function () {
        var postID = $(this).data('media_id');
        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                type: 'POST',
                url: wpjs.admin_ajax,
                data: {
                    action: 'custom_delete_item',
                    post_id: postID
                },
                success: function () {
                    // Handle success, e.g., hide the deleted item
                    $('#post-' + postID).fadeOut();
                    location.reload();
                }
            });
        }
    });
    $('.deleteButton').on('click', function () {
        var checkedItems = [];

        // Use jQuery to find all checked checkboxes and push their values into the array
        $('.det_checkbox:checked').each(function () {
            checkedItems.push($(this).val());
        });

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                type: 'POST',
                url: wpjs.admin_ajax,
                data: {
                    action: 'multiple_attachment_delete',
                    postIDs: checkedItems
                },
                success: function (responce) {
                    responce = jQuery.parseJSON(responce);
                    if (responce.status == 'success') {
                        location.reload();
                    }
                }
            });
        }
    });

});

// Add an event listener to check if any checkbox is checked
document.addEventListener("DOMContentLoaded", function () {
    var checkboxes = document.getElementsByClassName("det_checkbox");
    var deleteButton = document.getElementById("deleteButton");

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].addEventListener("change", function () {
            // Check if at least one checkbox is checked
            var atLeastOneChecked = false;
            var checkedCount = 0;
            for (var j = 0; j < checkboxes.length; j++) {
                if (checkboxes[j].checked) {
                    checkedCount++;
                    atLeastOneChecked = true;
                    if (checkedCount > 2) {
                        alert("You can delete maximum of two attachments.");
                        this.checked = false; // Uncheck the last checkbox
                        break;
                    }
                }
            }
            // Display or hide the delete button accordingly
            if (atLeastOneChecked) {
                deleteButton.style.display = "inline-block";
            } else {
                deleteButton.style.display = "none";
            }
        });
    }
});
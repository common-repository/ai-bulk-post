import $ from "jquery";
import jqueryConfirmMin from "jquery-confirm";
import jqueryValidate from "jquery-validation";

jconfirm.defaults = {
    theme: "aibp",
    draggable: false,
    backgroundDismiss: true,
    closeIcon: true,
};

// Add event
$(document).on("click", ".aibp-add-new-rule", function () {
    var ruleModal = $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: "json",
                data: {
                    action: "aibp/get/add_events_form",
                    nonce: AIBulkPost.nonce,
                },
            })
                .done(function (response) {
                    self.setContent(response.output);
                    self.setTitle(response.title);
                })
                .fail(function () {
                    self.setContent("Something went wrong.");
                });
        },
        onContentReady: function () {
            let form = this.$content.find(".ai-bulk-post-form");
            form.find("#ai").trigger("change");
        },
        buttons: {
            submit: {
                text: "Save",
                action: function () {
                    let form = $(this.$content);
                    form = form.find("form");

                    if (!form.valid()) {
                        return false;
                    }

                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        dataType: "json",
                        data: {
                            action: "aibp/add/event",
                            form: form.serialize(),
                            nonce: AIBulkPost.nonce,
                        },
                        success: function (response) {
                            if (response.error) {
                                alert(response.message);
                            } else {
                                ruleModal.close();
                                window.location.reload();
                            }
                        },
                    });
                    return false;
                },
            },
        },
    });
});

// Edit event
$(document).on("click", ".aibp-action-edit", function () {
    let table_row = $(this).closest("tr");
    let post_id = table_row.attr("data-post-id");

    var ruleModal = $.confirm({
        content: function () {
            var self = this;
            return $.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: "json",
                data: {
                    action: "aibp/get/add_events_form",
                    post_id: post_id,
                    nonce: AIBulkPost.nonce,
                },
            })
                .done(function (response) {
                    self.setContent(response.output);
                    self.setTitle(response.title);
                })
                .fail(function () {
                    self.setContent("Something went wrong.");
                });
        },
        buttons: {
            submit: {
                text: "Update",
                action: function () {
                    let form = $(this.$content);
                    form = form.find("form");

                    if (!form.valid()) {
                        return false;
                    }

                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        dataType: "json",
                        data: {
                            action: "aibp/update/event",
                            form: form.serialize(),
                            post_id: post_id,
                            nonce: AIBulkPost.nonce,
                        },
                        success: function (response) {
                            if (response.error) {
                                alert(response.message);
                            } else {
                                ruleModal.close();
                                window.location.reload();
                            }
                        },
                    });
                    return false;
                },
            },
        },
    });
});

// Delete event
$(document).on("click", ".aibp-action-delete", function () {
    let table_row = $(this).closest("tr");
    let post_id = table_row.attr("data-post-id");

    var modal = $.confirm({
        title: "Delete Event",
        content: "Your data will be lost. Are you sure?",
        closeIcon: false,
        buttons: {
            cancel: function () {},
            delete: function () {
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        action: "aibp/delete/event",
                        post_id: post_id,
                        nonce: AIBulkPost.nonce,
                    },
                    success: function (response) {
                        $.alert({
                            title: false,
                            content: response.message,
                            closeIcon: false,
                        });
                        if (!response.error) {
                            table_row.remove();
                        }
                        modal.close();
                    },
                });
            },
        },
    });
});

// Event status
$(document).on("click", ".aibp-action-status", function () {
    let table_row = $(this).closest("tr");
    let post_id = table_row.attr("data-post-id");
    let table_column = table_row.find(".aibp-column-status");

    $.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
            action: "aibp/update/event/status",
            post_id: post_id,
            nonce: AIBulkPost.nonce,
        },
        success: function (response) {
            if (response.error) {
                $.alert({
                    title: false,
                    content: response.message,
                    closeIcon: false,
                });
            } else {
                table_column.html(response.status);
                table_row.fadeOut("fast", function () {
                    table_row.fadeIn();
                });
            }
        },
    });
});

// Select condition
$(document).on("change", ".ai-bulk-post-form #ai", function (e) {
    let val = $(this).val();
    let $modelSelect = $(this).closest(".ai-bulk-post-form").find("#model");
    $modelSelect.find("option").prop("disabled", true);
    $modelSelect.find("." + val).prop("disabled", false);
    $modelSelect.trigger("change");
    $modelSelect.find("option:not(:disabled)").eq(0).prop("selected", true);
});

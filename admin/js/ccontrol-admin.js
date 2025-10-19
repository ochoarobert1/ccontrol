/**
 * CControl Admin JavaScript
 *
 * Handles admin functionality for the CControl plugin including:
 * - PDF generation and sending for quotes and invoices
 * - Media uploader for logo selection
 * - Dynamic form item management
 * - Tab navigation
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    // Print quote button handler
    $("#printQuote").on("click", function (e) {
      e.preventDefault();
      openWindowWithPostId("ccontrol_create_pdf", "#printQuote");
    });

    // Send quote via email button handler
    $("#sendQuote").on("click", function (e) {
      e.preventDefault();
      sendPostIdViaAjax("ccontrol_create_pdf_send", "#sendQuote");
    });

    // Print invoice button handler
    $("#printInvoice").on("click", function (e) {
      e.preventDefault();
      openWindowWithPostId("ccontrol_create_invoice_pdf", "#printInvoice");
    });

    // Send invoice via email button handler
    $("#sendInvoice").on("click", function (e) {
      e.preventDefault();
      sendPostIdViaAjax("ccontrol_create_invoice_pdf_send", "#sendInvoice");
    });

    // WordPress media uploader for logo selection
    $("#upload-btn").click(function (e) {
      e.preventDefault();
      var image = wp
        .media({
          title: ccontrol_admin_object.upload_logo_text,
          button: {
            text: ccontrol_admin_object.upload_logo_btn_text,
          },
          library: {
            type: "image",
          },
          multiple: false,
        })
        .open()
        .on("select", function () {
          var uploaded_image = image.state().get("selection").first().toJSON();
          $("#image_url").val(uploaded_image.url);
          $("#ccontrol_logo").attr("src", uploaded_image.url);
        });
    });

    // Add new invoice/quote item row
    $(document).on("click", ".item-factura-add", function (e) {
      e.preventDefault();
      var item = $(this).closest(".item-factura");
      var clone = item.clone();
      clone.find("input").val(""); // Clear input values
      clone.find(".item-factura-remove").show(); // Show remove button
      item.after(clone);
    });

    // Remove invoice/quote item row
    $(document).on("click", ".item-factura-remove", function (e) {
      e.preventDefault();
      $(this).closest(".item-factura").remove();
    });

    // Tab navigation handler
    $("#ccTabLinks a").click(function (e) {
      e.preventDefault();
      var tab = $(this).attr("href");
      $("#ccTabLinks a").removeClass("active");
      $(this).addClass("active");
      $(".tabs-content-wrapper .tabs-content").removeClass("active");
      $(tab).addClass("active");
    });
  });

  /**
   * Opens a new window with the specified action and post ID
   * @param {string} action - WordPress AJAX action name
   * @param {string} buttonId - jQuery selector for the button containing data-id
   */
  function openWindowWithPostId(action, buttonId) {
    window.open(
      ajaxurl + "?action=" + action + "&postid=" + $(buttonId).data("id"),
      "_blank"
    );
  }

  /**
   * Sends post ID via AJAX and handles the response
   * @param {string} action - WordPress AJAX action name
   * @param {string} buttonId - jQuery selector for the button containing data-id
   */
  function sendPostIdViaAjax(action, buttonId) {
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: {
        action: action,
        postid: $(buttonId).data("id"),
      },
      beforeSend: function () {
        // Show loading indicator based on action type
        if (action === "ccontrol_create_pdf_send") {
          jQuery("#sendQuoteResponse").html('<div class="loader"></div>');
        } else {
          jQuery("#sendInvoiceResponse").html('<div class="loader"></div>');
        }
      },
      success: function (response) {
        // Display response in appropriate container
        if (action === "ccontrol_create_pdf_send") {
          jQuery("#sendQuoteResponse").html(response.data);
        } else {
          jQuery("#sendInvoiceResponse").html(response.data);
        }
      },
    });
  }
})(jQuery);

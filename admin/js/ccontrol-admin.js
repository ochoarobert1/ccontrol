(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#printQuote").on("click", function (e) {
      e.preventDefault();
      openWindowWithPostId("ccontrol_create_pdf", "#printQuote");
    });

    $("#sendQuote").on("click", function (e) {
      e.preventDefault();
      sendPostIdViaAjax("ccontrol_create_pdf_send", "#sendQuote");
    });

    $("#printInvoice").on("click", function (e) {
      e.preventDefault();
      openWindowWithPostId("ccontrol_create_invoice_pdf", "#printInvoice");
    });

    $("#sendInvoice").on("click", function (e) {
      e.preventDefault();
      sendPostIdViaAjax("ccontrol_create_invoice_pdf_send", "#sendInvoice");
    });

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

    $(document).on("click", ".item-factura-add", function (e) {
      e.preventDefault();
      var item = $(this).closest(".item-factura");
      var clone = item.clone();
      clone.find("input").val("");
      clone.find(".item-factura-remove").show();
      item.after(clone);
    });

    $(document).on("click", ".item-factura-remove", function (e) {
      e.preventDefault();
      $(this).closest(".item-factura").remove();
    });

    $("#ccTabLinks a").click(function (e) {
      e.preventDefault();
      var tab = $(this).attr("href");
      $("#ccTabLinks a").removeClass("active");
      $(this).addClass("active");
      $(".tabs-content-wrapper .tabs-content").removeClass("active");
      $(tab).addClass("active");
    });
  });

  function openWindowWithPostId(action, buttonId) {
    window.open(
      ajaxurl + "?action=" + action + "&postid=" + $(buttonId).data("id"),
      "_blank"
    );
  }

  function sendPostIdViaAjax(action, buttonId) {
    $.ajax({
      type: "POST",
      url: ajaxurl,
      data: {
        action: action,
        postid: $(buttonId).data("id"),
      },
      beforeSend: function () {
        if (action === "ccontrol_create_pdf_send") {
          jQuery("#sendQuoteResponse").html('<div class="loader"></div>');
        } else {
          jQuery("#sendInvoiceResponse").html('<div class="loader"></div>');
        }
      },
      success: function (response) {
        if (action === "ccontrol_create_pdf_send") {
          jQuery("#sendQuoteResponse").html(response.data);
        } else {
          jQuery("#sendInvoiceResponse").html(response.data);
        }
      },
    });
  }
})(jQuery);

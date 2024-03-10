(function ($) {
  "use strict";

  $(document).ready(function () {
    $("#printQuote").on("click", function (e) {
      e.preventDefault();
      console.log("clicked");
      openWindowWithPostId("ccontrol_create_pdf", "#printQuote");
    });

    $("#sendQuote").on("click", function (e) {
      e.preventDefault();
      console.log("clicked");
      sendPostIdViaAjax("ccontrol_create_pdf_send", "#sendQuote");
    });

    $("#printInvoice").on("click", function (e) {
      e.preventDefault();
      console.log("clicked");
      openWindowWithPostId("ccontrol_create_invoice_pdf", "#printInvoice");
    });

    $("#sendInvoice").on("click", function (e) {
      e.preventDefault();
      console.log("clicked");
      sendPostIdViaAjax("ccontrol_create_invoice_pdf_send", "#sendInvoice");
    });

    $("#upload-btn").click(function (e) {
      e.preventDefault();
      var image = wp
        .media({
          title: "Upload Image",
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
      console.log("clicked");
      var item = $(this).closest(".item-factura");
      var clone = item.clone();
      clone.find("input").val("");
      clone.find(".item-factura-remove").show();
      item.after(clone);
    });

    $(document).on("click", ".item-factura-remove", function (e) {
      e.preventDefault();
      console.log("clicked");
      $(this).closest(".item-factura").remove();
    });

    $("#ccTabLinks a").click(function (e) {
      e.preventDefault();
      console.log("clicked");
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
      success: function (response) {
        console.log(response);
      },
    });
  }
})(jQuery);

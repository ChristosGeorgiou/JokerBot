$(function() {

  $(document)
    .on("submit", "form.modal", function(e) {
      e.preventDefault()
      var _this = $(this)
        // $("#processer").fadeIn();
      _this.find(".form").addClass("loading")
      _this.find(".actions .button.ok").addClass("loading")
      _this.find(".ui.negative.message").hide("slow").remove()
      $.ajax({
        url: _this.attr("action"),
        data: _this.serialize(),
        type: "post",
        dataType: "json",
        success: function(response) {
          _this.modal("hide")
          location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          _msg = $("<div/>", {
            "class": "ui negative message",
            "html": "<div class='header'>Σφάλμα Αποθήκευσης</div><p>" + errorThrown + "</p>"
          })
          _this.find(".form").prepend(_msg)
        },
        complete: function() {
          _this.find(".form").removeClass("loading")
          _this.find(".actions .button.ok").removeClass("loading")
        }
      })
      return false;
    })
    .on("click", ".amodal", function(e) {
      e.preventDefault()
      var _this = $(this)
      $("#loader").fadeIn();
      var _modal = $("<span/>").load(_this.attr("href"), function() {

        _modal.find(".ui.modal").modal({
          onHidden: function() {
            $(this).remove();
          },
          selector: {
            close: '.close',
          },
          debug: true,
          verbose: true
        }).modal("show")

        $('[data-lorem]').lorem();
        $("#loader").fadeOut("slow");
      })
    })

  $.ajaxSetup({
    cache: false,
    error: function(event, jqXHR) {
      if (403 === jqXHR.status) {
        console.log(jqXHR)
      }
    }
  });
})

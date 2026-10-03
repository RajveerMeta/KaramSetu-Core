$(document).ready(function () {
  if ($("#causesWrapper").length) {
    $(".contrib-matcher-btn").on("click", function () {
      $(".contrib-matcher-btn")
        .removeClass("border-gold bg-white shadow-md text-forest")
        .addClass(
          "border-transparent text-charcoal-light hover:text-charcoal hover:bg-white/50",
        );
      $(".contrib-matcher-dot")
        .removeClass("border-gold bg-gold")
        .addClass("border-gold/30 bg-white");
      $(this)
        .removeClass(
          "border-transparent text-charcoal-light hover:text-charcoal hover:bg-white/50",
        )
        .addClass("border-gold bg-white shadow-md text-forest");
      $(this)
        .find(".contrib-matcher-dot")
        .removeClass("border-gold/30 bg-white")
        .addClass("border-gold bg-gold");

      const selectedCauseType = $(this).data("type");
      $(".contrib-matcher-content").hide();
      $('.contrib-matcher-content[data-match="' + selectedCauseType + '"]').fadeIn();
    });
  }

  $(".impact-filter-btn").on("click", function () {
    $(".impact-filter-btn")
      .removeClass("bg-forest text-white shadow-md")
      .addClass("bg-white text-charcoal hover:bg-ivory");
    $(this)
      .removeClass("bg-white text-charcoal hover:bg-ivory")
      .addClass("bg-forest text-white shadow-md");
  });
});

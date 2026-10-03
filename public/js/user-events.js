$(document).ready(function () {
  if ($("#userEventsWrapper").length) {
    $(".user-event-tab").on("click", function () {
      $(".user-event-tab")
        .removeClass("bg-ivory text-forest border-forest")
        .addClass("bg-white text-charcoal hover:bg-ivory border-gold/20");

      $(this)
        .removeClass("bg-white text-charcoal hover:bg-ivory border-gold/20")
        .addClass("bg-ivory text-forest border-forest");
    });

    $(".user-event-view-ticket-btn").on("click", function () {
      const ticketId = $(this).data("ticket");
      $("#ticketModalIdDisplay").text(ticketId);
      $("#ticketModal").fadeIn().css("display", "flex");
    });

    $(".ticket-modal-close-btn").on("click", function () {
      $("#ticketModal").fadeOut();
    });

    $('.user-event-tab[data-tab="all"]')
      .addClass("bg-ivory text-forest border-forest")
      .removeClass("bg-white text-charcoal hover:bg-ivory border-gold/20");
  }
});

$(document).ready(function () {
  if ($(".event-tab-btn").length) {
    $(".event-tab-btn").on("click", function () {
      const selectedTab = $(this).data("tab");

      $(".event-tab-btn")
        .removeClass("border-forest text-forest")
        .addClass("border-transparent text-charcoal-light hover:text-charcoal");

      $(this)
        .removeClass(
          "border-transparent text-charcoal-light hover:text-charcoal",
        )
        .addClass("border-forest text-forest");

      $(".event-tab-content").hide();

      $('.event-tab-content[data-tab="' + selectedTab + '"]').show();
    });

    $('.event-tab-btn[data-tab="about"]').click();
  }

  // Ticket quantity
  // Ticket quantity
const donationCountDisplay = $("#donationCountDisplay");
const totalAmountDisplay = $("#totalAmountDisplay");
const ticketBookingCard = $("#ticketBookingCard");

const ticketPrice = parseInt(ticketBookingCard.data("ticket-price")) || 0;

function updateTotalAmount() {
    const count = parseInt(donationCountDisplay.text()) || 1;
    const total = ticketPrice * count;

    totalAmountDisplay.text("₹" + total.toLocaleString("en-IN"));
}

$("#donationMinusBtn").on("click", function () {
    const count = parseInt(donationCountDisplay.text()) || 1;

    if (count > 1) {
        donationCountDisplay.text(count - 1);
        updateTotalAmount();
    }
});

$("#donationPlusBtn").on("click", function () {
    const count = parseInt(donationCountDisplay.text()) || 1;

    if (count < 10) {
        donationCountDisplay.text(count + 1);
        updateTotalAmount();
    }
});
});

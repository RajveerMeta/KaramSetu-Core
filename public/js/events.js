$(document).ready(function () {
  if ($("#eventsWrapper").length) {
    let filterEvents = function () {
      const eventSearchQuery = $("#eventsSearchQuery").val().toLowerCase(),
        selectedEventCategory = $("#eventsCategory").val().toLowerCase(),
        selectedEventType = $("#eventsType").val().toLowerCase();
      let eventCount = 0;

      $(".event-item").each(function () {
        const eventCategory = $(this).attr("data-category") || "",
          eventPriceType = $(this).attr("data-price") || "",
          eventText = $(this).text().toLowerCase(),
          categoryMatches =
            selectedEventCategory === "all" ||
            eventCategory === selectedEventCategory,
          typeMatches =
            selectedEventType === "all" || eventPriceType === selectedEventType,
          searchMatches =
            eventText.includes(eventSearchQuery) || eventSearchQuery === "";

        if (categoryMatches && typeMatches && searchMatches) {
          $(this).show();
          eventCount++;
        } else {
          $(this).hide();
        }
      });

      if (
        eventSearchQuery === "" &&
        selectedEventCategory === "all" &&
        selectedEventType === "all" &&
        $("#eventsDate").val() === "all"
      ) {
        $("#eventsFeaturedSection").show();
      } else {
        $("#eventsFeaturedSection").hide();
      }

      if (
        eventCount === 0 &&
        (eventSearchQuery !== "" ||
          selectedEventCategory !== "all" ||
          selectedEventType !== "all" ||
          $("#eventsDate").val() !== "all")
      ) {
        $("#eventsEmptyState").show();
        $("#eventsPagination").hide();
      } else {
        $("#eventsEmptyState").hide();
        $("#eventsPagination").show();
      }
    };

    $("#eventsSearchQuery, #eventsCategory, #eventsType, #eventsDate").on(
      "input change",
      function () {
        filterEvents();
      },
    );

    $("#eventsClearBtn").on("click", function () {
      $("#eventsSearchQuery").val("");
      $("#eventsCategory").val("all");
      $("#eventsType").val("all");
      $("#eventsDate").val("all");
      filterEvents();
    });

    filterEvents();
  }

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

    const ticketCountDisplay = $("#ticketCountDisplay");

    $("#ticketMinusBtn").on("click", function () {
      const ticketCount = parseInt(ticketCountDisplay.text());
      if (ticketCount > 1) {
        ticketCountDisplay.text(ticketCount - 1);
      }
    });

    $("#ticketPlusBtn").on("click", function () {
      const ticketCount = parseInt(ticketCountDisplay.text());
      if (ticketCount < 10) {
        ticketCountDisplay.text(ticketCount + 1);
      }
    });

 

    

   

  }
});

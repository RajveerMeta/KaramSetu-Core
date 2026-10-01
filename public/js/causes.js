$(document).ready(function () {
  if ($("#causesWrapper").length) {
    let filterCauses = function () {
      const selectedCauseType = $(".cause-filter-btn.bg-forest").data("id") || "all",
        searchQuery = $("#searchQuery").val().toLowerCase(),
        selectedLocation = $("#locationFilter").val().toLowerCase();

      $(".cause-card-item").each(function () {
        const acceptedContributionTypes = JSON.parse(
            $(this).attr("data-accepts") || "[]",
          ),
          causeLocation = $(this).attr("data-location") || "",
          causeTitle = $(this).attr("data-title") || "",
          typeMatches =
            selectedCauseType === "all" ||
            acceptedContributionTypes.includes(selectedCauseType),
          locationMatches =
            selectedLocation === "all" || causeLocation === selectedLocation,
          titleMatches =
            causeTitle.includes(searchQuery) || searchQuery === "";

        if (typeMatches && locationMatches && titleMatches) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });

      if ($("#featuredCause").length) {
        const acceptedContributionTypes = JSON.parse(
          $("#featuredCause").attr("data-accepts") || "[]",
        );
        if (
          (selectedCauseType === "all" ||
            acceptedContributionTypes.includes(selectedCauseType)) &&
          (selectedLocation === "all" ||
            selectedLocation === $("#featuredCause").attr("data-location")) &&
          searchQuery === ""
        ) {
          $("#featuredCause").show();
        } else {
          $("#featuredCause").hide();
        }
      }
    };

    $(".cause-filter-btn").on("click", function () {
      $(".cause-filter-btn")
        .removeClass("bg-forest text-ivory border-forest")
        .addClass(
          "bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white",
        );
      $(this)
        .removeClass(
          "bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white",
        )
        .addClass("bg-forest text-ivory border-forest");
      filterCauses();
    });

    $("#searchQuery, #locationFilter").on("input change", function () {
      filterCauses();
    });

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

    $(".contrib-matcher-filter-btn").on("click", function () {
      const selectedCauseType = $(this).data("type");
      $('.cause-filter-btn[data-id="' + selectedCauseType + '"]').click();
      $("html, body").animate({ scrollTop: 0 }, "smooth");
    });

    $('.cause-filter-btn[data-id="all"]')
      .addClass("bg-forest text-ivory border-forest")
      .removeClass(
        "bg-ivory border-gold/20 text-charcoal hover:border-gold hover:bg-white",
      );

    filterCauses();
  }

  $(".hero-filter-btn").on("click", function () {
    const filterId = $(this).data("id");
    window.location.href = jsBaseUrl("?page=causes&type=") + filterId;
  });

  $(".impact-filter-btn").on("click", function () {
    $(".impact-filter-btn")
      .removeClass("bg-forest text-white shadow-md")
      .addClass("bg-white text-charcoal hover:bg-ivory");
    $(this)
      .removeClass("bg-white text-charcoal hover:bg-ivory")
      .addClass("bg-forest text-white shadow-md");
  });
});

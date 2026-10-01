$(document).ready(function () {
  if ($("#contributionForm").length) {
    let currentType = "money";

    const updateContributionType = function () {
      $(".contrib-section").hide();

      $(".contrib-" + currentType).fadeIn(300);

      $(".contrib-type-btn")
        .removeClass("bg-forest/5 border-forest text-forest ring-1 ring-forest")
        .addClass(
          "bg-white border-gold/30 text-charcoal hover:border-forest hover:text-forest",
        );

      $('.contrib-type-btn[data-type="' + currentType + '"]')
        .removeClass(
          "bg-white border-gold/30 text-charcoal hover:border-forest hover:text-forest",
        )
        .addClass("bg-forest/5 border-forest text-forest ring-1 ring-forest");

      $(".contrib-indicator").hide();
      $('.contrib-type-btn[data-type="' + currentType + '"]')
        .find(".contrib-indicator")
        .show();

      $("#summaryType").text(currentType);
      $(".summary-dynamic-row").hide();

      if (currentType === "money") {
        $("#summaryMoneyRow").show();
      } else if (currentType === "food") {
        $("#summaryFoodRow").show();
      } else if (currentType === "clothes") {
        $("#summaryClothesRow").show();
      } else if (currentType === "items") {
        $("#summaryItemsRow").show();
      }

      updateSummaryContent();
    };

    const updateSummaryContent = function () {
      if (currentType === "money") {
        let amt = $("#contribAmount").val();
        $("#summaryAmount").text(amt ? "₹" + amt : "₹1000");
      } else if (currentType === "food") {
        let qty = $("#contribFoodQuantity").val();
        let unit = $("#contribFoodUnit").val();
        $("#summaryFoodQty").text((qty || 10) + " " + (unit || "kg"));
      } else if (currentType === "clothes") {
        let qty = $("#contribClothesQuantity").val();
        $("#summaryClothesQty").text((qty || 5) + " items");
      } else if (currentType === "items") {
        let name = $("#contribItemName").val();
        let qty = $("#contribItemQuantity").val();
        $("#summaryItemDetails").text((qty || 1) + "x " + (name || "-"));
      }
    };

    $(".contrib-type-btn").on("click", function () {
      currentType = $(this).data("type");
      updateContributionType();
    });

    $(".contrib-preset-btn").on("click", function () {
      $(".contrib-preset-btn, #contribCustomBtn")
        .removeClass("bg-forest text-white border-forest")
        .addClass(
          "bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5",
        );

      $(this)
        .removeClass(
          "bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5",
        )
        .addClass("bg-forest text-white border-forest");

      $("#contribAmount").val($(this).data("amount"));
      $("#contribCustomWrapper, #contribCustomIndicator").hide();
      updateSummaryContent();
    });

    $("#contribCustomBtn").on("click", function () {
      $(".contrib-preset-btn")
        .removeClass("bg-forest text-white border-forest")
        .addClass(
          "bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5",
        );

      $(this)
        .removeClass(
          "bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5",
        )
        .addClass("bg-forest text-white border-forest");

      $("#contribCustomWrapper, #contribCustomIndicator").show();
      $("#contribAmount").focus();
    });

    $("input, select, textarea").on("input change", function () {
      updateSummaryContent();
    });

    updateContributionType();
    $('.contrib-preset-btn[data-amount="1000"]').click();
  }
});

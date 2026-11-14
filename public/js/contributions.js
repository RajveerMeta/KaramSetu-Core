$(document).ready(function () {
  if ($("#contributionForm").length) {
    let currentType = "money";

    // Set initial type from first visible button or hidden input
    const firstActiveBtn = $(".contrib-type-btn:first");
    if (firstActiveBtn.length && firstActiveBtn.data("type")) {
      currentType = String(firstActiveBtn.data("type")).toLowerCase();
    } else if ($("#contributionType").val()) {
      currentType = String($("#contributionType").val()).toLowerCase();
    }

    const updateContributionType = function () {
      // 1. Update hidden input
      $("#contributionType").val(currentType);

      // 2. Hide, disable and deactivate validation on all sections
      $(".contrib-section").each(function () {
        const sec = $(this);
        sec.hide();
        sec.find("input, select, textarea").each(function () {
          const el = $(this);
          el.prop("disabled", true);

          // Save and remove validation rules so hidden fields do not block submission
          if (el.attr("data-validate")) {
            el.attr("data-inactive-validate", el.attr("data-validate"));
            el.removeAttr("data-validate");
          }
          if (el.is("[required]")) {
            el.attr("data-inactive-required", "true");
            el.removeAttr("required");
          }
          el.removeClass("is-invalid is-valid");

          // Hide any error message spans
          el.siblings(".validation-error").text("").hide();
          const fieldName = el.attr("name") || el.attr("id");
          if (fieldName) {
            $("#" + fieldName.replace(/\[\]/g, "") + "Error").text("").hide();
          }
        });
      });

      // 3. Show, enable and restore validation on only the active section
      const activeSec = $(".contrib-" + currentType);
      activeSec.fadeIn(200);
      activeSec.find("input, select, textarea").each(function () {
        const el = $(this);
        el.prop("disabled", false);

        if (el.attr("data-inactive-validate")) {
          el.attr("data-validate", el.attr("data-inactive-validate"));
        }
        if (el.attr("data-inactive-required")) {
          el.attr("required", "required");
        }
      });

      // 4. Update button styles
      $(".contrib-type-btn")
        .removeClass("bg-forest/5 border-forest text-forest ring-1 ring-forest")
        .addClass("bg-white border-gold/30 text-charcoal hover:border-forest hover:text-forest");

      $('.contrib-type-btn[data-type="' + currentType + '"]')
        .removeClass("bg-white border-gold/30 text-charcoal hover:border-forest hover:text-forest")
        .addClass("bg-forest/5 border-forest text-forest ring-1 ring-forest");

      $(".contrib-indicator").hide();
      $('.contrib-type-btn[data-type="' + currentType + '"]')
        .find(".contrib-indicator")
        .show();

      // 5. Update summary text
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
        let unit = $("#contribFoodUnit").val() || "kg";
        let fType = $('select[name="food_type"]').val() || "Food";
        $("#summaryFoodQty").text((qty || 10) + " " + unit + " (" + fType + ")");
      } else if (currentType === "clothes") {
        let qty = $("#contribClothesQuantity").val();
        let cType = $('select[name="clothing_type"]').val() || "Clothes";
        $("#summaryClothesQty").text((qty || 5) + " items (" + cType + ")");
      } else if (currentType === "items") {
        let name = $("#contribItemName").val();
        let qty = $("#contribItemQuantity").val();
        $("#summaryItemDetails").text((qty || 1) + "x " + (name || "Item"));
      }
    };

    // Tab button click
    $(".contrib-type-btn").on("click", function () {
      currentType = String($(this).data("type")).toLowerCase();
      updateContributionType();
    });

    // Preset amounts for money
    $(".contrib-preset-btn").on("click", function () {
      $(".contrib-preset-btn, #contribCustomBtn")
        .removeClass("bg-forest text-white border-forest")
        .addClass("bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5");

      $(this)
        .removeClass("bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5")
        .addClass("bg-forest text-white border-forest");

      $("#contribAmount").val($(this).data("amount"));
      $("#contribCustomWrapper, #contribCustomIndicator").hide();
      updateSummaryContent();
    });

    // Custom amount for money
    $("#contribCustomBtn").on("click", function () {
      $(".contrib-preset-btn")
        .removeClass("bg-forest text-white border-forest")
        .addClass("bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5");

      $(this)
        .removeClass("bg-white text-forest border-gold/30 hover:border-forest hover:bg-forest/5")
        .addClass("bg-forest text-white border-forest");

      $("#contribCustomWrapper, #contribCustomIndicator").show();
      $("#contribAmount").focus();
    });

    // Live update summary on input change
    $("input, select, textarea").on("input change", function () {
      updateSummaryContent();
    });

    // Review Modal handling on Form Submit
    $("#contributionForm").on("submit", function (e) {
      e.preventDefault();

      // Check required donor inputs
      const donorName = $('input[name="full_name"]').val().trim();
      const donorEmail = $('input[name="email"]').val().trim();

      if (!donorName || !donorEmail) {
        return;
      }

      // Check type-specific inputs
      if (currentType === "money") {
        const amt = parseFloat($("#contribAmount").val());
        if (!amt || amt <= 0) return;
      } else if (currentType === "food") {
        const qty = parseInt($("#contribFoodQuantity").val(), 10);
        if (!qty || qty <= 0) return;
      } else if (currentType === "clothes") {
        const qty = parseInt($("#contribClothesQuantity").val(), 10);
        if (!qty || qty <= 0) return;
      } else if (currentType === "items") {
        const name = $("#contribItemName").val().trim();
        const qty = parseInt($("#contribItemQuantity").val(), 10);
        if (!name || !qty || qty <= 0) return;
      }

      // Populate review modal details
      $("#reviewType").text(currentType);
      $("#reviewAmountRow, #reviewQuantityRow, #reviewItemRow").hide();

      if (currentType === "money") {
        const amt = $("#contribAmount").val() || "1000";
        $("#reviewAmount").text("₹" + amt);
        $("#reviewAmountRow").show();
      } else if (currentType === "food") {
        const qty = $("#contribFoodQuantity").val() || "10";
        const unit = $("#contribFoodUnit").val() || "kg";
        const fType = $('select[name="food_type"]').val() || "Food";
        $("#reviewQuantity").text(qty + " " + unit + " (" + fType + ")");
        $("#reviewQuantityRow").show();
      } else if (currentType === "clothes") {
        const qty = $("#contribClothesQuantity").val() || "5";
        const cType = $('select[name="clothing_type"]').val() || "Clothes";
        $("#reviewQuantity").text(qty + " items (" + cType + ")");
        $("#reviewQuantityRow").show();
      } else if (currentType === "items") {
        const name = $("#contribItemName").val() || "Item";
        const qty = $("#contribItemQuantity").val() || "1";
        $("#reviewItem").text(qty + "x " + name);
        $("#reviewItemRow").show();
      }

      // Display the confirmation overlay
      $("#reviewOverlay").fadeIn(200).css("display", "flex");
    });

    // Close review overlay to edit
    $("#contribBackBtn").on("click", function () {
      $("#reviewOverlay").fadeOut(200);
    });

    // Confirm and submit the form
    $("#contribConfirmBtn").on("click", function () {
      $(this).prop("disabled", true).text("Submitting...");
      HTMLFormElement.prototype.submit.call(
        document.getElementById("contributionForm")
      );
    });

    // Initialize component state
    updateContributionType();
    if (currentType === "money" && $('.contrib-preset-btn[data-amount="1000"]').length) {
      $('.contrib-preset-btn[data-amount="1000"]').click();
    }
  }
});

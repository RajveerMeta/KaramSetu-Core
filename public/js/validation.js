$(document).ready(function () {
  function getErrorElement(field) {
    let fieldName = field.attr("name") || "";
    let fieldId = field.attr("id") || "";

    let errorElement = $();

    if (fieldName) {
      errorElement = $("#" + fieldName.replace(/\[\]/g, "") + "Error");
    }

    if (errorElement.length === 0 && fieldId) {
      errorElement = $("#" + fieldId + "Error");
    }

    if (errorElement.length === 0) {
      errorElement = field.next(".validation-error");
    }

    if (errorElement.length === 0) {
      errorElement = $("<p>").addClass(
        "validation-error text-red-500 text-sm mt-1",
      );

      field.after(errorElement);
    }

    return errorElement;
  }

  function validateField(input) {
    let field = $(input);

    let value = field.val() ? field.val().trim() : "";

    let fieldName = field.attr("name") || "";
    let fieldId = field.attr("id") || "";

    let errorSpan = getErrorElement(field);

    let validationType =
      field.attr("data-validate") || field.data("validation") || "";

    /*
      HTML required attribute also activates
      the custom required validation.
    */
    if (field.is("[required]") && !validationType.includes("required")) {
      validationType += " required";
    }

    let minLength = parseInt(field.data("min"), 10) || 0;
    let maxLength = parseInt(field.data("max"), 10) || 9999;

    let filesize = parseInt(field.data("filesize"), 10) || 0;

    let minItems = parseInt(field.data("min-items"), 10) || 0;
    let maxItems = parseInt(field.data("max-items"), 10) || 9999;

    let fileTypes = field.data("filetypes") || "";

    let errorMessage = "";

    let fieldNameStr = (fieldName || fieldId).toLowerCase();

    /* =========================
       AUTOMATIC ALPHA DETECTION
    ========================= */

    let alphaNames = [
      "name",
      "full_name",
      "fullname",
      "first_name",
      "firstname",
      "last_name",
      "lastname",
      "ngo_name",
      "ngoname",
      "organization_name",
      "organizationname",
      "contact_person",
      "city",
      "state",
    ];

    if (
      alphaNames.includes(fieldNameStr) &&
      field.attr("type") !== "password" &&
      field.attr("type") !== "email"
    ) {
      if (
        !validationType.includes("alpha") &&
        field.attr("data-no-name-val") !== "true"
      ) {
        validationType += " alpha";
      }
    }

    /* =========================
       AUTOMATIC PHONE DETECTION
    ========================= */

    let phoneNames = [
      "phone",
      "mobile",
      "contact",
      "contact_number",
      "phone_number",
      "mobile_number",
      "contactnumber",
      "phonenumber",
      "mobilenumber",
    ];

    let isPhone = false;

    if (
      (field.attr("type") === "tel" || phoneNames.includes(fieldNameStr)) &&
      field.attr("type") !== "password" &&
      field.attr("type") !== "email"
    ) {
      isPhone = true;
    }

    /*
      Do not validate fields that have
      no validation rule at all.
    */
    if (validationType === "" && !isPhone) {
      errorSpan.text("").hide();
      field.removeClass("is-invalid is-valid");
      return true;
    }

    function validateDateRange() {
      const startDate = $("#start_date").val();
      const endDate = $("#end_date").val();

      const endField = $("#end_date");

      if (!startDate || !endDate) {
        return true;
      }

      const errorElement = getErrorElement(endField);

      if (new Date(endDate) <= new Date(startDate)) {
        errorElement.text("End date must be after the start date.").show();

        endField.addClass("is-invalid").removeClass("is-valid");

        return false;
      }

      errorElement.text("").hide();

      endField.removeClass("is-invalid").addClass("is-valid");

      return true;
    }
    /* =========================
       REQUIRED
    ========================= */

    if (validationType.includes("required")) {
      if (field.attr("type") === "checkbox") {
        if (
          !field.is(":checked") &&
          !validationType.includes("min-items") &&
          !validationType.includes("max-items")
        ) {
          errorMessage = "This field is required.";
        }
      } else if (field.attr("type") === "file") {
        if (!field[0].files || field[0].files.length === 0) {
          errorMessage = "Please upload a file.";
        }
      } else if (value === "") {
        errorMessage = "This field is required.";
      }
    }

    /* =========================
       EMAIL
    ========================= */

    if (!errorMessage && validationType.includes("email") && value !== "") {
      let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!emailPattern.test(value)) {
        errorMessage = "Please enter a valid email address.";
      }
    }

    /* =========================
       STRONG PASSWORD
    ========================= */

    if (
      !errorMessage &&
      validationType.includes("strongPassword") &&
      value !== ""
    ) {
      let passwordRegex =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$/;

      if (!passwordRegex.test(value)) {
        errorMessage =
          "Password must be 8-25 characters and include uppercase, lowercase, number, and special character.";
      }
    }

    /* =========================
       CONFIRM PASSWORD
    ========================= */

    if (
      !errorMessage &&
      validationType.includes("confirmPassword") &&
      value !== ""
    ) {
      let passwordId = field.data("password-id");

      let passwordField = $("#" + passwordId);

      let password = passwordField.length ? passwordField.val().trim() : "";

      if (value !== password) {
        errorMessage = "Passwords do not match.";
      }
    }

    /* =========================
       TERMS
    ========================= */

    if (
      !errorMessage &&
      validationType.includes("terms") &&
      !field.is(":checked")
    ) {
      errorMessage = "You must agree to the Terms & Conditions.";
    }

    /* =========================
       ALPHA
    ========================= */

    if (!errorMessage && validationType.includes("alpha") && value !== "") {
      let alphaPattern = /^(?=(?:.*[A-Za-z]){2,})[A-Za-z\s]+$/;

      if (!alphaPattern.test(value)) {
        errorMessage =
          "Only letters are allowed and must contain at least 2 letters.";
      }
    }

    /* =========================
       NUMERIC & PHONE
    ========================= */

    if (!errorMessage && isPhone && value !== "") {
      let phonePattern = /^\d{10}$/;

      if (!phonePattern.test(value)) {
        errorMessage = "Phone number must be exactly 10 digits.";
      }
    } else if (
      !errorMessage &&
      validationType.includes("numeric") &&
      value !== ""
    ) {
      let numericPattern = /^[0-9]+$/;

      if (!numericPattern.test(value)) {
        errorMessage = "Only numbers are allowed.";
      }
    }

    /* =========================
       MINIMUM LENGTH
    ========================= */

    if (!errorMessage && validationType.includes("min") && value !== "") {
      if (value.length < minLength) {
        errorMessage = `Must be at least ${minLength} characters.`;
      }
    }

    /* =========================
       MAXIMUM LENGTH
    ========================= */

    if (!errorMessage && validationType.includes("max") && value !== "") {
      if (value.length > maxLength) {
        errorMessage = `Must be at most ${maxLength} characters.`;
      }
    }

    /* =========================
       CHECKBOX GROUP
    ========================= */

    if (
      !errorMessage &&
      field.attr("type") === "checkbox" &&
      (validationType.includes("min-items") ||
        validationType.includes("max-items"))
    ) {
      let groupName = field.attr("name");

      let checkedCount = $('input[name="' + groupName + '"]:checked').length;

      if (validationType.includes("min-items") && checkedCount < minItems) {
        errorMessage = `Please select at least ${minItems} option(s).`;
      }

      if (
        !errorMessage &&
        validationType.includes("max-items") &&
        checkedCount > maxItems
      ) {
        errorMessage = `You can only select up to ${maxItems} option(s).`;
      }
    }

    /* =========================
       FILE VALIDATION
    ========================= */

    if (!errorMessage && validationType.includes("file")) {
      let files = field[0].files;

      if (files && files.length > 0) {
        let file = files[0];

        let fileName = file.name;

        let fileSizeKB = file.size / 1024;

        let fileExtension = fileName.split(".").pop().toLowerCase();

        if (fileTypes) {
          let allowedTypes = fileTypes.split(",").map(function (type) {
            return type.trim().toLowerCase();
          });

          if (!allowedTypes.includes(fileExtension)) {
            errorMessage = "Invalid file type.";
          }
        }

        if (
          !errorMessage &&
          validationType.includes("filesize") &&
          filesize > 0 &&
          fileSizeKB > filesize
        ) {
          errorMessage = `File size must be at most ${filesize} KB.`;
        }
      } else if (validationType.includes("required")) {
        errorMessage = "Please upload a file.";
      }
    }

    /* =========================
       SELECT VALIDATION
    ========================= */

    if (
      !errorMessage &&
      field.is("select") &&
      validationType.includes("required")
    ) {
      if (value === "" || field.find("option:selected").index() === 0) {
        errorMessage = "Please select an option.";
      }
    }

    /* =========================
       TARGET
    ========================= */

    let target =
      field.attr("type") === "checkbox"
        ? $('input[name="' + fieldName + '"]')
        : field;

    /* =========================
       SHOW / HIDE ERROR
    ========================= */

    if (errorMessage) {
      errorSpan.text(errorMessage).show();

      target.addClass("is-invalid").removeClass("is-valid");

      return false;
    }

    errorSpan.text("").hide();

    target.removeClass("is-invalid").addClass("is-valid");

    return true;
  }

  /* =========================
     LIVE VALIDATION
  ========================= */

  $("input, textarea, select").on("input change", function () {
    validateField(this);

    if (this.id === "start_date" || this.id === "end_date") {
      validateDateRange();
    }
  });

  /* =========================
     FORM SUBMISSION
  ========================= */

  $("form.validation-form").on("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    $(this)
      .find("input, textarea, select")
      .each(function () {
        if (!validateField(this)) {
          isValid = false;
        }
      });

    if (
      $(this).find("#start_date").length &&
      $(this).find("#end_date").length
    ) {
      if (!validateDateRange()) {
        isValid = false;
      }
    }

    if (!isValid) {
      e.preventDefault();

      let firstInvalid = $(this).find(".is-invalid").first();

      if (firstInvalid.length) {
        firstInvalid[0].scrollIntoView({
          behavior: "smooth",
          block: "center",
        });

        firstInvalid.trigger("focus");
      }
    } else {
      $(this).trigger("validationSuccess");
    }
  });
});

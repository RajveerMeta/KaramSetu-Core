$(document).ready(function () {
  /* Logout */
  $(".logout-btn").on("click", function (e) {
    if (!$(this).attr("href")) {
      e.preventDefault();
      window.location.href = jsBaseUrl("auth/logout.php");
    }
  });


  /* Login modal */
  $(".login-btn").on("click", function () {
    $("#loginModal").fadeIn().css("display", "flex");
  });

  /* Register modal */
  $(".register-btn").on("click", function () {
    $("#loginModal").fadeOut();
    $("#registerModal").fadeIn().css("display", "flex");
  });

  /* Close authentication modals */
  $("#closeLoginModal, #closeRegisterModal").on("click", function () {
    $("#loginModal, #registerModal").fadeOut();
  });

  /* Switch login -> register */
  $(".auth-switch-to-register").on("click", function () {
    $("#loginModal").fadeOut();

    setTimeout(function () {
      $("#registerModal").fadeIn().css("display", "flex");
    }, 300);
  });

  /* Switch register -> login */
  $(".auth-switch-to-login").on("click", function () {
    $("#registerModal").fadeOut();

    setTimeout(function () {
      $("#loginModal").fadeIn().css("display", "flex");
    }, 300);
  });

  /* Login form submission */
  if ($("#LoginForm").length) {
    $("#LoginForm").on("validationSuccess", function () {
      HTMLFormElement.prototype.submit.call(this);
    });

    /* Clear login error while typing */
    $("#email, #password").on("input", function () {
      $("#loginErrorMsg").addClass("hidden");
      $("#email").removeClass("border-red-500");
      $("#password").removeClass("border-red-500");
    });
  }

  $("#ngoDropdownBtn").on("click", function (clickEvent) {
    clickEvent.stopPropagation();
    $("#ngoDropdownMenu").fadeToggle(150);
    $("#ngoDropdownIcon").toggleClass("rotate-180");
  });

  $(document).on("click", function () {
    $("#ngoDropdownMenu").fadeOut(150);
    $("#ngoDropdownIcon").removeClass("rotate-180");
  });
});

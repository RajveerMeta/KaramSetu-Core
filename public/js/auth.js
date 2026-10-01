$(document).ready(function () {
  const storedUserData = localStorage.getItem("karmaSetuDemoUser");

  if (storedUserData !== null) {
    try {
      const demoUserData = JSON.parse(storedUserData);

      $(".user-name").text(demoUserData.name);
      $(".user-email").text(demoUserData.email);

      const userInitials = demoUserData.name
        .split(" ")
        .map((namePart) => namePart[0])
        .join("")
        .substring(0, 2)
        .toUpperCase();

      $(".user-initial").text(userInitials);

      $(".auth-guest").hide();

      if (demoUserData.role === "ngo") {
        $(".auth-user").hide();
        $(".auth-ngo").removeClass("hidden").css("display", "flex");

        $(".desktop-nav-default").hide();
        $(".desktop-nav-ngo").removeClass("hidden").css("display", "flex");
      } else if (demoUserData.role === "admin") {
        $(".auth-user").hide();
        $(".auth-ngo").hide();
        $(".desktop-nav-default").hide();
        $(".desktop-nav-ngo").hide();
      } else {
        $(".auth-ngo").hide();
        $(".auth-user").removeClass("hidden").css("display", "flex");

        $(".desktop-nav-ngo").hide();
        $(".desktop-nav-default").removeClass("hidden").css("display", "flex");
      }
    } catch (error) {
      console.log("Invalid demo user data.");
    }
  } else {
    $(".auth-guest").removeClass("hidden").css("display", "flex");

    $(".auth-user").hide();
    $(".auth-ngo").hide();

    $(".desktop-nav-ngo").hide();
    $(".desktop-nav-default").removeClass("hidden").css("display", "flex");
  }

  $(".logout-btn").on("click", function () {
    localStorage.removeItem("karmaSetuDemoUser");
    window.location.reload();
  });

  $(".login-btn").on("click", function () {
    $("#loginModal").fadeIn().css("display", "flex");
  });

  $(".register-btn").on("click", function () {
    $("#loginModal").fadeOut();
    $("#registerModal").fadeIn().css("display", "flex");
  });

  $("#closeLoginModal, #closeRegisterModal").on("click", function () {
    $("#loginModal, #registerModal").fadeOut();
  });

  $(".auth-switch-to-register").on("click", function () {
    $("#loginModal").fadeOut();

    setTimeout(function () {
      $("#registerModal").fadeIn().css("display", "flex");
    }, 300);
  });

  $(".auth-switch-to-login").on("click", function () {
    $("#registerModal").fadeOut();

    setTimeout(function () {
      $("#loginModal").fadeIn().css("display", "flex");
    }, 300);
  });

  if ($("#LoginForm").length) {
    $("#LoginForm").on("submit", function (submitEvent) {
      submitEvent.preventDefault();

      const emailInput = $("#email");
      const passwordInput = $("#password");
      const errorMsg = $("#loginErrorMsg");

      const email = $.trim(emailInput.val());
      const password = passwordInput.val();

      errorMsg.addClass("hidden");
      emailInput.removeClass("border-red-500");
      passwordInput.removeClass("border-red-500");

      if (email === "demo@karmasetu.test" && password === "Demo@12345") {
        const demoUser = {
          name: "Rajveer Meta",
          email: "demo@karmasetu.test",
          role: "user",
          karmaPoints: 1280,
        };

        localStorage.setItem("karmaSetuDemoUser", JSON.stringify(demoUser));

        window.location.href = jsBaseUrl("user/dashboard.php");
      } else if (email === "demo@ngo.test" && password === "Ngo@12345") {
        const demoNgo = {
          name: "Seva Roots Initiative",
          email: "demo@ngo.test",
          role: "ngo",
        };

        localStorage.setItem("karmaSetuDemoUser", JSON.stringify(demoNgo));

        window.location.href = jsBaseUrl("ngo/dashboard.php");
      } else if (email === "admin@karmasetu.com" && password === "Admin@123") {
        const demoAdmin = {
          name: "Administrator",
          email: "admin@karmasetu.com",
          role: "admin",
        };

        localStorage.setItem("karmaSetuDemoUser", JSON.stringify(demoAdmin));

        window.location.href = jsBaseUrl("admin/dashboard.php");
      } else {
        errorMsg.removeClass("hidden").text("Invalid email or password.");

        emailInput.addClass("border-red-500");
        passwordInput.addClass("border-red-500");
      }
    });

    $("#email, #password").on("input", function () {
      $("#loginErrorMsg").addClass("hidden");
      $("#email").removeClass("border-red-500");
      $("#password").removeClass("border-red-500");
    });
  }
});

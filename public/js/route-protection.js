$(document).ready(function () {
  const userPages = [
    jsBaseUrl("user/dashboard.php"),
    jsBaseUrl("user/contributions.php"),
    jsBaseUrl("user/events.php"),
    jsBaseUrl("user/volunteer-activities.php"),
    jsBaseUrl("user/profile.php"),
  ];

  const ngoPages = [
    jsBaseUrl("ngo/dashboard.php"),
    jsBaseUrl("ngo/causes.php"),
    jsBaseUrl("ngo/events/index.php"),
    jsBaseUrl("ngo/donations.php"),
    jsBaseUrl("ngo/profile.php"),
  ];

  const currentPage = window.location.pathname;
  const demoUser = localStorage.getItem("karmaSetuDemoUser");
  const userData = demoUser ? JSON.parse(demoUser) : null;

  if (currentPage.includes('/admin/')) {
    if (!userData || userData.role !== 'admin') {
      if (userData && userData.role === 'user') {
        window.location.href = jsBaseUrl("user/dashboard.php");
      } else if (userData && userData.role === 'ngo') {
        window.location.href = jsBaseUrl("ngo/dashboard.php");
      } else {
        window.location.href = jsBaseUrl("auth/login.php");
      }
      return;
    }
  }

  if (
    (userPages.includes(currentPage) || ngoPages.includes(currentPage)) &&
    !demoUser
  ) {
    window.location.href = jsBaseUrl("auth/login.php");
  }
});

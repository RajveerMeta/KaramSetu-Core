$(document).ready(function () {

  $("#userDropdownBtn").on("click", function (clickEvent) {
    clickEvent.stopPropagation();
    $("#userDropdownMenu").fadeToggle(150);
    $("#userDropdownIcon").toggleClass("rotate-180");
  });

  $("#ngoDropdownBtn").on("click", function (clickEvent) {
    clickEvent.stopPropagation();
    $("#ngoDropdownMenu").fadeToggle(150);
    $("#ngoDropdownIcon").toggleClass("rotate-180");
  });


  $(document).on("click", function () {
    $("#userDropdownMenu").fadeOut(150);
    $("#userDropdownIcon").removeClass("rotate-180");
    $("#ngoDropdownMenu").fadeOut(150);
    $("#ngoDropdownIcon").removeClass("rotate-180");
  });
});

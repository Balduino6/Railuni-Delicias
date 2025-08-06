document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("toggleDark");
  const prefersDark = localStorage.getItem("darkmode") === "true";
  if (prefersDark) document.body.classList.add("dark");

  btn.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    localStorage.setItem("darkmode", document.body.classList.contains("dark"));
  });
});
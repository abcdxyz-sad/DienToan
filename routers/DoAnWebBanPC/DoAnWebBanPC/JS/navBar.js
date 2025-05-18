document.addEventListener("DOMContentLoaded", () => {
  const checkbox = document.getElementById("checkbox");
  const darkTheme = document.getElementById("dark-theme");
  const lightTheme = document.getElementById("light-theme");

  // Load saved theme
  const currentTheme = localStorage.getItem("theme");
  if (currentTheme) {
    document.documentElement.setAttribute("data-theme", currentTheme);
    if (currentTheme === "light") {
      checkbox.checked = true;
      darkTheme.disabled = true;
      lightTheme.disabled = false;
    }
  }

  // Theme switch handler
  checkbox.addEventListener("change", function () {
    if (this.checked) {
      // Switch to light theme
      document.documentElement.setAttribute("data-theme", "light");
      localStorage.setItem("theme", "light");
      darkTheme.disabled = true;
      lightTheme.disabled = false;
    } else {
      // Switch to dark theme
      document.documentElement.setAttribute("data-theme", "dark");
      localStorage.setItem("theme", "dark");
      darkTheme.disabled = false;
      lightTheme.disabled = true;
    }
  });
});

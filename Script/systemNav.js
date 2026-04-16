document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");

  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");

      if (sidebar.classList.contains("active")) {
        menuBtn.innerHTML = '<i data-lucide="x" style="color: white;"></i>';
        menuBtn.style.left = "220px";
      } else {
        menuBtn.innerHTML = '<i data-lucide="menu"></i>';
        menuBtn.style.left = "20px";
      }

      if (window.lucide) {
        lucide.createIcons();
      }
    });
  }

  // ✅ LOGOUT (FIXED)
  const logoutBtn = document.querySelector(".logoutBtn");

  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();

      Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the system.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = this.href;
        }
      });
    });
  }

  // initial render
  if (window.lucide) {
    lucide.createIcons();
  }
});
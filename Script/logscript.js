
let apiLog = "/BSIT-2E-G1-Group1-EduTrack/Api/login.php";
let email = $("#email");
let password = $("#password");
let loginBtn = $("#loginBtn");

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true
});

let emailError = $("#emailError");
let passwordError = $("#passwordError");
let loginError = $("#loginError");

let form = $("#loginForm");
let togglePassword = $("#toggleLoginPassword");

function postOne() {
  let payload = {
    email: email.val(),
    password: password.val(),
  };

  $.ajax({
    url: apiLog,
    type: "POST",
    data: "action=postOne&payload=" + JSON.stringify(payload),
    dataType: "json",
    success: function (response) {

      if (response.status == "success") {

        Swal.fire({
          icon: 'success',
          title: 'Welcome back!',
          text: response.message,
          showConfirmButton: false,
          timer: 1800
        });

        form[0].reset();
        loginBtn.prop("disabled", false);

        setTimeout(() => {
          window.location.href =
            "/BSIT-2E-G1-Group1-EduTrack/Html/dashboard.php";
        }, 1800);

      } else {

        Swal.fire({
          icon: 'error',
          title: 'Login Failed',
          text: response.message,
          confirmButtonColor: '#d33'
        });

      }
    },
    error: function () {
      Swal.fire({
        icon: 'error',
        title: 'Server Error',
        text: 'Something went wrong. Please try again.'
      });
    },
  });
}

// VALIDATION FUNCTIONS
function validateEmail() {
  const pattern = /^\S+@\S+\.\S+$/;
  if (email.val().trim() === "") {
    emailError.text("Email is required");
    return false;
  } else if (!pattern.test(email.val().trim())) {
    emailError.text("Invalid email format");
    return false;
  } else {
    emailError.text("");
    return true;
  }
}

function validatePassword() {
  if (password.val().trim() === "") {
    passwordError.text("Password is required");
    return false;
  } else {
    passwordError.text("");
    return true;
  }
}

// ENABLE BUTTON
function checkForm() {
  loginError.text("");
  if (validateEmail() && validatePassword()) {
    loginBtn.prop("disabled", false);
  } else {
    loginBtn.prop("disabled", true);
  }
}

// EVENTS
email.on("blur", validateEmail);
password.on("blur", validatePassword);

email.on("input", checkForm);
password.on("input", checkForm);

// SHOW/HIDE PASSWORD
togglePassword.on("click", function () {
  password.attr(
    "type",
    password.attr("type") === "password" ? "text" : "password",
  );
});

// FORM SUBMIT
form.on("submit", function (e) {
  e.preventDefault();
  if (validateEmail() && validatePassword()) {
    if (email.val() != "" && password.val() != "") {
      postOne();
      
    }
  }
});

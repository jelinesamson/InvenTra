const API = "/BSIT-2E-G1-Group1-EduTrack/Api/register.php";

// ELEMENTS
let registerform = $("#registerForm");
let firstName = $("#firstName");
let lastName = $("#lastName");
let regemail = $("#regemail");
let regpassword = $("#regpassword");
let confirmPassword = $("#confirmPassword");
let registerBtn = $("#registerBtn");

let toggleSPassword = $("#toggleSignupPassword");
let toggleCPassword = $("#toggleConfirmPassword");

// PATTERNS
const namePattern = /^[A-Za-z\s]{3,}$/;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

// STORE FUNCTION
function store() {
  let payload = {
    firstName: firstName.val().trim(),
    lastName: lastName.val().trim(),
    regemail: regemail.val().trim(),
    regpassword: regpassword.val().trim(),
  };

  $.ajax({
    url: API,
    type: "POST",
    dataType: "json",
    data: {
      action: "store",
      payload: JSON.stringify(payload),
    },
    success: function (response) {
      Swal.fire({
        icon: response.status === "success" ? "success" : "error",
        title: response.status === "success" ? "Success!" : "Error!",
        text: response.message,
        confirmButtonColor: "#3085d6",
      }).then(() => {

      if (response.status === "success") {
        registerform[0].reset();
        window.location.href = "/BSIT-2E-G1-Group1-EduTrack/index.php";
      }else {
        registerBtn.prop("disabled", false).text("Register");
       }
      });
    },
    error: function (error) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
        confirmButtonColor: "#d33",
      });
      registerBtn.prop("disabled", false).text("Register");
    },
  });
}

// PASSWORD TOGGLE
toggleSPassword.on("click", function () {
  regpassword.attr(
    "type",
    regpassword.attr("type") === "password" ? "text" : "password",
  );
});

toggleCPassword.on("click", function () {
  confirmPassword.attr(
    "type",
    confirmPassword.attr("type") === "password" ? "text" : "password",
  );
});

// ERROR HANDLING
function getErrorElement(input) {
  switch (input.attr("id")) {
    case "firstName":
      return $("#firstNameError");
    case "lastName":
      return $("#lastNameError");
    case "regemail":
      return $("#signupEmailErr");
    case "regpassword":
      return $("#signupPassErr");
    case "confirmPassword":
      return $("#confirmPassErr");
  }
}

function setError(input, message) {
  let error = getErrorElement(input);
  if (error) error.text(message);
  input.css("border", "1px solid red");
}

function setSuccess(input) {
  let error = getErrorElement(input);
  if (error) error.text("");
  input.css("border", "1px solid green");
}

// VALIDATION
function validateFirstName() {
  let value = firstName.val().trim();
  if (value === "") {
    setError(firstName, "First name is required");
    return false;
  } else if (!namePattern.test(value)) {
    setError(firstName, "At least 3 letters only");
    return false;
  } else {
    setSuccess(firstName);
    return true;
  }
}

function validateLastName() {
  let value = lastName.val().trim();
  if (value === "") {
    setError(lastName, "Last name is required");
    return false;
  } else if (!namePattern.test(value)) {
    setError(lastName, "At least 3 letters only");
    return false;
  } else {
    setSuccess(lastName);
    return true;
  }
}

function validateregEmail() {
  let value = regemail.val().trim();
  if (value === "") {
    setError(regemail, "Email is required");
    return false;
  } else if (!emailPattern.test(value)) {
    setError(regemail, "Invalid email format");
    return false;
  } else {
    setSuccess(regemail);
    return true;
  }
}

function validateregPassword() {
  let value = regpassword.val().trim();
  if (value === "") {
    setError(regpassword, "Password is required");
    return false;
  } else if (!passwordPattern.test(value)) {
    setError(regpassword, "Must be 8 chars, A-Z, a-z, 0-9");
    return false;
  } else {
    setSuccess(regpassword);
    return true;
  }
}

function validateConfirmPassword() {
  let value = confirmPassword.val().trim();
  if (value === "") {
    setError(confirmPassword, "Confirm your password");
    return false;
  } else if (value !== regpassword.val()) {
    setError(confirmPassword, "Passwords do not match");
    return false;
  } else {
    setSuccess(confirmPassword);
    return true;
  }
}

function validateInputs() {
  return (
    validateFirstName() &&
    validateLastName() &&
    validateregEmail() &&
    validateregPassword() &&
    validateConfirmPassword()
  );
}


function checkForm() {
  if (validateInputs()) {
    registerBtn.prop("disabled", false);
  } else {
    registerBtn.prop("disabled", true);
  }
}
firstName.on("blur", validateFirstName);
lastName.on("blur", validateLastName);
regemail.on("blur", validateregEmail);
regpassword.on("blur", validateregPassword);
confirmPassword.on("blur", validateConfirmPassword);

firstName.on("input", checkForm);
lastName.on("input", checkForm);
regemail.on("input", checkForm);
regpassword.on("input", checkForm);
confirmPassword.on("input", checkForm);
registerform.on("submit", function (e) {
  e.preventDefault();

  if (validateInputs()) {
    store();
    registerform[0].reset();
    registerBtn.prop("disabled", true);
  }
});

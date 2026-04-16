   <nav class="navbar shadow-sm p-3 navbar-expand-lg nav-custom fixed-top">
      <div class="container-fluid">
        <a class="nav-tittle fw-bold" href="#">InvenTra</a>

        <div class="d-flex d-lg-none ms-auto align-items-center gap-2">
          <button
            type="button"
            class="btn btn-outline-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#loginModal"
          >
            Log In
          </button>
          <button
            class="btn btn-outline-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#signupModal"
          >
            Sign Up
          </button>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav mx-auto gap-lg-5">
            
            <li class="nav-item">
              <a class="nav-link <?php if($currentPage == 'index.php') echo 'active'; ?>" 
              href="/BSIT-2E-G1-Group1-EduTrack/index.php">Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php if($currentPage == 'about.php') echo 'active'; ?>" 
              href="/BSIT-2E-G1-Group1-EduTrack/Html/about.php">About</a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php if($currentPage == 'services.php') echo 'active'; ?>" 
              href="/BSIT-2E-G1-Group1-EduTrack/Html/services.php">Services</a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php if($currentPage == 'gallery.php') echo 'active'; ?>" 
              href="/BSIT-2E-G1-Group1-EduTrack/Html/gallery.php">Gallery</a>
            </li>

            <li class="nav-item">
              <a class="nav-link <?php if($currentPage == 'contact.php') echo 'active'; ?>" 
              href="/BSIT-2E-G1-Group1-EduTrack/Html/contact.php">Contact</a>
            </li>

          </ul>
        </div>

        <div class="d-none d-lg-flex ms-lg-auto gap-2">
          <button
            type="button"
            class="btn btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#loginModal"
          >
            Log In
          </button>
          <button
            type="button"
            class="btn btn-outline-success"
            data-bs-toggle="modal"
            data-bs-target="#signupModal"
          >
            Sign Up
          </button>
        </div>
      </div>
    </nav>
<!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered glass-modal-dialog">
        <div class="modal-content glass-modal">
          <div class="modal-header border-0">
            <h5 class="modal-title w-100 text-center">Log In</h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
            ></button>
          </div>
          
        <div class="form-card">
  
            <form id="loginForm">

              <div class="modal-body">
                  <label class="form-label fw-bold">Email</label>
                  <input
                    type="email"
                    class="form-control mt-1"
                    placeholder="E-mail"
                    id="email"
                  />

                  <small class="text-danger" id="emailError"></small>
                <br />

                <div class="input-wrapper">
                  <label class="form-label fw-bold">Password</label>
                  <input
                    type="password"
                    class="form-control mt-1"
                    placeholder="Password"
                    id="password"
                  />
                  <span class="toggleEye" id="toggleLoginPassword">
                      <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                        <path
                          d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z"
                          fill="#555"
                        />
                      </svg>
                  </span>
                </div>

                  <small id="passwordError" class="text-danger"></small>
                  <small id="loginError" class="text-danger"></small>
                <br>

                <button type="submit" id="loginBtn" class="btnlog" disabled>
                  Log In
                </button>

            </form>
                <p class="small text-center">
                  Don't have an account yet?
                  <a
                    href="#"
                    class="text-info"
                    data-bs-toggle="modal"
                    data-bs-target="#signupModal"
                    data-bs-dismiss="modal"
                    >Register here...</a
                  >
                </p>
        </div>
      
          </div>
        </div>
      </div>
    </div>
    
<!-- Sign Up Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered glass-modal-dialog">
        <div class="modal-content glass-modal">
          <div class="modal-header border-0">
            <h5 class="modal-title w-100 text-center">Sign Up</h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              data-bs-dismiss="modal"
            ></button>
          </div>

        <form id="registerForm" novalidate>  
          <div class="modal-body">
            <label class="form-label fw-bold">First Name</label>
            <input
              type="text"
              class="form-control mt-1"
              placeholder="First Name"
              id="firstName"
            />
            <small id="firstNameError" class="text-danger"></small>
            <br />

            <label class="form-label fw-bold">Last Name</label>
            <input
              type="text"
              class="form-control mt-1"
              placeholder="Last Name"
              id="lastName"
            />
            <small id="lastNameError" class="text-danger"></small>
            <br />

            <label class="form-label fw-bold">Email</label>
            <input
              type="email"
              class="form-control mt-1"
              placeholder="E-mail"
              id="regemail"
            />

            <small id="signupEmailErr" class="text-danger"></small>
            <br />

            <div class="input-wrapper">
              <label class="form-label fw-bold">Password</label>
              <input
                type="password"
                class="form-control mt-1"
                placeholder="Password"
                id="regpassword"
              />
              <span class="toggleEye" id="toggleSignupPassword">
                        <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                          <path
                            d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z"
                            fill="#555"
                          />
                        </svg>
              </span>
            </div>

            <small id="signupPassErr" class="text-danger"></small>
            <br />

            <div class="input-wrapper">
              <label class="form-label fw-bold">Confirm Password</label>
              <input
                type="password"
                class="form-control mt-1"
                placeholder="Confirm Password"
                id="confirmPassword"
              />
              <span class="toggleEye" id="toggleConfirmPassword">
                        <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                          <path
                            d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z"
                            fill="#555"
                          />
                        </svg>
              </span>
            </div>

            <small id="confirmPassErr" class="text-danger"></small>
            <br />

            <p class="small text-center">
              Already have an account?
              <a
                href="#"
                class="text-info"
                data-bs-toggle="modal"
                data-bs-target="#loginModal"
                data-bs-dismiss="modal"
                >Log in here...</a
              >
            </p>

            <button
              id="registerBtn"
              class="btn btn-success w-100 mb-2"
            >
              Sign Up
            </button>

          
          </div>
          </form> 
        </div>
      </div>
    </div>
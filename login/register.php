<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #D19C97;
            border-color: #D19C97;
            color: #fff;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #b77a7a;
            border-color: #b77a7a;
        }

        .highlight {
            color: #D19C97;
            transition: color 0.3s;
        }

        .highlight:hover {
            color: #b77a7a;
        }

        body {
            /* Base background color */
            background-color: #f8f9fa;

            /* Gradient-like grid using repeating-linear-gradients */
            background-image:
                repeating-linear-gradient(0deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                repeating-linear-gradient(90deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                linear-gradient(rgba(183, 122, 122, 0.1),
                    rgba(183, 122, 122, 0.1));

            /* Blend the gradients for a subtle overlay effect */
            background-blend-mode: overlay;

            /* Define the size of the grid */
            background-size: 20px 20px;

            /* Ensure the background covers the entire viewport */
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .register-container {
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #D19C97;
            color: #fff;
        }

        .custom-radio .form-check-input:checked+.form-check-label::before {
            background-color: #D19C97;
            border-color: #D19C97;
        }

        .form-check-label {
            position: relative;
            padding-left: 2rem;
            cursor: pointer;
        }

        .form-check-label::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            border: 2px solid #D19C97;
            border-radius: 50%;
            background-color: #fff;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-check-input:focus+.form-check-label::before {
            box-shadow: 0 0 0 0.2rem rgba(209, 156, 151, 0.5);
        }

        .animate-pulse-custom {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="container register-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mt-4" id="registerForm" novalidate>
                            <div id="regMessage" class="mb-3" style="color:crimson;"></div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full name <i class="fa fa-user"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="full_name" name="full_name" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control animate__animated animate__fadeInUp" id="email" name="email" required maxlength="150">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control animate__animated animate__fadeInUp" id="password" name="password" required minlength="6" maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control animate__animated animate__fadeInUp" id="confirm_password" name="confirm_password" required minlength="6" maxlength="100">
                            </div>

                            <div class="mb-3">
    <label for="country" class="form-label">Country <i class="fa fa-globe"></i></label>
    <select class="form-select animate__animated animate__fadeInUp" id="country" name="country" required>
        <option value="">Select a country</option>
        <option value="Afghanistan">Afghanistan</option>
        <option value="Albania">Albania</option>
        <option value="Algeria">Algeria</option>
        <option value="Angola">Angola</option>
        <option value="Argentina">Argentina</option>
        <option value="Australia">Australia</option>
        <option value="Austria">Austria</option>
        <option value="Bangladesh">Bangladesh</option>
        <option value="Belgium">Belgium</option>
        <option value="Benin">Benin</option>
        <option value="Bolivia">Bolivia</option>
        <option value="Botswana">Botswana</option>
        <option value="Brazil">Brazil</option>
        <option value="Bulgaria">Bulgaria</option>
        <option value="Burkina Faso">Burkina Faso</option>
        <option value="Burundi">Burundi</option>
        <option value="Cambodia">Cambodia</option>
        <option value="Cameroon">Cameroon</option>
        <option value="Canada">Canada</option>
        <option value="Chile">Chile</option>
        <option value="China">China</option>
        <option value="Colombia">Colombia</option>
        <option value="Costa Rica">Costa Rica</option>
        <option value="Croatia">Croatia</option>
        <option value="Cuba">Cuba</option>
        <option value="Czech Republic">Czech Republic</option>
        <option value="Denmark">Denmark</option>
        <option value="Dominican Republic">Dominican Republic</option>
        <option value="DR Congo">DR Congo</option>
        <option value="Ecuador">Ecuador</option>
        <option value="Egypt">Egypt</option>
        <option value="Ethiopia">Ethiopia</option>
        <option value="Fiji">Fiji</option>
        <option value="Finland">Finland</option>
        <option value="France">France</option>
        <option value="Gabon">Gabon</option>
        <option value="Gambia">Gambia</option>
        <option value="Georgia">Georgia</option>
        <option value="Germany">Germany</option>
        <option value="Ghana">Ghana</option>
        <option value="Greece">Greece</option>
        <option value="Guatemala">Guatemala</option>
        <option value="Guinea">Guinea</option>
        <option value="Haiti">Haiti</option>
        <option value="Honduras">Honduras</option>
        <option value="Hungary">Hungary</option>
        <option value="Iceland">Iceland</option>
        <option value="India">India</option>
        <option value="Indonesia">Indonesia</option>
        <option value="Iran">Iran</option>
        <option value="Iraq">Iraq</option>
        <option value="Ireland">Ireland</option>
        <option value="Israel">Israel</option>
        <option value="Italy">Italy</option>
        <option value="Ivory Coast">Ivory Coast</option>
        <option value="Jamaica">Jamaica</option>
        <option value="Japan">Japan</option>
        <option value="Jordan">Jordan</option>
        <option value="Kazakhstan">Kazakhstan</option>
        <option value="Kenya">Kenya</option>
        <option value="Kuwait">Kuwait</option>
        <option value="Kyrgyzstan">Kyrgyzstan</option>
        <option value="Laos">Laos</option>
        <option value="Latvia">Latvia</option>
        <option value="Lebanon">Lebanon</option>
        <option value="Lesotho">Lesotho</option>
        <option value="Liberia">Liberia</option>
        <option value="Libya">Libya</option>
        <option value="Lithuania">Lithuania</option>
        <option value="Luxembourg">Luxembourg</option>
        <option value="Madagascar">Madagascar</option>
        <option value="Malawi">Malawi</option>
        <option value="Malaysia">Malaysia</option>
        <option value="Mali">Mali</option>
        <option value="Mauritania">Mauritania</option>
        <option value="Mauritius">Mauritius</option>
        <option value="Mexico">Mexico</option>
        <option value="Moldova">Moldova</option>
        <option value="Mongolia">Mongolia</option>
        <option value="Montenegro">Montenegro</option>
        <option value="Morocco">Morocco</option>
        <option value="Mozambique">Mozambique</option>
        <option value="Myanmar">Myanmar</option>
        <option value="Namibia">Namibia</option>
        <option value="Nepal">Nepal</option>
        <option value="Netherlands">Netherlands</option>
        <option value="New Zealand">New Zealand</option>
        <option value="Nicaragua">Nicaragua</option>
        <option value="Niger">Niger</option>
        <option value="Nigeria">Nigeria</option>
        <option value="North Korea">North Korea</option>
        <option value="Norway">Norway</option>
        <option value="Oman">Oman</option>
        <option value="Pakistan">Pakistan</option>
        <option value="Panama">Panama</option>
        <option value="Paraguay">Paraguay</option>
        <option value="Peru">Peru</option>
        <option value="Philippines">Philippines</option>
        <option value="Poland">Poland</option>
        <option value="Portugal">Portugal</option>
        <option value="Qatar">Qatar</option>
        <option value="Romania">Romania</option>
        <option value="Russia">Russia</option>
        <option value="Rwanda">Rwanda</option>
        <option value="Saudi Arabia">Saudi Arabia</option>
        <option value="Senegal">Senegal</option>
        <option value="Serbia">Serbia</option>
        <option value="Sierra Leone">Sierra Leone</option>
        <option value="Singapore">Singapore</option>
        <option value="Slovakia">Slovakia</option>
        <option value="Slovenia">Slovenia</option>
        <option value="Somalia">Somalia</option>
        <option value="South Africa">South Africa</option>
        <option value="South Korea">South Korea</option>
        <option value="Spain">Spain</option>
        <option value="Sri Lanka">Sri Lanka</option>
        <option value="Sudan">Sudan</option>
        <option value="Sweden">Sweden</option>
        <option value="Switzerland">Switzerland</option>
        <option value="Syria">Syria</option>
        <option value="Tanzania">Tanzania</option>
        <option value="Thailand">Thailand</option>
        <option value="Togo">Togo</option>
        <option value="Tunisia">Tunisia</option>
        <option value="Turkey">Turkey</option>
        <option value="Uganda">Uganda</option>
        <option value="Ukraine">Ukraine</option>
        <option value="United Arab Emirates">United Arab Emirates</option>
        <option value="United Kingdom">United Kingdom</option>
        <option value="United States">United States</option>
        <option value="Uruguay">Uruguay</option>
        <option value="Uzbekistan">Uzbekistan</option>
        <option value="Venezuela">Venezuela</option>
        <option value="Vietnam">Vietnam</option>
        <option value="Yemen">Yemen</option>
        <option value="Zambia">Zambia</option>
        <option value="Zimbabwe">Zimbabwe</option>
    </select>
</div>


                            <div class="mb-3">
                                <label for="city" class="form-label">City <i class="fa fa-city"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="city" name="city" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number <i class="fa fa-phone"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="contact_number" name="contact_number" required maxlength="20" pattern="[0-9+\-\s]{6,20}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Register As</label>
                                <div class="d-flex justify-content-start">
                                    <div class="form-check me-3 custom-radio">
                                        <input class="form-check-input" type="radio" name="user_role" id="customer" value="2" checked>
                                        <label class="form-check-label" for="customer">Customer</label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="user_role" id="owner" value="3">
                                        <label class="form-check-label" for="owner">Restaurant Owner</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="registerBtn" class="btn btn-custom w-100 animate-pulse-custom">
                                <span id="btnSpinner" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display:none;"></span>
                                <span id="btnText">Register</span>
                            </button>
                        </form>
                    </div>
                    <div the="card-footer text-center">
                        Already have an account? <a href="login.php" class="highlight">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../js/register.js"></script>
</body>

</html>

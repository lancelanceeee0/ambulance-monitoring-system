
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f7f7f2;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .register-container {
            width: 90%;
            max-width: 1050px;
            min-height: 600px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
        }

        .welcome-section,
        .register-section {
            padding: 60px;
        }

        .welcome-section {
            width: 60%;
            background: linear-gradient(135deg, #f6b0be, #4078a8);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-section h1 {
            font-size: 42px;
            margin: 0 0 15px;
        }

        .welcome-section p {
            font-size: 19px;
            line-height: 1.5;
            max-width: 450px;
        }

        .register-section {
            width: 40%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-section h2 {
            font-size: 36px;
            color: #444;
            margin: 0 0 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #bd467b;
        }

        button {
            margin-top: 10px;
            padding: 14px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(90deg, #bd466e, #6a96d8);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            opacity: .9;
        }

        .login-link {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #539dc4;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: -8px;
            font-size: 13px;
        }

        .strength-bar {
            height: 6px;
            background: #e5e5e5;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 6px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            background: #e53935;
            transition: width 0.2s;
        }

        .strength-text {
            margin-top: 5px;
            font-weight: 600;
        }

        .strength-weak {
            color: #e53935;
        }

        .strength-okay {
            color: #f39c12;
        }

        .strength-strong {
            color: #2e9d50;
        }

        @media (max-width: 750px) {
            .register-container {
                flex-direction: column;
            }

            .welcome-section,
            .register-section {
                width: 100%;
                padding: 45px 35px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">

        <div class="welcome-section">
            <h1>Welcome!</h1>
            <p>Create your account and join us. Fill in your information to get started.</p>
        </div>

        <div class="register-section">
            <h2>Create Account</h2>

            @if ($errors->any())
                <div style="color: red; font-size: 14px; margin-bottom: 15px;">
                    <ul style="padding-left: 20px; margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST">
                @csrf

                <input
                    type="text"
                    name="full_name"
                    placeholder="Full Name"
                    value="{{ old('full_name') }}"
                >

                <input
                    type="text"
                    name="name"
                    placeholder="Username (3-10 characters)"
                    value="{{ old('name') }}"
                >

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="{{ old('email') }}"
                >

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Password"
                >

                <div class="password-strength">
                    <div class="strength-bar">
                        <div id="strengthFill" class="strength-fill"></div>
                    </div>
                    <div id="strengthText" class="strength-text" aria-live="polite"></div>
                </div>

                <small style="color: #666;">
                    At least 8 characters with an uppercase letter, a lowercase letter, a number, and a special character.
                </small>

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm Password"
                >

                <button type="submit">Register</button>
            </form>

            <div class="login-link">
                Already have an account?
                <a href="/">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function () {
            const password = this.value;

            let score = 0;

            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            strengthFill.style.width = '0%';
            strengthFill.style.backgroundColor = '#e53935';
            strengthText.className = 'strength-text';

            if (password.length === 0) {
                strengthText.textContent = '';
            } else if (score <= 2) {
                strengthFill.style.width = '33%';
                strengthFill.style.backgroundColor = '#e53935';
                strengthText.textContent = 'Weak';
                strengthText.classList.add('strength-weak');
            } else if (score <= 4) {
                strengthFill.style.width = '66%';
                strengthFill.style.backgroundColor = '#f39c12';
                strengthText.textContent = 'Okay';
                strengthText.classList.add('strength-okay');
            } else {
                strengthFill.style.width = '100%';
                strengthFill.style.backgroundColor = '#2e9d50';
                strengthText.textContent = 'Strong';
                strengthText.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>
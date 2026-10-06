<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

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

        .login-container {
            width: 90%;
            max-width: 1050px;
            min-height: 600px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
        }

        .welcome-section {
            width: 60%;
            padding: 60px;
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

        .login-section {
            width: 40%;
            padding: 60px;
            background: #ffffff; /* Fixed invalid hex code */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-section h2 {
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

        .register-link {
            margin-top: 25px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #539dc4;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 750px) {
            .login-container {
                flex-direction: column;
            }

            .welcome-section,
            .login-section {
                width: 100%;
                padding: 45px 35px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="welcome-section">
            <h1>Welcome back!</h1>
            <p>You can log in to access with your existing account.</p>
        </div>

        <div class="login-section">
            @auth
                <h2>Logged In!</h2>
                <p>Welcome, <strong>{{ auth()->user()->full_name }}</strong>!</p>
                <p><a href="/dispatch">Go to Dispatch</a></p>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            @else
                <h2>Log In</h2>

                @if ($errors->any())
                    <div style="color: red; font-size: 14px; margin-bottom: 15px;">
                        <ul style="padding-left: 20px; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf

                    <input type="text" name="loginname" placeholder="Username">

                    <input type="password" name="loginpassword" placeholder="Password">

                    <button type="submit">Log In</button>
                </form>

                <div class="register-link">
                    New here?
                    <a href="/register">Create an Account</a>
                </div>
            @endauth
        </div>

    </div>

</body>
</html>
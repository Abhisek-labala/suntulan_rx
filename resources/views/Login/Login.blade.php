@include('Login.header')

<!-- Main Wrapper -->
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="left">
                <img class="img-fluid" src="{{ asset('uploads/logo/Suntulan_logo.png') }}" alt="Logo">
                <h1>Hello <br><strong>Welcome!</strong></h1>
            </div>

            <div class="right">
                <h2>Login</h2>
                <p class="account-subtitle">Access to our dashboard</p>

                <form id="loginForm">
                    @csrf

                    <div class="input-box">
                        <input class="form-control" id="username" type="text" name="username"
                               placeholder="Username / EMP ID" required>
                    </div>

                    <div class="input-box">
                        <input class="form-control" id="password" type="password"
                               name="password" placeholder="Password" required>

                        <span class="toggle-password" onclick="togglePassword()">
                            👁️
                        </span>
                    </div>

                    <div class="input-box">
                        <button id="loginButton" class="login-btn w-100" type="submit">
                            Login
                        </button>

                        <div class="dots">● ● ●</div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-credit">
        Designed &amp; Developed by
        <a href="https://nltechsolutions.in" target="_blank">
            NL Tech Solutions
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@include('Login.footer')
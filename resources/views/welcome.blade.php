<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUNTULAN | For a Balanced Metabolic Journey</title>
    <link rel="icon" href="{{ asset('uploads/logo/Suntulan_logo.png') }}" type="image/png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --burnt-sienna: #AE3B26;
            --orange: #F2851D;
            --tagline-gray: #7d7d7d;
            --white: #ffffff;
            --font-main: 'Montserrat', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--white);
            color: #333;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }

        /* Background Shapes */
        .bg-shape {
            position: fixed;
            border-radius: 50%;
            z-index: 1;
        }

        .shape-top-right {
            width: 55vh;
            height: 55vh;
            background-color: var(--burnt-sienna);
            top: -15vh;
            right: -10vh;
        }

        .shape-bottom-left {
            width: 45vh;
            height: 45vh;
            background-color: var(--orange);
            bottom: -15vh;
            left: -10vh;
        }

        /* Main Content Container */
        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 100%;
            max-width: 1100px;
            padding: 20px;
            margin-top: -50px; /* Offset branding slightly up */
        }

        /* Branding Section */
        .branding {
            margin-bottom: 50px;
        }

        .logo-img {
            max-width: 800px;
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.02);
        }

        .tagline {
            font-size: 44px;
            color: var(--tagline-gray);
            font-weight: 300;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-btn {
            width: 130px;
            height: 130px;
            background: linear-gradient(135deg, #bf4d39 0%, #ae3b26 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            padding: 15px;
            line-height: 1.2;
            box-shadow: 0 8px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            border: 4px solid #fff;
            position: relative;
        }

        /* White inner-like drop shadow/glow from the image */
        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 50%;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.3);
            pointer-events: none;
        }

        .nav-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 25px rgba(0,0,0,0.4);
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .logo-img { max-width: 450px; }
            .tagline { font-size: 28px; }
            .nav-btn { width: 110px; height: 110px; font-size: 13px; }
            .branding { margin-bottom: 50px; }
            .shape-top-right {
                width: 25vh;
                height: 25vh;
                top: -8vh;
                right: -8vh;
            }
            .shape-bottom-left {
                width: 20vh;
                height: 25vh;
                bottom: -8vh;
                left: -8vh;
            }
        }

        @media (max-width: 480px) {
            body { 
                overflow-y: auto; 
                height: auto; 
                min-height: 100vh;
                padding: 40px 0;
            }
            .container {
                margin-top: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .shape-top-right {
                width: 20vh;
                height: 20vh;
                top: -6vh;
                right: -6vh;
            }
            .shape-bottom-left {
                width: 15vh;
                height: 15vh;
                bottom: -6vh;
                left: -6vh;
            }
            .logo-img { max-width: 350px; }
            .tagline { font-size: 18px; line-height: 1.3; }
            .branding { margin-bottom: 40px; }
            .nav-buttons { gap: 15px; padding: 0 10px; }
            .nav-btn { width: 95px; height: 95px; font-size: 11px; padding: 8px; border-width: 3px; }
        }
    </style>
</head>
<body>

    <!-- Background Shapes -->
    <div class="bg-shape shape-top-right"></div>
    <div class="bg-shape shape-bottom-left"></div>

    <div class="container">
        <!-- Logo and Branding -->
        <div class="branding">
            <img class="logo-img" src="{{ asset('uploads/logo/Suntulan_logo.png') }}" alt="SANTULAN">
        </div>

        <!-- Navigation circles -->
        <div class="nav-buttons">
            <a href="{{ url('/login') }}" class="nav-btn">Sales Team</a>
            <a href="{{ url('/login') }}" class="nav-btn">ADMIN</a>
        </div>
    </div>

</body>
</html>

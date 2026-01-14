<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pokédex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }

        /* Background with the provided image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpH0Fz3YgjYLATFkcMD_0r-S_qyl-hpkO9HU_u5dTOuY-6Zt_y7yrerHjj&s=10');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.7);
            z-index: -2;
        }

        /* Gradient overlay for better contrast */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.7) 0%, rgba(30, 58, 138, 0.7) 100%);
            z-index: -1;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 18px;
            padding: 30px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            text-align: center;
            margin: 0 auto;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #FF0000, #FFCC00, #3B4CCA);
        }

        .pokeball {
            width: 90px;
            height: 90px;
            margin: 0 auto 18px;
            background: linear-gradient(#FF0000 50%, white 50%);
            border-radius: 50%;
            border: 6px solid #333;
            position: relative;
            animation: float 3s ease-in-out infinite;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .pokeball:before {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: #333;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 6px solid white;
            z-index: 2;
        }

        .pokeball:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 6px;
            background: #333;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }

        h1 {
            color: #333;
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: bold;
            background: linear-gradient(to right, #FF0000, #FFCC00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .welcome-text {
            color: #666;
            margin-bottom: 22px;
            font-size: 14px;
            text-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: bold;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
        }

        input:focus {
            outline: none;
            border-color: #FF0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
            background: white;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
            z-index: -1;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(255, 0, 0, 0.2);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .register-link {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #FF0000;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
            position: relative;
        }

        .register-link a:hover {
            color: #CC0000;
            transform: translateX(5px);
        }

        .demo-accounts {
            margin-top: 18px;
            padding: 12px;
            background: #FFF3E0;
            border-radius: 8px;
            text-align: left;
            font-size: 13px;
            border: 1px solid #FFE0B2;
        }

        .demo-accounts h4 {
            color: #F57C00;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .demo-accounts ul {
            padding-left: 18px;
            color: #666;
        }

        .demo-accounts li {
            margin-bottom: 5px;
            font-family: monospace;
            font-size: 12px;
            position: relative;
        }

        .demo-accounts li::before {
            content: '▶';
            color: #FF0000;
            font-size: 8px;
            position: absolute;
            left: -12px;
            top: 2px;
        }

        .error-message {
            background: #FFEBEE;
            color: #D32F2F;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: left;
            font-size: 13px;
            border-left: 4px solid #D32F2F;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .success-message {
            background: #E8F5E9;
            color: #2E7D32;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: left;
            font-size: 13px;
            border-left: 4px solid #2E7D32;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        /* Floating Pokémon icons */
        .floating-icon {
            position: fixed;
            font-size: 1.5rem;
            opacity: 0.1;
            z-index: 0;
            animation: floatAround 20s infinite linear;
            pointer-events: none;
        }

        .icon-1 {
            top: 10%;
            left: 5%;
            color: #FFCC00;
            animation-delay: 0s;
        }

        .icon-2 {
            top: 20%;
            right: 8%;
            color: #FF6600;
            animation-delay: 5s;
        }

        .icon-3 {
            bottom: 15%;
            left: 7%;
            color: #4A90E2;
            animation-delay: 10s;
        }

        .icon-4 {
            bottom: 25%;
            right: 5%;
            color: #78C850;
            animation-delay: 15s;
        }

        @keyframes floatAround {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -20px) rotate(90deg); }
            50% { transform: translate(40px, 0) rotate(180deg); }
            75% { transform: translate(20px, 20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                padding: 25px 18px;
                max-width: 340px;
            }
            
            .pokeball {
                width: 80px;
                height: 80px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .floating-icon {
                display: none;
            }
        }

        .pikachu-ears {
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 15px;
            background: #FFCC00;
            border-radius: 50% 50% 0 0;
            z-index: 1;
        }

        .pikachu-ears:before,
        .pikachu-ears:after {
            content: '';
            position: absolute;
            width: 12px;
            height: 15px;
            background: #FFCC00;
            border-radius: 50%;
            top: -7px;
        }

        .pikachu-ears:before {
            left: 4px;
        }

        .pikachu-ears:after {
            right: 4px;
        }
    </style>
</head>
<body>
    <!-- Floating background icons -->
    <div class="floating-icon icon-1">
        <i class="fas fa-bolt"></i>
    </div>
    <div class="floating-icon icon-2">
        <i class="fas fa-fire"></i>
    </div>
    <div class="floating-icon icon-3">
        <i class="fas fa-tint"></i>
    </div>
    <div class="floating-icon icon-4">
        <i class="fas fa-leaf"></i>
    </div>

    <div class="login-container">
        <div class="pokeball">
            <div class="pikachu-ears"></div>
        </div>
        
        <h1>Pokédex Login</h1>
        <p class="welcome-text">Welcome back, Trainer! Ready to catch 'em all?</p>

        @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="email" name="email" id="email" required 
                           placeholder="trainer@pokedex.com" value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" id="password" required 
                           placeholder="Enter your password">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> LOGIN TO POKÉDEX
            </button>
        </form>

        <div class="demo-accounts">
            <h4><i class="fas fa-info-circle"></i> Demo Accounts</h4>
            <ul>
                <li><strong>ash@pokedex.com</strong> / pikachu123</li>
                <li><strong>misty@pokedex.com</strong> / starmie456</li>
                <li><strong>brock@pokedex.com</strong> / onix789</li>
            </ul>
        </div>

        <div class="register-link">
            <a href="{{ route('register') }}">
                <i class="fas fa-user-plus"></i> New Trainer? Register here
            </a>
        </div>
    </div>

    <script>
        // Enhanced pokeball interaction
        const pokeball = document.querySelector('.pokeball');
        pokeball.addEventListener('click', function() {
            this.style.animation = 'none';
            this.style.transform = 'rotate(360deg) scale(1.2)';
            this.style.transition = 'transform 0.6s';
            
            // Add sparkle effect
            const sparkle = document.createElement('div');
            sparkle.style.cssText = `
                position: absolute;
                width: 100%;
                height: 100%;
                background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, transparent 70%);
                border-radius: 50%;
                animation: sparkle 0.6s;
                z-index: 1;
            `;
            this.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
                this.style.animation = 'float 3s ease-in-out infinite';
                this.style.transform = 'rotate(0deg) scale(1)';
            }, 600);
        });

        // Add CSS for sparkle animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0% { transform: scale(0); opacity: 1; }
                100% { transform: scale(1.5); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Add focus effects to inputs
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.parentElement.querySelector('i');
                icon.style.color = '#FF0000';
                icon.style.transform = 'translateY(-50%) scale(1.2)';
            });
            
            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('i');
                icon.style.color = '#999';
                icon.style.transform = 'translateY(-50%) scale(1)';
            });
        });

        // Add subtle background animation
        document.body.addEventListener('mousemove', (e) => {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            document.body.style.backgroundPosition = `${x * 20}px ${y * 20}px`;
        });
    </script>
</body>
</html>
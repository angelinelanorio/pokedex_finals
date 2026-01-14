<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pokédex</title>
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
            padding: 15px;
            position: relative;
            overflow-x: hidden;
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
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.8) 0%, rgba(220, 20, 60, 0.8) 100%);
            z-index: -1;
        }

        .register-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .register-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #3B4CCA, #FFCC00, #FF0000);
        }

        .header {
            text-align: center;
            margin-bottom: 22px;
            position: relative;
        }

        .pokeball {
            width: 70px;
            height: 70px;
            margin: 0 auto 12px;
            background: linear-gradient(#FF0000 50%, white 50%);
            border-radius: 50%;
            border: 5px solid #333;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            animation: gentleBounce 4s ease-in-out infinite;
        }

        @keyframes gentleBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .pokeball:before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background: #333;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid white;
            z-index: 2;
        }

        .pokeball:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 5px;
            background: #333;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }

        h1 {
            color: #333;
            margin-bottom: 8px;
            font-size: 24px;
            background: linear-gradient(to right, #3B4CCA, #FF0000);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .subtitle {
            color: #666;
            margin-bottom: 22px;
            font-size: 14px;
            text-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .form-row {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
            flex: 1;
            position: relative;
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
            transition: all 0.3s;
            z-index: 1;
        }

        input, select {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
            position: relative;
        }

        select {
            padding-left: 12px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #3B4CCA;
            box-shadow: 0 0 0 3px rgba(59, 76, 202, 0.1);
            background: white;
        }

        small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-top: 6px;
            padding-left: 4px;
        }

        .btn {
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
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
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

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(255, 0, 0, 0.2);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .error {
            background: #FFEBEE;
            color: #D32F2F;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            border-left: 4px solid #D32F2F;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .success {
            background: #E8F5E9;
            color: #2E7D32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            border-left: 4px solid #2E7D32;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .login-link {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #FF0000;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
            position: relative;
        }

        .login-link a:hover {
            color: #CC0000;
            transform: translateX(5px);
        }

        /* Floating Pokémon elements */
        .floating-pokemon {
            position: fixed;
            font-size: 1.3rem;
            opacity: 0.15;
            z-index: 0;
            animation: floatAround 25s infinite linear;
            pointer-events: none;
        }

        .pokemon-1 {
            top: 15%;
            left: 8%;
            color: #FFCC00;
            animation-delay: 0s;
        }

        .pokemon-2 {
            top: 25%;
            right: 10%;
            color: #FF6600;
            animation-delay: 7s;
        }

        .pokemon-3 {
            bottom: 20%;
            left: 12%;
            color: #4A90E2;
            animation-delay: 14s;
        }

        .pokemon-4 {
            bottom: 30%;
            right: 8%;
            color: #78C850;
            animation-delay: 21s;
        }

        @keyframes floatAround {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(15px, -15px) rotate(90deg); }
            50% { transform: translate(30px, 0) rotate(180deg); }
            75% { transform: translate(15px, 15px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Starter Pokémon selection animation */
        .starter-container {
            position: relative;
            margin: 10px 0;
            padding: 15px;
            background: #F5F5F5;
            border-radius: 10px;
            border: 2px dashed #ddd;
        }

        .starter-options {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .starter-option {
            text-align: center;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .starter-option:hover {
            background: rgba(59, 76, 202, 0.1);
            transform: scale(1.05);
        }

        .starter-option i {
            font-size: 2rem;
            margin-bottom: 5px;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .register-box {
                padding: 22px;
                max-width: 380px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 10px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .pokeball {
                width: 60px;
                height: 60px;
            }
            
            .floating-pokemon {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Pokémon elements -->
    <div class="floating-pokemon pokemon-1">
        <i class="fas fa-bolt"></i>
    </div>
    <div class="floating-pokemon pokemon-2">
        <i class="fas fa-fire"></i>
    </div>
    <div class="floating-pokemon pokemon-3">
        <i class="fas fa-tint"></i>
    </div>
    <div class="floating-pokemon pokemon-4">
        <i class="fas fa-leaf"></i>
    </div>

    <div class="register-box">
        <div class="header">
            <div class="pokeball"></div>
            <h1>Become a Pokémon Trainer!</h1>
            <p class="subtitle">Start your journey in the world of Pokémon</p>
        </div>

        @if($errors->any())
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i> First Name
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="first_name" id="first_name" required 
                               placeholder="Ash" value="{{ old('first_name') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i> Last Name
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="last_name" id="last_name" 
                               placeholder="Ketchum" value="{{ old('last_name') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="username">
                    <i class="fas fa-at"></i> Username
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-at"></i>
                    <input type="text" name="username" id="username" required 
                           placeholder="ashketchum" value="{{ old('username') }}">
                </div>
                <small>This will be your trainer ID</small>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" required 
                           placeholder="ash@pokedex.com" value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="region">
                    <i class="fas fa-map-marker-alt"></i> Home Region
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-globe-americas"></i>
                    <select name="region" id="region" required>
                        <option value="">Select your region</option>
                        <option value="Kanto" {{ old('region') == 'Kanto' ? 'selected' : '' }}>Kanto</option>
                        <option value="Johto" {{ old('region') == 'Johto' ? 'selected' : '' }}>Johto</option>
                        <option value="Hoenn" {{ old('region') == 'Hoenn' ? 'selected' : '' }}>Hoenn</option>
                        <option value="Sinnoh" {{ old('region') == 'Sinnoh' ? 'selected' : '' }}>Sinnoh</option>
                        <option value="Unova" {{ old('region') == 'Unova' ? 'selected' : '' }}>Unova</option>
                        <option value="Kalos" {{ old('region') == 'Kalos' ? 'selected' : '' }}>Kalos</option>
                        <option value="Alola" {{ old('region') == 'Alola' ? 'selected' : '' }}>Alola</option>
                        <option value="Galar" {{ old('region') == 'Galar' ? 'selected' : '' }}>Galar</option>
                        <option value="Paldea" {{ old('region') == 'Paldea' ? 'selected' : '' }}>Paldea</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" id="password" required 
                               placeholder="At least 6 characters">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                               placeholder="Repeat your password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> BEGIN YOUR JOURNEY
            </button>
        </form>

        <div class="login-link">
            Already a Pokémon Trainer? 
            <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i> Login to your account
            </a>
        </div>
    </div>

    <script>
        // Enhanced password matching check
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        const registerForm = document.getElementById('registerForm');
        
        function checkPasswordMatch() {
            if (password.value !== '' && confirmPassword.value !== '') {
                const icon = confirmPassword.parentElement.querySelector('i');
                if (password.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#FF0000';
                    confirmPassword.style.backgroundColor = '#FFEBEE';
                    icon.style.color = '#FF0000';
                    icon.style.transform = 'translateY(-50%) scale(1.1)';
                } else {
                    confirmPassword.style.borderColor = '#4CAF50';
                    confirmPassword.style.backgroundColor = '#E8F5E9';
                    icon.style.color = '#4CAF50';
                    icon.style.transform = 'translateY(-50%) scale(1.1)';
                }
            }
        }
        
        password.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Username auto-suggest with animation
        const firstName = document.getElementById('first_name');
        const usernameInput = document.getElementById('username');
        
        firstName.addEventListener('blur', function() {
            if (firstName.value && !usernameInput.value) {
                const suggestedUsername = firstName.value.toLowerCase()
                    .replace(/\s+/g, '')
                    .replace(/[^a-z0-9]/g, '');
                usernameInput.value = suggestedUsername;
                
                // Add animation effect
                usernameInput.style.transform = 'scale(1.05)';
                usernameInput.style.backgroundColor = '#E8F5E9';
                setTimeout(() => {
                    usernameInput.style.transform = 'scale(1)';
                    usernameInput.style.backgroundColor = '';
                }, 300);
            }
        });

        // Add focus effects to inputs
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#3B4CCA';
                this.style.boxShadow = '0 0 0 3px rgba(59, 76, 202, 0.1)';
                
                if (this.parentElement.querySelector('i')) {
                    const icon = this.parentElement.querySelector('i');
                    icon.style.color = '#3B4CCA';
                    icon.style.transform = 'translateY(-50%) scale(1.2)';
                }
            });
            
            input.addEventListener('blur', function() {
                this.style.boxShadow = 'none';
                
                if (this.parentElement.querySelector('i')) {
                    const icon = this.parentElement.querySelector('i');
                    if (this !== confirmPassword || !password.value) {
                        icon.style.color = '#999';
                        icon.style.transform = 'translateY(-50%) scale(1)';
                    }
                }
            });
        });

        // Form submission animation
        registerForm.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CREATING ACCOUNT...';
            btn.disabled = true;
            
            // Add some delay for demo
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> ACCOUNT CREATED!';
                btn.style.background = 'linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%)';
            }, 1500);
        });

        // Pokeball interaction
        const pokeball = document.querySelector('.pokeball');
        pokeball.addEventListener('click', function() {
            this.style.animation = 'none';
            this.style.transform = 'rotate(360deg) scale(1.1)';
            this.style.transition = 'transform 0.6s';
            
            setTimeout(() => {
                this.style.animation = 'gentleBounce 4s ease-in-out infinite';
                this.style.transform = 'rotate(0deg) scale(1)';
            }, 600);
        });
    </script>
</body>
</html>
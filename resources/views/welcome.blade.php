<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - Welcome</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
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

        /* Gradient overlay for better text contrast */
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

        .header {
            margin-bottom: 25px;
            position: relative;
        }

        .pokeball {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            background: linear-gradient(#FF0000 50%, white 50%);
            border-radius: 50%;
            border: 8px solid #333;
            position: relative;
            animation: bounce 2s infinite;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .pokeball:before {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
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
            height: 8px;
            background: #333;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 8px;
            text-shadow: 2px 2px 0 #333, 0 0 10px rgba(255, 255, 255, 0.5);
            background: linear-gradient(to right, #FFCC00, #FFDE59);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .tagline {
            font-size: 1rem;
            margin-bottom: 25px;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
            background: rgba(0, 0, 0, 0.3);
            padding: 6px 15px;
            border-radius: 50px;
            display: inline-block;
            font-weight: bold;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            border-radius: 15px;
            padding: 25px;
            width: 100%;
            max-width: 750px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, #ff0000, #ffcc00, #3b4cca, #ff0000);
            z-index: -1;
            filter: blur(10px);
            opacity: 0.3;
            border-radius: 20px;
        }

        .features {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .feature {
            background: rgba(255, 255, 255, 0.15);
            padding: 18px 12px;
            border-radius: 12px;
            width: 180px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .feature::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #FF0000, #FFCC00);
        }

        .feature:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .feature i {
            font-size: 2rem;
            margin-bottom: 12px;
            color: #FFCC00;
            text-shadow: 0 0 8px rgba(255, 204, 0, 0.7);
        }

        .feature h3 {
            margin-bottom: 8px;
            font-size: 1.1rem;
            color: #FFCC00;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
        }

        .feature p {
            line-height: 1.3;
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 22px;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            min-width: 160px;
            justify-content: center;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.2) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
            z-index: -1;
        }

        .btn:hover::before {
            transform: translateX(100%);
        }

        .btn-primary {
            background: linear-gradient(to right, #FFCC00, #FFDE59);
            color: #333;
            box-shadow: 0 3px 10px rgba(255, 204, 0, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #FFD700, #FFE873);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 204, 0, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(to right, #3B4CCA, #5A6CE6);
            color: white;
            box-shadow: 0 3px 10px rgba(59, 76, 202, 0.4);
        }

        .btn-secondary:hover {
            background: linear-gradient(to right, #2E3AA3, #4A5BD9);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(59, 76, 202, 0.6);
        }

        .footer {
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            background: rgba(0, 0, 0, 0.5);
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            max-width: 750px;
        }

        .footer p {
            margin-bottom: 4px;
        }

        /* Simplified floating elements */
        .floating-icon {
            position: absolute;
            font-size: 1.2rem;
            opacity: 0.2;
            z-index: -1;
            animation: float 10s infinite linear;
        }

        .pikachu {
            top: 15%;
            left: 5%;
            color: #FFCC00;
            animation-delay: 0s;
        }

        .charizard {
            top: 20%;
            right: 5%;
            color: #FF6600;
            animation-delay: 2s;
        }

        .squirtle {
            bottom: 25%;
            left: 8%;
            color: #4A90E2;
            animation-delay: 4s;
        }

        .bulbasaur {
            bottom: 20%;
            right: 10%;
            color: #78C850;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Responsive Design - Even more compact */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                justify-content: flex-start;
                min-height: auto;
            }
            
            .pokeball {
                width: 80px;
                height: 80px;
                border-width: 6px;
                margin-bottom: 10px;
            }
            
            .pokeball:before {
                width: 20px;
                height: 20px;
                border-width: 5px;
            }
            
            .pokeball:after {
                height: 6px;
            }
            
            h1 {
                font-size: 1.8rem;
                margin-bottom: 5px;
            }
            
            .tagline {
                font-size: 0.9rem;
                margin-bottom: 15px;
                padding: 5px 12px;
            }
            
            .container {
                padding: 15px;
                margin-top: 10px;
                max-width: 95%;
            }
            
            .features {
                margin-bottom: 15px;
                gap: 10px;
            }
            
            .feature {
                width: calc(50% - 10px);
                max-width: 160px;
                padding: 12px 8px;
                margin-bottom: 5px;
            }
            
            .feature i {
                font-size: 1.5rem;
                margin-bottom: 8px;
            }
            
            .feature h3 {
                font-size: 1rem;
                margin-bottom: 5px;
            }
            
            .feature p {
                font-size: 0.8rem;
            }
            
            .buttons {
                gap: 10px;
            }
            
            .btn {
                min-width: 140px;
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .footer {
                margin-top: 15px;
                padding: 8px;
                font-size: 0.7rem;
                max-width: 95%;
            }
            
            .floating-icon {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .pokeball {
                width: 70px;
                height: 70px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .tagline {
                font-size: 0.8rem;
            }
            
            .feature {
                width: 100%;
                max-width: 200px;
                margin: 0 auto 8px;
            }
            
            .buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating elements - simplified -->
    <div class="floating-icon pikachu">
        <i class="fas fa-bolt"></i>
    </div>
    <div class="floating-icon charizard">
        <i class="fas fa-fire"></i>
    </div>

    <div class="header">
        <div class="pokeball"></div>
        <h1>Pokédex</h1>
        <p class="tagline">Gotta Catch 'Em All!</p>
    </div>

    <div class="container">
        <div class="features">
            <div class="feature">
                <i class="fas fa-dragon"></i>
                <h3>Browse Pokémon</h3>
                <p>View stats, abilities & evolution chains</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-users"></i>
                <h3>Build Teams</h3>
                <p>Create your ultimate battle team</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-exchange-alt"></i>
                <h3>Trade Pokémon</h3>
                <p>Connect with trainers</p>
            </div>
        </div>

        <div class="buttons">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            
            <a href="{{ route('pokemon.index') }}" class="btn btn-secondary">
                <i class="fas fa-eye"></i> View Pokédex
            </a>
        </div>
    </div>

    <div class="footer">
        <p>© 2026 Pokédex - Created with Laravel</p>
    </div>

    <script>
        // Simple interaction for pokeball
        const pokeball = document.querySelector('.pokeball');
        
        pokeball.addEventListener('mouseenter', () => {
            pokeball.style.transform = 'scale(1.1)';
        });
        
        pokeball.addEventListener('mouseleave', () => {
            pokeball.style.transform = 'scale(1)';
        });
        
        // Button click effects
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>
</html>
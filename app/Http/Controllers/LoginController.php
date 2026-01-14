<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trainer;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    // Show login form
    public function show()
    {
        return view('login');
    }

    // Show register form
    public function showRegister()
    {
        return view('register');
    }

    // Process registration
    public function register(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:trainers,username',
            'email' => 'required|email|unique:trainers,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create trainer (START AT LEVEL 1)
        $trainer = Trainer::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? '',
            'region' => $validated['region'] ?? 'Kanto',
            'password' => Hash::make($validated['password']),
            'level' => 1, // START AT LEVEL 1
            'pokemon_caught' => 0,
            'trades_completed' => 0,
            'badges_earned' => 0,
            'daily_streak' => 0,
            'total_logins' => 0
        ]);

        // Process first login (GET TO LEVEL 2 IMMEDIATELY!)
        $loginResult = $trainer->processDailyLogin();
        
        // Auto-login after registration
        session([
            'trainer_id' => $trainer->id,
            'trainer_username' => $trainer->username,
            'trainer_name' => $trainer->first_name,
            'trainer_email' => $trainer->email,
            'trainer_region' => $trainer->region,
            'trainer_level' => $trainer->level, // THIS IS NOW LEVEL 2!
            'trainer_streak' => $trainer->daily_streak,
            'logged_in' => true
        ]);

        return redirect()->route('pokemon.index')
            ->with('success', 'Welcome to Pokémon World, ' . $trainer->first_name . '!')
            ->with('level_up', $loginResult); // SHOW LEVEL UP NOTIFICATION
    }

    // Process login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check database
        $trainer = Trainer::where('email', $credentials['email'])->first();

        if ($trainer && Hash::check($credentials['password'], $trainer->password)) {
            // PROCESS DAILY LOGIN - THIS WILL +1 LEVEL!
            $loginResult = $trainer->processDailyLogin();
            
            session([
                'trainer_id' => $trainer->id,
                'trainer_username' => $trainer->username,
                'trainer_name' => $trainer->first_name,
                'trainer_email' => $trainer->email,
                'trainer_region' => $trainer->region,
                'trainer_level' => $trainer->level, // UPDATED LEVEL!
                'trainer_streak' => $trainer->daily_streak,
                'trainer_avatar' => $trainer->avatar_url,
                'logged_in' => true
            ]);

            // Show level up notification
            if ($loginResult['level_up']) {
                session()->flash('level_up', $loginResult);
            }

            return redirect()->route('pokemon.index')
                ->with('login_message', $loginResult['message']);
        }

        // Fallback to hardcoded users
        $validUsers = [
            'ash@pokedex.com' => 'pikachu123',
            'misty@pokedex.com' => 'starmie456',
            'brock@pokedex.com' => 'onix789'
        ];

        if (isset($validUsers[$request->email]) && 
            $validUsers[$request->email] === $request->password) {
            
            // Get or create demo trainer
            $trainer = Trainer::where('email', $request->email)->first();
            
            if (!$trainer) {
                $names = [
                    'ash@pokedex.com' => ['first_name' => 'Ash', 'username' => 'ashketchum'],
                    'misty@pokedex.com' => ['first_name' => 'Misty', 'username' => 'mistywater'],
                    'brock@pokedex.com' => ['first_name' => 'Brock', 'username' => 'brockrock']
                ];
                
                $trainer = Trainer::create([
                    'username' => $names[$request->email]['username'],
                    'email' => $request->email,
                    'first_name' => $names[$request->email]['first_name'],
                    'password' => Hash::make($validUsers[$request->email]),
                    'region' => 'Kanto',
                    'level' => 5,
                    'daily_streak' => 0,
                    'total_logins' => 0
                ]);
            }
            
            // PROCESS DAILY LOGIN - +1 LEVEL!
            $loginResult = $trainer->processDailyLogin();
            
            session([
                'trainer_id' => $trainer->id,
                'trainer_username' => $trainer->username,
                'trainer_name' => $trainer->first_name,
                'trainer_email' => $trainer->email,
                'trainer_region' => $trainer->region,
                'trainer_level' => $trainer->level, // UPDATED!
                'trainer_streak' => $trainer->daily_streak,
                'logged_in' => true
            ]);

            // Show level up
            if ($loginResult['level_up']) {
                session()->flash('level_up', $loginResult);
            }

            return redirect()->route('pokemon.index')
                ->with('login_message', $loginResult['message']);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput();
    }

    // Logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('home');
    }
}
@extends('layouts.app')

@section('title', 'Trainer Profile')

@section('styles')
<style>
    .profile-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 20px;
    }

    .profile-card {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .profile-tabs {
        display: flex;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .tab-button {
        flex: 1;
        padding: 18px 20px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
    }

    .tab-button:hover {
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
    }

    .tab-button.active {
        background: white;
        color: #4f46e5;
        border-bottom: 3px solid #4f46e5;
    }

    .tab-content {
        padding: 30px;
        display: none;
        min-height: 600px;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Show Tab - 3 Column Layout */
    .show-content {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .show-content {
            grid-template-columns: 1fr;
        }
    }

    .avatar-column {
        text-align: center;
    }

    .avatar-container {
        width: 200px;
        height: 200px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-default {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: white;
    }

    .trainer-badge {
        display: inline-block;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #78350f;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 25px;
    }

    /* DAILY STREAK CARD STYLES */
    .streak-card {
        background: linear-gradient(135deg, #FF6B6B 0%, #EE5A24 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(255,107,107,0.3);
    }

    .streak-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .streak-count {
        background: rgba(255,255,255,0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .streak-status {
        background: rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .streak-progress {
        margin-bottom: 15px;
    }

    .progress-bar {
        height: 10px;
        background: rgba(255,255,255,0.3);
        border-radius: 5px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #FFD700, #FFA500);
        border-radius: 5px;
        transition: width 0.5s ease;
    }

    .streak-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 15px;
        text-align: center;
    }

    .streak-stat {
        background: rgba(255,255,255,0.1);
        padding: 10px;
        border-radius: 8px;
    }

    .streak-value {
        font-size: 20px;
        font-weight: bold;
        color: #FFD700;
    }

    .streak-label {
        font-size: 11px;
        opacity: 0.9;
    }

    .login-button {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        font-weight: bold;
        width: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .login-button:hover {
        background: #45a049;
        transform: translateY(-2px);
    }

    /* STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 25px;
    }

    .stat-card {
        background: white;
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-top: 4px solid #4f46e5;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #4f46e5;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .details-column {
        background: #f8fafc;
        padding: 25px;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
    }

    .details-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }

    @media (max-width: 576px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    .info-item {
        background: white;
        padding: 18px;
        border-radius: 12px;
        border-left: 4px solid #4f46e5;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .info-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }

    .region-badge {
        display: inline-block;
        padding: 6px 15px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 20px;
        font-weight: 700;
        font-size: 13px;
        margin-top: 5px;
    }

    .bio-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }

    .bio-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bio-content {
        color: #475569;
        line-height: 1.6;
        max-height: 200px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .bio-content::-webkit-scrollbar {
        width: 4px;
    }

    .bio-content::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 2px;
    }

    .bio-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .show-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e2e8f0;
    }

    .action-btn {
        flex: 1;
        padding: 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-back {
        background: white;
        color: #475569;
        border: 2px solid #cbd5e1;
    }

    .btn-back:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-edit-tab {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
    }

    .btn-edit-tab:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
    }

    /* Edit Tab - 2 Column Layout */
    .edit-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    @media (max-width: 992px) {
        .edit-content {
            grid-template-columns: 1fr;
        }
    }

    .edit-section {
        background: #f8fafc;
        padding: 25px;
        border-radius: 15px;
        border: 2px solid #e2e8f0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 576px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #334155;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    select.form-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 12px;
        padding-right: 40px;
    }

    .textarea {
        min-height: 120px;
        resize: vertical;
    }

    .char-counter {
        text-align: right;
        font-size: 12px;
        color: #64748b;
        margin-top: 5px;
    }

    .password-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 5px;
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .edit-actions {
        grid-column: 1 / -1;
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e2e8f0;
    }

    .edit-btn {
        flex: 1;
        padding: 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-cancel {
        background: white;
        color: #475569;
        border: 2px solid #cbd5e1;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-reset {
        background: #e2e8f0;
        color: #475569;
        border: 2px solid #cbd5e1;
    }

    .btn-reset:hover {
        background: #cbd5e1;
    }

    .btn-save {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #4338ca, #6d28d9);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
    }

    /* Alert Messages */
    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .alert-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .alert ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
    }

    .alert li {
        margin-bottom: 4px;
        font-size: 13px;
    }

    /* Level Up Notification */
    .level-up-notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 99999;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        padding: 30px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        text-align: center;
        border: 3px solid #FFD700;
        animation: popIn 0.8s ease;
        max-width: 500px;
    }

    @keyframes popIn {
        0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0; }
        70% { transform: translate(-50%, -50%) scale(1.1); }
        100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    /* Info Message */
    .info-message {
        text-align: center;
        margin-top: 20px;
    }

    .info-card {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 12px 24px;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <!-- Tabs -->
        <div class="profile-tabs">
            <button class="tab-button active" data-tab="show-tab">
                <i class="fas fa-user-circle"></i> View Profile
            </button>
            <button class="tab-button" data-tab="edit-tab">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
        </div>

        <!-- Show Tab -->
        <div id="show-tab" class="tab-content active">
            <div class="show-content">
                <!-- Left Column: Avatar & Stats -->
                <div class="avatar-column">
                    <div class="avatar-container @if(!$trainer->avatar_url) avatar-default @endif">
                        @if($trainer->avatar_url)
                            <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->first_name }}" 
                                 onerror="this.style.display='none'; this.parentElement.classList.add('avatar-default')">
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    
                    <div class="trainer-badge">
                        <i class="fas fa-certificate"></i> POKÉMON TRAINER
                    </div>
                    
                    <!-- SIMPLE STATS -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value">{{ $trainer->pokemon_caught }}</div>
                            <div class="stat-label">Pokémon Caught</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $trainer->level }}</div>
                            <div class="stat-label">Trainer Level</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $trainer->trades_completed }}</div>
                            <div class="stat-label">Trades Made</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $trainer->badges_earned }}</div>
                            <div class="stat-label">Badges Earned</div>
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Daily Streak System -->
                <div class="streak-column">
                    <!-- DAILY STREAK CARD -->
                    <div class="streak-card">
                        <div class="streak-header">
                            <h3 style="margin: 0; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-fire"></i> Daily Login Streak
                            </h3>
                            <div class="streak-count">
                                <i class="fas fa-fire" style="color: #FFD700;"></i> 
                                {{ $trainer->daily_streak ?? 0 }} days
                            </div>
                        </div>
                        
                        <!-- TODAY'S STATUS -->
                        <div class="streak-status">
                            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $lastLogin = $trainer->last_login_date ? \Carbon\Carbon::parse($trainer->last_login_date) : null;
                                    $todayLoggedIn = $lastLogin && $lastLogin->isToday();
                                @endphp
                                
                                @if($todayLoggedIn)
                                    <span style="color: #4CAF50;">
                                        <i class="fas fa-check-circle"></i> Already logged in today
                                    </span>
                                @else
                                    <span style="color: #FFD700;">
                                        <i class="fas fa-exclamation-circle"></i> Login today for +1 Level!
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 12px; opacity: 0.9;">
                                Last login: {{ $trainer->last_login_date ? \Carbon\Carbon::parse($trainer->last_login_date)->format('M d, Y') : 'Never' }}
                            </div>
                            
                            @if(!$todayLoggedIn)
                            <div style="text-align: center; margin-top: 10px;">
                                <button onclick="processDailyLogin()" class="login-button" id="loginBtn">
                                    <i class="fas fa-sign-in-alt"></i> Process Daily Login
                                </button>
                            </div>
                            @endif
                        </div>
                        
                        <!-- NEXT MILESTONE -->
                        <div class="streak-progress">
                            <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">
                                <i class="fas fa-calendar-star"></i> Next Milestone
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px;">
                                <span>Current: {{ $trainer->daily_streak ?? 0 }} days</span>
                                <span>Next: 
                                    @if(($trainer->daily_streak ?? 0) < 3)
                                        3 days
                                    @elseif(($trainer->daily_streak ?? 0) < 7)
                                        7 days
                                    @elseif(($trainer->daily_streak ?? 0) < 14)
                                        14 days
                                    @else
                                        30 days
                                    @endif
                                </span>
                            </div>
                            <div class="progress-bar">
                                @php
                                    $nextMilestone = 3;
                                    if(($trainer->daily_streak ?? 0) >= 3) $nextMilestone = 7;
                                    if(($trainer->daily_streak ?? 0) >= 7) $nextMilestone = 14;
                                    if(($trainer->daily_streak ?? 0) >= 14) $nextMilestone = 30;
                                    $progress = $nextMilestone > 0 ? min(100, (($trainer->daily_streak ?? 0) / $nextMilestone) * 100) : 0;
                                @endphp
                                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                            </div>
                            <div style="text-align: center; font-size: 11px; margin-top: 5px;">
                                @if(($trainer->daily_streak ?? 0) < 3)
                                    3-Day Streak: Poké Ball x3
                                @elseif(($trainer->daily_streak ?? 0) < 7)
                                    7-Day Streak: Great Ball x5
                                @elseif(($trainer->daily_streak ?? 0) < 14)
                                    14-Day Streak: Ultra Ball x3
                                @else
                                    30-Day Streak: Master Ball!
                                @endif
                            </div>
                        </div>
                        
                        <!-- STREAK STATS -->
                        <div class="streak-stats">
                            <div class="streak-stat">
                                <div class="streak-value">{{ $trainer->level }}</div>
                                <div class="streak-label">Current Level</div>
                            </div>
                            <div class="streak-stat">
                                <div class="streak-value">{{ $trainer->total_logins ?? 0 }}</div>
                                <div class="streak-label">Total Logins</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QUICK STATS -->
                    <div style="background: #f8fafc; padding: 20px; border-radius: 15px; border: 2px solid #e2e8f0; margin-top: 20px;">
                        <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-chart-bar"></i> Quick Stats
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <div style="text-align: center; padding: 10px; background: white; border-radius: 8px;">
                                <div style="font-size: 18px; font-weight: bold; color: #4f46e5;">{{ $trainer->teams()->count() }}</div>
                                <div style="font-size: 11px; color: #64748b;">Team Size</div>
                            </div>
                            <div style="text-align: center; padding: 10px; background: white; border-radius: 8px;">
                                <div style="font-size: 18px; font-weight: bold; color: #4f46e5;">{{ $trainer->getPokedexProgressAttribute()['percentage'] }}%</div>
                                <div style="font-size: 11px; color: #64748b;">Pokédex</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="details-column">
                    <h2 class="details-title">
                        <i class="fas fa-id-card"></i> Trainer Information
                    </h2>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i> First Name
                            </div>
                            <div class="info-value">
                                {{ $trainer->first_name ?? 'Not set' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i> Last Name
                            </div>
                            <div class="info-value">
                                {{ $trainer->last_name ?? 'Not set' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-envelope"></i> Email
                            </div>
                            <div class="info-value">
                                {{ $trainer->email }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt"></i> Region
                            </div>
                            <div class="info-value">
                                @if($trainer->region)
                                    <span class="region-badge">{{ $trainer->region }}</span>
                                @else
                                    <span style="color: #94a3b8;">Not specified</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bio-section">
                        <h3 class="bio-title">
                            <i class="fas fa-book-open"></i> Trainer Bio
                        </h3>
                        <div class="bio-content">
                            @if($trainer->bio && trim($trainer->bio) !== '')
                                {{ $trainer->bio }}
                            @else
                                <div style="text-align: center; color: #94a3b8; padding: 20px;">
                                    <i class="fas fa-feather-alt" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                    <p>No bio yet. Tell us about your Pokémon journey!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="show-actions">
                        <a href="{{ route('home') }}" class="action-btn btn-back">
                            <i class="fas fa-home"></i> Back Home
                        </a>
                        <button class="action-btn btn-edit-tab" onclick="switchToEditTab()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Tab -->
        <div id="edit-tab" class="tab-content">
            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                @csrf
                @method('PUT')

                <div class="edit-content">
                    <!-- Left Column: Basic Info -->
                    <div class="edit-section">
                        <h2 class="section-title">
                            <i class="fas fa-user-edit"></i> Basic Information
                        </h2>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> First Name
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       value="{{ old('first_name', $trainer->first_name) }}"
                                       class="form-input"
                                       placeholder="Enter first name">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Last Name
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       value="{{ old('last_name', $trainer->last_name) }}"
                                       class="form-input"
                                       placeholder="Enter last name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Home Region
                            </label>
                            <select name="region" class="form-input">
                                <option value="">Select Region</option>
                                <option value="Kanto" {{ old('region', $trainer->region) == 'Kanto' ? 'selected' : '' }}>Kanto</option>
                                <option value="Johto" {{ old('region', $trainer->region) == 'Johto' ? 'selected' : '' }}>Johto</option>
                                <option value="Hoenn" {{ old('region', $trainer->region) == 'Hoenn' ? 'selected' : '' }}>Hoenn</option>
                                <option value="Sinnoh" {{ old('region', $trainer->region) == 'Sinnoh' ? 'selected' : '' }}>Sinnoh</option>
                                <option value="Unova" {{ old('region', $trainer->region) == 'Unova' ? 'selected' : '' }}>Unova</option>
                                <option value="Kalos" {{ old('region', $trainer->region) == 'Kalos' ? 'selected' : '' }}>Kalos</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i> Profile Picture URL
                            </label>
                            <input type="url" 
                                   name="avatar_url" 
                                   value="{{ old('avatar_url', $trainer->avatar_url) }}"
                                   class="form-input"
                                   placeholder="https://example.com/avatar.jpg">
                            <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Leave empty for default avatar
                            </div>
                            @if($trainer->avatar_url)
                                <div class="avatar-preview">
                                    <img src="{{ $trainer->avatar_url }}" alt="Current Avatar">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Bio & Password -->
                    <div class="edit-section">
                        <h2 class="section-title">
                            <i class="fas fa-lock"></i> Security & Bio
                        </h2>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-book"></i> Trainer Bio
                            </label>
                            <textarea name="bio" 
                                      class="form-input textarea"
                                      placeholder="Tell us about your Pokémon journey..."
                                      oninput="updateCharCount(this)"
                                      maxlength="500">{{ old('bio', $trainer->bio) }}</textarea>
                            <div class="char-counter">
                                <span id="charCount">{{ strlen(old('bio', $trainer->bio)) }}</span>/500 characters
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key"></i> Current Password
                            </label>
                            <div class="password-group">
                                <input type="password" 
                                       name="current_password"
                                       id="currentPassword"
                                       class="form-input"
                                       placeholder="Enter current password">
                                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <div class="password-group">
                                <input type="password" 
                                       name="new_password"
                                       id="newPassword"
                                       class="form-input"
                                       placeholder="Enter new password">
                                <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key"></i> Confirm Password
                            </label>
                            <div class="password-group">
                                <input type="password" 
                                       name="new_password_confirmation"
                                       id="confirmPassword"
                                       class="form-input"
                                       placeholder="Confirm new password">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="edit-actions">
                        <button type="button" class="edit-btn btn-cancel" onclick="switchToShowTab()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="reset" class="edit-btn btn-reset">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="edit-btn btn-save" id="saveButton">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="info-message">
        <div class="info-card">
            <i class="fas fa-info-circle"></i>
            <span>Login daily to level up your trainer!</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching
    function switchTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
        
        // Activate selected button
        const selectedButton = document.querySelector(`[data-tab="${tabId}"]`);
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }

    function switchToEditTab() {
        switchTab('edit-tab');
    }
    
    function switchToShowTab() {
        switchTab('show-tab');
    }

    // Daily login function
    function processDailyLogin() {
        const loginBtn = document.getElementById('loginBtn');
        if (!loginBtn) return;
        
        // Show loading
        const originalText = loginBtn.innerHTML;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        loginBtn.disabled = true;
        
        // Make AJAX request
        fetch('{{ route("profile.daily-login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show level up notification
                showLevelUpNotification(data.old_level, data.new_level, data.streak);
                
                // Update UI after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showNotification(data.error || 'Failed to process daily login', 'error');
                loginBtn.innerHTML = originalText;
                loginBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
            loginBtn.innerHTML = originalText;
            loginBtn.disabled = false;
        });
    }

    // Level up notification
    function showLevelUpNotification(oldLevel, newLevel, streak) {
        const notification = document.createElement('div');
        notification.className = 'level-up-notification';
        notification.innerHTML = `
            <div style="font-size: 48px; margin-bottom: 10px;">
                🎉
            </div>
            <h2 style="margin: 0 0 15px 0; font-size: 24px;">DAILY LEVEL UP!</h2>
            <div style="font-size: 36px; font-weight: bold; margin: 15px 0; color: #FFD700;">
                Level ${oldLevel} → Level ${newLevel}
            </div>
            <p style="margin: 10px 0; font-size: 16px;">
                Daily streak: ${streak} days
            </p>
            <p style="margin: 10px 0; font-size: 14px; opacity: 0.9;">
                Come back tomorrow for another level!
            </p>
            <button onclick="this.parentElement.remove()" 
                    style="background: #FFD700; color: #333; border: none; padding: 10px 25px; border-radius: 25px; font-weight: bold; margin-top: 15px; cursor: pointer;">
                Awesome!
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Initialize tabs
    document.addEventListener('DOMContentLoaded', function() {
        // Tab button click handlers
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                switchTab(tabId);
            });
        });

        // Character counter for bio
        function updateCharCount(textarea) {
            const charCount = document.getElementById('charCount');
            if (!charCount) return;
            
            const length = textarea.value.length;
            charCount.textContent = length;
            
            if (length > 500) {
                charCount.style.color = '#ef4444';
                charCount.style.fontWeight = 'bold';
            } else if (length > 450) {
                charCount.style.color = '#f59e0b';
            } else {
                charCount.style.color = '#10b981';
            }
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const icon = input.parentElement.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form validation
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const bio = document.querySelector('textarea[name="bio"]');
                const saveButton = document.getElementById('saveButton');
                
                if (bio && bio.value.length > 500) {
                    e.preventDefault();
                    
                    // Add error styling
                    bio.style.borderColor = '#ef4444';
                    bio.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                    
                    // Switch to edit tab
                    switchToEditTab();
                    
                    // Focus on bio field
                    setTimeout(() => {
                        bio.focus();
                        bio.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                    
                    showNotification('Bio cannot exceed 500 characters. Please shorten your text.', 'error');
                } else if (saveButton) {
                    // Add loading animation
                    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                    saveButton.disabled = true;
                    saveButton.style.opacity = '0.8';
                }
            });
        }

        // Initialize character count
        const bioTextarea = document.querySelector('textarea[name="bio"]');
        if (bioTextarea) {
            updateCharCount(bioTextarea);
        }

        // Check for level up from session
        const levelUpData = @json(session('level_up'));
        if (levelUpData && levelUpData.level_up) {
            setTimeout(() => {
                showLevelUpNotification(levelUpData.old_level, levelUpData.new_level, levelUpData.streak);
            }, 1000);
        }

        // Expose functions to window
        window.updateCharCount = updateCharCount;
        window.togglePassword = togglePassword;
        window.switchToEditTab = switchToEditTab;
        window.switchToShowTab = switchToShowTab;
        window.processDailyLogin = processDailyLogin;
    });

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';
        notification.style.animation = 'slideDown 0.3s ease';
        
        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <div>${message}</div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideDown 0.3s ease reverse';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
</script>
@endsection
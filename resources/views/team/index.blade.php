@extends('layouts.app')

@section('title', 'My Team - Pokémon Trainer')

@section('content')

<style>

    
    /* ADJUST MAIN CONTAINER FOR FIXED NAVBAR */
    main.container {
        margin-top: 20px;
        padding: 20px;
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
        max-width: 1200px; /* optional: set max width */
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Team Header - Match Pokédex Header Style */
    .team-header-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
        border: 1px solid #eee;
        animation: slideUp 0.6s ease-out 0.1s both;
        transform: translateY(20px);
        opacity: 0;
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .team-header-title {
        color: #FF0000;
        margin: 0 0 10px 0;
        font-size: 36px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .team-header-title i {
    margin-right: 10px; /* Add spacing between icon and text */
}
    .team-header-subtitle {
        color: #666;
        font-size: 18px;
        margin: 0 0 30px 0;
        transition: all 0.3s ease;
    }
    
    /* Team Stats Container - Match Pokédex Style */
    .team-stats-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border: 1px solid #eee;
        animation: slideUp 0.6s ease-out 0.2s both;
        transform: translateY(20px);
        opacity: 0;
    }
    
    .team-stats-title {
        color: #333;
        margin: 0 0 20px 0;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: statCardAppear 0.5s ease-out forwards;
        animation-delay: calc(0.3s + (var(--card-index) * 0.1s));
        opacity: 0;
        transform: scale(0.95);
    }
    
    @keyframes statCardAppear {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    
    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .stat-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        opacity: 0.9;
        transition: opacity 0.3s ease;
    }
    
    .stat-card-value {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 16px;
        transition: all 0.3s ease;
    }
    
    .stat-card-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
    }
    
    .stat-main-value {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }
    
    .stat-subtitle {
        font-size: 14px;
        opacity: 0.9;
        transition: opacity 0.3s ease;
    }
    
    .progress-bar {
        height: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
        transition: all 0.3s ease;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #FFCC00, #FFD700);
        border-radius: 4px;
        transition: width 1s ease-in-out;
        transform-origin: left;
        animation: progressFill 1s ease-out forwards;
        animation-delay: 0.8s;
    }
    
    @keyframes progressFill {
        from {
            transform: scaleX(0);
        }
        to {
            transform: scaleX(1);
        }
    }
    
    /* FIX THE GRID - PROPER HORIZONTAL LAYOUT */
    .team-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px !important;
        width: 100% !important;
        margin: 0 auto !important;
        animation: fadeInGrid 0.5s ease-out 0.4s both;
        opacity: 0;
    }
    
    @keyframes fadeInGrid {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .team-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .team-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* FIX CARD SIZES */
    .pokemon-card {
        width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        background: white !important;
        border-radius: 15px !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        overflow: hidden !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        border: 1px solid #eee !important;
        position: relative;
        animation: cardAppear 0.5s ease-out forwards;
        animation-delay: calc(0.5s + (var(--card-index) * 0.1s));
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    
    @keyframes cardAppear {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .pokemon-card:hover {
        transform: translateY(-10px) scale(1.02) !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important;
        border-color: #FF0000 !important;
        z-index: 10;
    }
    
    .pokemon-card.caught {
        border: 3px solid #4CAF50 !important;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }
        50% {
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        100% {
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }
    }
    
    /* Empty Team State */
    .empty-team-container {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        border: 1px solid #e5e7eb;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin: 20px 0;
        animation: slideUp 0.6s ease-out 0.2s both;
        transform: translateY(20px);
        opacity: 0;
    }
    
    .empty-team-icon {
        width: 150px;
        height: 150px;
        margin: 0 auto 30px;
        position: relative;
        animation: bounceIn 1s ease-out 0.3s both;
    }
    
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.95);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .empty-team-icon-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, #60a5fa, #8b5cf6);
        border-radius: 50%;
        opacity: 0.2;
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .empty-team-icon i {
        position: relative;
        color: #9ca3af;
        font-size: 80px;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    .empty-team-title {
        font-size: 32px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        animation: fadeInUp 0.5s ease-out 0.5s both;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .empty-team-message {
        color: #666;
        font-size: 18px;
        max-width: 500px;
        margin: 0 auto 30px;
        line-height: 1.6;
        animation: fadeInUp 0.5s ease-out 0.6s both;
    }
    
    .empty-team-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 0.5s ease-out 0.7s both;
    }
    
    /* Button Styles Matching Pokédex */
    .btn-primary {
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(255, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 0, 0, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .btn-primary:hover::before {
        left: 100%;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #10b981, #14b8a6);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .btn-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary:hover::before {
        left: 100%;
    }
    
    /* Pokémon Card Header */
    .pokemon-card-header {
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        padding: 20px;
        border-radius: 15px 15px 0 0;
        color: white;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .pokemon-name-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .pokemon-display-name {
        font-size: 22px;
        font-weight: bold;
        margin: 0;
        transition: all 0.3s ease;
    }
    
    .pokemon-id-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 14px;
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
    }
    
    .pokemon-info-row {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 14px;
        opacity: 0.9;
        transition: all 0.3s ease;
    }
    
    /* Pokémon Image */
    .pokemon-image-container {
        padding: 25px;
        background: linear-gradient(to bottom, white, #f9fafb);
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .pokemon-image-wrapper {
        width: 160px;
        height: 160px;
        margin: 0 auto;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .pokemon-image-bg {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        opacity: 0.1;
        filter: blur(8px);
        transition: all 0.3s ease;
    }
    
    .pokemon-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        position: relative;
        z-index: 10;
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .pokemon-card:hover .pokemon-image {
        transform: scale(1.1) rotate(5deg);
    }
    
    /* Pokémon Types */
    .pokemon-types-container {
        padding: 0 20px;
        margin-bottom: 15px;
    }
    
    .pokemon-types {
        display: flex;
        gap: 10px;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .type-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        color: white;
        min-width: 70px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .type-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    /* Pokémon Stats */
    .pokemon-stats-container {
        padding: 0 20px 20px;
    }
    
    .stat-row {
        margin-bottom: 12px;
        animation: slideInLeft 0.5s ease-out forwards;
        animation-delay: calc(0.6s + (var(--stat-index) * 0.1s));
        opacity: 0;
        transform: translateX(-10px);
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .stat-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 13px;
        color: #666;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .stat-value {
        font-size: 14px;
        font-weight: bold;
        color: #333;
        transition: all 0.3s ease;
    }
    
    .stat-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .stat-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: left;
        animation: statGrow 1s ease-out forwards;
        animation-delay: 0.8s;
    }
    
    @keyframes statGrow {
        from {
            transform: scaleX(0);
        }
        to {
            transform: scaleX(1);
        }
    }
    
    /* Experience Bar */
    .exp-container {
        padding: 0 20px 20px;
        animation: fadeInUp 0.5s ease-out 0.7s both;
    }
    
    .exp-label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .exp-bar {
        height: 10px;
        background: #e5e7eb;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }
    
    .exp-fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #7c3aed);
        border-radius: 5px;
        transition: width 1s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: left;
        animation: expGrow 1s ease-out 0.9s forwards;
    }
    
    @keyframes expGrow {
        from {
            transform: scaleX(0);
        }
        to {
            transform: scaleX(1);
        }
    }
    
    .exp-text {
        font-size: 12px;
        color: #666;
        text-align: right;
        transition: all 0.3s ease;
    }
    
    /* Pokémon Actions */
    .pokemon-actions-container {
        padding: 15px 20px 20px;
        border-top: 1px solid #eee;
        background: #f9f9f9;
        border-radius: 0 0 15px 15px;
        transition: all 0.3s ease;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        animation: fadeInUp 0.5s ease-out 0.8s both;
    }
    
    .action-btn {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .action-btn:hover::before {
        left: 100%;
    }
    
    .action-btn.earn-exp {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }
    
    .action-btn.remove {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    /* Notification */
    .notification {
        position: fixed;
        top: 100px; /* Adjust for fixed navbar */
        right: 20px;
        z-index: 9999;
        width: 350px;
        max-width: 90%;
    }
    
    .notification-item {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 5px solid;
        animation: slideInRight 0.3s ease-out;
        transform: translateX(100%);
        animation-fill-mode: forwards;
        position: relative;
        overflow: hidden;
    }
    
    .notification-item.success {
        border-left-color: #4CAF50;
        color: #155724;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
    }
    
    .notification-item.error {
        border-left-color: #F44336;
        color: #721c24;
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    }
    
    .notification-item.info {
        border-left-color: #2196F3;
        color: #0c5460;
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    /* Demo Mode Warning */
    .demo-warning {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-left: 4px solid #eab308;
        color: #92400e;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideInLeft 0.5s ease-out both;
    }
    
    .demo-warning-icon {
        color: #eab308;
        font-size: 20px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    
    .demo-warning-content h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: bold;
    }
    
    .demo-warning-content p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }
</style>

<!-- Notification Container -->
<div class="notification" id="notificationContainer"></div>

<div class="container">
    <!-- Add warning message for demo mode -->
    @if(session('demo_mode') || !session('logged_in'))
    <div class="demo-warning">
        <div class="demo-warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="demo-warning-content">
            <h4>Demo Mode</h4>
            <p>{{ session('warning', 'Login to see your actual Pokémon team.') }}</p>
        </div>
    </div>
    @endif

    <!-- Team Header -->
    <div class="team-header-container">
        <h1 class="team-header-title">
            <i class="fas fa-users"></i> My Pokémon Team
        </h1>
        <p class="team-header-subtitle">Build your ultimate team of 6 Pokémon</p>
        
        <!-- Add Pokémon Button -->
        <div style="margin-top: 20px;">
            <a href="/pokedex" class="btn-primary">
                <i class="fas fa-plus"></i> Add Pokémon
            </a>
        </div>
    </div>

    <!-- Team Stats -->
    <div class="team-stats-container">
        <h3 class="team-stats-title">
            <i class="fas fa-chart-line"></i> Team Statistics
        </h3>
        
        <div class="stats-grid">
            <!-- Team Size -->
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">TEAM SIZE</h4>
                    <span class="stat-card-value">{{ $teamStats['count'] }}/6</span>
                </div>
                <div class="stat-card-content">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-main-value">{{ $teamStats['count'] }}</div>
                        <div class="stat-subtitle">Pokémon in Team</div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ ($teamStats['count'] / 6) * 100 }}%;"></div>
                </div>
            </div>
            
            <!-- Total Power -->
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">TOTAL POWER</h4>
                    <span class="stat-card-value">{{ number_format($teamStats['total_power']) }}</span>
                </div>
                <div class="stat-card-content">
                    <div class="stat-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <div class="stat-main-value">{{ number_format($teamStats['total_power']) }}</div>
                        <div class="stat-subtitle">Combined Stats</div>
                    </div>
                </div>
            </div>
            
            <!-- Average Level -->
            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">AVG. LEVEL</h4>
                    <span class="stat-card-value">{{ round($teamStats['avg_level']) }}</span>
                </div>
                <div class="stat-card-content">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="stat-main-value">{{ $teamStats['avg_level'] }}</div>
                        <div class="stat-subtitle">Team Strength</div>
                    </div>
                </div>
            </div>
            
            <!-- Types Covered -->
            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="stat-card-header">
                    <h4 class="stat-card-title">TYPES</h4>
                    <span class="stat-card-value">{{ $teamStats['types_covered'] }}</span>
                </div>
                <div class="stat-card-content">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="stat-main-value">{{ $teamStats['types_covered'] }}</div>
                        <div class="stat-subtitle">Type Coverage</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($team->isEmpty())
        <!-- Empty Team State -->
        <div class="empty-team-container">
            <div class="empty-team-icon">
                <div class="empty-team-icon-bg"></div>
                <i class="fas fa-users"></i>
            </div>
            <h2 class="empty-team-title">Your Team is Empty</h2>
            <p class="empty-team-message">Start your journey by catching Pokémon and building your ultimate team of 6!</p>
            <div class="empty-team-actions">
                <a href="/pokedex" class="btn-primary">
                    <i class="fas fa-search"></i> Explore Pokédex
                </a>
                <a href="/pokedex?tab=catch" class="btn-secondary">
                    <i class="fas fa-pokeball"></i> Catch Pokémon
                </a>
            </div>
        </div>
    @else
        <!-- Team Grid -->
        <div class="team-grid">
            @foreach($team as $member)
            @php
                $type1 = $member->pokemon->type1 ?? 'normal';
                $type2 = $member->pokemon->type2 ?? $type1;
                $typeColors = [
                    'fire' => ['bg' => '#F08030', 'text' => '#fff', 'gradient' => '#ef4444, #f97316'],
                    'water' => ['bg' => '#6890F0', 'text' => '#fff', 'gradient' => '#3b82f6, #06b6d4'],
                    'grass' => ['bg' => '#78C850', 'text' => '#fff', 'gradient' => '#10b981, #84cc16'],
                    'electric' => ['bg' => '#F8D030', 'text' => '#333', 'gradient' => '#eab308, #f59e0b'],
                    'psychic' => ['bg' => '#F85888', 'text' => '#fff', 'gradient' => '#ec4899, #8b5cf6'],
                    'ice' => ['bg' => '#98D8D8', 'text' => '#333', 'gradient' => '#22d3ee, #60a5fa'],
                    'dragon' => ['bg' => '#7038F8', 'text' => '#fff', 'gradient' => '#7c3aed, #dc2626'],
                    'normal' => ['bg' => '#A8A878', 'text' => '#fff', 'gradient' => '#9ca3af, #6b7280'],
                    'fighting' => ['bg' => '#C03028', 'text' => '#fff', 'gradient' => '#dc2626, #991b1b'],
                    'flying' => ['bg' => '#A890F0', 'text' => '#fff', 'gradient' => '#818cf8, #60a5fa'],
                    'poison' => ['bg' => '#A040A0', 'text' => '#fff', 'gradient' => '#a855f7, #8b5cf6'],
                    'ground' => ['bg' => '#E0C068', 'text' => '#333', 'gradient' => '#ca8a04, #92400e'],
                    'rock' => ['bg' => '#B8A038', 'text' => '#fff', 'gradient' => '#92400e, #78350f'],
                    'bug' => ['bg' => '#A8B820', 'text' => '#fff', 'gradient' => '#84cc16, #10b981'],
                    'ghost' => ['bg' => '#705898', 'text' => '#fff', 'gradient' => '#7c3aed, #4f46e5'],
                    'steel' => ['bg' => '#B8B8D0', 'text' => '#333', 'gradient' => '#6b7280, #4b5563'],
                ];
                $color1 = $typeColors[$type1] ?? $typeColors['normal'];
                $color2 = $typeColors[$type2] ?? $color1;
            @endphp
            
            <div class="pokemon-card" data-pokemon-id="{{ $member->id }}">
                <!-- Card Header -->
                <div class="pokemon-card-header" style="background: linear-gradient(135deg, {{ $color1['gradient'] }});">
                    <div class="pokemon-name-row">
                        <h3 class="pokemon-display-name">{{ $member->display_name }}</h3>
                        <span class="pokemon-id-badge">
                            #{{ str_pad($member->pokemon->id ?? '000', 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="pokemon-info-row">
                        <span>Level {{ $member->level }}</span>
                        <span>•</span>
                        <span>{{ $member->pokemon->name ?? 'Pokémon' }}</span>
                    </div>
                </div>
                
                <!-- Pokémon Image -->
                <div class="pokemon-image-container">
                    <div class="pokemon-image-wrapper">
                        <div class="pokemon-image-bg" style="background: linear-gradient(to right, {{ $color1['gradient'] }});"></div>
                        @if($member->pokemon->image_url)
                        <img src="{{ $member->pokemon->image_url }}" 
                             alt="{{ $member->pokemon->name }}" 
                             class="pokemon-image">
                        @else
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #9ca3af; font-size: 48px;">?</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Pokémon Info -->
                <div class="pokemon-types-container">
                    <div class="pokemon-types">
                        @if($member->pokemon->type1)
                        <span class="type-badge" style="background-color: {{ $typeColors[$member->pokemon->type1]['bg'] ?? '#A8A878' }}; color: {{ $typeColors[$member->pokemon->type1]['text'] ?? '#fff' }};">
                            {{ ucfirst($member->pokemon->type1) }}
                        </span>
                        @endif
                        @if($member->pokemon->type2 && $member->pokemon->type2 != $member->pokemon->type1)
                        <span class="type-badge" style="background-color: {{ $typeColors[$member->pokemon->type2]['bg'] ?? '#A8A878' }}; color: {{ $typeColors[$member->pokemon->type2]['text'] ?? '#fff' }};">
                            {{ ucfirst($member->pokemon->type2) }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="pokemon-stats-container">
                    @if($member->pokemon->hp)
                    <div class="stat-row">
                        <div class="stat-label-row">
                            <span class="stat-label">HP</span>
                            <span class="stat-value">{{ $member->pokemon->hp }}</span>
                        </div>
                        <div class="stat-bar">
                            <div class="stat-fill" style="background: linear-gradient(90deg, #4ade80, #22c55e); width: {{ min(100, ($member->pokemon->hp / 255) * 100) }}%;"></div>
                        </div>
                    </div>
                    @endif
                    
                    @if($member->pokemon->attack)
                    <div class="stat-row">
                        <div class="stat-label-row">
                            <span class="stat-label">Attack</span>
                            <span class="stat-value">{{ $member->pokemon->attack }}</span>
                        </div>
                        <div class="stat-bar">
                            <div class="stat-fill" style="background: linear-gradient(90deg, #f87171, #ef4444); width: {{ min(100, ($member->pokemon->attack / 255) * 100) }}%;"></div>
                        </div>
                    </div>
                    @endif
                    
                    @if($member->pokemon->defense)
                    <div class="stat-row">
                        <div class="stat-label-row">
                            <span class="stat-label">Defense</span>
                            <span class="stat-value">{{ $member->pokemon->defense ?? '0' }}</span>
                        </div>
                        <div class="stat-bar">
                            <div class="stat-fill" style="background: linear-gradient(90deg, #60a5fa, #3b82f6); width: {{ min(100, (($member->pokemon->defense ?? 0) / 255) * 100) }}%;"></div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Experience Bar -->
<div class="exp-container">
    <div class="exp-label-row">
        <span class="stat-label">Experience</span>
        <span class="stat-value">
            {{ $member->experience }}/{{ $member->experience_for_next_level }}
            (Level {{ $member->level }})
        </span>
    </div>
    <div class="exp-bar">
        <div class="exp-fill" style="width: {{ $member->experience_progress }}%;"></div>
    </div>
    <div class="exp-text">
        @if($member->experience_for_next_level > 0)
            {{ $member->experience }}/{{ $member->experience_for_next_level }} EXP
            ({{ round($member->experience_progress, 1) }}% to Level {{ $member->level + 1 }})
        @else
            MAX LEVEL
        @endif
    </div>
    
    <!-- Debug info (optional, remove in production) -->
    <div style="font-size: 10px; color: #888; margin-top: 5px; text-align: center;">
        Formula: (Level × 100) - 50 = {{ ($member->level * 100) - 50 }} EXP needed
    </div>
</div>
                
                <!-- Actions -->
                <div class="pokemon-actions-container">
                    <div class="action-buttons">
                        <<!-- BAGONG TRAINING BUTTON -->
        <button onclick="showTrainingModal({{ $member->id }})" 
                class="action-btn earn-exp">
            <i class="fas fa-dumbbell"></i> Training
        </button>
                        
                        <form action="{{ route('team.destroy', $member->id) }}" method="POST" style="flex: 1; margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Remove {{ $member->display_name }} from your team?')"
                                    class="action-btn remove">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Level Up Modal -->
<div id="levelModal" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.6); display: none; align-items: center; justify-content: center; z-index: 50; padding: 1rem;">
    <div style="background-color: white; border-radius: 1rem; max-width: 28rem; width: 100%; padding: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transition: all 300ms;">
        <div class="flex items-center mb-6">
            <div style="width: 3rem; height: 3rem; border-radius: 9999px; background: linear-gradient(to right, #facc15, #f59e0b); display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                <svg style="width: 1.5rem; height: 1.5rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">Level Up Pokémon</h3>
                <p style="color: #4b5563;">Adjust your Pokémon's level</p>
            </div>
        </div>
        
        <form id="levelForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: #374151; font-weight: 500; margin-bottom: 0.75rem;">Level (1-100)</label>
                <div class="flex items-center">
                    <input type="range" name="level" id="levelSlider" 
                           style="flex: 1; height: 0.5rem; background-color: #e5e7eb; border-radius: 0.5rem; appearance: none; cursor: pointer;"
                           min="1" max="100" value="5">
                    <span id="levelValue" style="margin-left: 1rem; font-size: 1.5rem; font-weight: 700; color: #1f2937; min-width: 3rem;">5</span>
                </div>
                <div style="display: flex; justify-content: between; font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem;">
                    <span>Level 1</span>
                    <span>Level 100</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeModal()" 
                        style="padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; color: #374151; border-radius: 0.75rem; font-weight: 500; background-color: transparent; transition: background-color 300ms; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" 
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(to right, #3b82f6, #7c3aed); color: white; border-radius: 0.75rem; font-weight: 500; transition: all 300ms; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: none; cursor: pointer;">
                    Update Level
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Training Session Modal -->
<!-- Training Session Modal -->
<div id="trainingModal" style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.8); display: none; align-items: center; justify-content: center; z-index: 100; padding: 1rem;">
    <div style="background: linear-gradient(135deg, #1e3a8a, #3730a3); border-radius: 1.5rem; max-width: 550px; width: 100%; padding: 2rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: white;">
                    <i class="fas fa-dumbbell"></i> Training Session
                </h3>
                <div id="dailyLimitDisplay" style="color: #c7d2fe; font-size: 0.875rem; margin-top: 0.25rem;">
                    <span id="remainingTrainings">3</span> trainings remaining today
                </div>
            </div>
            <button onclick="closeTrainingModal()" style="background: rgba(255, 255, 255, 0.1); color: white; width: 2.5rem; height: 2.5rem; border-radius: 9999px; border: none; cursor: pointer; font-size: 1.25rem;">
                ✕
            </button>
        </div>
        
        <!-- Pokémon Info -->
        <div id="trainingPokemonInfo" style="background: rgba(255, 255, 255, 0.1); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center;">
            <div id="trainingPokemonName" style="font-size: 1.25rem; font-weight: 700; color: #fbbf24;">-</div>
            <div style="color: #c7d2fe; font-size: 0.875rem;">
                Current: Level <span id="currentLevel">-</span> | 
                EXP: <span id="currentExp">-</span>/<span id="nextLevelExp">-</span>
            </div>
        </div>
        
        <!-- Training History -->
        <div id="trainingHistory" style="background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; max-height: 200px; overflow-y: auto;">
            <h4 style="color: white; margin-bottom: 0.75rem; font-size: 1rem;">
                <i class="fas fa-history"></i> Today's Trainings
            </h4>
            <div id="trainingLog" style="font-size: 0.875rem; color: #c7d2fe;">
                <div style="text-align: center; padding: 1rem; color: #94a3b8;">
                    No training sessions today
                </div>
            </div>
        </div>
        
        <p style="color: #c7d2fe; margin-bottom: 1.5rem; text-align: center;">
            Choose training intensity (3 per day max):
        </p>
        
        <!-- Training Options -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <!-- Easy Training -->
            <button id="easyTraining" onclick="startTraining('easy')" 
                    style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 1.5rem; border-radius: 1rem; border: none; cursor: pointer; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; opacity: 1;">
                <div style="font-size: 2rem;">🏃</div>
                <div style="font-weight: 700; font-size: 1.125rem;">Easy</div>
                <div style="font-size: 0.875rem; opacity: 0.9;">Light Exercise</div>
                <div style="font-size: 1rem; font-weight: 600; margin-top: 0.5rem;">+25 EXP</div>
            </button>
            
            <!-- Medium Training -->
            <button id="mediumTraining" onclick="startTraining('medium')" 
                    style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 1.5rem; border-radius: 1rem; border: none; cursor: pointer; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; opacity: 1;">
                <div style="font-size: 2rem;">💪</div>
                <div style="font-weight: 700; font-size: 1.125rem;">Medium</div>
                <div style="font-size: 0.875rem; opacity: 0.9;">Regular Workout</div>
                <div style="font-size: 1rem; font-weight: 600; margin-top: 0.5rem;">+50 EXP</div>
            </button>
            
            <!-- Hard Training -->
            <button id="hardTraining" onclick="startTraining('hard')" 
                    style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 1.5rem; border-radius: 1rem; border: none; cursor: pointer; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; opacity: 1;">
                <div style="font-size: 2rem;">🔥</div>
                <div style="font-weight: 700; font-size: 1.125rem;">Hard</div>
                <div style="font-size: 0.875rem; opacity: 0.9;">Intense Training</div>
                <div style="font-size: 1rem; font-weight: 600; margin-top: 0.5rem;">+100 EXP</div>
            </button>
        </div>
        
        <!-- Close Button -->
        <button onclick="closeTrainingModal()" 
                style="width: 100%; background: rgba(255, 255, 255, 0.1); color: white; padding: 0.75rem; border-radius: 0.75rem; border: none; cursor: pointer; font-weight: 600; transition: background-color 0.3s;">
            Close
        </button>
    </div>
</div>

@push('scripts')
<script>
// Level Up Functionality - WORKING VERSION
// Level Up Functionality - WORKING VERSION
function updateLevel(pokemonId) {
    console.log('updateLevel function called for ID:', pokemonId);
    
    // Get modal elements
    const modal = document.getElementById('levelModal');
    const form = document.getElementById('levelForm');
    const slider = document.getElementById('levelSlider');
    const valueDisplay = document.getElementById('levelValue');
    
    if (!modal || !form || !slider || !valueDisplay) {
        console.error('Modal elements not found!');
        showNotification('Error: Modal elements missing', 'error');
        return;
    }
    
    // Get current level from the page
    let currentLevel = 5; // Default
    
    // Try to find the Pokémon element
    const pokemonElement = document.querySelector(`[data-pokemon-id="${pokemonId}"]`);
    if (pokemonElement) {
        const levelElement = pokemonElement.querySelector('.pokemon-info-row span');
        if (levelElement) {
            const match = levelElement.textContent.match(/Level (\d+)/);
            if (match) {
                currentLevel = parseInt(match[1]);
                console.log('Found current level:', currentLevel);
            }
        }
    }
    
    // Update form action
    form.action = `/team/${pokemonId}/level`;
    console.log('Form action set to:', form.action);
    
    // Update slider and display
    slider.value = currentLevel;
    valueDisplay.textContent = currentLevel;
    
    // Show modal
    modal.style.display = 'flex';
    
    // Update slider event listener
    slider.oninput = function() {
        valueDisplay.textContent = this.value;
    };
}

function closeModal() {
    const modal = document.getElementById('levelModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const levelForm = document.getElementById('levelForm');
    if (levelForm) {
        levelForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Level form submitted');
            
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<span style="display: inline-flex; align-items: center;">' +
                    '<svg style="animation: spin 1s linear infinite; height: 1rem; width: 1rem; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24">' +
                    '<circle style="opacity: 0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path style="opacity: 0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                    '</svg>Updating...</span>';
                submitBtn.disabled = true;
                
                // Submit form via AJAX
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response:', data);
                    
                    if (data.success) {
                        showNotification(data.message || 'Level updated successfully!', 'success');
                        
                        // Reload page after 1.5 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(data.error || 'Failed to update level', 'error');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Network error. Please try again.', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            }
            
            return false;
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('levelModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target.id === 'levelModal') {
                closeModal();
            }
        });
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    
    // Test if functions are available
    console.log('Level Up functions loaded:', {
        updateLevel: typeof updateLevel,
        closeModal: typeof closeModal
    });
});

// Add CSS for spin animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// ====== TRAINING MODE FUNCTIONS ======
let currentTrainingPokemonId = null;
let trainingSessionActive = false;

// Show training modal
async function showTrainingModal(pokemonId) {
    console.log('Showing training modal for ID:', pokemonId);
    currentTrainingPokemonId = pokemonId;
    const modal = document.getElementById('trainingModal');
    
    // Get Pokémon info
    const pokemonElement = document.querySelector(`[data-pokemon-id="${pokemonId}"]`);
    if (pokemonElement) {
        const nameElement = pokemonElement.querySelector('h3');
        const levelElement = pokemonElement.querySelector('.pokemon-info-row span');
        const expElement = pokemonElement.querySelector('.exp-text');
        
        if (nameElement) {
            document.getElementById('trainingPokemonName').textContent = nameElement.textContent;
        }
        
        if (levelElement) {
            const match = levelElement.textContent.match(/Level (\d+)/);
            if (match) {
                document.getElementById('currentLevel').textContent = match[1];
            }
        }
        
        if (expElement) {
            const expMatch = expElement.textContent.match(/(\d+)\/(\d+)/);
            if (expMatch) {
                document.getElementById('currentExp').textContent = expMatch[1];
                document.getElementById('nextLevelExp').textContent = expMatch[2];
            } else {
                // Fallback if regex doesn't match
                document.getElementById('currentExp').textContent = '0';
                document.getElementById('nextLevelExp').textContent = '100';
            }
        }
    }
    
    // Load training history
    await loadTrainingHistory(pokemonId);
    
    // Show modal
    modal.style.display = 'flex';
    trainingSessionActive = false;
}

// Close training modal
function closeTrainingModal() {
    const modal = document.getElementById('trainingModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Reset training info
    document.getElementById('trainingPokemonInfo').innerHTML = `
        <div id="trainingPokemonName" style="font-size: 1.25rem; font-weight: 700; color: #fbbf24;">-</div>
        <div style="color: #c7d2fe; font-size: 0.875rem;">
            Current: Level <span id="currentLevel">-</span> | 
            EXP: <span id="currentExp">-</span>/<span id="nextLevelExp">-</span>
        </div>
    `;
    
    trainingSessionActive = false;
}

// Load training history
async function loadTrainingHistory(pokemonId) {
    try {
        const response = await fetch(`/team/${pokemonId}/training-history`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            updateDailyLimitDisplay(data.daily_limit);
            updateTrainingHistoryLog(data.history);
        } else {
            updateDailyLimitDisplay({ remaining: 3, max: 3, used: 0 });
        }
    } catch (error) {
        console.error('Error loading training history:', error);
        // Default to 3 remaining if API fails
        updateDailyLimitDisplay({ remaining: 3, max: 3, used: 0 });
    }
}

// Update daily limit display
function updateDailyLimitDisplay(limit) {
    const remainingElement = document.getElementById('remainingTrainings');
    const dailyLimitElement = document.getElementById('dailyLimitDisplay');
    
    if (remainingElement) {
        remainingElement.textContent = limit.remaining;
    }
    
    // Update button states
    const easyBtn = document.getElementById('easyTraining');
    const mediumBtn = document.getElementById('mediumTraining');
    const hardBtn = document.getElementById('hardTraining');
    
    if (limit.remaining <= 0) {
        // Disable all buttons
        [easyBtn, mediumBtn, hardBtn].forEach(btn => {
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            }
        });
        
        dailyLimitElement.style.color = '#fca5a5';
        dailyLimitElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> Daily limit reached! Try again tomorrow.';
    } else {
        // Enable buttons
        [easyBtn, mediumBtn, hardBtn].forEach(btn => {
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        });
        
        dailyLimitElement.style.color = limit.remaining > 1 ? '#c7d2fe' : '#fbbf24';
        dailyLimitElement.innerHTML = `<span id="remainingTrainings">${limit.remaining}</span> trainings remaining today`;
    }
}

// Update training history log
function updateTrainingHistoryLog(history) {
    const trainingLog = document.getElementById('trainingLog');
    
    if (!history || history.length === 0) {
        trainingLog.innerHTML = `
            <div style="text-align: center; padding: 1rem; color: #94a3b8;">
                <i class="fas fa-info-circle"></i> No training sessions today
            </div>
        `;
        return;
    }
    
    let html = '';
    history.forEach(item => {
        const time = new Date(item.created_at).toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const levelText = item.leveled_up 
            ? `<span style="color: #fbbf24;">Level up to ${item.new_level}!</span>`
            : `Level ${item.new_level}`;
        
        html += `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: rgba(255, 255, 255, 0.05); border-radius: 0.5rem; margin-bottom: 0.5rem;">
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: white;">
                        ${item.training_type} Training
                    </div>
                    <div style="font-size: 0.75rem; color: #c7d2fe;">
                        +${item.exp_gained} EXP • ${levelText}
                    </div>
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8;">
                    ${time}
                </div>
            </div>
        `;
    });
    
    trainingLog.innerHTML = html;
}

// Start training session with animation
async function startTraining(difficulty) {
    console.log('Starting training:', difficulty, 'for Pokémon ID:', currentTrainingPokemonId);
    
    if (!currentTrainingPokemonId) {
        console.error('No Pokémon ID');
        showNotification('Error: No Pokémon selected', 'error');
        return;
    }
    
    if (trainingSessionActive) {
        showNotification('Training session already in progress!', 'warning');
        return;
    }
    
    // Check remaining trainings first
    const remainingElement = document.getElementById('remainingTrainings');
    const remaining = parseInt(remainingElement.textContent);
    
    if (remaining <= 0) {
        showNotification('Daily training limit reached! Try again tomorrow.', 'error');
        return;
    }
    
    trainingSessionActive = true;
    
    const expMap = {
        'easy': 25,
        'medium': 50,
        'hard': 100
    };
    
    const expAmount = expMap[difficulty];
    const difficultyNames = {
        'easy': 'Easy Training',
        'medium': 'Medium Training', 
        'hard': 'Hard Training'
    };
    
    // Disable all training buttons
    const trainingButtons = document.querySelectorAll('#trainingModal button[onclick*="startTraining"]');
    trainingButtons.forEach(btn => {
        btn.disabled = true;
    });
    
    // Show training animation
    showTrainingAnimation(difficulty);
    
    // Add to training history log
    addTrainingLog(`Started ${difficultyNames[difficulty]}...`, 'pending');
    
    // Send request to server
    try {
        const response = await fetch(`/team/${currentTrainingPokemonId}/add-exp`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                exp: expAmount,
                training_type: difficultyNames[difficulty]
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Training response:', data);
        
        if (data.success) {
            // Show success animation
            showTrainingSuccessAnimation(difficulty, expAmount, data);
            
            // Update daily limit display
            if (data.daily_limit) {
                updateDailyLimitDisplay(data.daily_limit);
            }
            
            // Reload training history
            await loadTrainingHistory(currentTrainingPokemonId);
            
            // Show notification
            if (data.leveled_up) {
                showNotification(`🎉 ${data.pokemon_name} leveled up to Level ${data.new_level}!`, 'success');
            } else {
                showNotification(`✨ ${data.pokemon_name} gained +${expAmount} EXP!`, 'success');
            }
            
            // Auto-reload page after 3 seconds
            setTimeout(() => {
                location.reload();
            }, 3000);
            
        } else {
            // Show error
            showTrainingErrorAnimation(data.error || 'Training failed!');
            showNotification(data.error || 'Training failed!', 'error');
            
            // Re-enable buttons if not limit reached
            trainingButtons.forEach(btn => {
                btn.disabled = false;
            });
            trainingSessionActive = false;
        }
    } catch (error) {
        console.error('Network error:', error);
        showTrainingErrorAnimation('Network error! Please check console.');
        showNotification('Network error during training: ' + error.message, 'error');
        
        // Re-enable buttons
        trainingButtons.forEach(btn => {
            btn.disabled = false;
        });
        trainingSessionActive = false;
    }
}

// Show training animation - FIXED VERSION
function showTrainingAnimation(difficulty) {
    const trainingInfo = document.getElementById('trainingPokemonInfo');
    const emoji = difficulty === 'easy' ? '🏃' : difficulty === 'medium' ? '💪' : '🔥';
    const title = difficulty === 'easy' ? 'Light Exercise' : difficulty === 'medium' ? 'Regular Workout' : 'Intense Training';
    
    trainingInfo.innerHTML = `
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem; animation: bounce 1s infinite;">
                ${emoji}
            </div>
            <div style="color: white; font-weight: 600; font-size: 1.125rem;">${title} in Progress...</div>
            <div style="color: #c7d2fe; margin-top: 0.5rem;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <div style="width: 100%; max-width: 200px; height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 3px; overflow: hidden;">
                        <div id="progressBar" style="height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6); width: 0%; border-radius: 3px; transition: width 2s linear;"></div>
                    </div>
                    <span id="progressText">0%</span>
                </div>
            </div>
        </div>
    `;
    
    // Start progress animation
    let progress = 0;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    const interval = setInterval(() => {
        progress += 10;
        if (progress <= 100) {
            if (progressBar) progressBar.style.width = `${progress}%`;
            if (progressText) progressText.textContent = `${progress}%`;
        } else {
            clearInterval(interval);
        }
    }, 200);
}

// Show training success animation - FIXED VERSION
function showTrainingSuccessAnimation(difficulty, expAmount, data) {
    const trainingInfo = document.getElementById('trainingPokemonInfo');
    const emoji = difficulty === 'easy' ? '🏃' : difficulty === 'medium' ? '💪' : '🔥';
    
    let message = `✨ +${expAmount} EXP Earned!`;
    if (data.leveled_up) {
        message = `🎉 LEVEL UP! Now Level ${data.new_level}!`;
    }
    
    trainingInfo.innerHTML = `
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 4rem; margin-bottom: 0.5rem; animation: bounce 0.5s infinite;">
                ${data.leveled_up ? '🎉' : '✨'}
            </div>
            <div style="color: white; font-weight: 600; font-size: 1.25rem; margin-bottom: 0.5rem;">
                ${message}
            </div>
            <div style="color: #c7d2fe;">
                ${data.pokemon_name} is getting stronger!
            </div>
            <div style="margin-top: 1rem; font-size: 0.875rem; color: #fbbf24;">
                Page will reload in 3 seconds...
            </div>
        </div>
    `;
}

// Show training error animation - FIXED VERSION
function showTrainingErrorAnimation(errorMessage) {
    const trainingInfo = document.getElementById('trainingPokemonInfo');
    
    trainingInfo.innerHTML = `
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                ❌
            </div>
            <div style="color: #fca5a5; font-weight: 600; font-size: 1.125rem;">
                Training Failed!
            </div>
            <div style="color: #fecaca; margin-top: 0.5rem;">
                ${errorMessage}
            </div>
        </div>
    `;
}

// Helper function for training log
function addTrainingLog(message, status = 'info') {
    const trainingLog = document.getElementById('trainingLog');
    const timestamp = new Date().toLocaleTimeString([], { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    
    let color = '#c7d2fe';
    let icon = 'ℹ️';
    
    if (status === 'pending') {
        color = '#fbbf24';
        icon = '⏳';
    } else if (status === 'success') {
        color = '#4ade80';
        icon = '✅';
    } else if (status === 'error') {
        color = '#fca5a5';
        icon = '❌';
    }
    
    const logEntry = document.createElement('div');
    logEntry.style.marginBottom = '0.5rem';
    logEntry.style.padding = '0.75rem';
    logEntry.style.background = 'rgba(255, 255, 255, 0.05)';
    logEntry.style.borderRadius = '0.5rem';
    logEntry.style.borderLeft = `3px solid ${color}`;
    logEntry.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.875rem;">${icon}</span>
            <span style="flex: 1; color: white; font-size: 0.875rem;">${message}</span>
            <span style="color: #94a3b8; font-size: 0.75rem;">${timestamp}</span>
        </div>
    `;
    
    trainingLog.prepend(logEntry);
    
    // Auto-scroll to latest log
    trainingLog.scrollTop = 0;
}

// SIMPLE Notification function
function showNotification(message, type = 'info') {
    console.log('Showing notification:', message, type);
    
    const container = document.getElementById('notificationContainer');
    if (!container) {
        console.error('Notification container not found');
        alert(message);
        return;
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-item ${type}`;
    notification.style.animation = 'slideInRight 0.3s ease-out forwards';
    
    let icon = 'ℹ️';
    if (type === 'success') icon = '✅';
    if (type === 'error') icon = '❌';
    if (type === 'warning') icon = '⚠️';
    
    notification.innerHTML = `
        <div style="font-size: 20px;">${icon}</div>
        <div style="flex: 1; font-size: 14px;">${message}</div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:18px;">×</button>
    `;
    
    container.appendChild(notification);
    
    // Auto-remove after 5 seconds for success, 7 seconds for error
    const duration = type === 'success' ? 5000 : type === 'error' ? 7000 : 4000;
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease-out forwards';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, duration);
}

// Add animation styles
function addAnimationStyles() {
    if (document.querySelector('#training-animation-styles')) return;
    
    const style = document.createElement('style');
    style.id = 'training-animation-styles';
    style.textContent = `
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .notification-item {
            animation: slideInRight 0.3s ease-out forwards;
        }
    `;
    document.head.appendChild(style);
}

// Make functions globally available
window.showTrainingModal = showTrainingModal;
window.closeTrainingModal = closeTrainingModal;
window.startTraining = startTraining;
window.showNotification = showNotification;
window.updateLevel = updateLevel;
window.closeModal = closeModal;

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Training system with animations loaded');
    addAnimationStyles();
    
    // Add click event for outside modal click to close
    const trainingModal = document.getElementById('trainingModal');
    if (trainingModal) {
        trainingModal.addEventListener('click', function(e) {
            if (e.target === trainingModal) {
                closeTrainingModal();
            }
        });
    }
    
    // Add ESC key listener to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const trainingModal = document.getElementById('trainingModal');
            if (trainingModal && trainingModal.style.display === 'flex') {
                closeTrainingModal();
            }
            
            const levelModal = document.getElementById('levelModal');
            if (levelModal && levelModal.style.display === 'flex') {
                closeModal();
            }
        }
    });
    
    // Test that functions are available
    console.log('Available functions:', {
        showTrainingModal: typeof showTrainingModal,
        startTraining: typeof startTraining,
        showNotification: typeof showNotification
    });
});
</script>
@endsection
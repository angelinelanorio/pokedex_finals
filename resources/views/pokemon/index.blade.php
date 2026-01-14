@extends('layouts.app')

@section('title', 'Pokédex')

@section('content')

<div class="notification-container" id="notificationContainer"></div>

<style>

    /* ============ NOTIFICATION SYSTEM (FOR ALL PAGES) ============ */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 99999; /* Higher z-index */
    width: 350px;
    max-width: 90%;
    pointer-events: none; /* Allow clicking through container */
}

.notification {
    background: white;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    border-left: 5px solid;
    position: relative;
    overflow: hidden;
    pointer-events: auto; /* Enable clicking on notifications */
    transform: translateX(120%); /* Start off-screen to the right */
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.notification.show {
    transform: translateX(0); /* Slide in from right */
    opacity: 1;
}

.notification.hiding {
    transform: translateX(120%); /* Slide out to right */
    opacity: 0;
}

.notification.success {
    border-left-color: #4CAF50;
    color: #155724;
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
}

.notification.error {
    border-left-color: #F44336;
    color: #721c24;
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
}

.notification.info {
    border-left-color: #2196F3;
    color: #0c5460;
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
}

.notification.warning {
    border-left-color: #FF9800;
    color: #856404;
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
}

.notification-icon {
    font-size: 22px;
    flex-shrink: 0;
    min-width: 30px;
    text-align: center;
}

.notification-content {
    flex: 1;
    min-width: 0; /* Prevent overflow */
}

.notification-title {
    font-weight: bold;
    font-size: 15px;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-message {
    font-size: 14px;
    opacity: 0.9;
    word-break: break-word;
}

.notification-close {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 20px;
    opacity: 0.7;
    transition: opacity 0.3s;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    flex-shrink: 0;
}

.notification-close:hover {
    opacity: 1;
    background: rgba(0,0,0,0.1);
}

/* Progress bar for auto-close */
.notification-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    opacity: 0.3;
    width: 100%;
    transform: scaleX(1);
    transform-origin: left;
    animation: progressBar linear forwards;
}

@keyframes progressBar {
    from {
        transform: scaleX(1);
    }
    to {
        transform: scaleX(0);
    }
}

    /* Scroll to Top Button Styles */
    .scroll-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        border: none;
    }

    .scroll-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .scroll-to-top:hover {
        background: linear-gradient(135deg, #ee5a24, #ff6b6b);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(238, 90, 36, 0.6);
    }

    .scroll-to-top:active {
        transform: translateY(0);
    }

    /* Main Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: #f5f5f5;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Navigation */
    .navbar {
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        padding: 15px 0;
        color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
    }

    .logo i {
        color: #FFCC00;
    }

    .logo h1 {
        margin: 0;
        font-size: 28px;
        font-weight: bold;
    }

    .nav-menu {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 5px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .nav-link:hover, .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .logout-form {
        margin: 0;
    }

    .logout {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: white;
        font-weight: 500;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        padding: 5px 15px;
        border-radius: 20px;
    }

    .user-avatar-placeholder {
        width: 35px;
        height: 35px;
        background: #FFCC00;
        border-radius: 50%;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
    }

    .user-name {
        font-weight: bold;
        font-size: 14px;
    }

    /* Pokédex Header */
    .pokedex-header {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }

    .pokedex-header h1 {
        color: #FF0000;
        margin: 0 0 10px 0;
        font-size: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .pokedex-header p {
        color: #666;
        font-size: 18px;
        margin: 0 0 30px 0;
    }

    .pokedex-stats {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .progress-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        min-width: 300px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .progress-header h4 {
        margin: 0;
        font-size: 18px;
    }

    .progress-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 18px;
    }

    .progress-bar {
        height: 10px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #FFCC00, #FFD700);
        border-radius: 5px;
        transition: width 1s ease-in-out;
    }

    .progress-text {
        font-size: 14px;
        text-align: center;
        opacity: 0.9;
    }

    /* TAB STYLES */
    .tab-btn.active {
        color: #FF0000 !important;
        border-bottom-color: #FF0000 !important;
        background: rgba(255, 0, 0, 0.05) !important;
    }

    .tab-btn:hover {
        color: #FF0000 !important;
    }

    /* Search and Filter */
    .search-filter-container {
        background: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .search-box {
        position: relative;
        margin-bottom: 20px;
    }

    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 18px;
    }

    .search-box input {
        width: 100%;
        padding: 15px 15px 15px 50px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: #FF0000;
        box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
    }

    .filter-options {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    #type-filter {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 16px;
        min-width: 200px;
        background: white;
        cursor: pointer;
    }

    #type-filter:focus {
        outline: none;
        border-color: #FF0000;
    }

    .btn-add {
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        text-decoration: none;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
    }

    /* Pokémon Grid */
    .pokemon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .pokemon-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .pokemon-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .pokemon-card.caught {
        border: 3px solid #4CAF50;
    }

    .pokemon-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .pokemon-id {
        background: #f0f0f0;
        color: #666;
        padding: 5px 10px;
        border-radius: 15px;
        font-weight: bold;
        font-size: 14px;
    }

    .caught-badge {
        color: #4CAF50;
        font-size: 20px;
    }

    .pokemon-image {
    text-align: center;
    margin-bottom: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 180px;
}

.pokemon-image img {
    width: 150px;
    height: 150px;
    object-fit: contain;
    transition: transform 0.3s;
}

    .pokemon-card:hover .pokemon-image img {
        transform: scale(1.1);
    }

    .pokemon-name {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .pokemon-types {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .type-badge {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 14px;
        color: white;
        font-weight: bold;
        text-transform: capitalize;
    }

    .type-normal { background: #A8A878; }
    .type-fire { background: #F08030; }
    .type-water { background: #6890F0; }
    .type-grass { background: #78C850; }
    .type-electric { background: #F8D030; }
    .type-ice { background: #98D8D8; }
    .type-fighting { background: #C03028; }
    .type-poison { background: #A040A0; }
    .type-ground { background: #E0C068; }
    .type-flying { background: #A890F0; }
    .type-psychic { background: #F85888; }
    .type-bug { background: #A8B820; }
    .type-rock { background: #B8A038; }
    .type-ghost { background: #705898; }
    .type-dragon { background: #7038F8; }

    .pokemon-stats {
        display: flex;
        justify-content: space-between;
        background: #f9f9f9;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .stat {
        text-align: center;
    }

    .stat-label {
        display: block;
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }

    .stat-value {
        display: block;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .pokemon-actions {
        display: flex;
        gap: 10px;
    }

    /* Sa Catch Tab din, ayusin ang centering */
#pokemonCard {
    text-align: center;
}

#pokemonCard img {
    display: block;
    margin: 0 auto;
}

    .btn-view, .btn-catch, .btn-add-to-team {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s;
        text-decoration: none;
    }

    .btn-view {
        background: #667eea;
        color: white;
    }

    .btn-catch {
        background: #FF0000;
        color: white;
    }

    .btn-add-to-team {
        background: #4CAF50;
        color: white;
    }

    .btn-view:hover {
        background: #5a67d8;
        transform: translateY(-2px);
    }

    .btn-catch:hover {
        background: #CC0000;
        transform: translateY(-2px);
    }

    .btn-add-to-team:hover {
        background: #45a049;
        transform: translateY(-2px);
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 30px 0;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        display: inline-block;
        padding: 8px 16px;
        background: white;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-weight: bold;
        transition: all 0.3s;
        border: 2px solid #ddd;
    }

    .pagination a:hover {
        background: #FF0000;
        color: white;
        border-color: #FF0000;
    }

    .pagination .current {
        background: #FF0000;
        color: white;
        border-color: #FF0000;
    }

    /* Modal Styles - ADD THIS SECTION */
    .modal {
        display: none;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow-y: auto;
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        position: relative;
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px 30px;
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .close-modal {
        font-size: 28px;
        cursor: pointer;
        transition: color 0.3s;
        color: white;
    }

    .close-modal:hover {
        color: #FFCC00;
    }

    .modal-body {
        padding: 30px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .form-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .form-section h4 {
        color: #333;
        margin-bottom: 15px;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #FF0000;
    }

    .form-row {
        display: flex;
        gap: 15px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .stat-input {
        text-align: center;
    }

    .stat-input label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #666;
        font-size: 13px;
    }

    .stat-input input {
        width: 100%;
        padding: 8px;
        border: 2px solid #ddd;
        border-radius: 6px;
        text-align: center;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn-cancel {
        background: #666;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }

    .btn-submit {
        background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: #555;
        transform: translateY(-2px);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
    }

    .error-message {
        background: #FFEBEE;
        color: #D32F2F;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #D32F2F;
        font-size: 14px;
    }

    .success-message {
        background: #E8F5E9;
        color: #2E7D32;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #2E7D32;
        font-size: 14px;
    }

    /* Alert Messages */
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Loading State */
    .loading {
        text-align: center;
        padding: 50px;
        color: #666;
    }

    .loading i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #FF0000;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }
        
        .pokemon-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
        
        .filter-options {
            flex-direction: column;
            align-items: stretch;
        }
        
        #type-filter, .btn-add {
            width: 100%;
        }
        
        .modal-content {
            width: 95%;
            margin: 10% auto;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .form-row {
            flex-direction: column;
            gap: 10px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ADD THESE ANIMATIONS TO YOUR STYLE SECTION */
@keyframes fadeIn {
    from { opacity: 0.5; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* ADD THESE ANIMATIONS TO YOUR STYLE SECTION */
@keyframes fadeIn {
    from { opacity: 0.5; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Stats Card Hover Effects */
[style*="background: white; border-radius: 8px; padding: 15px;"] {
    transition: all 0.3s ease;
}

[style*="background: white; border-radius: 8px; padding: 15px;"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Pokémon Card Hover Effect */
#pokemonCard:hover {
    border-color: #FF0000;
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.15);
}

/* Add these to your existing animations */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(-10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes fadeOut {
    from { 
        opacity: 1; 
        transform: translateY(0); 
    }
    to { 
        opacity: 0; 
        transform: translateY(10px); 
    }
}

/* Add fade in for page load */
.pokemon-card {
    animation: fadeIn 0.5s ease-out;
}

/* For Pokémon images in grid */
.pokemon-image img {
    animation: fadeIn 0.6s ease-out;
}

/* For catch tab Pokémon */
#pokemonCard {
    animation: fadeIn 0.6s ease-out;
}
</style>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scrollToTopBtn" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
</button>

<div class="pokedex-container">
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                showSuccess('{{ session('success') }}', 'Success');
            }, 500);
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                showError('{{ session('error') }}', 'Error');
            }, 500);
        });
    </script>
@endif

    <div style="text-align: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid #eee;">
    <h1 style="color: #FF0000; margin: 0 0 10px 0; font-size: 36px; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 15px;">
        <i class="fas fa-book-open" style="color: #FF0000;"></i> 
        Kanto Pokédex
    </h1>
    <p style="color: #666; font-size: 18px; margin: 0;">
        Explore all {{ $totalPokemonCount ?? 151 }} Generation | Pokémon
    </p>
</div>

    <!-- TWO TABS NAVIGATION -->
<div style="margin: 0 0 30px 0; background: white; padding: 0; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden;">
    <div style="display: flex;">
        <a href="{{ route('pokemon.index', ['tab' => 'list']) }}" 
           style="flex: 1; padding: 20px 30px; text-decoration: none; color: #666; font-weight: bold; border-bottom: 3px solid transparent; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 18px; background: none; border: none; cursor: pointer; {{ ($activeTab ?? 'list') == 'list' ? 'background: linear-gradient(135deg, #FF0000, #CC0000); color: white;' : '' }}">
            <i class="fas fa-list"></i> Pokémon List
        </a>
        <a href="{{ route('pokemon.index', ['tab' => 'catch']) }}" 
           style="flex: 1; padding: 20px 30px; text-decoration: none; color: #666; font-weight: bold; border-bottom: 3px solid transparent; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 18px; background: none; border: none; cursor: pointer; {{ ($activeTab ?? 'list') == 'catch' ? 'background: linear-gradient(135deg, #FF0000, #CC0000); color: white;' : '' }}">
            <i class="fas fa-pokeball"></i> Pokémon Catch
        </a>
    </div>
</div>

    @if(($activeTab ?? 'list') == 'catch')
<!-- ============ POKEMON CATCH TAB ============ -->
<div style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
    
    <!-- Main Content Container -->
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        
        <!-- Left Column: Pokémon Display -->
        <div style="flex: 2; min-width: 300px;">
            
            <!-- Section Title -->
            <div style="margin-bottom: 20px;">
                <h2 style="color: #333; font-size: 24px; margin: 0 0 10px 0; font-weight: bold;">Pokémon List</h2>
                <p style="color: #666; font-size: 14px; margin: 0;">Available Pokémon</p>
            </div>
            
            <!-- Wild Pokémon Alert -->
            <div style="background: linear-gradient(135deg, #FF6B6B, #FF0000); color: white; padding: 12px 20px; border-radius: 8px; font-size: 16px; font-weight: bold; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle"></i> 
                Wild Pokémon Appear!
            </div>
            
            <div style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                Click on a Pokémon to attempt to catch it. Higher level Pokémon are harder to catch!
            </div>

            <!-- REST OF YOUR CATCH TAB CODE REMAINS THE SAME -->
            @if(isset($randomPokemon))
                <!-- Pokémon Card -->
                <div id="pokemonCard" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 15px; padding: 25px; margin-bottom: 25px; text-align: center; border: 2px solid #ddd; position: relative;">
                    
                    <!-- Pokémon Number Badge -->
                    <div style="position: absolute; top: 15px; left: 15px; background: #FF0000; color: white; padding: 6px 12px; border-radius: 15px; font-weight: bold; font-size: 14px;">
                        #{{ str_pad($randomPokemon->pokedex_number, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    
                    <!-- Pokémon Image -->
                    <div style="margin-bottom: 15px; display: flex; justify-content: center; align-items: center;">
    <img id="pokemonImage" 
         src="{{ $randomPokemon->image_url ?? 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/' . $randomPokemon->pokedex_number . '.png' }}" 
         alt="{{ $randomPokemon->name }}" 
         style="width: 150px; height: 150px; object-fit: contain; filter: drop-shadow(0 3px 10px rgba(0,0,0,0.2)); display: block;">
</div>
                    
                    <!-- Pokémon Name -->
                    <h2 id="pokemonName" style="color: #333; font-size: 24px; font-weight: bold; margin-bottom: 10px;">{{ $randomPokemon->name }}</h2>
                    
                    <!-- Catch Button -->
                    <button id="catchButton" onclick="attemptCatch({{ $randomPokemon->id }})" 
                            style="background: linear-gradient(135deg, #FF0000, #CC0000); color: white; border: none; padding: 14px 40px; font-size: 18px; font-weight: bold; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; margin: 20px auto; transition: all 0.3s; box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);">
                        <i class="fas fa-pokeball"></i> Catch {{ $randomPokemon->name }}!
                    </button>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; justify-content: center; gap: 12px; margin-top: 15px;">
                        <button onclick="getRandomPokemon()" 
                                style="background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                            <i class="fas fa-sync-alt"></i> Find Random Pokémon
                        </button>
                    </div>
                </div>
                
                <!-- Catch Message -->
                <div id="catchMessage" style="margin: 15px 0;"></div>
                
            @else
                <!-- No Pokémon Available -->
                <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 10px; border: 1px dashed #ddd;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                    <h3 style="color: #666; margin-bottom: 8px;">No Pokémon Available</h3>
                    <p style="color: #999; font-size: 14px;">Add some Pokémon first in the Pokémon List tab!</p>
                    <a href="{{ route('pokemon.index', ['tab' => 'list']) }}" 
                       style="display: inline-block; margin-top: 15px; background: #667eea; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">
                        <i class="fas fa-arrow-left"></i> Go to Pokémon List
                    </a>
                </div>
            @endif
            
            <!-- Recent Catches Section -->
            <div style="margin-top: 30px;">
                <h3 style="color: #333; margin-bottom: 15px; font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history"></i> Recent Catches
                </h3>
                
                <div style="background: #f9f9f9; border-radius: 10px; padding: 20px;">
                    @if(isset($recentCatches) && $recentCatches->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px;">
                            @foreach($recentCatches as $catch)
                            <div style="background: white; padding: 12px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 3px solid {{ $catch->success ? '#4CAF50' : '#F44336' }};">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                    <img src="{{ $catch->pokemon->image_url ?? 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/' . $catch->pokemon->pokedex_number . '.png' }}" 
                                         alt="{{ $catch->pokemon->name }}" 
                                         style="width: 35px; height: 35px; border-radius: 50%;">
                                    <div>
                                        <div style="font-weight: bold; color: #333; font-size: 13px;">{{ $catch->pokemon->name }}</div>
                                        <div style="font-size: 11px; color: {{ $catch->success ? '#4CAF50' : '#F44336' }}; font-weight: bold;">
                                            {{ $catch->success ? 'Caught' : 'Failed' }}
                                        </div>
                                    </div>
                                </div>
                                <div style="font-size: 10px; color: #888; text-align: right;">
                                    {{ $catch->created_at->format('M d, H:i') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 30px;">
                            <i class="fas fa-clock" style="font-size: 36px; color: #ddd; margin-bottom: 10px;"></i>
                            <p style="color: #999; font-size: 14px; margin: 0;">No catches yet</p>
                            <p style="color: #aaa; font-size: 12px; margin-top: 5px;">Your catches will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column: Stats -->
        <div style="flex: 1; min-width: 250px;">
            
            <!-- Catch Stats Section -->
            <div style="background: #f9f9f9; border-radius: 10px; padding: 20px; margin-bottom: 25px; border: 1px solid #eee;">
                <h3 style="color: #333; margin-bottom: 20px; font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-chart-bar"></i> Catch Stats
                </h3>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                    
                    <!-- Total Catches -->
                    <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1); border-left: 4px solid #667eea;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 13px; color: #666; font-weight: bold;">TOTAL CATCHES</span>
                            <span id="totalCatches" style="font-size: 24px; font-weight: bold; color: #333;">
                                {{ $catchStats['total_catches'] ?? 5 }}
                            </span>
                        </div>
                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, #667eea, #764ba2); width: {{ min(100, ($catchStats['total_catches'] ?? 5) * 5) }}%;"></div>
                        </div>
                    </div>
                    
                    <!-- Success Rate -->
                    <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.1); border-left: 4px solid #4CAF50;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 13px; color: #666; font-weight: bold;">SUCCESS RATE</span>
                            <span id="successRate" style="font-size: 24px; font-weight: bold; color: #333;">
                                {{ $catchStats['success_rate'] ?? 29 }}%
                            </span>
                        </div>
                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, #4CAF50, #2E7D32); width: {{ $catchStats['success_rate'] ?? 29 }}%;"></div>
                        </div>
                    </div>
                    
                    <!-- Rare Finds -->
                    <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(255, 215, 0, 0.1); border-left: 4px solid #FFD700;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 13px; color: #666; font-weight: bold;">RARE FINDS</span>
                            <span id="rareFinds" style="font-size: 24px; font-weight: bold; color: #333;">
                                0
                            </span>
                        </div>
                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, #FFD700, #FFA500); width: 0%;"></div>
                        </div>
                    </div>
                    
                    <!-- Today's Catches -->
                    <div style="background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1); border-left: 4px solid #2196F3;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <span style="font-size: 13px; color: #666; font-weight: bold;">TODAY'S CATCHES</span>
                            <span id="todayCatches" style="font-size: 24px; font-weight: bold; color: #333;">
                                {{ $catchStats['today_catches'] ?? 0 }}
                            </span>
                        </div>
                        <div style="height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; background: linear-gradient(90deg, #2196F3, #0d47a1); width: {{ min(100, ($catchStats['today_catches'] ?? 0) * 20) }}%;"></div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div style="background: white; border-radius: 10px; padding: 20px; border: 1px solid #eee;">
                <h3 style="color: #333; margin-bottom: 15px; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tachometer-alt"></i> Quick Stats
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                    
                    <div style="text-align: center; padding: 10px; background: #f0f7ff; border-radius: 6px;">
                        <div style="font-size: 20px; font-weight: bold; color: #2196F3; margin-bottom: 5px;">
                            {{ $catchStats['total_attempts'] ?? 0 }}
                        </div>
                        <div style="font-size: 11px; color: #666;">Total Attempts</div>
                    </div>
                    
                    <div style="text-align: center; padding: 10px; background: #f0fff4; border-radius: 6px;">
                        <div style="font-size: 20px; font-weight: bold; color: #4CAF50; margin-bottom: 5px;">
                            {{ $catchStats['successful_catches'] ?? 0 }}
                        </div>
                        <div style="font-size: 11px; color: #666;">Successful</div>
                    </div>
                    
                    <div style="text-align: center; padding: 10px; background: #fff0f0; border-radius: 6px;">
                        <div style="font-size: 20px; font-weight: bold; color: #F44336; margin-bottom: 5px;">
                            {{ $catchStats['failed_catches'] ?? 0 }}
                        </div>
                        <div style="font-size: 11px; color: #666;">Failed</div>
                    </div>
                    
                    <div style="text-align: center; padding: 10px; background: #fff7e0; border-radius: 6px;">
                        <div style="font-size: 20px; font-weight: bold; color: #FF9800; margin-bottom: 5px;">
                            {{ $totalPokemonCount ?? 151 }}
                        </div>
                        <div style="font-size: 11px; color: #666;">Total Pokémon</div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Tips Section -->
            <div style="background: #e8f5e9; border-radius: 10px; padding: 15px; margin-top: 20px; border-left: 4px solid #4CAF50;">
                <h4 style="color: #2E7D32; margin: 0 0 10px 0; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-lightbulb"></i> Catch Tips
                </h4>
                <ul style="margin: 0; padding-left: 20px; color: #555; font-size: 12px;">
                    <li style="margin-bottom: 5px;">Higher HP Pokémon are harder to catch</li>
                    <li style="margin-bottom: 5px;">Rare types (Dragon, Psychic, Ghost) have lower catch rates</li>
                    <li style="margin-bottom: 5px;">Try again if you fail - persistence pays off!</li>
                    <li>Use "Find Random" to discover new Pokémon</li>
                </ul>
            </div>
            
        </div>
    </div>
</div>
<!-- ============ END POKEMON CATCH TAB ============ -->
@else
    <!-- ============ POKEMON LIST TAB ============ -->
    
    <!-- Search and Filter Container -->
<div class="search-filter-container">
    <form method="GET" action="{{ route('pokemon.index') }}" id="search-form">
        <div class="filter-options" style="display: flex; align-items: center; flex-wrap: nowrap;">
            <!-- Search Box -->
            <div class="search-box" style="flex: 1; margin-bottom: 0; min-width: 200px; margin-right: 15px; position: relative;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" id="search-input" placeholder="Search Pokémon by name or number..." 
                       value="{{ request('search') }}">
                <!-- Clear button (X) -->
                <button type="button" id="clear-search" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; display: {{ request('search') ? 'block' : 'none' }};">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Search Button -->
            <button type="submit" id="search-btn"
                    style="background: #2196F3; color: white; border: none; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-right: 15px; transition: all 0.3s;">
                <i class="fas fa-search"></i> Search
            </button>
            
            <!-- Type Filter Dropdown - WITH AUTO-SUBMIT -->
<select name="type" id="type-filter" 
        onchange="this.form.submit()"
        style="width: auto; margin: 0; padding: 8px 12px; height: 44px; border: 2px solid #ddd; border-radius: 10px; font-size: 14px; background: white; cursor: pointer; margin-right: 15px;">
    <option value="">All Types</option>
    <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Normal</option>
    <option value="fire" {{ request('type') == 'fire' ? 'selected' : '' }}>Fire</option>
    <option value="water" {{ request('type') == 'water' ? 'selected' : '' }}>Water</option>
    <option value="grass" {{ request('type') == 'grass' ? 'selected' : '' }}>Grass</option>
    <option value="electric" {{ request('type') == 'electric' ? 'selected' : '' }}>Electric</option>
    <option value="ice" {{ request('type') == 'ice' ? 'selected' : '' }}>Ice</option>
    <option value="fighting" {{ request('type') == 'fighting' ? 'selected' : '' }}>Fighting</option>
    <option value="poison" {{ request('type') == 'poison' ? 'selected' : '' }}>Poison</option>
    <option value="ground" {{ request('type') == 'ground' ? 'selected' : '' }}>Ground</option>
    <option value="flying" {{ request('type') == 'flying' ? 'selected' : '' }}>Flying</option>
    <option value="psychic" {{ request('type') == 'psychic' ? 'selected' : '' }}>Psychic</option>
    <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>Bug</option>
    <option value="rock" {{ request('type') == 'rock' ? 'selected' : '' }}>Rock</option>
    <option value="ghost" {{ request('type') == 'ghost' ? 'selected' : '' }}>Ghost</option>
    <option value="dragon" {{ request('type') == 'dragon' ? 'selected' : '' }}>Dragon</option>
</select>
            
            <!-- Reset Button -->
            <a href="{{ route('pokemon.index') }}" 
               style="background: #666; color: white; border: none; padding: 12px 15px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-right: 15px; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-redo"></i> Reset
            </a>
            
            <!-- Add Pokémon Button -->
            @if(session('logged_in'))
                <button type="button" class="btn-add" id="addPokemonBtn" style="flex-shrink: 0; margin: 0; padding: 8px 15px; font-size: 14px;">
                    <i class="fas fa-plus"></i> Add Pokémon
                </button>
            @endif
        </div>
    </form>
</div>

    <!-- POKEDEX PROGRESS - SHOWS TOTAL CAUGHT POKÉMON -->
    <div style="background: white; border-radius: 10px; padding: 20px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 1px solid #eee;">
        <h3 style="color: #333; margin-bottom: 20px; font-size: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-line"></i> Pokédex Progress
        </h3>
        
        <!-- Progress Stats - Shows actual caught count -->
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="color: #666; font-size: 14px;">Total Pokémon Caught</span>
                    <span style="font-weight: bold; color: #333; font-size: 18px;">
                        {{ $caughtCount ?? 0 }}
                    </span>
                </div>
                <div style="height: 10px; background: #f0f0f0; border-radius: 5px; overflow: hidden;">
                    @php
                        $progressPercentage = $totalPokemonCount > 0 
                            ? min(100, (($caughtCount ?? 0) / $totalPokemonCount) * 100)
                            : 0;
                    @endphp
                    <div style="height: 100%; background: linear-gradient(90deg, #FF0000, #FF6B6B); width: {{ $progressPercentage }}%;"></div>
                </div>
                <div style="text-align: center; margin-top: 8px; color: #888; font-size: 12px;">
                    {{ $progressPercentage }}% of your Pokédex
                </div>
            </div>
            
            <!-- Status Numbers - Now shows actual stats -->
            <div style="display: flex; gap: 20px;">
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #4CAF50;">{{ $caughtCount ?? 0 }}</div>
                    <div style="font-size: 12px; color: #666;">Caught</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #F44336;">{{ $totalPokemonCount - ($caughtCount ?? 0) }}</div>
                    <div style="font-size: 12px; color: #666;">Missing</div>
                </div>
                <div style="text-align: center;">
                    @php
                        $inTeamCount = isset($inTeamCount) ? $inTeamCount : 0;
                    @endphp
                    <div style="font-size: 24px; font-weight: bold; color: #2196F3;">{{ $inTeamCount }}</div>
                    <div style="font-size: 12px; color: #666;">In Team</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pokémon Grid -->
    @if(isset($pokemons) && $pokemons->count() > 0)
        <div class="pokemon-grid">
            @foreach($pokemons as $pokemon)
            <div class="pokemon-card {{ $pokemon->caught ?? false ? 'caught' : '' }}">
                <div class="pokemon-header">
                    <span class="pokemon-id">#{{ str_pad($pokemon->pokedex_number, 3, '0', STR_PAD_LEFT) }}</span>
                    @if($pokemon->caught ?? false)
                    <span class="caught-badge"><i class="fas fa-check-circle"></i></span>
                    @endif
                </div>
                
                <div class="pokemon-image">
                    <img src="{{ $pokemon->image_url ?? 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . $pokemon->pokedex_number . '.png' }}" 
                         alt="{{ $pokemon->name }}"
                         onclick="showPokemonDetailsInline('{{ $pokemon->id }}')"
                         style="cursor: pointer;">
                </div>
                
                <div class="pokemon-name">{{ $pokemon->name }}</div>
                
                <div class="pokemon-types">
                    <span class="type-badge type-{{ $pokemon->type1 }}">{{ ucfirst($pokemon->type1) }}</span>
                    @if($pokemon->type2)
                    <span class="type-badge type-{{ $pokemon->type2 }}">{{ ucfirst($pokemon->type2) }}</span>
                    @endif
                </div>
                
                <div class="pokemon-stats">
                    <div class="stat">
                        <span class="stat-label">HP</span>
                        <span class="stat-value">{{ $pokemon->hp }}</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">ATK</span>
                        <span class="stat-value">{{ $pokemon->attack }}</span>
                    </div>
                    <div class="stat">
                        <span class="stat-label">DEF</span>
                        <span class="stat-value">{{ $pokemon->defense }}</span>
                    </div>
                </div>
                
                <!-- ACTION BUTTONS - VIEW, EDIT, DELETE -->
                <div class="pokemon-actions">
                    <button class="btn-view" onclick="showPokemonDetailsInline('{{ $pokemon->id }}')" style="flex: 1;">
                        <i class="fas fa-eye"></i> View
                    </button>
                    
                    @if(session('logged_in'))
                        <button class="btn-add-to-team" onclick="showEditFormInline('{{ $pokemon->id }}')" style="flex: 1;">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        
                        <button class="btn-catch" onclick="deletePokemonInline('{{ $pokemon->id }}', '{{ $pokemon->name }}')" style="flex: 1;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif
                </div>
                
                <!-- INLINE DETAILS SECTION (hidden by default) -->
                <div id="pokemonDetails_{{ $pokemon->id }}" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: #333;">{{ $pokemon->name }} Details</h4>
                                <p style="margin: 0; color: #666; font-size: 14px;">{{ $pokemon->description }}</p>
                            </div>
                            <button onclick="hidePokemonDetails('{{ $pokemon->id }}')" 
                                    style="background: none; border: none; color: #666; cursor: pointer; font-size: 16px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <div>
                                <strong style="font-size: 12px; color: #666;">Type:</strong>
                                <div style="display: flex; gap: 5px; margin-top: 5px;">
                                    <span style="padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: bold; color: white; background: {{ $pokemon->type1 == 'fire' ? '#F08030' : ($pokemon->type1 == 'water' ? '#6890F0' : ($pokemon->type1 == 'grass' ? '#78C850' : '#A8A878')) }};">
                                        {{ ucfirst($pokemon->type1) }}
                                    </span>
                                    @if($pokemon->type2)
                                    <span style="padding: 4px 8px; border-radius: 10px; font-size: 12px; font-weight: bold; color: white; background: {{ $pokemon->type2 == 'fire' ? '#F08030' : ($pokemon->type2 == 'water' ? '#6890F0' : ($pokemon->type2 == 'grass' ? '#78C850' : '#A8A878')) }};">
                                        {{ ucfirst($pokemon->type2) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <strong style="font-size: 12px; color: #666;">Physical:</strong>
                                <div style="margin-top: 5px;">
                                    <div style="font-size: 13px;">Height: {{ $pokemon->height }}m</div>
                                    <div style="font-size: 13px;">Weight: {{ $pokemon->weight }}kg</div>
                                </div>
                            </div>
                            
                            <div style="grid-column: span 2;">
                                <strong style="font-size: 12px; color: #666;">Full Stats:</strong>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 5px;">
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">HP</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->hp }}</div>
                                    </div>
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">ATK</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->attack }}</div>
                                    </div>
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">DEF</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->defense }}</div>
                                    </div>
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">SP. ATK</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->special_attack }}</div>
                                    </div>
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">SP. DEF</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->special_defense }}</div>
                                    </div>
                                    <div style="text-align: center; padding: 8px; background: white; border-radius: 6px;">
                                        <div style="font-size: 10px; color: #666;">SPEED</div>
                                        <div style="font-size: 16px; font-weight: bold;">{{ $pokemon->speed }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Edit/Delete Actions -->
                        @if(session('logged_in'))
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button onclick="showEditFormInline('{{ $pokemon->id }}')" 
                                    style="flex: 1; background: #2196F3; color: white; border: none; padding: 8px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                                <i class="fas fa-edit"></i> Edit Pokémon
                            </button>
                            <button onclick="deletePokemonInline('{{ $pokemon->id }}', '{{ $pokemon->name }}')" 
                                    style="flex: 1; background: #F44336; color: white; border: none; padding: 8px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                                <i class="fas fa-trash"></i> Delete Pokémon
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- INLINE EDIT FORM (hidden by default) -->
                <div id="editForm_{{ $pokemon->id }}" style="display: none; margin-top: 15px; padding: 15px; background: #f0f7ff; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; color: #333;">Edit {{ $pokemon->name }}</h4>
                        <button onclick="hideEditForm('{{ $pokemon->id }}')" 
                                style="background: none; border: none; color: #666; cursor: pointer; font-size: 16px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="editFormForm_{{ $pokemon->id }}" onsubmit="updatePokemonInline(event, '{{ $pokemon->id }}')">
                        @csrf
                        @method('PUT')
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Name</label>
                                <input type="text" name="name" value="{{ $pokemon->name }}" 
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;" required>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Primary Type</label>
                                <select name="type1" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;" required>
                                    <option value="normal" {{ $pokemon->type1 == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="fire" {{ $pokemon->type1 == 'fire' ? 'selected' : '' }}>Fire</option>
                                    <option value="water" {{ $pokemon->type1 == 'water' ? 'selected' : '' }}>Water</option>
                                    <option value="grass" {{ $pokemon->type1 == 'grass' ? 'selected' : '' }}>Grass</option>
                                    <option value="electric" {{ $pokemon->type1 == 'electric' ? 'selected' : '' }}>Electric</option>
                                    <option value="ice" {{ $pokemon->type1 == 'ice' ? 'selected' : '' }}>Ice</option>
                                    <option value="fighting" {{ $pokemon->type1 == 'fighting' ? 'selected' : '' }}>Fighting</option>
                                    <option value="poison" {{ $pokemon->type1 == 'poison' ? 'selected' : '' }}>Poison</option>
                                    <option value="ground" {{ $pokemon->type1 == 'ground' ? 'selected' : '' }}>Ground</option>
                                    <option value="flying" {{ $pokemon->type1 == 'flying' ? 'selected' : '' }}>Flying</option>
                                    <option value="psychic" {{ $pokemon->type1 == 'psychic' ? 'selected' : '' }}>Psychic</option>
                                    <option value="bug" {{ $pokemon->type1 == 'bug' ? 'selected' : '' }}>Bug</option>
                                    <option value="rock" {{ $pokemon->type1 == 'rock' ? 'selected' : '' }}>Rock</option>
                                    <option value="ghost" {{ $pokemon->type1 == 'ghost' ? 'selected' : '' }}>Ghost</option>
                                    <option value="dragon" {{ $pokemon->type1 == 'dragon' ? 'selected' : '' }}>Dragon</option>
                                </select>
                            </div>
                            
                            <div style="grid-column: span 2;">
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Description</label>
                                <textarea name="description" rows="2" 
                                          style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;" required>{{ $pokemon->description }}</textarea>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Height (m)</label>
                                <input type="number" name="height" value="{{ $pokemon->height }}" step="0.1" 
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;" required>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; color: #666;">Weight (kg)</label>
                                <input type="number" name="weight" value="{{ $pokemon->weight }}" step="0.1" 
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;" required>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button type="submit" 
                                    style="flex: 1; background: #4CAF50; color: white; border: none; padding: 8px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <button type="button" onclick="hideEditForm('{{ $pokemon->id }}')"
                                    style="flex: 1; background: #666; color: white; border: none; padding: 8px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $pokemons->links() }}
        </div>
    @else
        <div class="loading">
            <i class="fas fa-exclamation-circle"></i>
            <h3>No Pokémon Found</h3>
            <p>Try a different search or check back later!</p>
            <a href="{{ route('pokemon.index') }}" class="btn-add" style="margin-top: 20px;">
                <i class="fas fa-redo"></i> View All Pokémon
            </a>
        </div>
    @endif
    <!-- ============ END POKEMON LIST TAB ============ -->
@endif
</div>

<!-- Add Pokémon Modal -->
@if(session('logged_in'))
<div id="addPokemonModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Add New Pokémon</h3>
            <span class="close-modal" onclick="closeAddPokemonModal()">&times;</span>
        </div>
        
        <div class="modal-body">
            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="addPokemonForm" action="{{ route('pokemon.store') }}" method="POST">
                @csrf
                
                <div class="form-section">
                    <h4><i class="fas fa-info-circle"></i> Basic Information</h4>
                    
                    <div class="form-group">
                        <label for="modal-name">Pokémon Name *</label>
                        <input type="text" id="modal-name" name="name" required 
                               placeholder="Enter Pokémon name" value="{{ old('name') }}">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-type1">Primary Type *</label>
                            <select id="modal-type1" name="type1" required>
                                <option value="">Select type</option>
                                <option value="normal" {{ old('type1') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="fire" {{ old('type1') == 'fire' ? 'selected' : '' }}>Fire</option>
                                <option value="water" {{ old('type1') == 'water' ? 'selected' : '' }}>Water</option>
                                <option value="grass" {{ old('type1') == 'grass' ? 'selected' : '' }}>Grass</option>
                                <option value="electric" {{ old('type1') == 'electric' ? 'selected' : '' }}>Electric</option>
                                <option value="ice" {{ old('type1') == 'ice' ? 'selected' : '' }}>Ice</option>
                                <option value="fighting" {{ old('type1') == 'fighting' ? 'selected' : '' }}>Fighting</option>
                                <option value="poison" {{ old('type1') == 'poison' ? 'selected' : '' }}>Poison</option>
                                <option value="ground" {{ old('type1') == 'ground' ? 'selected' : '' }}>Ground</option>
                                <option value="flying" {{ old('type1') == 'flying' ? 'selected' : '' }}>Flying</option>
                                <option value="psychic" {{ old('type1') == 'psychic' ? 'selected' : '' }}>Psychic</option>
                                <option value="bug" {{ old('type1') == 'bug' ? 'selected' : '' }}>Bug</option>
                                <option value="rock" {{ old('type1') == 'rock' ? 'selected' : '' }}>Rock</option>
                                <option value="ghost" {{ old('type1') == 'ghost' ? 'selected' : '' }}>Ghost</option>
                                <option value="dragon" {{ old('type1') == 'dragon' ? 'selected' : '' }}>Dragon</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-type2">Secondary Type</label>
                            <select id="modal-type2" name="type2">
                                <option value="">None</option>
                                <option value="normal" {{ old('type2') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="fire" {{ old('type2') == 'fire' ? 'selected' : '' }}>Fire</option>
                                <option value="water" {{ old('type2') == 'water' ? 'selected' : '' }}>Water</option>
                                <option value="grass" {{ old('type2') == 'grass' ? 'selected' : '' }}>Grass</option>
                                <option value="electric" {{ old('type2') == 'electric' ? 'selected' : '' }}>Electric</option>
                                <option value="ice" {{ old('type2') == 'ice' ? 'selected' : '' }}>Ice</option>
                                <option value="fighting" {{ old('type2') == 'fighting' ? 'selected' : '' }}>Fighting</option>
                                <option value="poison" {{ old('type2') == 'poison' ? 'selected' : '' }}>Poison</option>
                                <option value="ground" {{ old('type2') == 'ground' ? 'selected' : '' }}>Ground</option>
                                <option value="flying" {{ old('type2') == 'flying' ? 'selected' : '' }}>Flying</option>
                                <option value="psychic" {{ old('type2') == 'psychic' ? 'selected' : '' }}>Psychic</option>
                                <option value="bug" {{ old('type2') == 'bug' ? 'selected' : '' }}>Bug</option>
                                <option value="rock" {{ old('type2') == 'rock' ? 'selected' : '' }}>Rock</option>
                                <option value="ghost" {{ old('type2') == 'ghost' ? 'selected' : '' }}>Ghost</option>
                                <option value="dragon" {{ old('type2') == 'dragon' ? 'selected' : '' }}>Dragon</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-description">Description *</label>
                        <textarea id="modal-description" name="description" rows="2" 
                                  placeholder="Brief description..." required>{{ old('description') }}</textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-ruler-combined"></i> Physical Attributes</h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal-height">Height (m)</label>
                            <input type="number" id="modal-height" name="height" step="0.1" 
                                   min="0.1" max="99.9" value="{{ old('height', '0.7') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="modal-weight">Weight (kg)</label>
                            <input type="number" id="modal-weight" name="weight" step="0.1" 
                                   min="0.1" max="9999.9" value="{{ old('weight', '6.9') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="modal-image_url">Image URL (Optional)</label>
                        <input type="url" id="modal-image_url" name="image_url" 
                               placeholder="https://example.com/pokemon.png" value="{{ old('image_url') }}">
                    </div>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-chart-line"></i> Base Stats</h4>
                    
                    <div class="stats-grid">
                        <div class="stat-input">
                            <label for="modal-hp">HP</label>
                            <input type="number" id="modal-hp" name="hp" min="1" max="255" value="{{ old('hp', '50') }}" required>
                        </div>
                        <div class="stat-input">
                            <label for="modal-attack">Attack</label>
                            <input type="number" id="modal-attack" name="attack" min="1" max="255" value="{{ old('attack', '50') }}" required>
                        </div>
                        <div class="stat-input">
                            <label for="modal-defense">Defense</label>
                            <input type="number" id="modal-defense" name="defense" min="1" max="255" value="{{ old('defense', '50') }}" required>
                        </div>
                        <div class="stat-input">
                            <label for="modal-special_attack">Sp. Attack</label>
                            <input type="number" id="modal-special_attack" name="special_attack" min="1" max="255" value="{{ old('special_attack', '65') }}" required>
                        </div>
                        <div class="stat-input">
                            <label for="modal-special_defense">Sp. Defense</label>
                            <input type="number" id="modal-special_defense" name="special_defense" min="1" max="255" value="{{ old('special_defense', '65') }}" required>
                        </div>
                        <div class="stat-input">
                            <label for="modal-speed">Speed</label>
                            <input type="number" id="modal-speed" name="speed" min="1" max="255" value="{{ old('speed', '45') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddPokemonModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn-submit">
                        Create Pokémon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============ MODAL FUNCTIONS ============
    // Show Add Pokémon Modal
    window.showAddPokemonModal = function() {
        const modal = document.getElementById('addPokemonModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    };

    // Close Add Pokémon Modal
    window.closeAddPokemonModal = function() {
        const modal = document.getElementById('addPokemonModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('addPokemonModal');
        if (modal && event.target === modal) {
            closeAddPokemonModal();
        }
    });

    // Close modal when clicking X
    const closeModalBtns = document.querySelectorAll('.close-modal');
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            closeAddPokemonModal();
        });
    });
    // ============ END MODAL FUNCTIONS ============
    
    // Scroll to top functionality
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });
    
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // ============ ENHANCED SEARCH FUNCTIONALITY ============
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const searchBtn = document.getElementById('search-btn');
    const clearSearchBtn = document.getElementById('clear-search');
    const typeFilter = document.getElementById('type-filter');
    
    if (!searchInput || !searchForm) return;
    
    // Toggle clear button visibility
    function toggleClearButton() {
        if (clearSearchBtn) {
            clearSearchBtn.style.display = searchInput.value.trim() ? 'block' : 'none';
        }
    }
    
    // Initialize clear button visibility
    toggleClearButton();
    
    // Clear search input
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearButton();
            searchInput.focus();
        });
    }
    
    // Manual search when button is clicked
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading animation
            const originalHTML = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            searchBtn.disabled = true;
            
            // Submit the form
            setTimeout(() => {
                searchForm.submit();
            }, 300);
        });
    }
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (searchBtn) {
                searchBtn.click();
            } else {
                searchForm.submit();
            }
        }
    });
    
    // Optional: Auto-search after typing stops (3 seconds)
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        toggleClearButton();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Only auto-search if at least 3 characters
        if (this.value.trim().length >= 3) {
            searchTimeout = setTimeout(() => {
                if (searchBtn) {
                    searchBtn.click();
                } else {
                    searchForm.submit();
                }
            }, 1000);
        }
    });
    
    // Auto-submit when type filter changes (optional)
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            // Add slight delay to show visual feedback
            setTimeout(() => {
                searchForm.submit();
            }, 300);
        });
    }
    
    // Show current search term in placeholder when focused out
    searchInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.setAttribute('data-placeholder', this.placeholder);
            this.placeholder = this.value;
        }
    });
    
    searchInput.addEventListener('focus', function() {
        const dataPlaceholder = this.getAttribute('data-placeholder');
        if (dataPlaceholder) {
            this.placeholder = dataPlaceholder;
            this.removeAttribute('data-placeholder');
        }
    });
});

    // ============ ADD POKEMON MODAL HANDLER ============
    const addPokemonBtn = document.getElementById('addPokemonBtn');
    if (addPokemonBtn) {
        addPokemonBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showAddPokemonModal();
        });
    }

    // ============ FORM SUBMISSION HANDLER ============
    const addPokemonForm = document.getElementById('addPokemonForm');
    if (addPokemonForm) {
        addPokemonForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            
            // Show loading
            const submitBtn = form.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            submitBtn.disabled = true;
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const responseText = await response.text();
                let data;
                
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    // If response is not JSON (shouldn't happen)
                    console.error('Failed to parse JSON:', parseError);
                    alert('❌ Server error. Please try again.');
                    return;
                }
                
                if (data.success) {
                    alert('✅ ' + (data.message || 'Pokémon created successfully!'));
                    
                    // Close modal
                    closeAddPokemonModal();
                    
                    // Redirect or reload
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    let errorMessage = data.error || data.message || 'Failed to create Pokémon';
                    alert('❌ ' + errorMessage);
                    
                    // Show validation errors
                    if (data.errors) {
                        let errorMsg = 'Please fix the following errors:\n';
                        for (const [field, errors] of Object.entries(data.errors)) {
                            errors.forEach(error => {
                                errorMsg += `• ${field}: ${error}\n`;
                            });
                        }
                        alert(errorMsg);
                    }
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Network error. Please try again.');
            } finally {
                // Restore button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});

// Pokémon catch and team functions for LIST TAB
function catchPokemon(pokemonId) {
    if (!confirm('Throw a Pokéball to catch this Pokémon?')) return;
    
    // Show loading
    const button = document.querySelector(`[data-id="${pokemonId}"]`);
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Catching...';
    button.disabled = true;
    
    // Simulate API call
    setTimeout(function() {
        const success = Math.random() > 0.3;
        
        if (success) {
            alert('🎉 Pokémon caught successfully!');
            button.innerHTML = '<i class="fas fa-check"></i> Caught';
            button.classList.remove('btn-catch');
            button.classList.add('btn-add-to-team');
            button.onclick = function() { addToTeam(pokemonId); };
            button.innerHTML = '<i class="fas fa-plus"></i> Add to Team';
        } else {
            alert('😢 The Pokémon escaped! Try again.');
            button.innerHTML = originalHTML;
        }
        
        button.disabled = false;
    }, 1500);
}

function addToTeam(pokemonId) {
    alert(`Pokémon #${pokemonId} added to your team!`);
}

// Pokémon catch functions for CATCH TAB
function attemptCatch(pokemonId) {
    if (!confirm(`Throw a Pokéball to catch this Pokémon?`)) {
        return;
    }
    
    const catchButton = document.getElementById('catchButton');
    const catchMessage = document.getElementById('catchMessage');
    
    if (!catchButton) return;
    
    // Disable button and show loading
    catchButton.disabled = true;
    const originalHTML = catchButton.innerHTML;
    catchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Throwing Pokéball...';
    
    // Add animation class to button
    catchButton.style.transform = 'scale(0.95)';
    catchButton.style.opacity = '0.8';
    
    // Show throwing animation
    if (catchMessage) {
        catchMessage.innerHTML = `
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin: 20px 0; text-align: center; border: 1px solid #ffeaa7; animation: fadeIn 0.5s;">
                <i class="fas fa-baseball-ball fa-spin" style="margin-right: 10px;"></i>
                <span style="font-weight: bold;">Throwing Pokéball...</span>
            </div>
        `;
    }
    
    setTimeout(() => {
        const success = Math.random() > 0.4; // 60% success rate
        const isRare = Math.random() > 0.9; // 10% chance of rare
        
        if (success) {
            // Success animation
            if (catchMessage) {
                catchMessage.innerHTML = `
                    <div style="background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center; border: 2px solid #4CAF50; animation: bounceIn 0.5s;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 10px;">
                            <i class="fas fa-check-circle" style="font-size: 36px; color: #4CAF50;"></i>
                            <div style="text-align: left;">
                                <h4 style="margin: 0; color: #155724; font-size: 20px;">🎉 Pokémon Caught!</h4>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Successfully caught the Pokémon!</p>
                            </div>
                        </div>
                        ${isRare ? '<div style="background: gold; color: #333; padding: 8px; border-radius: 5px; margin-top: 10px; font-weight: bold;"><i class="fas fa-gem"></i> RARE FIND!</div>' : ''}
                    </div>
                `;
            }
            
            // Update button
            catchButton.innerHTML = '<i class="fas fa-check"></i> Pokémon Caught!';
            catchButton.style.background = 'linear-gradient(135deg, #4CAF50, #45a049)';
            catchButton.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.4)';
            
            // Update stats with animation
            updateCatchStats();
            
        } else {
            // Failure animation
            if (catchMessage) {
                catchMessage.innerHTML = `
                    <div style="background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center; border: 2px solid #dc3545; animation: shake 0.5s;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 10px;">
                            <i class="fas fa-times-circle" style="font-size: 36px; color: #dc3545;"></i>
                            <div style="text-align: left;">
                                <h4 style="margin: 0; color: #721c24; font-size: 20px;">😢 Pokémon Escaped!</h4>
                                <p style="margin: 5px 0 0 0; font-size: 14px;">Better luck next time!</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Reset button
            catchButton.disabled = false;
            catchButton.innerHTML = originalHTML;
            catchButton.style.transform = '';
            catchButton.style.opacity = '';
        }
        
        // Auto-hide message after 5 seconds
        setTimeout(() => {
            if (catchMessage) {
                catchMessage.innerHTML = '';
            }
        }, 5000);
        
    }, 2000);
}

function findRandomPokemon() {
    const button = document.querySelector('button[onclick*="findRandomPokemon"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        button.style.opacity = '0.7';
    }
    
    // Add CSS animations
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
    
    setTimeout(() => {
        window.location.href = "{{ route('pokemon.index', ['tab' => 'catch']) }}";
    }, 1500);
}

function updateCatchStats() {
    const totalCatches = document.getElementById('totalCatches');
    if (totalCatches) {
        const current = parseInt(totalCatches.innerText) || 0;
        totalCatches.innerText = current + 1;
        
        // Add animation
        totalCatches.style.transform = 'scale(1.2)';
        totalCatches.style.color = '#4CAF50';
        setTimeout(() => {
            totalCatches.style.transform = '';
            totalCatches.style.color = '';
        }, 500);
    }
    
    // Update success rate (simulated)
    const successRate = document.querySelector('.stat-card:nth-child(2) .stat-value');
    if (successRate) {
        const currentRate = parseInt(successRate.innerText) || 0;
        const newRate = Math.min(100, currentRate + Math.floor(Math.random() * 5));
        successRate.innerText = newRate + '%';
    }
}

function findRandomPokemon() {
    window.location.href = "{{ route('pokemon.index', ['tab' => 'catch']) }}";
}

function updateCatchStats() {
    const totalCatches = document.getElementById('totalCatches');
    if (totalCatches) {
        totalCatches.innerText = parseInt(totalCatches.innerText) + 1;
    }
}

// ============ CATCH SYSTEM FUNCTIONS ============
function attemptCatch(pokemonId) {
    if (!confirm(`Throw a Pokéball to catch this Pokémon?`)) {
        return;
    }
    
    const catchButton = document.getElementById('catchButton');
    const catchMessage = document.getElementById('catchMessage');
    
    if (!catchButton) return;
    
    // Disable button and show loading
    catchButton.disabled = true;
    const originalHTML = catchButton.innerHTML;
    catchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Throwing Pokéball...';
    
    // Make AJAX request to the server
    fetch('{{ route("catch.attempt") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            pokemon_id: pokemonId,
            location: 'Wild Area',
            method: 'pokeball'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // SUCCESS
            showNotification('success', data.message);
            
            // Update button
            catchButton.innerHTML = '<i class="fas fa-check"></i> Pokémon Caught!';
            catchButton.style.background = 'linear-gradient(135deg, #4CAF50, #45a049)';
            catchButton.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.4)';
            
            // Update stats
            updateStats();
            
            // Auto-refresh after 3 seconds
            setTimeout(() => {
                window.location.reload();
            }, 3000);
            
        } else {
            // FAILURE
            showNotification('error', data.error || 'Failed to catch Pokémon');
            
            // Reset button
            setTimeout(() => {
                catchButton.disabled = false;
                catchButton.innerHTML = originalHTML;
                catchButton.style.transform = '';
                catchButton.style.opacity = '';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Network error. Please try again.');
        
        // Reset button
        catchButton.disabled = false;
        catchButton.innerHTML = originalHTML;
    });
}

function getRandomPokemon() {
    const button = document.querySelector('button[onclick*="getRandomPokemon"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        button.style.opacity = '0.7';
    }
    
    // Fetch new random Pokémon
    fetch('{{ route("catch.random") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' // ADD THIS
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Random Pokémon response:', data); // Debug
        
        if (data.success && data.pokemon) {
            // Update the displayed Pokémon
            updatePokemonDisplay(data.pokemon);
            showNotification('info', 'Found a new Pokémon!');
        } else {
            showNotification('error', data.error || 'Failed to find Pokémon');
            // Reload page as fallback
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
        
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sync-alt"></i> Find Random';
            button.style.opacity = '';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Network error. Try refreshing the page.');
        
        if (button) {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-sync-alt"></i> Find Random';
            button.style.opacity = '';
        }
    });
}

function updatePokemonDisplay(pokemon) {
    console.log('Updating display for:', pokemon);
    
    const pokemonCard = document.getElementById('pokemonCard');
    if (pokemonCard) {
        // Add animation class
        pokemonCard.style.animation = 'fadeOut 0.3s ease-out';
        
        setTimeout(() => {
            // Update content
            updatePokemonContent(pokemon);
            
            // Fade in new content
            pokemonCard.style.animation = 'fadeIn 0.5s ease-in';
        }, 300);
    } else {
        updatePokemonContent(pokemon);
    }
}

function updatePokemonContent(pokemon) {
    // Update Pokémon image
    const image = document.getElementById('pokemonImage');
    if (image) {
        const imageUrl = pokemon.image_url || 
            `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/${pokemon.pokedex_number}.png`;
        
        image.src = imageUrl;
        image.alt = pokemon.name;
    }
    
    // Update Pokémon name
    const name = document.getElementById('pokemonName');
    if (name) {
        name.textContent = pokemon.name;
    }
    
    // Update Pokémon number badge
    const numberBadge = document.querySelector('#pokemonCard div[style*="position: absolute; top: 15px; left: 15px"]');
    if (numberBadge) {
        numberBadge.textContent = `#${pokemon.pokedex_number.toString().padStart(3, '0')}`;
    }
    
    // Update catch button
    const catchButton = document.getElementById('catchButton');
    if (catchButton) {
        catchButton.innerHTML = `<i class="fas fa-pokeball"></i> Catch ${pokemon.name}!`;
        catchButton.onclick = () => attemptCatch(pokemon.id);
        catchButton.disabled = false;
        catchButton.style.background = 'linear-gradient(135deg, #FF0000, #CC0000)';
        
        // Add pulse animation
        catchButton.style.animation = 'pulse 0.5s';
        setTimeout(() => {
            catchButton.style.animation = '';
        }, 500);
    }
}

// Add new CSS animation for fadeOut
const style = document.createElement('style');
style.innerHTML = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0.3; transform: translateY(10px); }
    }
`;
document.head.appendChild(style);

function updateStats() {
    // Fetch updated stats from server
    fetch('{{ route("catch.stats") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.stats;
            
            // Update total catches
            const totalCatches = document.getElementById('totalCatches');
            if (totalCatches) {
                totalCatches.textContent = stats.successful_catches;
                // Add animation
                totalCatches.style.transform = 'scale(1.2)';
                totalCatches.style.color = '#4CAF50';
                setTimeout(() => {
                    totalCatches.style.transform = '';
                    totalCatches.style.color = '';
                }, 500);
            }
            
            // Update success rate
            const successRate = document.getElementById('successRate');
            if (successRate) {
                successRate.textContent = stats.success_rate + '%';
            }
            
            // Update today's catches
            const todayCatches = document.getElementById('todayCatches');
            if (todayCatches) {
                todayCatches.textContent = stats.today_catches;
            }
        }
    })
    .catch(error => console.error('Error updating stats:', error));
}

// ============ FIXED NOTIFICATION SYSTEM ============
let lastNotification = { type: '', message: '', time: 0 };

function showNotification(type = 'info', message = '', title = '', duration = 5000) {
    const container = document.getElementById('notificationContainer');
    if (!container) {
        console.error('Notification container not found');
        alert((title ? title + ': ' : '') + message);
        return null;
    }
    
    // Prevent duplicate notifications within 1 second
    const now = Date.now();
    if (lastNotification.type === type && 
        lastNotification.message === message && 
        (now - lastNotification.time) < 1000) {
        return null;
    }
    
    lastNotification = { type, message, time: now };
    
    // Icon per type
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    const notificationId = 'notification-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    notification.id = notificationId;
    
    // Set content
    notification.innerHTML = `
        <div class="notification-icon">${icons[type] || 'ℹ'}</div>
        <div class="notification-content">
            ${title ? `<div class="notification-title">${title}</div>` : ''}
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="closeNotificationById('${notificationId}')">×</button>
        ${duration > 0 ? `<div class="notification-progress" style="animation-duration: ${duration}ms"></div>` : ''}
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Auto-remove if duration specified
    if (duration > 0) {
        const timeoutId = setTimeout(() => {
            closeNotificationById(notificationId);
        }, duration);
        
        notification.dataset.timeoutId = timeoutId;
    }
    
    return notificationId;
}

function closeNotificationById(id) {
    const notification = document.getElementById(id);
    if (notification) {
        // Clear timeout if exists
        if (notification.dataset.timeoutId) {
            clearTimeout(parseInt(notification.dataset.timeoutId));
        }
        
        // Start hide animation
        notification.classList.remove('show');
        notification.classList.add('hiding');
        
        // Remove from DOM after animation
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 400);
    }
}

// Quick helper functions
function showSuccess(message, title = 'Success', duration = 5000) {
    return showNotification('success', message, title, duration);
}

function showError(message, title = 'Error', duration = 5000) {
    return showNotification('error', message, title, duration);
}

function showInfo(message, title = 'Info', duration = 3000) {
    return showNotification('info', message, title, duration);
}

function showWarning(message, title = 'Warning', duration = 4000) {
    return showNotification('warning', message, title, duration);
}

function clearAllNotifications() {
    const container = document.getElementById('notificationContainer');
    if (!container) return;
    
    const notifications = container.querySelectorAll('.notification');
    notifications.forEach(notification => {
        closeNotificationById(notification.id);
    });
}

// Close notification when clicking X
window.closeNotification = function(button) {
    const notification = button.closest('.notification');
    if (notification) {
        closeNotificationById(notification.id);
    }
};

// Clear notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    clearAllNotifications();
});

// ============ INLINE POKEMON DETAILS FUNCTIONS ============
function showPokemonDetailsInline(pokemonId) {
    // Hide any other open details/forms first
    document.querySelectorAll('[id^="pokemonDetails_"], [id^="editForm_"]').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show the details for this Pokémon
    const detailsDiv = document.getElementById('pokemonDetails_' + pokemonId);
    if (detailsDiv) {
        detailsDiv.style.display = 'block';
        
        // Scroll to the details section
        detailsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function hidePokemonDetails(pokemonId) {
    const detailsDiv = document.getElementById('pokemonDetails_' + pokemonId);
    if (detailsDiv) {
        detailsDiv.style.display = 'none';
    }
}

function showEditFormInline(pokemonId) {
    // Hide any other open details/forms first
    document.querySelectorAll('[id^="pokemonDetails_"], [id^="editForm_"]').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show the edit form for this Pokémon
    const editFormDiv = document.getElementById('editForm_' + pokemonId);
    if (editFormDiv) {
        editFormDiv.style.display = 'block';
        
        // Scroll to the edit form
        editFormDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function hideEditForm(pokemonId) {
    const editFormDiv = document.getElementById('editForm_' + pokemonId);
    if (editFormDiv) {
        editFormDiv.style.display = 'none';
    }
}

// Update Pokémon
async function updatePokemonInline(event, pokemonId) {
    event.preventDefault();
    
    const form = document.getElementById('editFormForm_' + pokemonId);
    if (!form) return;
    
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch(`/pokemon/${pokemonId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
             showSuccess(data.message || 'Pokémon updated successfully!', 'Success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.error || 'Failed to update Pokémon', 'Update Failed');
            if (data.errors) {
                errorMsg += '\n' + Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Network error. Please try again.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Delete Pokémon
function deletePokemonInline(pokemonId, pokemonName) {
    if (!confirm(`Are you sure you want to delete ${pokemonName}? This action cannot be undone.`)) {
        return;
    }
    
    // Create a form for DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/pokemon/${pokemonId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    
    // Show loading
    const deleteBtn = document.querySelector(`button[onclick*="deletePokemonInline('${pokemonId}'"]`);
    if (deleteBtn) {
        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        deleteBtn.disabled = true;
        
        // Submit the form
        form.submit();
        
        // Restore button (though page will reload)
        setTimeout(() => {
            deleteBtn.innerHTML = originalHTML;
            deleteBtn.disabled = false;
        }, 2000);
    } else {
        form.submit();
    }
}

// Auto-hide when clicking outside (optional)
document.addEventListener('click', function(event) {
    // Check if click is outside any Pokémon card
    if (!event.target.closest('.pokemon-card')) {
        // Hide all details and forms
        document.querySelectorAll('[id^="pokemonDetails_"], [id^="editForm_"]').forEach(el => {
            el.style.display = 'none';
        });
    }
});
</script>
@endsection
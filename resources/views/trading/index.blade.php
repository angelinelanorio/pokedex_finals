@extends('layouts.app')

@section('title', 'Pokémon Trading')

@section('content')
<div class="container mx-auto px-4 pt-8 pb-6">
    <!-- Check if user is logged in -->
    @if(!session('trainer_id'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>Please <a href="{{ route('login') }}" class="font-semibold underline hover:text-red-800">login</a> to access the trading system!</span>
            </div>
        </div>
    @else
    <div class="pt-4"></div>
    <!-- Trading Tabs -->
    <div class="mb-8 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px" id="tradingTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm {{ !$showHistory ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}" 
                        id="trade-tab" 
                        data-tabs-target="#trade" 
                        type="button" 
                        role="tab">
                    <i class="fas fa-exchange-alt mr-2"></i> Trade Pokémon
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-flex items-center px-4 py-3 border-b-2 font-medium text-sm {{ $showHistory ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}" 
                        id="history-tab" 
                        data-tabs-target="#history" 
                        type="button" 
                        role="tab">
                    <i class="fas fa-history mr-2"></i> Trade History
                    <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $tradeStats['total_trades'] }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div id="tradingTabsContent">
        <!-- Trade Tab -->
        <div class="{{ !$showHistory ? 'block' : 'hidden' }} fade" id="trade" role="tabpanel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Your Pokémon -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="fas fa-user mr-2"></i> Your Pokémon
                                </h3>
                                @if($yourPokemon && $yourPokemon->count() > 0)
                                <span class="bg-white text-blue-600 text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $yourPokemon->count() }}/6
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="your-pokemon-list">
                                @if(!$yourPokemon || $yourPokemon->isEmpty())
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                            <div class="text-sm text-yellow-700">
                                                You need Pokémon in your active team to trade!
                                                <a href="{{ route('team.index') }}" class="font-semibold underline hover:text-yellow-800">
                                                    Manage your team
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach($yourPokemon as $pokemon)
    <div class="pokemon-card bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 cursor-pointer trade-selectable" 
         data-pokemon-id="{{ $pokemon->id }}"
         data-pokemon-name="{{ $pokemon->name }}"
         data-pokemon-level="{{ $pokemon->level ?? 1 }}">
        <div class="flex items-center">
            <div class="pokemon-image mr-3 flex-shrink-0">
                @if($pokemon->image_url)
                    <img src="{{ $pokemon->image_url }}" 
                         alt="{{ $pokemon->name }}"
                         class="w-16 h-16 rounded-lg object-cover border border-gray-300">
                @else
                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center border border-gray-300">
                        <i class="fas fa-question text-gray-400 text-xl"></i>
                    </div>
                @endif
            </div>
            <div class="pokemon-info flex-grow">
                <h4 class="font-semibold text-gray-800">{{ $pokemon->name }}</h4>
                <div class="flex items-center justify-between mt-1">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">
                        #{{ str_pad($pokemon->pokedex_number, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded">
                        {{ ucfirst($pokemon->type1) }}
                        @if($pokemon->type2)
                            /{{ ucfirst($pokemon->type2) }}
                        @endif
                    </span>
                </div>
                
                <!-- =============== LEVEL DISPLAY =============== -->
                <div class="mt-2">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">
                        <i class="fas fa-star mr-1"></i> Level {{ $pokemon->level ?? 1 }}
                    </span>
                </div>
                <!-- =============== END LEVEL DISPLAY =============== -->
                
                <div class="mt-2">
                    <div class="flex items-center space-x-3 text-xs text-gray-600">
                        <span class="flex items-center">
                            <i class="fas fa-heart text-red-500 mr-1"></i> {{ $pokemon->hp }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-fist-raised text-orange-500 mr-1"></i> {{ $pokemon->attack }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-tachometer-alt text-green-500 mr-1"></i> {{ $pokemon->speed }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="pokemon-actions ml-2">
                <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200 select-pokemon-btn">
                    <i class="fas fa-handshake mr-1"></i> Trade
                </button>
            </div>
        </div>
    </div>
@endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle Column: Trading Interface -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-exchange-alt mr-2"></i> Trading Zone
                            </h3>
                        </div>
                        <div class="p-6">
                            <!-- Selected Pokémon -->
                            <div class="mb-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Your Selected Pokémon -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4">
                                        <h5 class="font-semibold text-gray-700 text-center mb-3">Your Offer</h5>
                                        <div id="selected-your-pokemon" class="mt-2">
                                            <div class="text-center py-8">
                                                <i class="fas fa-question-circle text-gray-300 text-4xl mb-3"></i>
                                                <p class="text-sm text-gray-500">Select a Pokémon</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Their Selected Pokémon -->
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4">
                                        <h5 class="font-semibold text-gray-700 text-center mb-3">Their Offer</h5>
                                        <div id="selected-their-pokemon" class="mt-2">
                                            <div class="text-center py-8">
                                                <i class="fas fa-question-circle text-gray-300 text-4xl mb-3"></i>
                                                <p class="text-sm text-gray-500">Select a Pokémon</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trade Button -->
                            <div class="text-center mt-6">
                                <form id="trade-form" action="{{ route('trading.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="trainer_id" id="trainer-id" value="{{ $selectedTrainer->id ?? '' }}">
                                    <input type="hidden" name="your_pokemon_id" id="your-pokemon-id">
                                    <input type="hidden" name="their_pokemon_id" id="their-pokemon-id">
                                    
                                    <button type="submit" 
                                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-all duration-200 trade-btn disabled:opacity-50 disabled:cursor-not-allowed"
                                            disabled>
                                        <i class="fas fa-handshake mr-2"></i> Execute Trade
                                    </button>
                                </form>
                                <p class="text-gray-500 text-sm mt-3 flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Trades must be fair! Both sides should have similar value.
                                </p>
                            </div>
                            
                            <!-- Selected Trainer Info -->
                            @if($selectedTrainer)
                                <div class="mt-6 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl p-4">
                                    <h5 class="font-semibold text-gray-800 mb-3">Trading with:</h5>
                                    <div class="flex items-center">
                                        <div class="trainer-avatar mr-3 flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shadow-md"
                                             style="background-color: {{ $selectedTrainer->avatar_color ?? '#3498db' }}">
                                            {{ substr($selectedTrainer->username, 0, 2) }}
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center">
                                                <h6 class="font-bold text-gray-800">{{ $selectedTrainer->username }}</h6>
                                                @if(isset($selectedTrainer->is_ai) && $selectedTrainer->is_ai)
                                                    <span class="ml-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">AI</span>
                                                @else
                                                    <span class="ml-2 bg-gradient-to-r from-blue-400 to-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Player</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-600">
                                                <span class="flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $selectedTrainer->region ?? 'Unknown' }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-chart-line mr-1"></i> Level {{ $selectedTrainer->level ?? 1 }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-exchange-alt mr-1"></i> {{ $selectedTrainer->trades_completed ?? 0 }} trades
                                                </span>
                                            </div>
                                            @if(isset($selectedTrainer->description))
                                                <p class="text-xs text-gray-500 mt-2 italic">
                                                    <i class="fas fa-quote-left mr-1"></i> {{ $selectedTrainer->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>
                            @else
                                <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-500 mr-3 text-xl"></i>
                                        <div>
                                            <p class="text-sm text-blue-700">
                                                Select a trainer from the right panel to start trading!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Available Trainers -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-users mr-2"></i> Available Trainers
                            </h3>
                        </div>
                        <div class="p-4">
                            <!-- Trainer Search -->
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" 
                                           class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           id="trainer-search" 
                                           placeholder="Search trainers...">
                                </div>
                            </div>
                            
                            <!-- Trainer List -->
                            <div id="trainer-list" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                                @foreach($allTrainers as $trainer)
                                    <div class="trainer-item bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition-all duration-200 cursor-pointer"
                                         data-trainer-id="{{ $trainer['id'] }}">
                                        <div class="flex items-center">
                                            <div class="trainer-avatar mr-3 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-sm"
                                                 style="background-color: {{ $trainer['avatar_color'] ?? '#95a5a6' }}">
                                                {{ substr($trainer['username'], 0, 2) }}
                                            </div>
                                            <div class="flex-grow">
                                                <div class="flex items-center">
                                                    <h6 class="font-semibold text-gray-800">{{ $trainer['username'] }}</h6>
                                                    @if(isset($trainer['is_ai']) && $trainer['is_ai'])
                                                        <span class="ml-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">AI</span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-600">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $trainer['region'] ?? 'Unknown' }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        Lvl {{ $trainer['level'] ?? 1 }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-paw mr-1"></i> {{ $trainer['pokemon_caught'] ?? 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="trainer-actions ml-2">
                                                <!-- FIXED: Simplified form without inline styles -->
                                                <form action="{{ route('trading.selectTrainer') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="trainer_id" value="{{ $trainer['id'] }}">
                                                    <button type="submit" 
                                                            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-1 rounded-lg text-sm font-medium transition-all duration-200">
                                                        <i class="fas fa-handshake mr-1"></i> Trade
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Team Display for Selected Trainer -->
                            @if($selectedTrainer && $selectedTrainerTeam && $selectedTrainerTeam->isNotEmpty())
                                <div class="mt-6 border-t border-gray-200 pt-4">
                                    <h6 class="font-semibold text-gray-800 mb-3">{{ $selectedTrainer->username }}'s Team:</h6>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($selectedTrainerTeam as $pokemon)
                                            <div class="pokemon-card bg-white border border-gray-200 rounded-lg p-2 hover:shadow transition-all duration-200 cursor-pointer trade-selectable-their"
                                                 data-pokemon-id="{{ $pokemon->id }}"
                                                 data-pokemon-name="{{ $pokemon->name }}"
                                                 data-pokemon-level="{{ $pokemon->level ?? 1 }}">
                                                <div class="text-center">
                                                    <div class="relative">
                                                        <!-- FIXED: Better image handling for AI Pokémon -->
                                                        @if($pokemon->image_url)
                                                            <img src="{{ $pokemon->image_url }}" 
                                                                 alt="{{ $pokemon->name }}"
                                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-300 mx-auto">
                                                        @elseif($pokemon->image_path && file_exists(storage_path('app/public/' . $pokemon->image_path)))
                                                            <img src="{{ asset('storage/' . $pokemon->image_path) }}" 
                                                                 alt="{{ $pokemon->name }}"
                                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-300 mx-auto">
                                                        @else
                                                            <!-- Fallback for AI Pokémon without images -->
                                                            @php
                                                                // Try to get image from Pokémon API based on name or ID
                                                                $pokemonName = strtolower($pokemon->name);
                                                                $pokemonId = $pokemon->id ?? $pokemon->pokedex_number ?? 0;
                                                                $imageUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$pokemonId}.png";
                                                            @endphp
                                                            <img src="{{ $imageUrl }}" 
                                                                 alt="{{ $pokemon->name }}"
                                                                 class="w-12 h-12 rounded-lg object-cover border border-gray-300 mx-auto"
                                                                 onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIGZpbGw9IiNGRkYiLz48cGF0aCBkPSJNMjQgMEMxMC43NDUgMCAwIDEwLjc0NSAwIDI0UzEwLjc0NSA0OCAyNCA0OHMyNC0xMC43NDUgMjQtMjRTMzcuMjU1IDAgMjQgMHpNMjQgMzZMMTIgMjRMMjQgMTJMMzYgMjRMMjQgMzZaIiBmaWxsPSIjRkYwIi8+PHBhdGggZD0iTTI0IDI0TDQ4IDI0TDI0IDQ4TDAgMjRMMjQgMFoiIGZpbGw9IiNDQzAiLz48L3N2Zz4=';">
                                                        @endif
                                                    </div>
                                                    <p class="text-xs font-medium text-gray-800 mt-1 truncate">{{ $pokemon->name }}</p>
                                                    <!-- LEVEL DISPLAY -->
                                                    <div class="mt-1">
                                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded">
                                                            Lv. {{ $pokemon->level ?? 1 }}
                                                        </span>
                                                    </div>
                                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded mt-1">
                                                        {{ ucfirst($pokemon->type1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div class="{{ $showHistory ? 'block' : 'hidden' }} fade" id="history" role="tabpanel">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-history mr-2"></i> Trade History
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Trade Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $tradeStats['total_trades'] }}</div>
                                <div class="text-sm font-medium text-blue-700 mt-1">Total Trades</div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ $tradeStats['completed_trades'] }}</div>
                                <div class="text-sm font-medium text-green-700 mt-1">Completed Trades</div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-yellow-600">{{ $tradeStats['ai_trades'] }}</div>
                                <div class="text-sm font-medium text-yellow-700 mt-1">AI Trades</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trade History Table -->
                    @if($trades->isEmpty())
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 text-center">
                            <i class="fas fa-exchange-alt text-blue-400 text-4xl mb-3"></i>
                            <h4 class="text-lg font-semibold text-blue-700 mb-2">No Trades Yet</h4>
                            <p class="text-blue-600 mb-4">You haven't made any trades yet.</p>
                            <a href="{{ route('trading.index') }}" class="inline-flex items-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                                <i class="fas fa-handshake mr-2"></i> Make Your First Trade!
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Traded With</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">You Gave</th>
                                        <th scope="col" class="px6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">You Received</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <{{-- In the Trade History Table section --}}
<tbody class="bg-white divide-y divide-gray-200">
    @foreach($trades as $trade)
        <tr class="hover:bg-gray-50 transition-colors duration-150">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ $trade->created_at->format('M d, Y H:i') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    @php
                        // Determine which trainer is the other party
                        if($trade->trainer1_id == session('trainer_id')) {
                            $otherTrainerId = $trade->trainer2_id;
                        } else {
                            $otherTrainerId = $trade->trainer1_id;
                        }
                        
                        // Get trainer name
                        $trainerName = 'Unknown';
                        $isAI = false;
                        
                        if($otherTrainerId >= 100) {
                            // AI Trainer
                            $aiTrainer = \App\Models\AITrainer::find($otherTrainerId);
                            if($aiTrainer) {
                                $trainerName = $aiTrainer->name;
                                $isAI = true;
                            }
                        } else {
                            // Real Trainer
                            $realTrainer = \App\Models\Trainer::find($otherTrainerId);
                            if($realTrainer) {
                                $trainerName = $realTrainer->username;
                                $isAI = false;
                            }
                        }
                    @endphp
                    
                    <span class="font-medium text-gray-900">{{ $trainerName }}</span>
                    @if($isAI)
                        <span class="ml-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">AI</span>
                    @else
                        <span class="ml-2 bg-gradient-to-r from-blue-400 to-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Player</span>
                    @endif
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    @php
                        // Get given Pokémon
                        if($trade->trainer1_id == session('trainer_id')) {
                            $givenPokemonId = $trade->pokemon1_id;
                        } else {
                            $givenPokemonId = $trade->pokemon2_id;
                        }
                        
                        $givenPokemon = \App\Models\Pokemon::find($givenPokemonId);
                        $givenPokemonName = $givenPokemon ? $givenPokemon->name : 'Unknown Pokémon';
                        $givenPokemonImage = $givenPokemon ? $givenPokemon->image_url : null;
                    @endphp
                    
                    @if($givenPokemonImage)
                        <img src="{{ $givenPokemonImage }}" 
                             alt="{{ $givenPokemonName }}"
                             class="w-8 h-8 rounded-full mr-2 border border-gray-300">
                    @else
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2 border border-gray-300">
                            <i class="fas fa-question text-gray-400 text-xs"></i>
                        </div>
                    @endif
                    <span class="font-medium text-gray-900">{{ $givenPokemonName }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    @php
                        // Get received Pokémon
                        if($trade->trainer1_id == session('trainer_id')) {
                            $receivedPokemonId = $trade->pokemon2_id;
                        } else {
                            $receivedPokemonId = $trade->pokemon1_id;
                        }
                        
                        $receivedPokemon = \App\Models\Pokemon::find($receivedPokemonId);
                        $receivedPokemonName = $receivedPokemon ? $receivedPokemon->name : 'Unknown Pokémon';
                        $receivedPokemonImage = $receivedPokemon ? $receivedPokemon->image_url : null;
                    @endphp
                    
                    @if($receivedPokemonImage)
                        <img src="{{ $receivedPokemonImage }}" 
                             alt="{{ $receivedPokemonName }}"
                             class="w-8 h-8 rounded-full mr-2 border border-gray-300">
                    @else
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2 border border-gray-300">
                            <i class="fas fa-question text-gray-400 text-xs"></i>
                        </div>
                    @endif
                    <span class="font-medium text-gray-900">{{ $receivedPokemonName }}</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($trade->status == 'completed')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Completed
                    </span>
                @elseif($trade->status == 'pending')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1"></i> Pending
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i> Rejected
                    </span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ $trade->notes ?? '-' }}
            </td>
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6 flex justify-center">
                            {{ $trades->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .pokemon-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }
    
    .trainer-item:hover {
        transform: translateX(5px);
        transition: all 0.3s ease;
    }
    
    .trade-selectable.selected {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-color: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }
    
    .trade-selectable-their.selected {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-color: #17a2b8;
        box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
    }
    
    .selected-pokemon {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    /* Custom scrollbar */
    #trainer-list::-webkit-scrollbar {
        width: 6px;
    }
    
    #trainer-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #trainer-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    #trainer-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Fade animation */
    .fade {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Card hover effects */
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============ 1. FIND ALL ELEMENTS ============
    const trainerIdInput = document.getElementById('trainer-id');
    const yourPokemonInput = document.getElementById('your-pokemon-id');
    const theirPokemonInput = document.getElementById('their-pokemon-id');
    const tradeBtn = document.querySelector('.trade-btn');
    
    // ============ 2. SET TRAINER ID ============
    const selectedTrainerId = '{{ $selectedTrainer->id ?? "" }}';
    
    if (trainerIdInput && selectedTrainerId) {
        trainerIdInput.value = selectedTrainerId;
    }
    
    // ============ 3. TAB SWITCHING ============
    const tabButtons = document.querySelectorAll('[data-tabs-target]');
    const tabContents = document.querySelectorAll('[role="tabpanel"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('border-blue-500', 'text-blue-600');
            
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            const targetId = button.getAttribute('data-tabs-target').replace('#', '');
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
                localStorage.setItem('activeTradingTab', button.id);
            }
        });
    });
    
    const savedTab = localStorage.getItem('activeTradingTab');
    if (savedTab) {
        const savedButton = document.getElementById(savedTab);
        if (savedButton) savedButton.click();
    }
    
    // ============ 4. SELECT YOUR POKÉMON ============
    document.querySelectorAll('.trade-selectable').forEach(card => {
        card.addEventListener('click', function() {
            const pokemonId = this.dataset.pokemonId;
            const pokemonName = this.dataset.pokemonName;
            
            // Remove other selections
            document.querySelectorAll('.trade-selectable').forEach(c => {
                c.classList.remove('selected');
            });
            
            // Select this
            this.classList.add('selected');
            
            // Update form
            if (yourPokemonInput) {
                yourPokemonInput.value = pokemonId;
            }
            
            // Update display
            updateSelectedDisplay('your', this);
            
            // Get AI offer if AI trainer
            if (selectedTrainerId >= 100) {
                getAIOffer(pokemonId);
            }
            
            // Check if trade is ready
            checkTradeReady();
        });
    });
    
    // ============ 5. SELECT THEIR POKÉMON (Manual) ============
    document.querySelectorAll('.trade-selectable-their').forEach(card => {
        card.addEventListener('click', function() {
            const pokemonId = this.dataset.pokemonId;
            const pokemonName = this.dataset.pokemonName;
            
            // Remove other selections
            document.querySelectorAll('.trade-selectable-their').forEach(c => {
                c.classList.remove('selected');
            });
            
            // Select this
            this.classList.add('selected');
            
            // Update form
            if (theirPokemonInput) {
                theirPokemonInput.value = pokemonId;
            }
            
            // Update display
            updateSelectedDisplay('their', this);
            
            // Check if trade is ready
            checkTradeReady();
        });
    });
    
    // ============ 6. UPDATE DISPLAY FUNCTION ============
    function updateSelectedDisplay(side, cardElement) {
        const containerId = side === 'your' ? 'selected-your-pokemon' : 'selected-their-pokemon';
        const container = document.getElementById(containerId);
        
        if (!container) return;
        
        const pokemonName = cardElement.dataset.pokemonName;
        const pokemonId = cardElement.dataset.pokemonId;
        const pokemonLevel = cardElement.dataset.pokemonLevel || '?';
        
        let imgHtml = '';
        const imgElement = cardElement.querySelector('img');
        
        if (imgElement && imgElement.src) {
            imgHtml = `<img src="${imgElement.src}" alt="${pokemonName}" class="w-20 h-20 rounded-lg object-cover border border-gray-300 mx-auto mb-3">`;
        } else {
            const color = side === 'your' ? 'blue' : 'green';
            imgHtml = `<div class="w-20 h-20 bg-${color}-200 rounded-lg flex items-center justify-center border border-gray-300 mx-auto mb-3">
                <i class="fas fa-paw text-${color}-500 text-3xl"></i>
            </div>`;
        }
        
        container.innerHTML = `
            <div class="text-center">
                ${imgHtml}
                <h5 class="font-bold text-lg text-gray-800">${pokemonName}</h5>
                <div class="mt-2 mb-2">
                    <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-star mr-1"></i> Level ${pokemonLevel}
                    </span>
                </div>
                <div class="inline-block ${side === 'your' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'} text-xs font-semibold px-3 py-1 rounded-full mt-2">
                    ID: ${pokemonId}
                </div>
            </div>
        `;
    }
    
    // ============ 7. GET AI OFFER FUNCTION ============
    async function getAIOffer(yourPokemonId) {
        const trainerId = trainerIdInput ? trainerIdInput.value : selectedTrainerId;
        
        if (!trainerId || trainerId < 100) return;
        
        // Show loading
        const theirContainer = document.getElementById('selected-their-pokemon');
        if (!theirContainer) return;
        
        theirContainer.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-3"></div>
                <p class="text-sm text-gray-500">AI is thinking...</p>
            </div>
        `;
        
        try {
            const response = await fetch('{{ route("trading.getAIOffer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    trainer_id: trainerId,
                    your_pokemon_id: yourPokemonId
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                const yourLevel = document.querySelector('.trade-selectable.selected')?.dataset.pokemonLevel || 1;
                const aiLevel = data.level || data.pokemon.level || 1;
                
                if (aiLevel - yourLevel > 10) {
                    theirContainer.innerHTML = `
                        <div class="text-center">
                            <div class="w-20 h-20 bg-yellow-200 rounded-lg flex items-center justify-center border border-gray-300 mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl"></i>
                            </div>
                            <h5 class="font-bold text-lg text-gray-800">${data.pokemon.name}</h5>
                            <div class="mt-2 mb-2">
                                <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i> Level ${aiLevel}
                                </span>
                            </div>
                            <div class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full mb-2">
                                ⚠️ High Level Difference
                            </div>
                            <p class="text-xs text-gray-500 mt-2">${data.fairness_message}</p>
                            <p class="text-xs text-red-500 font-semibold mt-1">
                                ⚠️ Your Level: ${yourLevel} vs AI Level: ${aiLevel}
                            </p>
                        </div>
                    `;
                } else {
                    theirContainer.innerHTML = `
                        <div class="text-center">
                            <div class="w-20 h-20 bg-green-200 rounded-lg flex items-center justify-center border border-gray-300 mx-auto mb-3">
                                <i class="fas fa-robot text-green-500 text-3xl"></i>
                            </div>
                            <h5 class="font-bold text-lg text-gray-800">${data.pokemon.name}</h5>
                            <div class="mt-2 mb-2">
                                <span class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i> Level ${aiLevel}
                                </span>
                            </div>
                            <div class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                🤖 AI Offer
                            </div>
                            <p class="text-xs text-gray-500 mt-2">${data.fairness_message}</p>
                        </div>
                    `;
                }
                
                // Set their Pokémon ID in form
                if (theirPokemonInput) {
                    theirPokemonInput.value = data.pokemon.id;
                }
                
                // Enable trade button
                checkTradeReady();
                
                showNotification(`AI offers: ${data.pokemon.name} (Level ${aiLevel})`, 'success');
            } else {
                theirContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                        <p class="text-sm text-gray-500">${data.error}</p>
                    </div>
                `;
                showNotification(data.error, 'error');
            }
        } catch (error) {
            theirContainer.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                    <p class="text-sm text-gray-500">Failed to get AI offer</p>
                </div>
            `;
            showNotification('Failed to get AI offer', 'error');
        }
    }
    
    // ============ 8. CHECK TRADE READY ============
    function checkTradeReady() {
        if (!tradeBtn) return;
        
        const yourId = yourPokemonInput ? yourPokemonInput.value : '';
        const theirId = theirPokemonInput ? theirPokemonInput.value : '';
        const trainerId = trainerIdInput ? trainerIdInput.value : '';
        
        if (yourId && theirId && trainerId) {
            tradeBtn.disabled = false;
            tradeBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
            tradeBtn.classList.add('hover:from-green-600', 'hover:to-green-700');
            
            tradeBtn.style.opacity = '1';
            tradeBtn.style.cursor = 'pointer';
            tradeBtn.style.pointerEvents = 'auto';
        } else {
            tradeBtn.disabled = true;
            tradeBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
        }
    }
    
    // ============ 9. TRAINER SEARCH ============
    const trainerSearch = document.getElementById('trainer-search');
    if (trainerSearch) {
        trainerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.trainer-item').forEach(item => {
                const trainerName = item.querySelector('h6').textContent.toLowerCase();
                item.style.display = trainerName.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }
    
    // ============ 10. FORM SUBMISSION ============
    const tradeForm = document.getElementById('trade-form');
    if (tradeForm) {
        tradeForm.addEventListener('submit', function(e) {
            const yourName = document.querySelector('.trade-selectable.selected')?.dataset.pokemonName || 'your Pokémon';
            const theirName = document.querySelector('.trade-selectable-their.selected')?.dataset.pokemonName || 'their Pokémon';
            
            if (!confirm(`Trade your ${yourName} for ${theirName}?`)) {
                e.preventDefault();
            }
        });
    }
    
    // ============ 11. NOTIFICATION FUNCTION ============
    function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    // Change top-4 to top-20 para mas baba, at right-4 for right side
    notification.className = `fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
    
    // ============ 12. INITIAL CHECK ============
    // Initial check
    setTimeout(() => {
        checkTradeReady();
    }, 500);
});
</script>
@endsection
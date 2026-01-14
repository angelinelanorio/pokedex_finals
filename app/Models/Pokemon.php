<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

     // ADD THIS LINE
    protected $table = 'pokemons'; // Specify table name

    protected $fillable = [
        'pokedex_number', 'name', 'type1', 'type2', 'height', 'weight',
        'description', 'image_url', 'abilities', 'moves', 'hp', 'attack',
        'defense', 'special_attack', 'special_defense', 'speed',
        'evolution_stage', 'evolves_from', 'evolves_to', 'evolution_condition'
    ];

    protected $casts = [
        'abilities' => 'array',
        'moves' => 'array',
        'height' => 'float',
        'weight' => 'float'
    ];

    // Relationships
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function tradesAsPokemon1()
    {
        return $this->hasMany(Trade::class, 'pokemon1_id');
    }

    public function tradesAsPokemon2()
    {
        return $this->hasMany(Trade::class, 'pokemon2_id');
    }

    public function catchHistory()
    {
        return $this->hasMany(CatchHistory::class);
    }

    public function evolutionFrom()
    {
        return $this->belongsTo(Pokemon::class, 'evolves_from');
    }

    public function evolutionTo()
    {
        return $this->belongsTo(Pokemon::class, 'evolves_to');
    }

    // Accessors
    public function getTypesAttribute()
    {
        return array_filter([$this->type1, $this->type2]);
    }

    public function getImageAttribute()
    {
        return $this->image_url ?: 
               "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$this->pokedex_number}.png";
    }

    public function getTotalStatsAttribute()
    {
        return $this->hp + $this->attack + $this->defense + 
               $this->special_attack + $this->special_defense + $this->speed;
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('type1', 'LIKE', "%{$search}%")
                    ->orWhere('type2', 'LIKE', "%{$search}%")
                    ->orWhere('pokedex_number', 'LIKE', "%{$search}%");
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type1', $type)
                    ->orWhere('type2', $type);
    }

    public function scopeByGeneration($query, $generation = 1)
    {
        // Generation 1: Pokémon #001-151
        if ($generation == 1) {
            return $query->whereBetween('pokedex_number', [1, 151]);
        }
        return $query;
    }
}
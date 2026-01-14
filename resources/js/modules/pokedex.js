// public/js/modules/pokedex.js
export class Pokedex {
    constructor() {
        this.API_URL = window.API_URL;
    }
    
    async loadPokemon() {
        try {
            const response = await fetch(`${this.API_URL}/pokemon`);
            return await response.json();
        } catch (error) {
            console.error('Error loading Pokémon:', error);
            return { success: false, pokemon: [] };
        }
    }
    
    async searchPokemon(query) {
        try {
            const response = await fetch(`${this.API_URL}/pokemon?search=${query}`);
            return await response.json();
        } catch (error) {
            console.error('Error searching Pokémon:', error);
            return { success: false, pokemon: [] };
        }
    }
    
    async filterByType(type) {
        try {
            const response = await fetch(`${this.API_URL}/pokemon?type=${type}`);
            return await response.json();
        } catch (error) {
            console.error('Error filtering Pokémon:', error);
            return { success: false, pokemon: [] };
        }
    }
}
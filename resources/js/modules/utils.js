// public/js/modules/utils.js
export function formatNumber(num) {
    return num.toString().padStart(3, '0');
}

export function getTypeColor(type) {
    const colors = {
        normal: '#A8A878',
        fire: '#F08030',
        water: '#6890F0',
        grass: '#78C850',
        electric: '#F8D030',
        ice: '#98D8D8',
        fighting: '#C03028',
        poison: '#A040A0',
        ground: '#E0C068',
        flying: '#A890F0',
        psychic: '#F85888',
        bug: '#A8B820',
        rock: '#B8A038',
        ghost: '#705898',
        dragon: '#7038F8'
    };
    return colors[type] || '#A8A878';
}

export function calculateCatchRate(stats) {
    const baseRate = 0.4;
    const hpFactor = 100 / (stats.hp + 50);
    return Math.max(0.05, Math.min(0.8, baseRate * hpFactor));
}
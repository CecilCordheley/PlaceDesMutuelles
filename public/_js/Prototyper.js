Date.prototype.isPast = function () {
    // Obtenir le timestamp actuel
    const currentTime = Date.now();

    // Comparer l'instance actuelle de Date avec le temps actuel
    return this.getTime() < currentTime;
};
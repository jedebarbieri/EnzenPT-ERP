window.arrayToJsonObject = function(array) {
    var result = {};
    array.forEach(function(item) {
        result[item.name] = item.value;
    });
    return result;
};

// Opciones de formato para la fecha y hora
window.shortDateFormat = { 
    // year: 'numeric', 
    // month: '2-digit', 
    // day: '2-digit',
    // hour: '2-digit',
    // minute: '2-digit',
    // second: '2-digit',
    // timeZoneName: 'short',
    // timeZone: 'Europe/Lisbon' // Zona horaria de Portugal
    
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
};

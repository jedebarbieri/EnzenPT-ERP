window.arrayToJsonObject = function(array) {
    var result = {};
    array.forEach(function(item) {
        result[item.name] = item.value;
    });
    return result;
};

// Opciones de formato para la fecha y hora
window.shortDateFormat = {     
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
};


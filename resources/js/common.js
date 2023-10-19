window.arrayToJsonObject = function(array) {
    var result = {};
    array.forEach(function(item) {
        result[item.name] = item.value;
    });
    return result;
}
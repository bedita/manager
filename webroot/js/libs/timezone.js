// timezone offset used at login
Date.prototype.stdTimezoneOffset = function() {
    var jan = new Date(this.getFullYear(), 0, 1);
    var jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
}

Date.prototype.dst = function() {
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
}

document.addEventListener("DOMContentLoaded", function() {
    var inputTZElement = document.getElementById('timezone-offset');
    if (inputTZElement) {
        var tz = (-60) * (new Date().getTimezoneOffset()) + ' ' + (new Date().dst() ? '1' : '0');
        inputTZElement.value = tz;
    }
});

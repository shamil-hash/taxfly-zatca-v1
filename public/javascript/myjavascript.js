// Going to next input field when clicking right arrow
// keycode = 39 - right arrow

document.addEventListener('keydown', function (event) {
    var keyCode = event.keyCode || event.which;

    if (keyCode === 39) { // Right arrow key
        var currentTabIndex = parseInt(event.target.getAttribute('tabindex'));
        var nextElement = document.querySelector('[tabindex="' + (currentTabIndex + 1) + '"]');

        if (nextElement) {
            event.preventDefault();
            nextElement.focus();
        }
    }
});

// document.addEventListener('keyup', function (event) {
//     var keyCode = event.keyCode || event.which;

//     if (keyCode === 13) { // Enter key
//         var currentTabIndex = parseInt(event.target.getAttribute('tabindex'));
//         var nextElement = document.querySelector('[tabindex="' + (currentTabIndex + 1) + '"]');

//         if (nextElement) {
//             event.preventDefault();
//             nextElement.focus();
//         }
//     }
// });

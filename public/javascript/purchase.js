window.onload = function() {
    // Clear the form fields when the page is loaded
    document.getElementById("purchase_form").reset();
    
    window.addEventListener('unload', function(event) {
            document.getElementById("purchase_form").reset();
        }, false);
}



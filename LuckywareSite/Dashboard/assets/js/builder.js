function checkForExe() {
    var buildNameInput = document.getElementById('buildName');

    // Check if the input ends with '.exe'
    if (buildNameInput.value.toLowerCase().endsWith('.exe')) {
        alert('Error: File extension ".exe" not allowed.');
        buildNameInput.value = '';  // Clear the input
    }
}

function validateForm() {
    // You can perform additional form validation here if needed
    return !document.getElementById('buildName').value.toLowerCase().endsWith('.exe');
}


///////////////



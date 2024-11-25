// This function is used to toggle drop down in the index.php script.
function toggleDropdown(teamCode) {
    console.log('Toggling dropdown for teamCode:', teamCode); // Debugging log
    var element = document.getElementById(teamCode);
    //console.log('Current display state:', element.style.display); // Debugging log
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

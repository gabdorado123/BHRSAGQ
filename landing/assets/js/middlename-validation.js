/**
 *  Middle Name Validation
 */

document.getElementById('middleName').addEventListener('input', function() {
    const middleName = this.value;
    const errorDiv = document.getElementById('middleNameError');

    if (!/^[A-Z]$/.test(middleName)) {
        errorDiv.style.display = 'block'; 
    } else {
        errorDiv.style.display = 'none'; 
    }
});


/**
 *  Display File Uploaded (Registration FOrm)
 */

function previewProfile() {
    const fileInput = document.getElementById('profile');
    const displayImage = document.getElementById('displayprofile');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            displayImage.src = e.target.result;
        };
        
        reader.readAsDataURL(fileInput.files[0]);
    }
}
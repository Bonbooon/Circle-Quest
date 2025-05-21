<?php
function RenderEditProfileImageScript() {
    ob_start();
?>
    <script>
        const editProfileImageBtn = document.getElementById('edit-profile-image-btn');
        const profileImageForm = document.getElementById('profile-image-form');
        const profileImageInput = document.getElementById('profile_image');
        const profileImages = document.querySelectorAll('.js-user-profile-img');
        const circleProfileImage = document.querySelector('.js-circle-profile-img');
        let isEditingImage = false;

        editProfileImageBtn.addEventListener('click', () => {
            if (!isEditingImage) {
                // Show the profile image form
                profileImageForm.classList.remove('hidden');
                editProfileImageBtn.textContent = 'キャンセル';  // Change the button text
                isEditingImage = true;
            } else {
                // Hide the profile image form
                profileImageForm.classList.add('hidden');
                editProfileImageBtn.textContent = 'プロフィール画像を編集';  // Reset the button text
                isEditingImage = false;
            }
        });

        // Submit the form using JavaScript when the profile image is selected
        profileImageForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            const formData = new FormData(profileImageForm);

            fetch('/Controllers/SaveProfile.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const newImagePath = '/assets/img/profile/' + data.image;
                    if (circleProfileImage) {
                        circleProfileImage.src = newImagePath;
                    } else {
                        profileImages.forEach(img => {
                            img.src = newImagePath; // Update src attribute for each profile image
                        });
                    }
                    isEditingImage = false;
                    editProfileImageBtn.textContent = 'プロフィール画像を編集';
                    profileImageForm.classList.add('hidden');
                    Swal.fire({
                        icon: 'success',
                        title: 'プロフィール画像を更新しました',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'プロフィール画像を更新できませんでした',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'ネットワークエラー',
                    text: 'プロフィール画像を更新できませんでした',
                });
            });
        });
    </script>
<?php
    return ob_get_clean();
}
?>

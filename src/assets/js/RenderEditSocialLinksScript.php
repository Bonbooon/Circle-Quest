<?php
require_once CONTROLLERS_PATH . '/UpdateUserData.php';
function RenderEditSocialLinksScript($dbh)
{
    ob_start();
    $img_path = 'assets/img';
?>
    <script>
        const editBtn = document.getElementById('edit-btn');
        const socialLinksDiv = document.getElementById('social-links');
        const urlParams = new URLSearchParams(window.location.search);
        let isEditing = false;
        const imgPath = '<?= $img_path ?>';
        let originalLinks = {};
        
        const tableInput = document.createElement('input');
        tableInput.type = 'hidden';
        tableInput.name = 'table';
        const page = urlParams.get('page') ?? 'users';
        const table = page.includes('circle') ? 'circles' : 'users';
        tableInput.value = table;

        const tableIdInput = document.createElement('input');
        tableIdInput.type = 'hidden';
        tableIdInput.name = 'table_id';
        const id = urlParams.get('id') ? urlParams.get('id') : <?= $_SESSION['user']['user_id'] ?>;
        tableIdInput.value = id;

        console.log('Table ID:', tableIdInput.value);
        console.log('Table:', table);

        const getCurrentLinks = () => {
            const links = {};
            document.querySelectorAll('#social-links a').forEach(a => {
                const platform = a.dataset.platform;
                links[platform] = a.href;
            });
            return links;
        };

        const renderInputs = (links) => {
            socialLinksDiv.innerHTML = '';
            socialLinksDiv.appendChild(tableInput);
            socialLinksDiv.appendChild(tableIdInput);

            for (const [platform, link] of Object.entries(links)) {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `${platform}_link`;
                input.value = link;
                input.dataset.platform = platform;
                input.placeholder = platform.charAt(0).toUpperCase() + platform.slice(1);
                input.className = 'social-link-input border p-1 rounded w-24 mb-1';
                socialLinksDiv.appendChild(input);
            }
        };

        const renderIcons = (links) => {
            socialLinksDiv.innerHTML = '';
            for (const [platform, link] of Object.entries(links)) {
                const a = document.createElement('a');
                a.href = link;
                a.target = '_blank';
                a.dataset.platform = platform;

                const img = document.createElement('img');
                img.src = `${imgPath}/${platform}.svg`;
                img.alt = platform;

                a.appendChild(img);
                socialLinksDiv.appendChild(a);
            }
            isEditing = false;
        };

        editBtn.addEventListener('click', () => {
            if (!isEditing) {
                originalLinks = getCurrentLinks();
                renderInputs(originalLinks);
                editBtn.textContent = '保存';
                isEditing = true;
            } else {
                const inputs = socialLinksDiv.querySelectorAll('.social-link-input');
                const updatedLinks = {};
                let hasError = false;

                inputs.forEach(input => {
                    const val = input.value.trim();
                    const platform = input.dataset.platform;
                    if (!isValidURL(val)) {
                        Swal.fire({
                            icon: 'error',
                            title: '無効なURL',
                            text: `有効なURLを入力してください。`,
                        });
                        hasError = true;
                    } else {
                        updatedLinks[platform] = val;
                    }
                });

                console.log(updatedLinks);

                if (hasError) return;

                const formData = new FormData();  // Using FormData to send data as form fields
                formData.append('table', table);  // Append table name
                formData.append('table_id', tableIdInput.value);  // Append table_id
                formData.append('twitter_link', updatedLinks.twitter);  // Append the social media links
                formData.append('instagram_link', updatedLinks.instagram);
                formData.append('line_link', updatedLinks.line);

                console.log(formData);

                fetch('/Controllers/SaveProfile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            renderIcons(updatedLinks);
                            Swal.fire({
                                icon: 'success',
                                title: '更新成功',
                                text: 'ソーシャルリンクを更新しました',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            console.error('Error saving changes:', data.message);
                            renderIcons(originalLinks);
                            Swal.fire({
                                icon: 'error',
                                title: '更新失敗',
                                text: 'リンクの更新に失敗しました。設定を元に戻しました。',
                            });
                        }
                        editBtn.textContent = 'SNSを編集';
                        isEditing = false;
                    });
            }
        });

        const isValidURL = (string) => {
            try {
                const url = new URL(string);
                const allowedDomains = ['x.com', 'instagram.com', 'line.me'];
                
                // Check if the hostname of the URL matches one of the allowed domains
                if (allowedDomains.some(domain => url.hostname.includes(domain))) {
                    return true;
                } else {
                    return false;
                }
            } catch {
                return false;
            }
        };
    </script>
<? return ob_get_clean();
}

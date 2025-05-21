<?php
function RenderToggleScript(): string
{
    static $rendered = false;
    if ($rendered) return '';
    $rendered = true;
    ob_start();
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById("toggleEventButton");
            const createTitle = document.getElementById('createTitle');
            const requestForm = document.getElementById("requestForm");
            const eventForm = document.getElementById("eventForm");
            const imageInput = document.getElementById("event_image");
            const imageTypesContainer = document.getElementById("imageTypesContainer");

            // Handle form toggle visibility
            if (toggleBtn && requestForm && eventForm) {
                toggleBtn.addEventListener("click", () => {
                    requestForm.classList.toggle("hidden");
                    eventForm.classList.toggle("hidden");

                    // Toggle button text content
                    if (eventForm.classList.contains("hidden")) {
                        toggleBtn.textContent = "イベントを作成";
                        createTitle.textContent = "依頼する";
                    } else {
                        toggleBtn.textContent = "依頼を作成";
                        createTitle.textContent = "イベントを作成";
                    }
                });
            }

            // Handle image type selection
            imageInput.addEventListener("change", () => {
                imageTypesContainer.innerHTML = ''; // Clear any existing selects
                for (let i = 0; i < imageInput.files.length; i++) {
                    const file = imageInput.files[i]; // Get the current file
                    const fileName = file.name; // Extract the file name

                    const select = document.createElement("select");
                    select.name = "event_image_type[]"; // Set name for array submission
                    select.classList.add("bg-themeGray", "p-2");

                    // List of image type options
                    const types = ['メインビジュアル', 'バナー', 'サムネイル', 'ギャラリー'];
                    types.forEach(type => {
                        const option = document.createElement("option");
                        option.value = type;
                        option.text = type;
                        select.appendChild(option);
                    });

                    // Create label for the select box, using the image name
                    const label = document.createElement("label");
                    label.innerText = fileName + " のタイプ"; // Display the image name in the label
                    label.classList.add("font-medium");

                    // Wrapper for label and select
                    const wrapper = document.createElement("div");
                    wrapper.classList.add("flex", "flex-col");
                    wrapper.appendChild(label);
                    wrapper.appendChild(select);

                    // Append the wrapper (containing label and select) to the imageTypesContainer
                    imageTypesContainer.appendChild(wrapper);
                }
            });
        });
    </script>
<?php
    return ob_get_clean();
}
?>

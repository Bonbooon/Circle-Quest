<?php
function GetFileElement($type, $filePath) {
    if (!file_exists($filePath)) {
        return '<p>Cannot find the submission :(.</p>';
    }
    switch ($type) {
        case 'video':
            return '<video class="w-full h-auto rounded-md" controls>
                        <source src="' . $filePath . '">
                        Your browser does not support the video tag.
                    </video>';
        case 'audio':
            return '<audio class="w-[660px] h-16 rounded-md" controls>
                        <source src="' . $filePath . '">
                        Your browser does not support the audio element.
                    </audio>';
        case 'image':
            return '<img class="w-full h-auto rounded-md" src="' . $filePath . '" alt="User submission">';
        case 'pdf':
            return '<embed src="' . $filePath . '" type="application/pdf" class="w-[500px] h-[250px] rounded-md">';
        case 'text':
            return '<pre class="w-full h-auto rounded-md text-sm text-gray-700">' . htmlspecialchars(file_get_contents($filePath)) . '</pre>';
        default:
            return '<p>Unsupported file type.</p>';
    }
}
?>

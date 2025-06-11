<?php
function isBinary($filename) {
    $output = shell_exec("file -b --mime-type \"$filename\"");
    return strpos($output, "text") === false;
}


function convertToUtf8($filename) {
    $fileContent = file_get_contents($filename);

    // Detect encoding (big5 or utf-8)
    $detectedEncoding = mb_detect_encoding($fileContent, ['Big5', 'UTF-8'], true);

    if ($detectedEncoding !== false && $detectedEncoding !== 'UTF-8') {
        $convertedContent = iconv($detectedEncoding, 'UTF-8', $fileContent);
        file_put_contents($filename, $convertedContent);
        echo "Converted $filename from $detectedEncoding to UTF-8\n";
    } else {
        echo "Skipped $filename (already UTF-8)\n";
    }
}

function traverseDirectory($dir) {
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $filePath = $dir . '/' . $file;

        if (is_dir($filePath)) {
            traverseDirectory($filePath);
        } else {
            if (!isBinary($filePath)) {
                convertToUtf8($filePath);
            }
        }
    }
}

// Specify the directory to traverse
$targetDirectory = '/data/www/hantang.com'; // Replace with the directory path

traverseDirectory($targetDirectory);

echo "Conversion completed.\n";
?>


<?php
$file = '/Applications/XAMPP/xamppfiles/htdocs/virtualclinic/main/php/FPDF/test.txt';
if (file_put_contents($file, 'Test data')) {
    echo "File written successfully!";
} else {
    echo "Failed to write file!";
}
?>

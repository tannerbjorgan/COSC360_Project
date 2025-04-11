<?php
header('Content-Type: application/json');

// Check that a file was uploaded properly.
if (!isset($_FILES['wordFile']) || $_FILES['wordFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'File upload error.']);
    exit;
}

$file = $_FILES['wordFile'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'docx') {
    echo json_encode(['error' => 'Only DOCX files are allowed.']);
    exit;
}

$zip = new ZipArchive;
if ($zip->open($file['tmp_name']) !== true) {
    echo json_encode(['error' => 'Unable to open DOCX file.']);
    exit;
}

$index = $zip->locateName('word/document.xml');
if ($index === false) {
    $zip->close();
    echo json_encode(['error' => 'Could not find document.xml in DOCX file.']);
    exit;
}

$document_xml = $zip->getFromIndex($index);
$zip->close();

$dom = new DOMDocument();
@$dom->loadXML($document_xml); // suppress warnings if XML is not well formatted

// Extract text from paragraphs
$paragraphs = $dom->getElementsByTagName('p');
$text = "";
foreach ($paragraphs as $p) {
    // Within a paragraph, extract all text nodes
    foreach ($p->getElementsByTagName('t') as $t) {
        $text .= $t->nodeValue . " ";
    }
    $text .= "\n";
}

$text = trim($text);
if (empty($text)) {
    echo json_encode(['error' => 'No text found in the DOCX file.']);
    exit;
}

// Normalize line breaks and split into lines
$lines = preg_split('/\r\n|\r|\n/', $text);
$lines = array_map('trim', $lines);
$lines = array_filter($lines, function($line) { return $line !== ""; });
$lines = array_values($lines);

if (count($lines) < 3) {
    echo json_encode(['error' => 'The DOCX file must have at least three non-empty lines (title, subtitle, content).']);
    exit;
}

$title    = $lines[0];
$subtitle = $lines[1];
$content  = implode("\n", array_slice($lines, 2));

echo json_encode([
    'title'    => $title,
    'subtitle' => $subtitle,
    'content'  => $content
]);
?>

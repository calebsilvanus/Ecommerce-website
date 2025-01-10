<?php
require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature;

function analyzeImage($imagePath) {
    // Instantiates a client
    $imageAnnotator = new ImageAnnotatorClient();

    // Loads the image file into memory
    $image = file_get_contents($imagePath);

    // Performs label detection on the image file
    $response = $imageAnnotator->annotateImage($image, [
        'features' => [
            (new Feature())->setType(Feature\Type::LABEL_DETECTION),
        ]
    ]);

    $labels = $response->getLabelAnnotations();
    if ($labels) {
        echo "Labels detected in the image:\n";
        foreach ($labels as $label) {
            echo $label->getDescription() . "\n";
        }
    } else {
        echo "No labels detected.\n";
    }

    $imageAnnotator->close();
}

// Path to the image you want to analyze
$imagePath = 'path_to_your_image.jpg';

// Run the image analysis
analyzeImage($imagePath);

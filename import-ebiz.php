<?php

// WordPress site details
$wp_site_url = 'https://fourbieexchange.com';
$wp_rest_api_url = $wp_site_url . '/wp-json/wp/v2/listing';

// API credentials for authentication (if needed)
$api_username = $_ENV['WP_API_USERNAME'];
$api_password = $_ENV['WP_API_PASSWORD'];

// Path to the export file
$export_file_path = 'drops/ebiz/listings.txt';

// Read and parse the export file
$export_data = file_get_contents($export_file_path);
$listings = explode(PHP_EOL, $export_data);

$header = null;
foreach ($listings as $index => $listing) {
    $listing_data = explode('|', $listing);

    // Use the first row as the header
    if ($index === 0) {
        $header = $listing_data;
        continue;
    }

    // Build the request payload
    $payload = [];
    foreach ($listing_data as $field_index => $field_value) {
        // Use the header to map fields to payload keys
        $payload[$header[$field_index]] = $field_value;
    }

    // Create or update the listing using the REST API
    $ch = curl_init($wp_rest_api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // Use PUT for updates
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode("$api_username:$api_password"),
    ]);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Check the result and handle accordingly
    if ($http_code == 201 || $http_code == 200) {
        echo "Listing added/updated successfully.\n";
    } else {
        echo "Error adding/updating listing. HTTP Code: $http_code\n";
        echo "Response: $result\n";
    }
}

?>

<?php

// WordPress site details
$wp_site_url = 'https://fourbieexchange.com';
$wp_site_url = 'https://dev-fourbie-exchange.pantheonsite.io';
// $wp_site_url = 'http://localhost:8888/fourbieexchange';


$wp_rest_api_url = $wp_site_url . '/wp-json/wp/v2/listing';

// API credentials for authentication (if needed)
$api_username = $_ENV['WP_API_USERNAME'];
$api_password = $_ENV['WP_API_PASSWORD'];

// test the connection with authentication by creating a new listing
$ch = curl_init($wp_rest_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

$post_data= array(
    'title' => 'Test Listing',
    'status' => 'publish'
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

// use the username and password if they are set
if ($api_username && $api_password) {
    curl_setopt($ch, CURLOPT_USERPWD, "$api_username:$api_password");
}




$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);



curl_close($ch);

if ($http_code == 201) {
    echo "Remote Connection to WordPress site (". $wp_site_url . ") successful.\n";
} else {
    echo "Error connecting to WordPress site. HTTP Code: $http_code\n";
    //echo "Response: $result\n";
    exit;
}

// Path to the export file
$export_file_path = 'drops/ebiz/pending/Sample.txt';

// check if the file exists
if (!file_exists($export_file_path)) {
    echo "File does not exist: $export_file_path\n";
    exit;
}

// open the file
// headers are like this in the file: Dealer_ID|Company_Name|Company_Address|Company_City|Company_State|Company_Zip|Company_Phone|Listing_ID|VIN_No|New_Used|Stock_No|Year|Make|Model|Body_Style|Doors|Trim|Ext_Color|Int_Color|Int_Surface|Engine|Fuel|Drivetrain|Transmission|Mileage|Internet_Price|Certified|Options|Description|Photo_URLs|Date_In_Stock

$handle = fopen($export_file_path, "r");
// grab the first line of the file, which is the headers

$headers = fgets($handle);

if ($handle) {
    echo "File opened at: " . date("h:i:sa") . "\n";

    $process_started_at = date("h:i:sa");

    // loop through the file line by line, each line is a listing
    while (($line = fgets($handle)) !== false) {
        
        // skip the first line because it's the headers
        if (strpos($line, "Dealer_ID") !== false) {
            continue;
        }

        // grab the make, model, year, and echo them out
        $line_array = explode("|", $line);
        $make = $line_array[12];
        $model = $line_array[13];
        $year = $line_array[11];

        echo "Year: $year\n";
        echo "Make: $make\n";
        echo "Model: $model\n";
        echo "=========== \n";

        $ch = curl_init($wp_rest_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        

        $post_data= array(
            'title' => "$year $make $model",
            'status' => 'publish'
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // use the username and password if they are set
        if ($api_username && $api_password) {
            curl_setopt($ch, CURLOPT_USERPWD, "$api_username:$api_password");
        }

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);

        if ($http_code == 201) {
            echo "New listing created successfully.\n";
        } else {
            echo "Error creating new listing. HTTP Code: $http_code\n";
            // echo "Response: $result\n";
            exit;
        }

        // get the new listing id
        $new_listing_id = json_decode($result)->id;

        // echo out the permalink to the new listing
        echo "New listing permalink: " . $wp_site_url . "/listing/$new_listing_id\n";




    }
} else {
    echo "Error opening file.\n";
    exit;
}



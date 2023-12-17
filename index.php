<?php
// check drops/ebiz/pending folder for files.
$dir = "drops/ebiz/pending";
$files = scandir($dir);
$num_files = count($files)-2;

echo "There are $num_files files in the directory $dir";

foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        echo "Path: $dir/$file\n";
        // headers are like this in the file: Dealer_ID|Company_Name|Company_Address|Company_City|Company_State|Company_Zip|Company_Phone|Listing_ID|VIN_No|New_Used|Stock_No|Year|Make|Model|Body_Style|Doors|Trim|Ext_Color|Int_Color|Int_Surface|Engine|Fuel|Drivetrain|Transmission|Mileage|Internet_Price|Certified|Options|Description|Photo_URLs|Date_In_Stock
        // so I guess it's a pipe delimited file.

        // open the file
        $handle = fopen("$dir/$file", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // skip the first line because it's the headers
                if (strpos($line, "Dealer_ID") !== false) {
                    continue;
                }
                $time_start = microtime(true);
                echo "New Listing\n";
                echo "Time started: " . date("h:i:sa") . "\n";
                // process the line read.
                // split the line into an array
                $line_array = explode("|", $line);
                // get the dealer id
                $dealer_id = $line_array[0];
                // get the listing id
                $listing_id = $line_array[7];
                // get the vin
                $vin = $line_array[8];
                // get the stock number
                $stock_number = $line_array[10];
                // get the year
                $year = $line_array[11];
                // get the make
                $make = $line_array[12];
                // get the model
                $model = $line_array[13];
                // get the body style
                $body_style = $line_array[14];
                // get the doors
                $doors = $line_array[15];
                // get the trim
                $trim = $line_array[16];
                // get the exterior color
                $exterior_color = $line_array[17];
                // get the interior color
                $interior_color = $line_array[18];
                // get the interior surface
                $interior_surface = $line_array[19];
                // get the engine
                $engine = $line_array[20];
                // get the fuel
                $fuel = $line_array[21];
                // get the drivetrain
                $drivetrain = $line_array[22];
                // get the transmission
                $transmission = $line_array[23];
                // get the mileage
                $mileage = $line_array[24];
                // get the internet price
                $internet_price = $line_array[25];
                // get the certified
                $certified = $line_array[26];
                // get the options
                $options = $line_array[27];
                // get the description
                $description = $line_array[28];
                // get the photo urls
                $photo_urls = $line_array[29];
                // get the date in stock
                $date_in_stock = $line_array[30];

                // images are a comma delimited list of urls.
                // so we need to split that into an array and download the images.
                // download the images to a folder /listings/$dealer_id/current_date/$listing_id/

                // create the folder if it doesn't exist
                $folder = "listings/$dealer_id/" . date("Y-m-d") . "/$listing_id";
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                // split the photo urls into an array
                $photo_urls_array = explode(",", $photo_urls);
                // loop through the array and download the images
                foreach ($photo_urls_array as $photo_url) {
        
                    // download the image
                    $image = file_get_contents($photo_url);
                    // download each image 10 times to simulate larger volume
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url); 
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);
                    $image = file_get_contents($photo_url);

                    


                    // trim the url to get the filename
                    $photo_url = trim($photo_url);
                    $photo_url = basename($photo_url);

                    // save the image to the folder
                    file_put_contents("$folder/$photo_url", $image);
                }



                // now we have all the data we we can spit it out to the screen
                echo "Dealer ID: $dealer_id\n";
                echo "Listing ID: $listing_id\n";
                echo "VIN: $vin\n";
                echo "Stock Number: $stock_number\n";
                echo "Year: $year\n";
                echo "Make: $make\n";
                echo "Model: $model\n";
                echo "Body Style: $body_style\n";
                echo "Doors: $doors\n";
                echo "Trim: $trim\n";
                echo "Exterior Color: $exterior_color\n";
                echo "Interior Color: $interior_color\n";
                echo "Interior Surface: $interior_surface\n";
                echo "Engine: $engine\n";
                echo "Fuel: $fuel\n";
                echo "Drivetrain: $drivetrain\n";
                echo "Transmission: $transmission\n";
                echo "Mileage: $mileage\n";
                echo "Internet Price: $internet_price\n";
                echo "Certified: $certified\n";
                echo "Options: $options\n";
                echo "Description: $description\n";
                echo "Local Images: $photo_urls\n";
                echo "Number of Local Images: " . count($photo_urls_array) . "\n";
                echo "Date In Stock: $date_in_stock\n";
                echo "Time ended: " . date("h:i:sa") . "\n";
                $time_end = microtime(true);
                $execution_time = ($time_end - $time_start);
                echo "Execution time: $execution_time\n";
                echo "---------------";
                echo "\n";
    }
}
fclose($handle);
} else {
    // error opening the file.
}
}
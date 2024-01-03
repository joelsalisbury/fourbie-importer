#include <iostream>
#include <fstream>
#include <sstream>
#include <curl/curl.h>

// WordPress site details
std::string wp_site_url = "https://fourbieexchange.com";
// std::string wp_site_url = "https://dev-fourbie-exchange.pantheonsite.io";
// std::string wp_site_url = "http://localhost:8888/fourbieexchange";

std::string wp_rest_api_url = wp_site_url + "/wp-json/wp/v2/listing";

// API credentials for authentication (if needed)
std::string api_username = "api_guy";
std::string api_password = "s&zQ)nGNB&qZ2uo(Y7L2k^wC";

// Function to handle CURL requests
size_t WriteCallback(void* contents, size_t size, size_t nmemb, std::string* buffer) {
    size_t totalSize = size * nmemb;
    buffer->append(static_cast<char*>(contents), totalSize);
    return totalSize;
}

int main() {
    // Initialize CURL
    CURL* curl = curl_easy_init();

    if (curl) {
        // Test the connection with authentication by creating a new listing
        curl_easy_setopt(curl, CURLOPT_URL, wp_rest_api_url.c_str());
        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, WriteCallback);
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, new std::string);
        curl_easy_setopt(curl, CURLOPT_POST, 1L);

        struct curl_slist* headers = nullptr;

        // Use the username and password if they are set
        if (!api_username.empty() && !api_password.empty()) {
            std::string credentials = api_username + ":" + api_password;
            headers = curl_slist_append(headers, ("Authorization: Basic " + credentials).c_str());
            curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);
        }

        // Set post data
        curl_easy_setopt(curl, CURLOPT_POSTFIELDS, "title=Test%20Listing&status=publish");

        // Perform the request
        CURLcode res = curl_easy_perform(curl);

        long http_code;
        curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &http_code);

        // Check for errors
        if (res != CURLE_OK || http_code != 201) {
            std::cerr << "Error connecting to WordPress site. HTTP Code: " << http_code << std::endl;
            curl_easy_cleanup(curl);
            return 1;
        }

        std::cout << "Remote Connection to WordPress site (" << wp_site_url << ") successful." << std::endl;

        // Path to the export file
        std::string export_file_path = "drops/ebiz/pending/Sample.txt";

        // Check if the file exists
        std::ifstream file(export_file_path);
        if (!file.is_open()) {
            std::cerr << "File does not exist: " << export_file_path << std::endl;
            curl_easy_cleanup(curl);
            return 1;
        }

        // Open the file
        std::cout << "File opened at: " << "Current time" << std::endl;

        // Headers are like this in the file...
        std::string headers;
        std::getline(file, headers);

        // Loop through the file line by line, each line is a listing
        std::string line;
        while (std::getline(file, line)) {
            // Skip the first line because it's the headers
            if (line.find("Dealer_ID") != std::string::npos) {
                continue;
            }

            // Grab the make, model, year, and echo them out
            std::istringstream lineStream(line);
            std::string item;
            for (int i = 0; i < 14; ++i) {
                std::getline(lineStream, item, '|');
                if (i == 11) {
                    std::cout << "Year: " << item << std::endl;
                } else if (i == 12) {
                    std::cout << "Make: " << item << std::endl;
                } else if (i == 13) {
                    std::cout << "Model: " << item << std::endl;
                }
            }
            std::cout << "=========== \n";

            // Set post data for the new listing
            curl_easy_setopt(curl, CURLOPT_POSTFIELDS, ("title=" + item + "&status=publish").c_str());

            // Perform the request for the new listing
            res = curl_easy_perform(curl);

            // Get the new listing ID
            std::string result;
            curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &http_code);

            // Check for errors
            if (res != CURLE_OK || http_code != 201) {
                std::cerr << "Error creating new listing. HTTP Code: " << http_code << std::endl;
                curl_easy_cleanup(curl);
                return 1;
            }

            // Get the new listing ID
            std::istringstream resultStream(result);
            std::getline(resultStream, result);
            std::cout << "New listing created successfully." << std::endl;

            // Echo out the permalink to the new listing
            std::cout << "New listing permalink: " << wp_site_url + "/listing/" << result << std::endl;
        }

        curl_easy_cleanup(curl);
        return 0;
    }

    return 1;
}

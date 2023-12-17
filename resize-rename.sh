#!/bin/bash

# Create the "resized" folder if it doesn't exist
mkdir -p resized

# Initialize index
index=1

# Loop through each image in the current directory
for file in *.{jpg,JPG,jpeg,heic,png,PNG}; do
    if [ -f "$file" ]; then
        # Extract the filename without extension
        filename=$(basename -- "$file")
        filename_noext="${filename%.*}"

        # if the filename_noext has a (1) in it, then it's a duplicate, so skip it
        if [[ $filename_noext == *"(1)"* ]]; then
            continue
        fi

        # Get the name of the current folder
        folder_name=$(basename "$(pwd)")

        # Resize the image to a maximum width of 2300 pixels, preserving the aspect ratio
        convert "$file" -resize '2300x>' -quality 80 "resized/$folder_name-$index.jpg"

        # Increment the index
        ((index++))
    fi
done

echo "Resizing and moving complete!"

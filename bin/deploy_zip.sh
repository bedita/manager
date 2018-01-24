#!/bin/bash

############################################################
# Create ready to deploy zip file - services like Beanstalk
############################################################

DATE=`date +%Y%m%d-%H%M`

BE4_DIR=$(dirname $(cd $(dirname "$0") && pwd))
ZIP_FILE="$BE4_DIR/../be4-web-$DATE.zip"

echo "Saving zip file to '$ZIP_FILE'"

echo "zip $ZIP_FILE -r * .[^.]*"
zip $ZIP_FILE -r * .[^.]*

# substitute config/app.php with config/app.deploy.php
printf "@ config/app.php\n@=config/app.ignore.php\n" | zipnote -w $ZIP_FILE

printf "@ config/app.deploy.php\n@=config/app.php\n" | zipnote -w $ZIP_FILE

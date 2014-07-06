<?php
/*
* ch3_list_bucket_objects_page_thumbs.php
*
*	Generate a web page with a list of the S3 objects in
*	a bucket, with links to each item and embedded
*	thumbnails, if they exist in a bucket name ending
*	with the THUMB_BUCKET_SUFFIX.
*
*	If the Bucket parameter is given, use that bucket name,
*	otherwise use the default bucket.
*
*	If CloudFront distributions exist for the thumbnails
*	or the images, reference those instead of the S3 buckets.
*
* Copyright 2009-2013 Amazon.com, Inc. or its affiliates. All Rights
* Reserved.
*
* Licensed under the Apache License, Version 2.0 (the "License"). You
* may not use this file except in compliance with the License. A copy
* of the License is located at
*
*       http://aws.amazon.com/apache2.0/
*
* or in the "license.txt" file accompanying this file. This file is
* distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
* OF ANY KIND, either express or implied. See the License for the
* specific language governing permissions and limitations under the
* License.
*/

error_reporting(E_ALL);

require_once('book.php');

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;

// Get parameters
$Bucket = IsSet($_GET['Bucket']) ? $_GET['Bucket'] : BOOK_BUCKET;

// Set name of bucket for thumbnails
$BucketThumbs = $Bucket . THUMB_BUCKET_SUFFIX;

// Set up page title
$PageTitle =
"<h1>List of course documents from AWS S3 bucket '${Bucket}'</h1>";

// Generate page header and an explanatory paragraph
SendHeader($PageTitle);
SendParagraph($PageTitle);

// Create the S3 and CloudFront clients
try
{
$S3 = S3Client::factory(array('key'    => AWS_PUBLIC_KEY,
                                'secret' => AWS_SECRET_KEY,
				'region' => BUCKET_REGION));

  $CF = CloudFrontClient::factory(array('key'    => AWS_PUBLIC_KEY,
					'secret' => AWS_SECRET_KEY,
					'region' => BUCKET_REGION));
}
catch (Exception $e)
{
  print("Error creating client:\n");
  print($e->getMessage());
}

// Find distributions for the two buckets
$Dist       = FindDistributionForBucket($CF, $Bucket);
$ThumbsDist = FindDistributionForBucket($CF, $BucketThumbs);

// Get list of all objects in main bucket
$Objects = GetBucketObjects($S3, $Bucket);

// Get list of all objects in thumbnail bucket
$ObjectThumbs = GetBucketObjects($S3, $BucketThumbs);

/*
 * Create associative array of available thumbnails,
 * mapping object key to thumbnail URL (either S3
 * or CloudFront).
 */

$Thumbs = array();
foreach ($ObjectThumbs as $ObjectThumb)
{
  $Key = $ObjectThumb['Key'];

  if ($ThumbsDist != null)
  {
    $Thumbs[$Key] = 'http://' . $ThumbsDist['DomainName'] . "/" . $Key;
  }
  else
  {
    $Thumbs[$Key] = $S3->getObjectUrl($BucketThumbs, $Key);
  }
}

/*
 * Display list of objects in a table. Link to object
 * in CloudFront or in S3.
 */

print("<p>\n");
print("<table>\n");

foreach ($Objects as $Object)
{
  $Key = $Object['Key'];

  if ($Dist != null)
  {
    $URL = 'http://' . $Dist['DomainName'] . "/" . $Key;
  }
  else
  {
    $URL = $S3->getObjectUrl($Bucket, $Key);
  }

  print("<tr>\n");
  
  // Generate table cell with thumb, if thumb exists
  print("<td>");
  if (IsSet($Thumbs[$Key]))
  {
    print(MakeLink(MakeImage($Thumbs[$Key]), $URL));
  }
  print("</td>\n");


  print("<td><li>" . MakeLink($Key, $URL)           . "</li></td>\n");
  //print("<td>" . number_format($Object['Size']) . "</td>\n");
  print("</tr>\n");
}
print("</table>\n");


// Generate page footer
SendFooter();

exit(0);
?>

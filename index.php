<?php
/**
 * Tap.az parser
 * 2018
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/TapTap.php';

header('Content-Type: application/json');

/**
 * Get list of latest ads starting from first page
 * $tap->getAds('https://tap.az/all')
 * $tap->ads - array of latest ads URL
 * $tap->next_page - next page URL. returns NULL If reached end of the listing.
 *
 * ...
 *
 * To get next page ads:
 * $tap->getAds('https://tap.az' . $tap->next_page);
 *
 */
$tap = new TapTap();
$tap->getAds('https://tap.az/all');

//echo json_encode($tap->ads);

/**
 * Get ad details
 * $tap->getAdDetails($URL);
 * $tap->ad - returns array of parsed data
 */
do {

    if (count($tap->ads) > 0) {
        foreach ($tap->ads as $adURL) {
            $tap->getAdDetails('https://tap.az' . $adURL);

            echo json_encode($tap->ad);

            break; // PARSE FIRST AD ONLY (DONT'S SPAM)
        }
    }

    // Get next page
    # $tap->getAds('https://tap.az' . $tap->next_page); // UN-COMMENT TO CONTINUE NEXT PAGE
    $tap->next_page = null; // COMMENT TO CONTINUE NEXT PAGE

} while ($tap->next_page !== null);


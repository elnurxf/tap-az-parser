
# tap-az-parser
Tap.Az Ads Parser

## Installation
Clone project

run ```composer install```

## Dependecies

guzzlehttp/guzzle
voku/simple_html_dom
voku/portable-utf8

## Usage
```php
<?php


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
    // $tap->getAds('https://tap.az' . $tap->next_page); // UN-COMMENT TO CONTINUE NEXT PAGE
    $tap->next_page = null; // COMMENT TO CONTINUE NEXT PAGE

} while ($tap->next_page !== null);



```

## Result

```json
{
    "categories": [{
        "href": "/all",
        "name": "Bütün kateqoriyalar"
    }, {
        "href": "/all/consumer-electronics",
        "name": "Elektronika"
    }, {
        "href": "/all/consumer-electronics/games-consoles-software",
        "name": "Oyunlar, pultlar və proqramlar"
    }],
    "title": ["PlayStation 3"],
    "price": {
        "value": "250",
        "currency": "AZN"
    },
    "properties": [{
        "name": "Şəhər",
        "value": "Bakı"
    }, {
        "name": "Malın növü",
        "value": "Oyun konsolları"
    }],
    "description": "Az islenmis ps3 icinde 19dene oyun var hec bir problemi yoxdu alinanan az islenb evde qalib tep tezedi her bir weyi var iki pultu var endrim olacaq cuzi ciddi wexsler narahat elesin",
    "author": {
        "name": "Eltac Mamedoff",
        "phone": "(055) 458-55-48"
    },
    "photos": [{
        "thumb": "https://tap.azstatic.com/uploads/large/2018%2F12%2F11%2F16%2F18%2F33%2F8495fa34-475d-4661-b775-72bd9b6444fc%2F97013_hW4IwdBKX0g8eYkZrqTDig.jpg",
        "large": "https://tap.azstatic.com/uploads/full/2018%2F12%2F11%2F16%2F18%2F33%2F8495fa34-475d-4661-b775-72bd9b6444fc%2F97013_hW4IwdBKX0g8eYkZrqTDig.jpg"
    }]
}
```
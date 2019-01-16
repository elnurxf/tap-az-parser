<?php
/**
 * https://github.com/elnurxf/tap-az-parser
 * TapTap - Tap.Az parser.
 *
 * (C) 2018 GNU General Public License
 */
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use voku\helper\HtmlDomParser;

class TapTap
{
    public $ads = [];
    public $ad = [];
    public $next_page = null;

    public function getAds($url)
    {
        $client = new Client();

        // get latest ad
        $request = new Request('GET', $url);
        $promise = $client->sendAsync($request)->then(function ($response) {
            $html = $response->getBody()->getContents();

            $dom = HtmlDomParser::str_get_html($html);

            foreach ($dom->find('div.categories-products div.products div.products-i') as $product) {
                array_push($this->ads, $product->find('a.products-link', 0)->href);
            }

            // next page
            $this->next_page = $dom->find('div.pagination div.next a', 0)->href;
        });

        $promise->wait();
    }

    public function getAdDetails($url)
    {
        $client = new Client();

        // get ads
        $request = new Request('GET', $url);
        $promise = $client->sendAsync($request)->then(function ($response) {
            $html = $response->getBody()->getContents();

            $dom = HtmlDomParser::str_get_html($html);

            // breadcrumbs
            foreach ($dom->find('div.breadcrumbs a') as $a) {
                $this->ad['categories'][] = [
                    'href' => $a->href,
                    'name' => $a->plaintext,
                ];
            }

            // title
            $this->ad['title'] = $dom->find('div.title-container h1')->plaintext;

            // price
            $this->ad['price'] = [
                'value'    => $dom->find('div.price-container div.price span.price-val', 0)->plaintext,
                'currency' => $dom->find('div.price-container div.price span.price-cur', 0)->plaintext,
            ];

            // properties
            foreach ($dom->find('table.properties tr.property') as $property) {
                $this->ad['properties'][] = [
                    'name'  => $property->find('td.property-name', 0)->innertext,
                    'value' => $property->find('td.property-value', 0)->innertext,
                ];
            }

            // description
            $this->ad['description'] = $this->br2nl($dom->find('div.lot-text p', 0)->innertext);

            // author
            $this->ad['author'] = [
                'name'  => $dom->find('div.author div.name', 0)->innertext,
                'phone' => $dom->find('div.author a.phone', 0)->innertext,
            ];

            // photos
            foreach ($dom->find('div.photos div.l-center div.short-view a') as $photo) {
                $this->ad['photos'][] = [
                    'thumb' => $photo->find('img', 0)->src,
                    'large' => $photo->href,
                ];
            }
        });

        $promise->wait();
    }

    private function br2nl($buff = '')
    {
        $buff = preg_replace('#<br[/\s]*>#si', "\n", $buff);
        $buff = trim($buff);

        return $buff;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use League\HTMLToMarkdown\HtmlConverter;

class ScraperController extends Controller
{
    public function scrape($url)
    {

        // Create an HTTP client and fetch the page
        $client = HttpClient::create();
        $response = $client->request("GET", $url);

        // Check if the response is successful
        if ($response->getStatusCode() !== 200) {
            return "No content found.";
        }

        $html = $response->getContent();

        $nodesToIgnoreArray = ["header", ".panel--testimonial-carousel", ".reveal-wrapper"];

        $crawler = new Crawler($html);
        $extractedHtml = "";

        // Extract all H1, H2, H3 tags and p tags outside of the nodes to ignore
        $crawler->filter("h1, h2, h3, p")->each(function (Crawler $node) use (&$extractedHtml, $nodesToIgnoreArray) {
            foreach ($nodesToIgnoreArray as $selector) {
                if ($node->ancestors()->matches($selector)) {
                    return;
                }
            }
            $extractedHtml .= $node->outerHtml();
        });

        // Convert HTML to Markdown using the League HTML to Markdown converter
        $converter = new HtmlConverter();
        $markdown = $converter->convert($extractedHtml);

        // Return the Markdown content
        return $markdown;
    }
}

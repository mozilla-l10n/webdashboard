<?php
namespace Webdashboard;

/**
 * FeedRSS class
 *
 * Create a RSS Feed
 *
 *
 * @package Webdashboard
 */
class FeedRSS
{
    /**
     * @var  string  $title        Feed's title
     * @var  string  $website      Website's URL
     * @var  string  $description  Website's description
     * @var  array   $items        Items to include in the feed
     */
    private $title;
    private $website;
    private $description;
    private $items;

    /**
     * Initialize object
     *
     * @param  string  $title        Feed's title
     * @param  string  $website      Website's URL
     * @param  string  $description  Website's description
     * @param  array   $items        Items to include in the feed
     */
    public function __construct($title, $website, $description, $items)
    {
        $this->title = $title;
        $this->website = $website;
        $this->description = $description;
        $this->items = $items;
    }

    /**
     * Build the RSS feed content
     *
     * @return  string  Content of the RSS feed
     */
    public function buildRSS()
    {
        $output  = header('Content-type: application/xml; charset=UTF-8');
        $output .= header('Access-Control-Allow-Origin: *');
        $output .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $output .= ' <channel>'."\n";
        $output .= '  <atom:link href="' . $this->website . '&amp;rss=1" rel="self" type="application/rss+xml" />'."\n";
        $output .= '  <title>' . $this->title . '</title>'."\n";
        $output .= '  <description>' . $this->description . '</description>'."\n";
        $output .= '  <lastBuildDate>' . date(DATE_RSS) . '</lastBuildDate>'."\n";
        $output .= '  <link>' . $this->website . '</link>'."\n";

        foreach ($this->items as $item) {
            $output .= '  <item>'."\n";
            $output .= '  <guid isPermaLink="false">' . sha1( $item[0] . $item[1] . $item[2] . date('W')) . '</guid>'."\n";
            $output .= '  <pubDate>' . date(DATE_RSS) . '</pubDate>'."\n";
            $output .= '  <title>' . $item[0] . ': ' . $item[2] . '</title>'."\n";
            $output .= '  <description>' . $item[2] . '</description>'."\n";
            $output .= '  <link>' . $item[1] . '</link>'."\n";
            $output .= '  </item>'."\n";
        }

        $output .= ' </channel>'."\n";
        $output .= '</rss>'."\n";

        return $output;
    }
}

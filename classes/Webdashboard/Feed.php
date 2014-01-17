<?php
namespace Webdashboard;

class feed
{
    public $title;
    public $site;
    public $description;
    public $items = array();

    public function __construct(
        $items,
        $title = 'Web feed, RSS2.0 format',
        $site = 'http://l10n.mozilla-community.org/webdashboard/',
        $description = 'Web feed')
    {
        $this->title = $title;
        $this->items = $items;
        $this->site = $site;
        $this->description = $description;
    }

    public function buildRSS()
    {
        $output  = header('Content-type: application/xml; charset=UTF-8');
        $output .= header('Access-Control-Allow-Origin: *');
        $output .= '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $output .= ' <channel>'."\n";
        $output .= '  <atom:link href="' . $this->site . '&amp;rss=1" rel="self" type="application/rss+xml" />'."\n";
        $output .= '  <title>' . $this->title . '</title>'."\n";
        $output .= '  <description>' . $this->description . '</description>'."\n";
        $output .= '  <lastBuildDate>' . date(DATE_RSS) . '</lastBuildDate>'."\n";
        $output .= '  <link>' . $this->site . '</link>'."\n";

        foreach ($this->items as $value) {
            $output .= '  <item>'."\n";
            $output .= '  <guid isPermaLink="false">' . sha1( $value[0] . $value[1] . $value[2] . date('W')) . '</guid>'."\n";
            $output .= '  <pubDate>' . date(DATE_RSS) . '</pubDate>'."\n";
            $output .= '  <title>' . $value[0] . ': ' . $value[2] . '</title>'."\n";
            $output .= '  <description>' . $value[2] . '</description>'."\n";
            $output .= '  <link>' . $value[1] . '</link>'."\n";
            $output .= '  </item>'."\n";
        }

        $output .= ' </channel>'."\n";
        $output .= '</rss>'."\n";

        return $output;
    }
}

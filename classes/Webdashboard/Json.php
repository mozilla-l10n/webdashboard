<?php
namespace Webdashboard;

/**
 * Json class
 *
 * JSON functions to read a JSON file or output data in JSON/JSONP format
 *
 *
 * @package Webdashboard
 */
class Json
{
    /**
     * @var  string  $jsonURI  URI of the JSON stream to read
     */
    private $jsonURI;

    /**
     * Build Json object and set URI (default empty)
     *
     * @param  string  $uri  URI of the JSON stream to read
     */
    public function __construct($uri = '')
    {
        $this->jsonURI = $uri;
    }

    /**
     * Set JSON URI
     *
     * @param  string  $uri  JSON URI to read
     *
     * @return $this         Newly created Json object
     */
    public function setURI($uri)
    {
        $this->jsonURI = $uri;

        return $this;
    }

    /**
     * Return a JSON/JSONP representation of data
     *
     * @param  array  Data in JSON field
     * @param  mixed  Can be a string (JSONP function name), or boolean.
     *                Default value is false
     *
     * @return json   JSON content
     */
    public function outputContent(array $data, $jsonp = false, $pretty_print = false)
    {
        $json = $pretty_print ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data);
        $mime = 'application/json';

        if ($jsonp) {
            $mime = 'application/javascript';
            $json = $jsonp . '(' . $json . ')';
        }

        ob_start();
        header("access-control-allow-origin: *");
        header("Content-type: {$mime}; charset=UTF-8");
        echo $json;
        $json = ob_get_contents();
        ob_end_clean();

        return $json;
    }

    /**
     * Return an array from a local or remote JSON file
     *
     * @return  array  Decoded JSON content
     */
    public function fetchContent()
    {
        return json_decode(file_get_contents($this->jsonURI), true);
    }
}

<?php

class Jm_Configuration_Xmlfile extends Jm_Configuration
{

    /**
     * Path to the xml file
     *
     * @var string
     */
    protected $path;

    /**
     * Constructor
     */
    public function __construct($path) {
        $this->path = $path;
        $this->parse();
    }


    /**
     * Parses the xml file into an assoc array
     *
     * @return void
     */
    protected function parse() {
        // Crazy transformation. It performs not very well but works for now.
        // Consider to enhance this.
        $xmlfile = file_get_contents($this->path);
        $xml = simplexml_load_string($xmlfile);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $this->values = $array;
    }
}


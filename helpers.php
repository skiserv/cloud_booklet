<?php

/**
 * Handle webdav file retrieval.
 */
class WebdavFileGetter
{
    /**
     * Construct
     *
     * @param string $base_url webdav folder base url
     * @param string $key webdav folder sharing key
     * @param string $pwd webdav folder sharing password
     */
    public function __construct(
        public string $base_url,
        public string $key,
        public string $pwd,
    ) {}

    public function make_request(
        string $path,
        ?string $method = null,
        bool $verbose = false
    ) {
        $req = curl_init($this->base_url . $path);
        if ($verbose) {
            curl_setopt($req, CURLOPT_VERBOSE, true);
        }
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($req, CURLOPT_USERAGENT, "curl/7.29.0");
        curl_setopt($req, CURLOPT_USERPWD, $this->key . ":" . $this->pwd);
        curl_setopt($req, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
        if ($method) {
            curl_setopt($req, CURLOPT_CUSTOMREQUEST, $method);
        }

        $result = curl_exec($req);
        if (!$result) {
            if ($verbose) {
                var_dump(curl_error($req));
            }
            throw new ErrorException();
        }
        curl_close($req);

        return $result;
    }

    /**
     * Get all files path
     *
     * @return array
     */
    public function getFilesPath(): array
    {
        $r = $this->make_request("/public.php/webdav/", $method = "PROPFIND");
        $dir = new SimpleXMLElement($r);

        $paths = [];

        foreach ($dir->children("d", true) as $f) {
            $path = $f->children("d", true)[0];
            if ($path == "/public.php/webdav/") {
                continue;
            }
            $paths[] = $path;
        }
        return $paths;
    }
}


/**
 * Generate the body and the summary of a webdav folder.
 */
class ContentGenerator
{
    private WebdavFileGetter $webdavGetter;

    /**
     * Construct
     *
     * @param string $base_url webdav folder base url
     * @param string $key webdav folder sharing key
     * @param string $pwd webdav folder sharing password
     */
    public function __construct(
        public string $base_url,
        public string $key,
        public string $pwd,
    ) {
        $this->webdavGetter = new WebdavFileGetter($base_url, $key, $pwd);
    }

    /**
     * Generate the body and the summary from the webdav folder
     *
     * @return array An array containing two keys : `body` as the html aggregated
     * content of the file and a `summary (id, name)`.    
     */
    function generateBodyAndSummary(): array
    {
        $body = "";
        $summary = [];

        foreach ($this->webdavGetter->getFilesPath() as $path) {

            $explodedPath = explode("/", $path);
            if (str_starts_with(end($explodedPath), "_")) {
                continue;
            }

            $file_content = $this->webdavGetter->make_request($path);

            $Parsedown = new Parsedown();
            $html = $Parsedown->text($file_content);

            // extract title
            $title = (new SimpleXMLElement("<root>" . $html . "</root>"))->children()[0];

            if (!$title['id']) {
                throw new Exception("Missing title for file " . $path);
            }

            $id = $title["id"]->__toString();
            $text = $title->__toString();
            $summary[] = [$id, $text];

            $body .= '<section id="section-' . $id . '">' . $html . '</section>';
        }

        return ["body" => $body, "summary" => $summary];
    }

    public function getSpecials($pages)
    {
        $result = [];
        foreach ($pages as $id) {
            $file_content = $this->webdavGetter->make_request('/public.php/webdav/' . $id);
            $Parsedown = new Parsedown();
            $result[$id] = $Parsedown->text($file_content);
        }
        return $result;
    }
}

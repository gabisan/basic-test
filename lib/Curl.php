<?php
namespace app\lib;

/**
 * Class Curl
 * @package app\lib\helpers
 *
 * @author Angelo <angelo@sportspass.com.au>
 */
class Curl
{
    const AUTH_TYPE_BASIC = 'basic';

    /**
     * @var bool log all connection activity
     */
    public $logging = false;
    /**
     * @var string last curl response
     */
    public $response;
    /**
     * @var string last curl info
     */
    public $info;
    /**
     * @var int last error number
     */
    public $errorNo;
    /**
     * @var string last error message
     */
    public $errorMsg;
    /**
     * @var $authType
     * Header Authentication Type
     */
    public $authType;
    /**
     * @var array curl options
     */
    protected $_curlOpts = [];
    /**
     * @var resource last curl response
     */
    private $_curlHandler;
    /**
     * @var resource verbose output handler
     */
    private $_verboseHandler;

    /**
     * @var string log for the specific call
     */
    private $_log;

    /**
     * constructor
     * @param array $curlOptions
     */
    public function __construct(array $curlOptions=[])
    {
        // set default curl options
        $this->setOptions([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false, // cannot use follow location on openbase dir enabled
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36',
        ]);

        // set user defined curl options
        if ($curlOptions)
        {
            $this->setOptions($curlOptions);
        }

        // init curl handler
        $this->_curlHandler = curl_init();
    }

    /**
     * Set curl options
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->_curlOpts = array_replace($this->_curlOpts, $options);
    }

    /**
     * @param $type
     */
    public function setAuthType($type)
    {
        $this->authType = $type;
    }

    /**
     * Enable logging
     * @throws \Exception
     */
    public function enableLogging()
    {
        // set logging param
        $this->logging = true;

        // open temporary memory storage (php://temp means once it hits the php limit it will spill over into a file)
        $this->_verboseHandler = fopen('php://temp', 'rw+');
        $this->setOptions([
            CURLOPT_VERBOSE => true,
            CURLOPT_STDERR => $this->_verboseHandler
        ]);
    }

    /**
     * Switch off logging
     */
    public function disableLogging()
    {
        $this->logging = false;

        fclose($this->_verboseHandler);
        $this->setOptions([
            CURLOPT_VERBOSE => false,
            CURLOPT_STDERR => null
        ]);
    }

    /**
     * Get log for last call
     * @return string
     */
    public function getLog()
    {
        return $this->_log;
    }

    /**
     * Return curl options currently set
     * @return array
     */
    public function getOptions()
    {
        return $this->_curlOpts;
    }

    /**
     * Return Auth Type
     * @return mixed
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * @param $url
     * @param array $params
     * @param null $header
     * @return string
     */
    public function get($url, $params=[], $header = null)
    {
        // set url params
        if ($params)
        {
            $this->buildUrl($url, $params);
        }

        // make the curl call
        $this->executeCall($url, '', 'GET', $header);

        return $this->response;
    }

    /**
     * Build url correctly if a get
     * @param string $url
     * @param array $params
     */
    protected function buildUrl(&$url, $params=[])
    {
        // if params passed in via data then append to the URL
        if (substr($url, -1) != '?')
        {
            $url .= '?';
        }
        $url .= (is_array($params)) ? http_build_query($params) : $params;
    }

    /**
     * @param $url
     * @param array $data
     * @param string $method
     * @param null $header
     */
    public function executeCall($url, $data=[], $method='GET', $header = null)
    {
        $method = strtoupper($method);

        // get request
        if ($method == 'GET')
        {
            // if url params
            if ($data)
            {
                $this->buildUrl($url, $data);
            }
            // set url to our get
            $this->setOptions([
                CURLOPT_URL => $url,
                CURLOPT_HTTPGET => true,
                CURLOPT_POST => false,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => false,
            ]);
        }
        // post request
        elseif ($method == 'POST')
        {
            $this->setOptions([
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
            ]);
        }
        // custom request (put, delete etc.)
        else
        {
            $this->setOptions([
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => $method,
            ]);
        }

        // set options
        curl_setopt_array($this->_curlHandler, $this->_curlOpts);

        $this->response = curl_exec($this->_curlHandler);
        $this->info = curl_getinfo($this->_curlHandler);
        $this->errorNo = curl_errno($this->_curlHandler);
        $this->errorMsg = curl_error($this->_curlHandler);

        // close the curl connection
        curl_close($this->_curlHandler);

        // get verbose log
        if ($this->logging)
        {
            rewind($this->_verboseHandler);
            $verboseLog = stream_get_contents($this->_verboseHandler);

            // get curl request info
            $curlVersion = curl_version();
            $date = date('c'); // iso date
            $this->_log =
                "URL....: {$this->_curlOpts[CURLOPT_URL]}\n".
                "Date...: $date\n".
                "UA.....: {$this->_curlOpts[CURLOPT_USERAGENT]}\n".
                (!empty($this->_curlOpts[CURLOPT_PROXY]) ? "Proxy..: ".$this->_curlOpts[CURLOPT_PROXY]."\n" : '').
                "Code...: {$this->info['http_code']} ({$this->info['redirect_count']} redirect(s) in {$this->info['redirect_time']} secs)\n".
                "Content: {$this->info['content_type']} Size: {$this->info['download_content_length']} (Own: {$this->info['size_download']} Filetime: {$this->info['filetime']})\n".
                "Time...: {$this->info['total_time']} Start @ {$this->info['starttransfer_time']} (DNS: {$this->info['namelookup_time']} Connect: {$this->info['connect_time']} Request: {$this->info['pretransfer_time']})\n".
                "Speed..: Down: {$this->info['speed_download']} (avg.) Up: {$this->info['speed_upload']} (avg.)\n".
                "Curl...: v{$curlVersion['version']}\n".
                ($this->errorNo ? "ERROR..: (#$this->errorNo): $this->errorMsg\n" : '').
                ($verboseLog ? "\n$verboseLog" : '')."\n\n";
        }
    }

    /**
     * Curl post
     * @param string $url
     * @param array $data
     * @return string
     */
    public function post($url, $data=[], $header = null)
    {
        // make the curl call
        $this->executeCall($url, $data, 'POST', $header);

        return $this->response;
    }
}
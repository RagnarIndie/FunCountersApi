<?php

namespace Test\Counters\Library;


class Router
{
    /**
     * Main application
     * @var null
     */
    protected $application = null;

    /**
     * HTTP verb like GET, POST, etc.
     * @var string
     */
    protected $method = '';

    /**
     * Requested Endpoint
     * @var string
     */
    protected $endpoint = '';

    /**
     * Additional URI parts (args after endpoint)
     * @var array
     */
    protected $args = [];

    /**
     * Request input data (I.e. POST data)
     * @var array
     */
    protected $input = [];

    /**
     * Request headers for a current request
     * @var array
     */
    protected $requestHeaders = [];

    /**
     * Response format
     * @var string
     */
    protected $responseFormat = '';

    /**
     * Active controller class for current route
     * @var \Test\Counters\Library\Interfaces\IController;
     */
    protected $controller = null;

    public function __construct($application)
    {
        $this->application = $application;

        //Prepare request
        $this->setupCors();
        $this->processRequestHeaders();
        $this->parseRequest();
    }

    /**
     * Resolves request to controller::action
     *
     * @throws \Exception
     */
    public function resolve()
    {
        $routeData = (!empty($this->application->config['routes'][$this->method][$this->endpoint]))
            ? $this->application->config['routes'][$this->method][$this->endpoint]
            : false;

        if ($routeData) {
            //Select proper response format
            $responseFormat = (!empty($routeData['response_formats']) && in_array($this->responseFormat, $routeData['response_formats']))
                ? $this->responseFormat
                : $this->application->config['api']['default_response_format'];

            //Setup controller for a current request
            $this->controller = new $routeData['controller']($this->application);
            $this->controller->setRouteData($routeData);
            $this->controller->setRequestHeaders($this->requestHeaders);
            $this->controller->setInput($this->input);
            $this->controller->initResponse($responseFormat);

            //If everything is ok then we can fire controller action
            //from the current route configuration
            if (method_exists($this->controller, $routeData['action'])) {
                $this->controller->{$routeData['action']}($this->args);
            } else {
                throw new \Exception('Endpoint Not Found', 404);
            }
        } else {
            throw new \Exception('Endpoint Not Found', 404);
        }
    }

    /**
     * Configures CORS
     */
    protected function setupCors()
    {
        //CORS setup
        $allowMethods = (!empty($this->config['api']['access_control']['allow_methods']) && is_array($this->config['api']['access_control']['allow_methods']))
            ? join(',', $this->config['api']['access_control']['allow_methods'])
            : '*';
        $allowOrigin = (!empty($this->config['api']['access_control']['allow_origin']))
            ? $this->config['api']['access_control']['allow_origin']
            : '*';

        header("Access-Control-Allow-Methods: {$allowMethods}");
        header("Access-Control-Allow-Orgin: {$allowOrigin}");
    }

    /**
     * Sets request headers for a current request
     */
    protected function processRequestHeaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->requestHeaders[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }

    /**
     * Parses current request
     *
     * @throws \Exception
     */
    protected function parseRequest()
    {
        $request = ltrim(rtrim($_SERVER['REQUEST_URI']), '/');

        //Trying to get response format
        $parts = explode('.', $request);

        if (count($parts) > 1) {
            $this->responseFormat = array_pop($parts);
        }

        $parts = (is_array($parts)) ? array_shift($parts) : $parts;

        //Parse request
        $this->args = explode('/', $parts);
        $this->endpoint = array_shift($this->args);
        $this->method = $_SERVER['REQUEST_METHOD'];

        //index has requested
        if (empty($this->endpoint)) {
            $this->endpoint = '/';
        }

        //Trying to detect PUT or DELETE verbs
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            switch ($_SERVER['HTTP_X_HTTP_METHOD']) {
                case 'PUT':
                    $this->method = 'PUT';
                    break;
                case 'DELETE':
                    $this->method = 'DELETE';
                    break;
                default:
                    throw new \Exception('Invalid HTTP Method', 405);
            }
        }

        //HTTP method isn't allowed (see config file)
        if (!in_array($this->method, $this->application->config['api']['access_control']['allow_methods'])) {
            throw new \Exception('Method Not Allowed', 405);
        }

        //Get proper request input data
        switch ($this->method) {
            case 'GET':
                $this->input = $this->cleanInput($_REQUEST);
                break;
            case 'POST':
            case 'DELETE':
                $input = json_decode(file_get_contents('php://input'), true);
                $this->input = $this->cleanInput($input);
                break;
            case 'PUT':
                //TODO: file upload can be added here
                break;
            default:
                throw new \Exception('Invalid HTTP Method', 405);
                break;
        }
    }

    /**
     * Cleans request input data
     *
     * @param $data
     * @return array|string
     */
    protected function cleanInput($data)
    {
        $input = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $input[$key] = $this->cleanInput($value);
            }
        } else {
            $input = trim(strip_tags($data));
        }

        return $input;
    }
}
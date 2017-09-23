<?php

namespace Test\Counters\Library;

use Test\Counters\Library\Interfaces\IController;


class Controller implements IController
{
    /**
     * Application instance
     * @var array
     */
    protected $application = null;

    /**
     * Current route data
     * @var array
     */
    protected $routeData = [];

    /**
     * Current request headers
     * @var array
     */
    protected $requestHeaders = [];

    /**
     * Current request input
     * @var array
     */
    protected $input = [];

    /**
     * Response object
     * @var \Test\Counters\Library\Interfaces\IResponse
     */
    protected $response = null;

    /**
     * @return \Test\Counters\Library\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Sets current route data
     *
     * @param array $routeData
     */
    public function setRouteData(array $routeData)
    {
        $this->routeData = $routeData;
    }

    /**
     * Sets current request headers
     *
     * @param array $requestHeaders.
     */
    public function setRequestHeaders(array $requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;
    }

    /**
     * Sets current request input data
     *
     * @param array $input
     */
    public function setInput(array $input)
    {
        $this->input = $input;
    }

    /**
     * Gets current request input data
     *
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Controller constructor.
     * @param \Test\Counters\Library\Application $app
     */
    public function __construct($app)
    {
        $this->application = $app;
    }

    /**
     * Inits object which handles response formatting
     *
     * @param string $format
     */
    public function initResponse(string $format)
    {
        $this->response = new Response($this->application->config['api']['default_response_format']);
        $this->response->setFormatter($format);
    }

    /**
     * Shows formatted response data
     *
     * @param array $data
     * @param int $code
     */
    public function response(array $data, int $code)
    {
        $responseBody = $this->response->getFormatter()->formatResponse($data);
        header($this->response->getFormatter()->getContentTypeHeader());
        $this->response->setResponseHeaderByCode($code);
        echo $responseBody;
        exit();
    }
}
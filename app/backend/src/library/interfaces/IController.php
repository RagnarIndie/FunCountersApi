<?php

namespace Test\Counters\Library\Interfaces;

interface IController
{
    public function getApplication();
    public function setRouteData(array $routeData);
    public function setRequestHeaders(array $requestHeaders);
    public function setInput(array $input);
    public function getInput();
    public function initResponse(string $format);
    public function response(array $data, int $code);
}
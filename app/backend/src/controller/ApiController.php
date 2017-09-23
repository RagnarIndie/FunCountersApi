<?php

namespace Test\Counters\Controller;


use Test\Counters\Library\Controller;
use Test\Counters\Model\EventCounters;
use Test\Counters\Model\EventQueue;

class ApiController extends Controller
{

    public function hello($params)
    {
        $this->response([
            'status' => 'alive'
        ], 200);
    }

    public function summary($params)
    {
        $countersModel = new EventCounters($this->application);
        $countersData = $countersModel->getEventCountersForTopCountries();
        $this->response($countersData, 200);
    }

    public function counters($params)
    {
        $code = 400;
        $response = [
            'success' => false
        ];

        $post = $this->getInput();

        if (array_key_exists('country', $post) && array_key_exists('event', $post)) {
           $model = new EventQueue($this->getApplication());
           $model->setCountry($post['country']);
           $model->setEventTitle($post['event']);
           $model->save();

           if ($id = $model->getId()) {
               $response['success'] = true;
               $response['id'] = $id;
               $code = 201;
           }
        }

        $this->response($response, $code);
    }
}
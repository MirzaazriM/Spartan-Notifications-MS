<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 9:52 AM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\NotificationsService;
use Symfony\Component\HttpFoundation\Request;

class NotificationsController
{

    private $notificationsService;

    public function __construct(NotificationsService $notificationsService)
    {
        $this->notificationsService = $notificationsService;
    }


    /**
     * Get notification/s by id or type
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function get(Request $request):ResponseBootstrap {
        // get data
        $id = $request->get('id');
        $type = $request->get('type');

        // create response object in case of failure
        $response = new ResponseBootstrap();

        // check which service to call
        if(isset($id)){
            return $this->notificationsService->getPushNotification($id);
        }else if(isset($type)) {
            return $this->notificationsService->getPushNotifications($type);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }


    /**
     * Add notification
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function post(Request $request):ResponseBootstrap {

        $dat = json_decode($request->getContent(), true);
        $to = $dat['to'];
        // $message = $data['message'];
        $data = $dat['data'];

        // create response object in case of failure
        $response = new ResponseBootstrap();

        if(isset($to) && isset($data)){
            // return $this->notificationsService->createPushNotification($to, $data);
            return $this->notificationsService->sendNotification($to, $data);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }
        return $response;
    }


    /**
     * Delete notification
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function delete(Request $request):ResponseBootstrap {

        // get id if exists
        $id = $request->get('id');

        // create response object
        $response = new ResponseBootstrap();

        if(isset($id)){
            return $this->notificationsService->deletePushNotification($id);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }
        return $response;
    }

}
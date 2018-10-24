<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 9:52 AM
 */

namespace Model\Service;


use Model\Core\Helper\Monolog\MonologSender;
use Model\Entity\NotificationsFCM;
use Model\Entity\Push;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\NotificationsMapper;

class NotificationsService
{

    private $notificationsMapper;
    private $configuration;
    private $monologHelper;

    public function __construct(NotificationsMapper $notificationsMapper)
    {
        $this->notificationsMapper = $notificationsMapper;
        $this->configuration = $notificationsMapper->getConfiguration();
        $this->monologHelper = new MonologSender();
    }


    /**
     * Get notification by id
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function getPushNotification(int $id):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity
            $entity = new Push();
            $entity->setId($id);

            // get response
            $res = $this->notificationsMapper->getPushNotification($entity);
            $id = $res->getId();

            // check data and set response
            if(isset($id)){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData([
                    'id' => $res->getId(),
                    'title' => $res->getTitle(),
                    'body' => $res->getMessage(),
                    'type' => $res->getType(),
                    'fcm' => $res->getFcm(),
                    'date' => $res->getDate(),
                    'image' => $res->getImage(),
                    'link' => $res->getLink(),
                    'package_id' => $res->getPackageId()
                ]);
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Get notification service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Get notifications by type
     *
     * @param string $type
     * @return ResponseBootstrap
     */
    public function getPushNotifications(string $type):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity
            $entity = new Push();
            $entity->setType($type);

            // get response
            $res = $this->notificationsMapper->getPushNotifications($entity);

            // convert data to array for appropriate response
            $data = [];

            for($i = 0; $i < count($res); $i++){
                $data[$i]['id'] = $res[$i]->getId();
                $data[$i]['title'] = $res[$i]->getTitle();
                $data[$i]['body'] = $res[$i]->getMessage();
                $data[$i]['type'] = $res[$i]->getType();
                $data[$i]['fcm'] = $res[$i]->getFcm();
                $data[$i]['date'] = $res[$i]->getDate();
                $data[$i]['image'] = $res[$i]->getImage();
                $data[$i]['link'] = $res[$i]->getLink();
                $data[$i]['package_id'] = $res[$i]->getPackageId();
            }

            // check data and set response
            if($res->getStatusCode() == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData(
                    $data
                );
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Get notifications service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Create notification service
     *
     * @param array $to
     * @param $message
     * @param $data
     * @return ResponseBootstrap
     */
    public function createPushNotification(array $to, $data):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // initialize counter for counting number of sended notifications
            $counter = 0;

            // loop through array to and send notifications to each FCM
            foreach ($to as $user){
                // get status of sended notification
                $status = $this->sendNotification($data);

                // if notification is sended, write it into database
                // if($status == 200){
                    // create entity
                    $entity = new Push();
                    $entity->setTo($user);
                    $entity->setData($data);

                    // call mapper
                   // $this->notificationsMapper->createPushNotification($entity)->getResponse();

                    // increment counter by one
                    $counter++;
                 // }
            }

            // check data and set response
            if($counter > 0){
                $response->setStatus(200);
                $response->setMessage($counter . ' notification/s sended');
            }else {
                $response->setStatus(304);
                $response->setMessage('Notification/s not sended');
            }

            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Create notification service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Send notification/s
     *
     * @param string $to
     * @param $message
     * @param $data
     * @return int|ResponseBootstrap
     */
    public function sendNotification($to, $data) {

        try {

            // create response object
            $response = new ResponseBootstrap();

            // server data
            $serverKey = $this->configuration['server_key'];
            $url = $this->configuration['fcm_url'];

            $fields = [
                'to' => $to[0],
                'content_available' => true,
                'mutable_content' => true,
                'notification' => $data,
                'data' => $data,
                'priority' => 'high'
            ];

            $fields = json_encode ( $fields );

            $headers = array (
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json'
            );

            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_POST, true );
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

            $karinas = curl_exec ($ch);


            $result = json_decode($karinas, true);

            curl_close ($ch);

            if($result['success'] != 0){
                $status = 200;
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData([$result]);
            }else {
                $status = 304;
                $response->setStatus(404);
                $response->setMessage('Bad');
                $response->setData([$result]);
            }

            return $response;

        }catch(\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Send notification service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }

    /**
     * Delete notification service
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function deletePushNotification(int $id):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity
            $entity = new Push();
            $entity->setId($id);

            // get response
            $res = $this->notificationsMapper->deletePushNotification($entity)->getResponse();

            // check data and set response
            if($res[0] == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
            }else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Delete notification service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }
}
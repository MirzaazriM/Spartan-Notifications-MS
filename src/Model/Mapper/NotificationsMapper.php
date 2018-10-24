<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 9:52 AM
 */

namespace Model\Mapper;

use PDO;
use PDOException;
use Component\DataMapper;
use Model\Entity\Push;
use Model\Entity\PushCollection;
use Model\Entity\Shared;

class NotificationsMapper extends  DataMapper
{

    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Fetch single notification
     *
     * @param Push $push
     * @return Push
     */
    public function getPushNotification(Push $push):Push {
        // create response object
        $response = new Push();

        try {

            $sql = "SELECT * FROM notifications WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $push->getId()
            ]);

            // fetch data
            $data = $statement->fetch();

            // set entity values
            if($statement->rowCount() > 0){
                $response->setId($data['id']);
                $response->setTitle($data['title']);
                $response->setMessage($data['body']);
                $response->setType($data['type']);
                $response->setFcm($data['fcm']);
                $response->setDate($data['date']);
                $response->setDate($data['date']);
                $response->setImage($data['image']);
                $response->setLink($data['link']);
                $response->setPackageId($data['package_id']);
            }

        }catch(PDOException $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Get notification mapper: " . $e->getMessage());
        }

        return $response;
    }


    /**
     * Fetch notifications by type
     *
     * @param Push $push
     * @return PushCollection
     */
    public function getPushNotifications(Push $push):PushCollection {

        // create response object
        $pushCollection = new PushCollection();

        try {

            $sql = "SELECT * FROM notifications WHERE type = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $push->getType()
            ]);

            // Fetch Data
            while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $push = new Push();
                $push->setId($row['id']);
                $push->setTitle($row['title']);
                $push->setMessage($row['body']);
                $push->setType($row['type']);
                $push->setFcm($row['fcm']);
                $push->setDate($row['date']);
                $push->setImage($row['image']);
                $push->setLink($row['link']);
                $push->setPackageId($row['package_id']);

                $pushCollection->addEntity($push);
            }

            // set entity values
            if($statement->rowCount() == 0){
                $pushCollection->setStatusCode(204);
            }else {
                $pushCollection->setStatusCode(200);
            }

        }catch(PDOException $e){
            $pushCollection->setStatusCode(204);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Get notifications mapper: " . $e->getMessage());
        }

        return $pushCollection;
    }


    /**
     * Insert notification
     *
     * @param Push $push
     * @return Shared
     */
    public function createPushNotification(Push $push):Shared {
//die(print_r($push));
        // create response object
        $shared = new Shared();

        try {


            if($push->getData()['type'] == 'PROMO'){
                // set database instructions
                $sql = "INSERT INTO notifications
                      (fcm, title, body, type, image, link)
                    VALUES (?,?,?,?,?,?)";

                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $push->getTo(),
                    $push->getData()['message']['title'],
                    $push->getData()['message']['text'],
                    $push->getData()['type'],
                    $push->getData()['image'],
                    $push->getData()['link']
                ]);
            }else if($push->getData()['type'] == 'PACKAGE'){
                // set database instructions
                $sql = "INSERT INTO notifications
                      (fcm, title, body, type, package_id)
                    VALUES (?,?,?,?,?)";

                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $push->getTo(),
                    $push->getData()['message']['title'],
                    $push->getData()['message']['text'],
                    $push->getData()['type'],
                    $push->getData()['package_id']
                ]);
            }else {
                $shared->setResponse([304]);
            }



            // set status code
            if($statement->rowCount() > 0){
                $shared->setResponse([200]);
            }else {
                $shared->setResponse([304]);
            }

        }catch(PDOException $e){
            $shared->setResponse([304]);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Create notification mapper: " . $e->getMessage());
        }

        return $shared;
    }


    /**
     * Delete notification record
     *
     * @param Push $push
     * @return Shared
     */
    public function deletePushNotification(Push $push):Shared {

        // create response object
        $shared = new Shared();

        try {

            $sql = "DELETE FROM notifications WHERE id = ?";

            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $push->getId()
            ]);

            // set status code
            if($statement->rowCount() > 0){
                $shared->setResponse([200]);
            }else {
                $shared->setResponse([304]);
            }

        }catch(PDOException $e){
            $shared->setResponse([304]);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Delete notification mapper: " . $e->getMessage());
        }

        return $shared;

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/30/18
 * Time: 3:13 PM
 */

namespace Model\Entity;


class NotificationsFCM
{

    private $fcm;
    private $date;
    private $network;
    private $userId;
    private $type;
    private $image;
    private $link;
    private $message;
    private $packageId;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link): void
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getPackageId()
    {
        return $this->packageId;
    }

    /**
     * @param mixed $packageId
     */
    public function setPackageId($packageId): void
    {
        $this->packageId = $packageId;
    }

    /**
     * @return mixed
     */
    public function getFcm()
    {
        return $this->fcm;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $fcm
     */
    public function setFcm($fcm)
    {
        $this->fcm = $fcm;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param mixed $network
     */
    public function setNetwork($network)
    {
        $this->network = $network;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

}
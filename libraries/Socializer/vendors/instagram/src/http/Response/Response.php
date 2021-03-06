<?php

namespace InstagramAPI;

class Response
{
    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';

    protected $status;
    protected $message;
    protected $fullResponse;

    public function __construct($data)
    {
        $this->setStatus($data['status']);
        if (isset($data['message'])) {
            $this->setMessage($data['message']);
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setFullResponse($response)
    {
        $this->fullResponse = $response;
    }

    public function getFullResponse()
    {
        return $this->fullResponse;
    }

    public function isOk()
    {
        return $this->getStatus() == self::STATUS_OK;
    }
}

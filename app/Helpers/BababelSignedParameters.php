<?php

namespace App\Helpers;

class BababelSignedParameters
{
    protected string $meetingId;
    protected string $recordId;

    public function __construct(string $meetingId, string $recordId)
    {
        $this->setMeetingId($meetingId);
        $this->setRecordId($recordId);
    }

    /**
     * @param string $meetingId
     */
    public function setMeetingId(string $meetingId): void
    {
        $this->meetingId = $meetingId;
    }

    /**
     * @param string $recordId
     */
    public function setRecordId(string $recordId): void
    {
        $this->recordId = $recordId;
    }

    /**
     * @return string
     */
    public function getMeetingId(): string
    {
        return $this->meetingId;
    }

    /**
     * @return string
     */
    public function getRecordId(): string
    {
        return $this->recordId;
    }
}

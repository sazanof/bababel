<?php

namespace App\Helpers\Parameters;

class GetRecordingsParameters extends \BigBlueButton\Parameters\GetRecordingsParameters
{
    protected ?string $meetingId = null;
    protected ?string $recordId = null;
    protected ?string $state = null;
    protected int $limit = 10;
    protected int $offset = 0;

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getHTTPQuery()
    {
        $queries = [
            'meetingID' => $this->meetingId,
            'recordID' => $this->recordId,
            'state' => $this->state,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];

        $this->buildMeta($queries);

        return $this->buildHTTPQuery($queries);
    }
}

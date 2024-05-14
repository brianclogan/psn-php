<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\TrophyTitleInformationFactory;
use Tustin\PlayStation\Factory\TrophyTitlesFactory;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;

class TrophyTitleInformationIterator extends AbstractApiIterator
{
    // private $platforms;

    public function __construct(private TrophyTitleInformationFactory $trophyTitleInformationFactory)
    {
        parent::__construct($trophyTitleInformationFactory->getHttpClient());

        // $this->platforms = implode(',', $trophyTitlesFactory->getPlatforms());

        $this->title = $trophyTitleInformationFactory->getTitle();

        $this->limit = 100;

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $body = [
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('trophy/v1/npCommunicationIds/' . $this->title . '/trophyGroups/all/trophies', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    /**
     * Gets the current user trophy title in the iterator. 
     */
    public function current(): UserTrophyTitle
    {
        $title = new UserTrophyTitle($this->trophyTitleInformationFactory->getHttpClient());
        $title->setFactory($this->trophyTitleInformationFactory);
        $title->setCache($this->getFromOffset($this->currentOffset));

        return $title;
    }
}

<?php

namespace PerformRomance\ActiveCampaign;

use PerformRomance\ActiveCampaign\Support\Request;
use PerformRomance\ActiveCampaign\Services\TagManager;
use PerformRomance\ActiveCampaign\Contracts\ActiveCampaignSyncable;

class ActiveCampaign
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Contact $contact,
        protected readonly TagManager $tagManager,
    ) {}

    public function contact(): Contact
    {
        return app(Contact::class);
    }

    /**
     * Sync a model with ActiveCampaign.
     */
    public function syncModel(ActiveCampaignSyncable $model): void
    {
        $contactData = $model->toActiveCampaignArray();

        $this->contact()->setContactData($contactData)->sync();
    }

    public function tags(): TagManager
    {
        return $this->tagManager;
    }
}

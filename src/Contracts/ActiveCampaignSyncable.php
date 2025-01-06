<?php

namespace PerformRomance\ActiveCampaign\Contracts;

use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;

interface ActiveCampaignSyncable
{
    /**
     * Convert the model instance to an array for ActiveCampaign.
     * The array keys should match ActiveCampaign's API field names.
     */
    public function toActiveCampaignArray(): array|ContactDto;

    /**
     * Determine if the model should be synced to ActiveCampaign.
     */
    public function shouldSyncWithActiveCampaign(): bool;

    /**
     * Get Tags that should be applied to ActiveCampaign contact.
     */
    public function getActiveCampaignTags(): null|array;
}

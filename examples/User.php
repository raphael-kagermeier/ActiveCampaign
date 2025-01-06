<?php

namespace Examples;

use Illuminate\Database\Eloquent\Model;
use PerformRomance\ActiveCampaign\Contracts\ActiveCampaignSyncable;
use PerformRomance\ActiveCampaign\Concerns\InteractsWithActiveCampaign;

class User extends Model implements ActiveCampaignSyncable
{
    use InteractsWithActiveCampaign;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Convert the model instance to an array for ActiveCampaign.
     * The array keys should match ActiveCampaign's API field names.
     */
    public function toActiveCampaignArray(): array
    {
        return [
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    /**
     * Determine if the model should be synced to ActiveCampaign.
     */
    public function shouldSyncWithActiveCampaign(): bool
    {
        // Only sync verified users
        return ! is_null($this->email_verified_at);
    }

    /**
     * Get Tags that should be applied to ActiveCampaign contact.
     */
    public function getActiveCampaignTags(): null|array
    {
        return null;
    }
}

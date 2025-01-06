<?php

namespace PerformRomance\ActiveCampaign\Concerns;

use PerformRomance\ActiveCampaign\ActiveCampaign;
use PerformRomance\ActiveCampaign\Contracts\ActiveCampaignSyncable;
use PerformRomance\ActiveCampaign\Jobs\SyncWithActiveCampaign;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithActiveCampaign
{
    /**
     * Boot the trait.
     */
    public static function bootInteractsWithActiveCampaign(): void
    {
        static::created(function (Model $model) {
            if ($model instanceof ActiveCampaignSyncable && $model->shouldSyncWithActiveCampaign()) {
                SyncWithActiveCampaign::dispatch($model);
            }
        });

        static::updated(function (Model $model) {
            if ($model instanceof ActiveCampaignSyncable && $model->shouldSyncWithActiveCampaign()) {
                SyncWithActiveCampaign::dispatch($model);
            }
        });
    }

    /**
     * Determine if the model should be synced to ActiveCampaign.
     */
    public function shouldSyncWithActiveCampaign(): bool
    {
        return true;
    }

    /**
     * Sync the model with ActiveCampaign immediately.
     */
    public function syncWithActiveCampaign(): void
    {
        app(ActiveCampaign::class)->syncModel($this);
    }

    /**
     * Queue the model to be synced with ActiveCampaign.
     */
    public function queueSyncWithActiveCampaign(): void
    {
        SyncWithActiveCampaign::dispatch($this);
    }
}

<?php

namespace PerformRomance\ActiveCampaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PerformRomance\ActiveCampaign\ActiveCampaign;
use PerformRomance\ActiveCampaign\Contracts\ActiveCampaignSyncable;

class SyncWithActiveCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ActiveCampaignSyncable $model
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ActiveCampaign $activeCampaign): void
    {
        $contactData = $this->model->toActiveCampaignArray();

        $tags = $this->model->getActiveCampaignTags();

        $activeCampaign
            ->contact()
            ->setContactData($contactData)
            ->sync()
            ->applyTags($tags);
    }
}

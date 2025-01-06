<?php

namespace PerformRomance\ActiveCampaign\Commands;

use Illuminate\Console\Command;

class ActiveCampaignCommand extends Command
{
    public $signature = 'activecampaign';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

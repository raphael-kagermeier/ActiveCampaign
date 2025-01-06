<?php

namespace PerformRomance\ActiveCampaign;

use PerformRomance\ActiveCampaign\Commands\ActiveCampaignCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ActiveCampaignServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('activecampaign')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_activecampaign_table')
            ->hasCommand(ActiveCampaignCommand::class);
    }
}

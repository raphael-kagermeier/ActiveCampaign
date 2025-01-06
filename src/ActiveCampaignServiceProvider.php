<?php

namespace PerformRomance\ActiveCampaign;

use PerformRomance\ActiveCampaign\Commands\ActiveCampaignCommand;
use PerformRomance\ActiveCampaign\Services\FieldManager;
use PerformRomance\ActiveCampaign\Services\TagManager;
use PerformRomance\ActiveCampaign\Support\Request;
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

    public function packageRegistered(): void
    {
        // Register the Request class
        $this->app->singleton(Request::class, function ($app) {
            return new Request(
                config('activecampaign.url'),
                config('activecampaign.key'),
                config('activecampaign.version')
            );
        });

        // Register the TagManager
        $this->app->singleton(TagManager::class, function ($app) {
            return new TagManager(
                $app->make(Request::class)
            );
        });

        // Register the Contact class
        $this->app->singleton(Contact::class, function ($app) {
            return new Contact(
                $app->make(Request::class),
                $app->make(TagManager::class),
                $app->make(FieldManager::class)
            );
        });

        // Register the main ActiveCampaign class
        $this->app->singleton(ActiveCampaign::class, function ($app) {
            return new ActiveCampaign(
                $app->make(Request::class),
                $app->make(Contact::class),
                $app->make(TagManager::class)
            );
        });

        // Register facade
        $this->app->bind('activecampaign', function ($app) {
            return $app->make(ActiveCampaign::class);
        });
    }
}

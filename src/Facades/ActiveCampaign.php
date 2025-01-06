<?php

namespace PerformRomance\ActiveCampaign\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PerformRomance\ActiveCampaign\ActiveCampaign
 */
class ActiveCampaign extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PerformRomance\ActiveCampaign\ActiveCampaign::class;
    }
}

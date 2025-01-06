<?php

namespace PerformRomance\ActiveCampaign;

use PerformRomance\ActiveCampaign\Support\Request;

class ActiveCampaign
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Contact $contact
    ) {}

    public function contact(): Contact
    {
        return $this->contact;
    }
}

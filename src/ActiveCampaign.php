<?php

namespace PerformRomance\ActiveCampaign;

use PerformRomance\ActiveCampaign\Support\Request;

class ActiveCampaign
{
    protected Contact $contact;
    protected Request $request;

    protected string $api_url;
    protected string $api_key;
    protected string $api_version;

    public function __construct(
        ?Request $request = null
    ) {

        $this->api_url = config('activecampaign.api_url');
        $this->api_key = config('activecampaign.api_key');
        $this->api_version = config('activecampaign.api_version');

        $this->request = $request ?? new Request($this->api_url, $this->api_key, $this->api_version);
    }

    public function contact(array $data = []): Contact
    {
        return $this->contact = new Contact($this->request, $data);
    }
}

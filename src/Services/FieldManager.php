<?php

namespace PerformRomance\ActiveCampaign\Services;

use PerformRomance\ActiveCampaign\Support\Request;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;

class FieldManager
{
    public function __construct(
        protected readonly Request $request,
    ) {}

    public function search(string $title): ?object
    {
        $response = $this->request->make(
            $this->request->getEndpoint('fields'),
            query: [
                'filters[perstag][eq]' => $title,
            ],
            method: 'GET'
        );

        return empty($response->fields) ? null : $response->fields[0];
    }

    public function prepareFieldValues(array $fields): array
    {
        $cacheKey = 'activecampaign_fields_' . md5(implode(',', array_keys($fields)));

        // if ($cachedFields = cache()->get($cacheKey)) {
        //     return $cachedFields;
        // }

        $fieldValues = [];
        foreach ($fields as $title => $value) {
            if ($foundField = $this->search($title)) {
                $fieldValues[] = [
                    'field' => (string) $foundField->id,
                    'value' => $value,
                ];

                dd($fieldValues, $foundField);
            }
        }

        cache()->put($cacheKey, $fieldValues, now()->addMinutes(config('activecampaign.sync.tag_cache_duration')));

        return $fieldValues;
    }
}

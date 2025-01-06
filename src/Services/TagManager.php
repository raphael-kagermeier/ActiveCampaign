<?php

namespace PerformRomance\ActiveCampaign\Services;

use PerformRomance\ActiveCampaign\Support\Request;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;

class TagManager
{
    public function __construct(
        protected readonly Request $request,
    ) {}

    public function search(string $tag): ?object
    {
        $response = $this->request->make(
            $this->request->getEndpoint('tags'),
            query: [
                'filters[search][eq]' => $tag,
                'orders[search]' => 'weight'
            ],
            method: 'GET'
        );

        return empty($response->tags) ? null : $response->tags[0];
    }

    public function create(string $tagName): object
    {
        return $this->request->make(
            $this->request->getEndpoint('tags'),
            [
                'tag' => [
                    'tag' => $tagName,
                    'tagType' => 'contact',
                    'description' => ''
                ]
            ],
            method: 'POST'
        );
    }

    public function getOrCreate(array|string $tags): array
    {
        $requestedTags = is_array($tags) ? $tags : [$tags];
        $cacheKey = 'activecampaign_tags_' . md5(implode(',', $requestedTags));

        if ($cachedTags = cache()->get($cacheKey)) {
            return $cachedTags;
        }

        $existingTags = [];
        foreach ($requestedTags as $tag) {
            if ($foundTag = $this->search($tag)) {
                $existingTags[] = $foundTag;
            } else {
                $newTag = $this->create($tag);
                if (isset($newTag->tag)) {
                    $existingTags[] = $newTag->tag;
                }
            }
        }

        cache()->put($cacheKey, $existingTags, now()->addHour());

        return $existingTags;
    }

    public function attachToContact(int $contactId, array $tags): void
    {
        foreach ($tags as $tag) {
            $this->request->make(
                $this->request->getEndpoint('contactTags'),
                [
                    'contactTag' => [
                        'contact' => $contactId,
                        'tag' => $tag->id,
                    ]
                ],
                method: 'POST'
            );
        }
    }

    public function getContactTagId(int $contactId, int $tagId): ?int
    {
        $response = $this->request->make(
            $this->request->getEndpoint("contacts/{$contactId}/contactTags"),
            query: [
                'filters[tag][eq]' => (string) $tagId,
            ],
            method: 'GET'
        );

        if (!empty($response->contactTags)) {
            foreach ($response->contactTags as $contactTag) {
                if ($contactTag->tag === (string) $tagId) {
                    return (int) $contactTag->id;
                }
            }
        }

        return null;
    }

    public function detachFromContact(int $contactId, string $tag): void
    {
        $foundTag = $this->search($tag);
        if (!$foundTag) {
            return;
        }

        $contactTagId = $this->getContactTagId($contactId, $foundTag->id);
        if ($contactTagId) {
            $this->request->make(
                $this->request->getEndpoint("contactTags/{$contactTagId}"),
                method: 'DELETE'
            );
        }
    }
}

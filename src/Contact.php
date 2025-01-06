<?php

namespace PerformRomance\ActiveCampaign;

use Illuminate\Support\Facades\Validator;
use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;
use PerformRomance\ActiveCampaign\Exceptions\ValidationException;
use PerformRomance\ActiveCampaign\Services\FieldManager;
use PerformRomance\ActiveCampaign\Services\TagManager;
use PerformRomance\ActiveCampaign\Support\Request;

class Contact
{
    protected ContactDto $contactData;

    protected array $tags = [];

    public function __construct(
        protected readonly Request $request,
        protected readonly TagManager $tagManager,
        protected readonly FieldManager $fieldManager,
    ) {}

    /**
     * Sync a contact with ActiveCampaign
     *
     * @throws ValidationException|ActiveCampaignException
     */
    public function sync(): self
    {

        $this->handleCustomFields();

        $data = $this->getContactData()->toSyncData();

        $validator = Validator::make($data, [
            'email' => 'required|email|max:255',
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'fieldValues' => 'nullable|array',
            'fieldValues.*.field' => 'required_with:fieldValues|string',
            'fieldValues.*.value' => 'required_with:fieldValues|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first());
        }

        $response = $this->request->make(
            $this->request->getEndpoint('contact/sync'),
            ['contact' => $data]
        );

        $this->contactData = ContactDto::fromResponse($response);

        return $this;
    }

    public function getContactData(): ContactDto
    {
        return $this->contactData;
    }

    /**
     * @param  ContactDto  $contactData
     */
    public function setContactData(array|ContactDto $contactData): self
    {
        $this->contactData = $contactData instanceof ContactDto
            ? $contactData
            : ContactDto::fromArray($contactData);

        return $this;
    }

    public function handleCustomFields(): self
    {
        if (empty($this->getContactData()->custom_fields)) {
            return $this;
        }

        $fieldValues = $this->fieldManager->prepareFieldValues($this->getContactData()->custom_fields);

        $this->contactData = $this->contactData->withFieldValues($fieldValues);

        return $this;
    }

    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Apply tags to the current contact
     *
     * @param  array|string|null  $tags  Optional tags to apply. If not provided, uses previously set tags.
     *
     * @throws ActiveCampaignException
     */
    public function applyTags(array|string|null $tags = null): self
    {
        if ($tags !== null) {
            $this->setTags(is_array($tags) ? $tags : [$tags]);
        }

        if (empty($this->tags)) {
            return $this;
        }

        $tags = $this->tagManager->getOrCreate($this->tags);
        $contactId = $this->getContactData()->id;

        if (! $contactId) {
            throw new ActiveCampaignException('Contact ID not found');
        }

        $this->tagManager->attachToContact($contactId, $tags);

        return $this;
    }

    /**
     * Remove a tag from the contact
     *
     * @throws ActiveCampaignException
     */
    public function removeTag(string $tag): self
    {
        $contactId = $this->getContactData()->id;
        if (! $contactId) {
            throw new ActiveCampaignException('Contact ID not found');
        }

        $this->tagManager->detachFromContact($contactId, $tag);
        $this->tags = array_values(array_filter($this->tags, fn ($t) => $t !== $tag));

        return $this;
    }

    /**
     * Remove multiple tags from the contact
     *
     * @throws ActiveCampaignException
     */
    public function removeTags(array $tags): self
    {
        foreach ($tags as $tag) {
            $this->removeTag($tag);
        }

        return $this;
    }
}

<?php

namespace PerformRomance\ActiveCampaign;

use Illuminate\Support\Facades\Validator;
use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;
use PerformRomance\ActiveCampaign\Exceptions\ValidationException;
use PerformRomance\ActiveCampaign\Support\Request;

class Contact
{
    protected ContactDto $contactData;

    public function __construct(
        protected readonly Request $request,
    ) {}

    /** 
     * Sync a contact with ActiveCampaign
     * @throws ValidationException|ActiveCampaignException
     */
    public function sync(): self
    {

        $dto = $this->getContactData();
        $data = [
            'email' => $dto->email,
            'firstName' => $dto->first_name,
            'lastName' => $dto->last_name,
            'phone' => $dto->phone,
            'fieldValues' => $dto->fieldValues,
        ];

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


    /**
     * @return ContactDto
     */
    public function getContactData(): ContactDto
    {
        return $this->contactData;
    }

    /**
     * @param ContactDto $contactData
     */
    public function setContactData(array|ContactDto $contactData): self
    {
        $this->contactData = $contactData instanceof ContactDto
            ? $contactData
            : ContactDto::fromArray($contactData);
        return $this;
    }
}

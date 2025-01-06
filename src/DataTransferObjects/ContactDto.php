<?php

namespace PerformRomance\ActiveCampaign\DataTransferObjects;

class ContactDto
{
    public function __construct(
        public readonly string $email,
        public readonly ?int $id,
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $phone,
        public readonly ?string $organization,
        public readonly ?string $country,
        public readonly ?string $address,
        public readonly ?string $zip,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $hash,
        public readonly ?string $created_at,
        public readonly ?string $updated_at,
        public readonly ?string $deleted_at,
        public readonly ?bool $is_duplicate,
        public readonly ?array $links,
        public readonly ?int $organization_id,
        public readonly ?array $fieldValues,
    ) {}

    public static function fromResponse(object $response): self
    {
        $contact = $response->contact;

        return new self(
            email: $contact->email,
            id: $contact->id ?? null,
            first_name: $contact->firstName ?? null,
            last_name: $contact->lastName ?? null,
            phone: $contact->phone ?? null,
            organization: $contact->organization ?? null,
            country: $contact->country ?? null,
            address: $contact->addressStreet ?? null,
            zip: $contact->addressZip ?? null,
            city: $contact->addressCity ?? null,
            state: $contact->addressState ?? null,
            hash: $contact->hash ?? null,
            created_at: $contact->cdate ?? null,
            updated_at: $contact->udate ?? null,
            deleted_at: $contact->deleted_at ?? null,
            is_duplicate: $contact->is_duplicate ?? false,
            links: (array) ($contact->links ?? []),
            organization_id: $contact->orgid ?? null,
            fieldValues: (array) ($contact->fieldValues ?? []),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            id: $data['id'] ?? null,
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            phone: $data['phone'] ?? null,
            organization: $data['organization'] ?? null,
            country: $data['country'] ?? null,
            address: $data['address'] ?? null,
            zip: $data['zip'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            hash: $data['hash'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null,
            is_duplicate: $data['is_duplicate'] ?? false,
            links: $data['links'] ?? [],
            organization_id: $data['organization_id'] ?? null,
            fieldValues: $data['fieldValues'] ?? [],
        );
    }
}

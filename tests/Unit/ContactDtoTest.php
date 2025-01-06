<?php

use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;

it('creates DTO from API response', function () {
    $response = (object) [
        'contact' => (object) [
            'id' => 123,
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
            'organization' => 'Test Corp',
            'country' => 'US',
            'addressStreet' => '123 Test St',
            'addressZip' => '12345',
            'addressCity' => 'Test City',
            'addressState' => 'TS',
            'hash' => 'abc123',
            'cdate' => '2023-01-01',
            'udate' => '2023-01-02',
            'deleted_at' => null,
            'is_duplicate' => false,
            'links' => [],
            'orgid' => 456,
            'fieldValues' => [],
        ],
    ];

    $dto = ContactDto::fromResponse($response);

    expect($dto)
        ->toBeInstanceOf(ContactDto::class)
        ->id->toBe(123)
        ->email->toBe('test@example.com')
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->phone->toBe('1234567890')
        ->organization->toBe('Test Corp')
        ->country->toBe('US')
        ->address->toBe('123 Test St')
        ->zip->toBe('12345')
        ->city->toBe('Test City')
        ->state->toBe('TS')
        ->hash->toBe('abc123')
        ->created_at->toBe('2023-01-01')
        ->updated_at->toBe('2023-01-02')
        ->deleted_at->toBeNull()
        ->is_duplicate->toBeFalse()
        ->links->toBeArray()->toBeEmpty()
        ->organization_id->toBe(456)
        ->fieldValues->toBeArray()->toBeEmpty();
});

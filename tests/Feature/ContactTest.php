<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PerformRomance\ActiveCampaign\ActiveCampaign;
use PerformRomance\ActiveCampaign\DataTransferObjects\ContactDto;
use PerformRomance\ActiveCampaign\Exceptions\ValidationException;
use PerformRomance\ActiveCampaign\Exceptions\ActiveCampaignException;
use PerformRomance\ActiveCampaign\Support\Request;

beforeEach(function () {
    config([
        'activecampaign.api_url' => 'https://test.api.activecampaign.com',
        'activecampaign.api_key' => 'fake-key-123',
        'activecampaign.api_version' => '3',
    ]);
});

function createMockedActiveCampaign(Response $response): ActiveCampaign
{
    $mock = new MockHandler([$response]);
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $request = new Request(
        config('activecampaign.api_url'),
        config('activecampaign.api_key'),
        config('activecampaign.api_version'),
        $client
    );

    return new ActiveCampaign(
        $request
    );
}

it('can sync a contact', function () {
    $response = [
        'contact' => [
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

    $activeCampaign = createMockedActiveCampaign(
        new Response(200, ['Content-Type' => 'application/json'], json_encode($response))
    );

    $contact = $activeCampaign->contact()
        ->setContactData([
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
        ])
        ->sync();

    expect($contact->getContactData())
        ->toBeInstanceOf(ContactDto::class)
        ->email->toBe('test@example.com')
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->phone->toBe('1234567890')
        ->id->toBe(123);
});

it('validates contact data', function () {
    $activeCampaign = new ActiveCampaign();

    $activeCampaign->contact()
        ->setContactData([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890',
        ])
        ->sync();
})->throws(ValidationException::class, 'The email field is required');

it('throws exception on API error', function () {
    $activeCampaign = createMockedActiveCampaign(
        new Response(401, [], json_encode(['error' => 'Invalid API key']))
    );

    $activeCampaign->contact()
        ->setContactData([
            'email' => 'test@example.com',
        ])
        ->sync();
})->throws(ActiveCampaignException::class);

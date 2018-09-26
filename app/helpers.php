<?php

use Spatie\Url\Url;
use Illuminate\Support\Str;
use WhichBrowser\Constants\DeviceType;
use WhichBrowser\Parser as UserAgentParser;

function parseHost(string $host) : string
{
    $host = parse_url($host);

    return $host['scheme'].'://'.$host['host'];
}

function isBot(string $userAgent) : bool
{
    return (new UserAgentParser($userAgent))->device->type == DeviceType::BOT;
}

function parseReferrer(?string $referrer) : string
{
    $referrer = Url::fromString($referrer ?? '');

    $referrer = $referrer->withoutQueryParameter('amp')
                         ->withoutQueryParameter('utm_campaign')
                         ->withoutQueryParameter('utm_medium')
                         ->withoutQueryParameter('utm_source');

    $referrer = Str::endsWith($path = Str::finish($referrer->getPath(), '/'), '/amp/') ? $referrer->withPath(Str::before($path, '/amp/')) : $referrer;

    return (string) $referrer;
}

<?php

namespace LonghornOpen\LaravelCelticLTI\Adaptors;

use ceLTIc\LTI\Cookie\ClientInterface;
use Illuminate\Support\Facades\Cookie;

class LonghornLaravelCookieClient implements ClientInterface
{

    /**
     * @inheritDoc
     */
    public function numCookies(): int
    {
        // cookies we didn't set will get set to null by the EncryptCookies middleware.  Filter them out.
        return count(array_filter(
            Cookie::get(),
            static fn ($value) => $value !== null
        ));
    }

    /**
     * @inheritDoc
     */
    public function hasCookie(string $name): bool
    {
        return Cookie::has($name);
    }

    /**
     * @inheritDoc
     */
    public function getValue(string $name): ?string
    {
        return Cookie::get($name);
    }

    /**
     * @inheritDoc
     */
    public function createCookie(string $name, string $value, int $expires, string $path, string $domain, bool $secure, bool $httpOnly, string $sameSite): bool
    {
        $cookie = Cookie::make($name, $value, $expires, $path, $domain, $secure, $httpOnly, false, $sameSite);
        if ($secure) {
            $cookie = $cookie->withPartitioned();
        }

        Cookie::queue($cookie);
        return true;
    }
}
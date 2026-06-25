<?php

namespace LonghornOpen\LaravelCelticLTI\Adaptors;

use ceLTIc\LTI\Session\ClientInterface;
use Illuminate\Support\Facades\Session;

class LonghornLaravelSessionClient implements ClientInterface
{

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return Session::getId();
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id): void
    {
        if ($id === Session::getId()) {
            return;
        }

        Session::save();
        Session::flush();
        Session::setId($id);
        Session::start();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return Session::getName();
    }

    /**
     * @inheritDoc
     */
    public function openSession(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function closeSession(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $name): bool
    {
        return Session::has($name);
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $name, mixed $default = null): mixed
    {
        return Session::get($name, $default);
    }

    /**
     * @inheritDoc
     */
    public function setItem(string $name, mixed $value): void
    {
        if (!is_null($value)) {
            Session::put($name, $value);
        } else {
            Session::forget($name);
        }
    }
}
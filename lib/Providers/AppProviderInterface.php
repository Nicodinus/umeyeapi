<?php


namespace Nicodinus\UmeyeApi\Providers;


use Nicodinus\UmeyeApi\App;

interface AppProviderInterface
{
    /**
     * @param App $app
     * @return static
     */
    public function load(App $app): self;

    /**
     * @return void
     */
    public function unload(): void;
}
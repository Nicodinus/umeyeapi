<?php

namespace Nicodinus\UmeyeApi;

define('APP_NAME' , 'Nicodinus\\UmeyeApi');
define('APP_VERSION' , 'v0');

use HaydenPierce\ClassFinder\ClassFinder;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Nicodinus\UmeyeApi\Providers\AppProviderInterface;
use Nicodinus\UmeyeApi\Utils\Utils;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class App extends Application
{
    /** @var static */
    private static self $_instance;

    /**
     * @throws \Throwable
     * @return static
     */
    public static function createInstance(): self {
        if (!empty(static::$_instance))
            throw new \LogicException("Application instance already exists!");

        static::$_instance = new static(APP_NAME, APP_VERSION);

        return static::$_instance;
    }

    /**
     * @return static
     */
    public static function getInstance(): self {
        if (empty(static::$_instance))
            throw new \LogicException("Application instance is not exists!");

        return static::$_instance;
    }

    /**
     * @var Filesystem
     */
    protected Filesystem $fileSystem;

    /** @var AppProviderInterface[]|string[] */
    private array $providerClassnamesRegistry = [];

    /** @var AppProviderInterface[] */
    private array $providerRegistry = [];

    /**
     * App constructor.
     * @param string $name
     * @param string $version
     * @throws \Throwable
     */
    private function __construct(string $name, string $version)
    {
        $fsAdapter = new LocalFilesystemAdapter(APP_DIRECTORY);
        $this->fileSystem = new Filesystem($fsAdapter);

        parent::__construct($name, $version);

        $this
            ->locateProviders()
            ->loadProviders()
        ;
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws \Throwable
     */
    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        return parent::run($input, $output);
    }

    /**
     * @param AppProviderInterface|string $appProvider
     * @return bool
     */
    public function isProviderLoaded($appProvider): bool
    {
        $classname = $appProvider;

        if (is_object($appProvider)) {
            $classname = get_class($appProvider);
        } else if (!is_string($appProvider)) {
            throw new \LogicException("Invalid app provider class!");
        }

        return isset($this->providerRegistry[$classname]);
    }

    /**
     * @return static
     * @throws \Throwable
     */
    public function reloadProviders()
    {
        return $this
            ->unloadProviders()
            ->loadProviders()
        ;
    }

    /**
     * @return static
     * @throws \Throwable
     */
    protected function loadProviders()
    {
        foreach ($this->providerClassnamesRegistry as $classname) {

            if ($this->isProviderLoaded($classname)) {
                continue;
            }

            /** @var AppProviderInterface $appProvider */
            $appProvider = new $classname();
            $appProvider->load($this);

            if ($appProvider instanceof Command) {
                $this->add($appProvider);
            }

            $this->providerRegistry[] = $appProvider;
        }

        return $this;
    }

    /**
     * @return static
     * @throws \Throwable
     * @unsafe Using reflection, to change Symfony\Console\Application `commands` property value
     */
    protected function unloadProviders()
    {
        $reflectionClass = (new \ReflectionClass(static::class))
            ->getParentClass()
        ;

        $commandsProperty = $reflectionClass->getProperty('commands');
        $commandsProperty->setAccessible(true);

        while (sizeof($this->providerRegistry) > 0) {
            $appProvider = array_pop($this->providerRegistry);
            $appProvider->unload();
            unset($appProvider);
        }

        $commandsProperty->setValue($this, []);
        $commandsProperty->setAccessible(false);

        return $this;
    }

    /**
     * @param array|null $classnames
     * @return static
     * @throws \Throwable
     */
    protected function locateProviders(?array $classnames = null)
    {
        if (is_null($classnames)) {
            return $this
                ->locateProviders(get_declared_classes())
                ->locateProviders(ClassFinder::getClassesInNamespace('Nicodinus\UmeyeApi\Providers'))
            ;
        }

        foreach ($classnames as $classname) {

            if (!Utils::isImplementsClassname($classname, AppProviderInterface::class)) {
                continue;
            }

            if (array_search($classname, $this->providerClassnamesRegistry) !== false) {
                continue;
            }

            /** @var AppProviderInterface|string $classname */
            $this->providerClassnamesRegistry[] = $classname;
        }

        return $this;
    }

    /**
     * @param AppProviderInterface|string $appProvider
     * @return static
     * @throws \Throwable
     */
    public function registerProvider($appProvider): self {

        if (is_string($appProvider)) {
            return $this->locateProviders([$appProvider]);
        } else if (!is_object($appProvider)) {
            throw new \LogicException("Invalid app provider class!");
        }

        if (!Utils::isImplementsClassname($appProvider, AppProviderInterface::class)) {
            throw new \LogicException("Invalid app provider class!");
        }

        /** @var AppProviderInterface|string $classname */
        $classname = get_class($appProvider);

        if ($this->isProviderLoaded($classname)) {
            throw new \LogicException("{$classname} already registered!");
        }

        $this->providerRegistry[$classname] = $appProvider;

        return $this;
    }
}
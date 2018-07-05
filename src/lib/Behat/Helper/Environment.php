<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\Helper;

use EzSystems\PlatformInstallerBundle\Installer\Installer;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Environment
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface Symfony DI service container */
    private $serviceContainer;

    /** @var array Directories expected in directory where database restoring script can be run - root directory */
    private $expectedDirectories = ['vendor', 'var', 'web', 'src', 'bin'];

    /** @var array Names of available installer services in Studio */
    private $installerServices = ['platform' => 'ezplatform.installer.clean_installer',
        'platform-demo' => 'app.installer.demo_installer',
        'platform-ee' => 'ezstudio.installer.studio_installer',
        'platform-ee-demo' => 'app.installer.ee_demo_installer', ];

    /**
     * EnvironmentRestore constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Detects installation type and restores the database using parameters from parameters.yml.
     */
    public function restoreDatabase(): void
    {
        $currentDirectory = getcwd();
        $installer = $this->getInstallerService();
        $this->findDirectoryToRunScripts();
        $installer->setOutput(new NullOutput());
        $installer->importSchema();
        $installer->importData();
        chdir($currentDirectory);
    }

    /**
     * Clears cache.
     *
     * Override cache path in AppKernel (getCacheDir method) to point to another directory
     */
    public function clearCache(): void
    {
        $pool = $this->serviceContainer->get('ezpublish.cache_pool');
        $pool->clear();
    }

    /**
     * Traverses up the directory tree until a directory where database scripts should be run is found.
     *
     * @throws \Exception When the directory is not found going up to 5 levels
     */
    private function findDirectoryToRunScripts(): void
    {
        $counter = 0;
        while (!$this->areExpectedDirectoriesPresent()) {
            chdir('..');
            if ($counter > 4) {
                throw new \Exception('Directory to run restoring scripts was not found!');
            }

            ++$counter;
        }
    }

    /**
     * Verifies whether the current folder contains the expected directories.
     *
     * @return bool
     */
    private function areExpectedDirectoriesPresent(): bool
    {
        $actualDirectories = scandir(getcwd(), 0);

        $presentExpectedDirectories = array_intersect($this->expectedDirectories, $actualDirectories);
        sort($this->expectedDirectories);
        sort($presentExpectedDirectories);

        return $this->expectedDirectories === $presentExpectedDirectories;
    }

    /**
     *  Returns installer service depending on current install type.
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException  When no suitable service is found
     *
     * @return Installer Installer service
     */
    private function getInstallerService(): Installer
    {
        switch ($this->getInstallType()) {
            case InstallType::ENTERPRISE_DEMO:
                return $this->serviceContainer->get($this->installerServices['platform-ee-demo']);
            case InstallType::ENTERPRISE:
                return $this->serviceContainer->get($this->installerServices['platform-ee']);
            case InstallType::PLATFORM_DEMO:
                return $this->serviceContainer->get($this->installerServices['platform-demo']);
            case InstallType::PLATFORM:
                return $this->serviceContainer->get($this->installerServices['platform']);
            default:
                throw new ServiceNotFoundException('Installer service not found');
        }
    }

    public function getInstallType(): int
    {
        if ($this->serviceContainer->has($this->installerServices['platform-ee-demo'])) {
            return InstallType::ENTERPRISE_DEMO;
        }

        if ($this->serviceContainer->has($this->installerServices['platform-ee'])) {
            return InstallType::ENTERPRISE;
        }

        if ($this->serviceContainer->has($this->installerServices['platform-demo'])) {
            return InstallType::PLATFORM_DEMO;
        }

        if ($this->serviceContainer->has($this->installerServices['platform'])) {
            return InstallType::PLATFORM;
        }
    }
}

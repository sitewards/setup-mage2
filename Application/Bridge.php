<?php

/**
 * This file is part of the Setup package.
 *
 * (c) Sitewards GmbH
 */

namespace Sitewards\SetupMage2\Application;

use Sitewards\Setup\Application\BridgeInterface;

use Magento\Framework\Console\Cli;
use Magento\Framework\App\ObjectManager;

use Sitewards\SetupMage2\Repository\PageRepository;

final class Bridge implements BridgeInterface
{
    /** @var Cli */
    private $coreCliApp;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(){
        $this->initMagento();
    }

    /**
     * Init the magento bootstrap and create the cli app and object manager
     */
    private function initMagento()
    {
        $projectRootDir = __DIR__ . '/../../../..';

        if (file_exists($projectRootDir . '/app/bootstrap.php')) {
            require $projectRootDir . '/app/bootstrap.php';
        }

        $this->coreCliApp    = new Cli();
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageRepository()
    {
        return new PageRepository(
            $this->objectManager->get(\Magento\Cms\Api\PageRepositoryInterface::class),
            $this->objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class),
            $this->objectManager->get(\Magento\Cms\Api\Data\PageInterfaceFactory::class)
        );
    }
}

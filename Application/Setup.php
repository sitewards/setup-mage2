<?php

namespace Sitewards\SetupMage2\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

use Magento\Framework\Console\Cli;
use Magento\Framework\App\ObjectManager;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;

use Sitewards\Setup\Command\Page\Export;
use Sitewards\SetupMage2\Repository\PageRepository;
use Sitewards\Setup\Service\Page\Exporter;
use Sitewards\Setup\Service\Page\Importer;
use Sitewards\Setup\Command\Page\Import;

class Setup extends Application
{
    /** @var Cli */
    private $coreCliApp;

    /** @var ObjectManager */
    private $objectManager;

    /** @var Serializer */
    private $serializer;

    public function __construct($defaultName = 'Sitewards Setup', $version = '1.0.0')
    {
        parent::__construct($defaultName, $version);
        $this->initSerializer();
        $this->initMagento();
        $this->initCommands();
    }

    private function initSerializer()
    {
        AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation',
            'vendor/jms/serializer/src'
        );

        $this->serializer = SerializerBuilder::create()->build();
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
     * @throws \LogicException
     */
    private function initCommands()
    {
        $mage2Bridge = new PageRepository(
            $this->objectManager->get('\Magento\Cms\Api\PageRepositoryInterface'),
            $this->objectManager->get('\Magento\Framework\Api\SearchCriteriaBuilder'),
            $this->objectManager->get('\Magento\Cms\Api\Data\PageInterfaceFactory')
        );

        $exporter = new Exporter(
            $mage2Bridge,
            $this->serializer,
            new Filesystem()
        );

        $this->add(
            new Export($exporter)
        );

        $importer = new Importer(
            $mage2Bridge,
            $this->serializer
        );

        $this->add(
            new Import($importer)
        );
    }
}

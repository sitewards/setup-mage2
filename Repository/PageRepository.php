<?php

/**
 * This file is part of the Setup package.
 *
 * (c) Sitewards GmbH
 */

namespace Sitewards\Setup\Repository;

use Sitewards\Setup\Domain\Page;
use Sitewards\Setup\Domain\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class PageRepository implements PageRepositoryInterface
{
    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteria;

    /**
     * @var \Magento\Cms\Api\Data\PageInterfaceFactory
     */
    private $pageFactory;

    /**
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria
     * @param \Magento\Cms\Api\Data\PageInterfaceFactory $pageFactory
     */
    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        \Magento\Cms\Api\Data\PageInterfaceFactory $pageFactory
    ) {
        $this->pageRepository = $pageRepository;
        $this->searchCriteria = $searchCriteria;
        $this->pageFactory    = $pageFactory;
    }

    /**
     * @param array $ids
     *
     * @return Page[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findByIds(array $ids)
    {
        $this->setIdFilter($ids);

        return $this->findItems();
    }

    /**
     * @return Page[]
     */
    public function findAll()
    {
        return $this->findItems();
    }

    /**
     * @return Page[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function findItems()
    {
        /** @var \Magento\Framework\Api\SearchResults $results */
        $results = $this->pageRepository->getList($this->searchCriteria->create());

        $pages = [];

        foreach ($results->getItems() as $page) {
            $pages[] = new Page(
                $page['identifier'],
                $page['title'],
                $page['content'],
                $page['active']
            );
        }

        return $pages;
    }

    /**
     * Set the filter ids
     *
     * @param array $ids
     */
    private function setIdFilter(array $ids)
    {
        if (!empty($ids)) {
            $this->searchCriteria->addFilter(
                'identifier',
                implode(',', $ids),
                'in'
            );
        }
    }

    /**
     * Save a given page to the magento page repository
     *
     * @param Page $page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function import(Page $page)
    {
        try {
            $pageToSave = $this->pageRepository->getById($page->getIdentifier());
        } catch (NoSuchEntityException $e) {
            $pageToSave = $this->pageFactory->create();
        }

        $pageToSave
            ->setIdentifier($page->getIdentifier())
            ->setTitle($page->getTitle())
            ->setContent($page->getContent())
            ->setIsActive($page->getActive());

        $this->pageRepository->save(
            $pageToSave
        );
    }
}

# Sitewards Setup Magento 2 Bridge #

This Magento 2 module is a bridge for the [sitewards setup](https://github.com/sitewards/setup) system to allow the import and export of content and configuration in Magento 2.

## Architecture ##

This module contains a bin script and an implementation of the [main module's page repository interface](https://github.com/sitewards/setup#architecture).

**bin/setup**

* Inject the Magento 2 bridge into the main application,
* Run the main application,

**Application/Bridge.php**

* Initialise the Magento2 CLI Application,
* Build the Magento2 specific page repository,

**Repository/PageRepository.php**

* Requires the `\Magento\Cms\Api\PageRepositoryInterface`, `\Magento\Framework\Api\SearchCriteriaBuilder` and `\Magento\Cms\Api\Data\PageInterfaceFactory` classes,
* Implement the `find` and `save` methods from the main application,

## Commands ##

Current commands are as follows:

### Export ###
Export page(s) from Magento 2 to JSON format.

`bin/setup page:export [<pageID>] [<pageID>] ...`

### Import ###
Import page(s) from JSON to Magento 2.

`bin/setup page:import`

## Authors ##

* David Manners <david.manners@sitewards.com>
* Darko Poposki <darko.poposki@sitewards.com>

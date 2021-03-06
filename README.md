![treopim_h80](docs/_assets/treopim_h80.png)

## What is TreoPIM?

![anne](docs/_assets/zs_ft_19_01_2018_employee_eng2.png)

TreoPIM is a single page application (SPA) with an API-centric architecture and flexible data model based on entities, entity attributes and relations of all kinds among them. TreoPIM allows you to gather and store all your product content in one place, enrich it and spread it to several channels like own online shop, amazon, eBay, online shops of your distributors, on a tablet or mobile application. TreoPIM will help you to structure and organize all your flexible data and get rid of excel mess. You can use TreoPIM together with EspoCRM as a single instance, so if you need both - a PIM and a CRM, TreoPIM is the best solution for you! 

## Why TreoPIM?

- **Extensible with Modules**
- Really **quick Time-to-Market** and low implementation costs!
- TreoPIM is 100% free open source solution licenced under GPLv3
- TreoPIM is web based and thus platform independent
- No limitations
- Configurable, flexible and customizable
- Based on modern Technologies
- Modern software architecture it good code quality
- Role-based permissions, even on field level
- Flexible Data Modell
- CRM functions included!
- REST API

## Functions

TreoPIM comes with a lot of functions directly from the box.

![unctions_banne](docs/_assets/how_it_works_scheme_en.png)

| Basic functions     | Advanced functions |
| --------------------------------- | -------------------------------- |
| Simple Data Import and Data Export | Dashboards |
| Channels                    | Knowledge Management |
| Product Associations | Document Management |
| Categorisation and Segmentation | Entity Configurator |
| Product Families           | Team Work            |
| Attribute Groups and Attributes | Roles and Team-Based Access |
| Product Management     | Change history, Notifications and Comments |
| Image Management         | and much more.. |

Want to know more about TreoPIM functions? Please [visit our website](http://treopim.com)!

## Technology

TreoPIM is based on EspoCRM and uses PHP7 and backbone.js.

![Technology_schem](docs/_assets/technologie_scheme_eng.png)

Want to know more about TreoPIM Technology please [visit our website](http://treopim.com)!

## Integrations

TreoPIM can be integration with any system, which are import and export channels. For example, most common way to use TreoPIM is together with you ERP System and several online shops.

We are already working on the following connectors for export channels:

|                      Icon                       | System     | Current State                         |
| :---------------------------------------------: | ---------- | ------------------------------------- |
| ![ystem_magento](docs/_assets/system_magento1.png) | Magento 1  | Connector is currently in development |
| ![ystem_magento](docs/_assets/system_magento2.png) | Magento 2  | Connector is currently in development |
|      ![ystem_oxi](docs/_assets/system_oxid.png)      | OXID eShop | Connector is currently in development |
|  ![ystem_shopwar](docs/_assets/system_shopware.png)  | Shopware   | Connector is currently in development |

### Documentation

- Documentation for users is available [here](docs/).
- Documentation for administrators is available [here](docs/en/administration/).
- Documentation for developers is available [here](docs/).

### Requirements

* Unix-based system
* PHP 7.1 or above (with pdo_mysql, openssl, json, zip, gd, mbstring, xml, curl,exif extensions)
* MySQL 5.5.3 or above

See [Server Configuration](docs/en/administration/server-configuration.md) article for more information.

### Installation
To create your new TreoPIM application, first make sure you're using PHP 7.1 or above and have [Composer](https://getcomposer.org/) installed. 

1. Create your new project by running:
   ```
   composer create-project treo/treopim my-treopim-project
   ```
2. Make cron handler file executable:
   ```
   chmod +x bin/cron.sh 
   ```
3. Configure crontab:
   ```
   * * * * * cd /var/www/my-treopim-project; ./bin/cron.sh process-treopim-1 /usr/bin/php 
   ```
   - **/var/www/my-treopim-project** - path to project root
   - **process-treopim-1** - an unique id of process. You should use different process id if you have few TreoPIM project in one server
   - **/usr/bin/php** - PHP7.1 or above
4. Install TreoPIM by following installation wizard in web interface. Just go to http://YOUR_PROJECT/

### License

TreoPIM is published under the GNU GPLv3 [license](LICENSE.txt).

### Support

- TreoPIM is a developed and supported by [TreoLabs GmbH](https://treolabs.com/).
- Be a part of our [community](https://community.treolabs.com/).
- To Contact Us please visit [TreoPIM Website](http://treopim.com).

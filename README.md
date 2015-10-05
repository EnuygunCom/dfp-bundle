Enuygun Dfp Bundle
==================

This bundle is used to manage DFP ad display settings. Reduces the number of redundant ad requests made to the DFP servers.

This document contains information on how to download, install, and start
using Enuygun Dfp Bundle.

1) Installing the Enuygun Dfp Bundle
------------------------------------

### Use Composer

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

Add following lines into your composer.json

    [...]
    "require" : {
        [...]
        "enuyguncom/dfp-bundle" : "dev-master"
    },
    "repositories" : [{
        "type" : "vcs",
        "url" : "https://github.com/EnuygunCom/dfp-bundle.git"
    }],
    [...]

and then install via composer

    composer update enuyguncom/dfp-bundle

Now you need to add the following configuration into config.yml file

    enuygun_com_dfp:
         publisher_id: %your_dfp_publisher_id%
         default_class: ~
         targets:
             modul:      'your-project-modul-name'
             sub_modul:  ~



Add this bundle to your application kernel:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new EnuygunCom\DfpBundle\EnuygunComDfpBundle(),
            // ...
        );
    }
    
You will be needing a dfp_settings table:

    DROP TABLE IF EXISTS `dfp_settings`;
    CREATE TABLE IF NOT EXISTS `dfp_settings` (
      `id` int(11) NOT NULL,
      `modul` varchar(30) NOT NULL,
      `sub_modul` varchar(30) DEFAULT NULL,
      `settings` text NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

2) How to Use in your views
---------------------------

### Add Control Code in Base Template

    // app/views/base.html.twig
    <head>
        ...
        <!-- DfpBundle Control Code -->
    </head>
    
### Call add units from twig

    {{ dfp_ad_unit('some/campaign', [300, 250]) }}
    
### Define Targets

    {{ dfp_targets({modul: 'modulName', sub_modul: 'subModulName'}) }}
    
### Disable Ad Units

    {{ dfp_disable() }}
    
### Enable Ad Units

    {{ dfp_enable() }}
    
    
3) Enuygun Custom Ad Units
--------------------------
    
### Scroll Ad Unit

    {{ dfp_scroll_ad_unit('EnuygunCom_300x100_scrolldown', [300, 100], 'scrolldown_ad') }}
    
### PageSkin Ad Unit

    {{ dfp_scroll_ad_unit('EnuygunCom_1200x600', [1200, 600], 'page_skin') }}



Note: This bundle is inspired of NodrewDfpBundle.
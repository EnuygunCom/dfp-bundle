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
         env:
           - prod
         locale:
           - tr
         targets:
             modul:      'your-project-modul-name'
             sub_modul:  ~
         cache_lifetime: 300



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
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `modul` varchar(30) NOT NULL,
      `sub_modul` varchar(30) DEFAULT NULL,
      `settings` text NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
    
### Extended usage with class, target and attributes
    {{ dfp_ad_unit('some/campaign', [300, 250], 'custom-class', {cutom: target}, { 'data-attr' : 'attribute-values'}) }}
    
### Define Targets

    {{ dfp_targets({modul: 'modulName', sub_modul: 'subModulName'}) }}
    
### Disable Ad Units

    {{ dfp_disable() }}
    
    {# if you need to call outside the block #} 
    {% set hide_all_ads = dfp_disable() %}
    
### Enable Ad Units

    {{ dfp_enable() }}
    
    {# if you need to call outside the block #} 
    {% set show_all_ads = dfp_enable() %}
    
    
3) Enuygun Custom Ad Units
--------------------------
    
### Scroll Ad Unit

    {{ dfp_scroll_ad_unit('EnuygunCom_300x100_scrolldown', [300, 100], 'scrolldown_ad') }}
    
### PageSkin Ad Unit

    {{ dfp_scroll_ad_unit('EnuygunCom_1200x600', [1200, 600], 'page_skin') }}
    
    
4) If you need to change the default dbal_connection or memcached client, just overwrite the settings service definition
------------------------------------------------------------------------------------------------------------------------

    enuygun_com_dfp.settings:
        class: %enuygun_com_dfp.settings.class%
        arguments: [%enuygun_com_dfp.publisher_id%, %enuygun_com_dfp.default_div_class%,  %enuygun_com_dfp.targets%, %enuygun_com_dfp.env%, %enuygun_com_dfp.locale%, %enuygun_com_dfp.cache_lifetime%, @kernel, @doctrine.dbal.v3_connection, @session.memcached, @request_stack]

    
5) If you wish to use the unit checker
--------------------------------------

NOT: this is not safe, it is open for everyone, TODO a secure way to check the units
    
### Add following to your routing.yml:

    enuygun_com_dfp_checker:
        resource: "@EnuygunComDfpBundle/Controller/"
        type:     annotation
        prefix:   /_dfp/

### Call to enable unit

    {{ path('enuygun_com_dfp_unit_checker', {modul: 'your-modul-name', sub_modul: 'your-sub-modul-name', path: 'your-unit-path', action: 'enable'}) }}

### Call to disable unit

    {{ path('enuygun_com_dfp_unit_checker', {modul: 'your-modul-name', sub_modul: 'your-sub-modul-name', path: 'your-unit-path', action: 'disable'}) }}



Note: This bundle is inspired of NodrewDfpBundle.
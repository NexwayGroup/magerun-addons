magerun-addons
==============

n98magerun addons

Some additional commands for the excellent N98-MageRun Magento command-line tool.

Installation
------------
There are a few options.

Here's the easiest:

1. Create ~/.n98-magerun/modules/ if it doesn't already exist.

        mkdir -p ~/.n98-magerun/modules/

2. Clone the magerun-commands repository in there

        cd ~/.n98-magerun/modules/
        git clone git@github.com:NexwayGroup/magerun-addons.git

Commands
--------

### Save magento configuration, cms, blocks, taxes etc ###

This command will save magento instance configuration

    $ n98-magerun.phar nexway:config:save all path_to_directory

If you want to check all possible configuration types just run

    $ n98-magerun.phar nexway:config:save --help

### Load magento configuration, cms, blocks, taxes etc ###

This command will load magento instance configuration

    $ n98-magerun.phar nexway:config:load path_to_directory

### Squeeze magento dumped configuration ###

This command will squeeze dumped magento configuration

    $ n98-magerun.phar nexway:config:squeeze path_to_directory_or_file

Actions
-------

* salesrule [details](src/Nexway/SetupManager/Util/Processor/Action/Salesrule/README.md)

# Magento 2 Translations Merger
Merge translations from magento i18n:collect command result with current translations.

## Install

    composer config repositories.erighetto vcs https://github.com/erighetto/magento2-translation-merger.git
    composer require --dev erighetto/magento2-translation-merger:dev-master

## Usage

    bin/magento translation-merger:merge [input-directory] [locale]

#### Arguments:

 - **input-directory** - *Input directory of collected Magento CSV file (full path)*
 - **locale** - *Locale (Default: en_US)*

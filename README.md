# ACF Block Creator

[![BracketSpace Micropackage](https://img.shields.io/badge/BracketSpace-Micropackage-brightgreen)](https://bracketspace.com)
[![Latest Stable Version](https://poser.pugx.org/micropackage/acf-block-creator/v/stable)](https://packagist.org/packages/micropackage/acf-block-creator)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/micropackage/acf-block-creator.svg)](https://packagist.org/packages/micropackage/acf-block-creator)
[![Total Downloads](https://poser.pugx.org/micropackage/acf-block-creator/downloads)](https://packagist.org/packages/micropackage/acf-block-creator)
[![License](https://poser.pugx.org/micropackage/acf-block-creator/license)](https://packagist.org/packages/micropackage/acf-block-creator)

## 🧬 About ACF Block Creator

This package simplifies block creation for Gutengberg editor in WordPress using Advanced Custom Fields plugin.
It extends functonality of [Block Loader](https://github.com/micropackage/block-loader) package and is intended to use alongside it.

This package will automatically create a block template file with basic markup for ACF fields while creating a field group for a block.

## 💾 Installation

``` bash
composer require --dev micropackage/acf-block-creator
```

## 🕹 Usage

Before you can start creating blocks this package needs to be initialized:
```php
Micropackage\ACFBlockCreator\ACFBlockCreator::init( [
	'blocks_dir'            => 'blocks',
	'scss_dir'              => false,
	'default_category'      => 'common',
	'block_container_class' => 'block-inner',
	'package'               => true,
	'license'               => 'GPL-3.0-or-later',
] );
```

Only thing to do is to create new ACF field group which name starts with "Block:". It will automatically create a block template file and set the field group location to this created block.

Block params (block name, slug, category etc.) can be adjusted using additional fields at the bottom of field group creation form.

![ACF Block Creator demo](./.github/assets/demo.gif?raw=true)

### Silent initialization

Since this is a development package and is not useful in production environment we probably don't want it's initialization code in the production package of our theme.
That's why this package will be automatically initialized by [Block Loader](https://github.com/micropackage/block-loader) if both packages will be present.

So if we will add this as dev dependency and then run `composer install --no-dev` this package will not be present and just won't get loaded by BlockLoader.

All configuration params of this package can be then passed directly to `BlockLoader::init` method.

Also `blocks_dir` param will not be necessary since it will automatically get the value of `dir` param for BlockLoader.
If you configure a custom category it will also be automatically used as default category for new blocks.

## ⚙️ Configuration
All parameters are optional.

| Parameter                 | Type              | Description                                                  |
| ------------------------- | ----------------- | ------------------------------------------------------------ |
| **blocks_dir**            | (*string*)        | Directory inside the theme for block templates.<br />**Default:** `'blocks'` |
| **scss_dir**              | (*false\|string*) | Directory inside the theme for block sass styles file.<br/>If set, the empty scss file will be created for each block in this directory.<br/>**Default:** `false` |
| **default_category**      | (*string*)        | Default category for new blocks.<br/>**Default:** `'common'` |
| **block_container_class** | (*string*)        | Optional class for wrapping `<div>` element inside the block template.<br/>**Default:** `'block-inner'` |
| **package**               | (*bool\|string*)  | String containing package name for file block comment. If set to true, WordPress site name will be used. If false, no `@package` comment will be added.<br/>**Default:** `true` |
| **license**               | (*false\|string*) | String containing license name for file block comment. If set to false no `@license` comment will be added.<br/>**Default:** `'GPL-3.0-or-later'` |



## 📦 About the Micropackage project

Micropackages - as the name suggests - are micro packages with a tiny bit of reusable code, helpful particularly in WordPress development.

The aim is to have multiple packages which can be put together to create something bigger by defining only the structure.

Micropackages are maintained by [BracketSpace](https://bracketspace.com).

## 📖 Changelog

[See the changelog file](./CHANGELOG.md).

## 📃 License

GNU General Public License (GPL) v3.0. See the [LICENSE](./LICENSE) file for more information.

# DocHooks

[![BracketSpace Micropackage](https://img.shields.io/badge/BracketSpace-Micropackage-brightgreen)](https://bracketspace.com)
[![Latest Stable Version](https://poser.pugx.org/micropackage/acf-block-creator/v/stable)](https://packagist.org/packages/micropackage/acf-block-creator)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/micropackage/acf-block-creator.svg)](https://packagist.org/packages/micropackage/acf-block-creator)
[![Total Downloads](https://poser.pugx.org/micropackage/acf-block-creator/downloads)](https://packagist.org/packages/micropackage/acf-block-creator)
[![License](https://poser.pugx.org/micropackage/acf-block-creator/license)](https://packagist.org/packages/micropackage/acf-block-creator)

## 🧬 About ACF Block Creator

This package simplifies block creation for Gutengberg editor in WordPress using Advanced Custom Fields plugin.


```php
class Example extends HookAnnotations {

	/**
	 * @action test
	 */
	public function test_action() {}

	/**
	 * @filter test 5
	 */
	public function test_filter( $val, $arg ) {
		return $val;
	}

	/**
	 * @shortcode test
	 */
	public function test_shortcode( $atts, $content ) {
		return 'This is test';
	}

}

$example = new Example();
$example->add_hooks();
```

Instead of old:

```php
$example = new Example();

add_action( 'test', [ $example, 'test_action' ] );
add_filter( 'test', [ $example, 'test_filter' ], 5, 2 );
add_shortcode( 'test', [ $example, 'test_shortcode' ] );
```

## 💾 Installation

``` bash
composer require micropackage/acf-block-creator
```

## 🕹 Usage

### Annotations

```
@action <hook_name*> <priority>
@filter <hook_name*> <priority>
@shortcode <shortcode_name*>
```

The hook and shortcode name is required while default priority is `10`.

You don't provide the argument count, the class will figure it out. Just use the callback params you want.

### Test if DocHooks are working

When OPCache has the `save_comments` and `load_comments` disabled, this package won't work, because the comments are stripped down. [See the fallback solution](#fallback).

```php
use Micropackage\DocHooks\Helper;

(bool) Helper::is_enabled();
```

### Using within the class

```php
class Example extends HookAnnotations {

	/**
	 * @action test
	 */
	public function test_action() {}

}

$example = new Example();
$example->add_hooks();
```

### Using as a standalone object

```php
use Micropackage\DocHooks\Helper;

class Example {

	/**
	 * @action test
	 */
	public function test_action() {}

}

$example = Helper::hook( new Example() );
```

### Fallback

Because the HookAnnotations object stores the called hooks in `_called_doc_hooks` property, you are able to pull them out and parse them into a list of old `add_action`, `add_filter` and `add_shortcode` functions.

For this you'll need a central "repository" of all objects with hooks ie. Runtime class. [See the example of this approach](https://github.com/BracketSpace/Notification/blob/master/bin/dump-hooks.php) in the Notification plugin, which uses the WP CLI to dump all the hooks into separate file.

## 📦 About the Micropackage project

Micropackages - as the name suggests - are micro packages with a tiny bit of reusable code, helpful particularly in WordPress development.

The aim is to have multiple packages which can be put together to create something bigger by defining only the structure.

Micropackages are maintained by [BracketSpace](https://bracketspace.com).

## 📖 Changelog

[See the changelog file](./CHANGELOG.md).

## 📃 License

GNU General Public License (GPL) v3.0. See the [LICENSE](./LICENSE) file for more information.

# PHPStan extension for php-enum
[![License](https://poser.pugx.org/marc-mabe/php-enum-phpstan/license)](https://github.com/marc-mabe/php-enum-phpstan/blob/master/LICENSE.txt)
[![Latest Stable](https://img.shields.io/packagist/v/marc-mabe/php-enum-phpstan)](https://packagist.org/packages/marc-mabe/php-enum-phpstan)

[PHP-Enum](https://github.com/marc-mabe/php-enum) enumerations with native PHP.

[PHPStan](https://phpstan.org/) is a static code analysis tool.

> PHPStan focuses on finding errors in your code without actually running it.
> It catches whole classes of bugs even before you write tests for the code.
> It moves PHP closer to compiled languages in the sense that the correctness
> of each line of the code can be checked before you run the actual line.  

This PHPStan extension makes enumerator accessor methods and enum possible values known to PHPStan.

## Install

Install via [Composer](https://getcomposer.org)

```
composer require --dev marc-mabe/php-enum-phpstan
```

and include extension.neon in your project's PHPStan config

```
includes:
  - vendor/marc-mabe/php-enum-phpstan/extension.neon
```

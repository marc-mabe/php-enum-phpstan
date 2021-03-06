# PHPStan extension for php-enum
[![Build Status](https://github.com/marc-mabe/php-enum-phpstan/workflows/Test/badge.svg?branch=master)](https://github.com/marc-mabe/php-enum-phpstan/actions?query=workflow%3ATest%20branch%3Amaster)
[![Code Coverage](https://codecov.io/github/marc-mabe/php-enum-phpstan/coverage.svg?branch=master)](https://codecov.io/gh/marc-mabe/php-enum-phpstan/branch/master/)
[![License](https://poser.pugx.org/marc-mabe/php-enum-phpstan/license)](https://github.com/marc-mabe/php-enum-phpstan/blob/master/LICENSE.txt)
[![Latest Stable](https://poser.pugx.org/marc-mabe/php-enum-phpstan/v/stable.png)](https://packagist.org/packages/marc-mabe/php-enum-phpstan)

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

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/maksimru/conditional-laravel-debugbar/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/maksimru/conditional-laravel-debugbar/?branch=master)
[![codecov](https://codecov.io/gh/maksimru/conditional-laravel-debugbar/branch/master/graph/badge.svg)](https://codecov.io/gh/maksimru/conditional-laravel-debugbar)
[![StyleCI](https://github.styleci.io/repos/143376291/shield?branch=master)](https://github.styleci.io/repos/143376291)
[![CircleCI](https://circleci.com/gh/maksimru/conditional-laravel-debugbar.svg?style=svg)](https://circleci.com/gh/maksimru/conditional-laravel-debugbar)

## About

Allows to boot laravel-debugbar with custom conditions

## Installation

```bash
composer require maksimru/conditional-laravel-debugbar
```

## Usage

1) Publish config file with 
```bash
php artisan vendor:publish
```
2) Create new boot validator implementing MaksimM\ConditionalDebugBar\Interfaces\DebugModeChecker interface
3) Replace validator class in config/conditional-debugbar.php in debugbar-boot-validator
4) See provided example for more details

---

Thanks to barryvdh for awesome barryvdh/laravel-debugbar
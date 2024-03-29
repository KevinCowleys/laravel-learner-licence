<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About

This project was created to test a basic app with NativePHP with the help of livewire.

## Requirements

PHP 8.1

## Project Setup

- composer install
- php artisan native:install
- php artisan native:serve

## Testing

All the code has been covered by unit tests and to run the unit tests you need to run the following command:

```
php artisan test --coverage
```

## NativePHP Fixes for Windows


### Can't serve application

This is due to the platform detection not working properly and the only / quickest fix for this now is just to edit the following file:

```
/vendor/nativephp/electron/resources/js/electron-builder.js
```

And change line 16 to just this:

```
const isWindows = true;
```


# Laravel Pepipost Driver

A Mail Driver with support for Pepipost Send Email Web API, using the original Laravel API. This library extends the original Laravel classes, so it uses exactly the same methods.

To use this package required your [Pepipost Api Key](https://app.pepipost.com). Please make it [Here](https://app.pepipost.com).

## Installation

### Prerequisites

[PHP > 7.1.3](https://www.php.net/manual/en/install.php)

[Composer v1.8](https://getcomposer.org/download/)

[Laravel 5.8](https://laravel.com/docs/5.8/installation)

guzzlehttp/guzzle 6.2.0

A free account on Pepipost. If you don't have a one, [click here](https://app.pepipost.com) to signup and get 30,000 emails free every month.

## Usage

### Configuration in laravel project

Create Laravel project 

laravel new testproject

Add the package to your composer.json and run composer update.

```json

"require": {
    "pepipost/laravel-pepipost-driver": "~1.0"
},
```
or installed with composer

$ composer require pepipost/laravel-pepipost-driver

Add the sendgrid service provider in config/app.php: (Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.)

```php

'providers' => [
    Pepipost\LaravelPepipostDriver\PepipostTransportServiceProvider::class
];
```

in config/services.php


```php
    'pepipost' => [
        'api_key' => env('PEPIPOST_API_KEY'),
    ],

```

endpoint config

If you need to set custom endpoint, you can set any endpoint by using endpoint key. For example, calls to Pepipost Web API through a proxy,configure endpoint in config/services.php.

```php    
'pepipost' => [
        'api_key' => env('PEPIPOST_API_KEY'),
        'endpoint' => 'https://app.example.com/sendEmail',
    ],
```

in .env file add following

.env

MAIL_DRIVER=pepipost

PEPIPOST_API_KEY='YOUR_PEPIPOST_API_KEY'


### Laravel Steps

Define Controller

```bash

php artisan make:controller TestController

```
include following function sendMail

viewname.name sent as content of email

```php
function sendMail(){

Mail::send('viewname.name',$data, function ($message) {
    $message
        ->to($data['recipient'], $data['recipent_name'])
        ->from($data['sender_email'], $data['sender_name'])
        ->subject($data['email_subject'])
        ->cc($data['recipient_cc'],$data['recipient_cc_name'])
        ->bcc($data['recipient_bcc'],$data['recipient_bcc_name'])
        ->replyTo($data['reply_to'], $data['recipient_bcc'])
        ->attach('/myfilename.pdf');
});
```
create file in resources/views/viewname/name.blade.php 
and include your email content 


IF want to pass others parameters of Pepipost SendEmail API use embedData function and include below code as below
Add parameters as per your requirement 

```php
function sendMail(){

Mail::send('viewname.name',$data, function ($message) {
    $message
        ->to($data['recipient'], $data['recipent_name'])
        ->from($data['sender_email'], $data['sender_name'])
        ->subject($data['email_subject'])
        ->cc($data['recipient_cc'],$data['recipient_cc_name'])
        ->bcc($data['recipient_bcc'],$data['recipient_bcc_name'])
        ->replyTo($data['reply_to'], $data['recipient_bcc'])
        ->attach('/myfilename.pdf')
        ->embedData([
            'personalizations' => ['attributes'=>['ACCOUNT_BAL'=>'String','NAME'=>'NAME'],'x-apiheader'=>'x-apiheader_value','x-apiheader_cc'=>'x-apiheader_cc_value'],'settings' => ['bcc'=>'bccemail@gmail.com','clicktrack'=>1,'footer'=>1,'opentrack'=>1,'unsubscribe'=>1 ],'tags'=>'tags_value','templateId'=>''
        ],'pepipostapi');        
});
```

Create Route in routes/web.php

```php

Route::get('/send/email', 'TestController@sendMail')->name('sendEmail');

```


## License
[MIT](https://choosealicense.com/licenses/mit/)

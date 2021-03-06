![pepipostlogo](https://pepipost.com/wp-content/uploads/2017/07/P_LOGO.png)

[![Twitter Follow](https://img.shields.io/twitter/follow/pepi_post.svg?style=social&label=Follow)](https://twitter.com/pepi_post)

# Laravel Driver for [Pepipost](http://www.pepipost.com/?utm_campaign=GitHubSDK&utm_medium=GithubSDK&utm_source=GithubSDK)

A Mail Driver with support for Pepipost Send Email Web API, using the original Laravel API. This library extends the original Laravel classes, so it uses exactly the same methods.

To use this package required your [Pepipost Api Key](https://app.pepipost.com). Please make it [Here](https://app.pepipost.com).


We are trying to make our libraries Community Driven- which means we need your help in building the right things in proper order we would request you to help us by sharing comments, creating new [issues](https://github.com/pepipost/laravel-pepipost-driver/issues) or [pull requests](https://github.com/pepipost/laravel-pepipost-driver/pulls).


We welcome any sort of contribution to this library.

The latest 1.0.0 version of this library provides is fully compatible with the latest Pepipost v2.0 API.

For any update of this library check [Releases](https://github.com/pepipost/laravel-pepipost-driver/releases).

# Table of Content
  
* [Installation](#installation)
* [Quick Start](#quick-start)
* [Usage of library in Project](#inproject)
* [Sample Example](#eg)
* [Announcements](#announcements)
* [Roadmap](#roadmap)
* [About](#about)
* [License](#license)

<a name="installation"></a>
# Installation

<a name="prereq"></a>

### Prerequisites

[PHP > 7.1.3](https://www.php.net/manual/en/install.php)

[Composer v1.8](https://getcomposer.org/download/)

[Laravel 5.8](https://laravel.com/docs/5.8/installation)

guzzlehttp/guzzle 6.2.0

A free account on Pepipost. If you don't have a one, [click here](https://app.pepipost.com) to signup.

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
or install with composer

$ composer require pepipost/laravel-pepipost-driver

Add the pepipost service provider in config/app.php: (Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.)

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
        'endpoint' => 'https://api.pepipost.com/v2/sendEmail',
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

viewname.name will be sent as content of email

```php
function sendMail(){

Mail::send('viewname.name',$data, function ($message) {
    $message
        ->to($data['recipient'], $data['recipent_name'])
        ->from($data['sender_email'], $data['sender_name'])
        ->subject($data['email_subject'])
        ->cc($data['recipient_cc'],$data['recipient_cc_name'])
        ->bcc($data['recipient_bcc'],$data['recipient_bcc_name'])
        ->replyTo($data['reply_to'], $data['recipient_bcc']);
        ->attach('/myfilename.pdf');
});
```
create file in resources/views/viewname/name.blade.php 
and include your email content 

Create Route in routes/web.php

```php

Route::get('/send/email', 'TestController@sendMail')->name('sendEmail');

```
IF want to pass others parameters of Pepipost SendEmail API use embedData function and include below code as below
Add parameters as per your requirement. Do not use multiple to's,cc's,bcc's with this method.

```php
function sendMail(){

Mail::send('viewname.name',$data, function ($message) {
    $message
        ->to('foo@example.com', 'foo_name')
        ->from('sender@example.com', 'sender_name')
        ->subject('subject')
        ->cc('cc@example.com','recipient_cc_name')
        ->bcc('recipient_bcc'@example.com,'recipient_bcc_name')
        ->replyTo('reply_to'@example.com,'recipient_bcc')
        ->attach('/myfilename.pdf')
        ->embedData([
            'personalizations' => ['attributes'=>['ACCOUNT_BAL'=>'String','NAME'=>'NAME'],'x-apiheader'=>'x-apiheader_value','x-apiheader_cc'=>'x-apiheader_cc_value'],'settings' => ['bcc'=>'bccemail@gmail.com','clicktrack'=>1,'footer'=>1,'opentrack'=>1,'unsubscribe'=>1 ],'tags'=>'tags_value','templateId'=>''
        ],'pepipostapi');        

```

For multiple to's,cc's,bcc's pass recipient,recipient_cc,recipient_bcc as below, create personalizations as required

```php


function sendMail(){

Mail::send('viewname.name',$data, function ($message) {
    $message
        ->from('sender@example.com', 'sender_name')
        ->subject('subject')
        ->replyTo('reply_to'@example.com,'recipient_bcc')
        ->attach('/myfilename.pdf')
        ->embedData([
                    'personalizations' => [['recipient'=>'foo@example.com','attributes'=>['ACCOUNT_BAL'=>'String','NAME'=>'name'],'recipient_cc'=>['cc@example.com','cc2@example.com'],'recipient_bcc'=>['bcc@example.com','bcc2@example.com'],'x-apiheader'=>'x-apiheader_value','x-apiheader_cc'=>'x-apiheader_cc_value'],['recipient'=>'foo@example.com','attributes'=>['ACCOUNT_BAL'=>'String','NAME'=>'name'],'x-apiheader'=>'x-apiheader_value','x-apiheader_cc'=>'x-apiheader_cc_value']],'settings' => ['bcc'=>'bccemail@gmail.com','clicktrack'=>1,'footer'=>1,'opentrack'=>1,'unsubscribe'=>1 ],'tags'=>'tags_value','templateId'=>''
                ],'pepipostapi');
        });

```




<a name="announcements"></a>
# Announcements

v1.0.0 has been released! Please see the [release notes](https://github.com/pepipost/laravel-pepipost-driver/releases/) for details.

All updates to this library are documented in our [releases](https://github.com/pepipost/laravel-pepipost-driver/releases). For any queries, feel free to reach out us at dx@pepipost.com

<a name="roadmap"></a>
## Roadmap

If you are interested in the future direction of this project, please take a look at our open [issues](https://github.com/pepipost/laravel-pepipost-driver/issues) and [pull requests](https://github.com/pepipost/laravel-pepipost-driver/pulls). We would love to hear your feedback.

<a name="about"></a>
## About
pepipost-laravel library is guided and supported by the [Pepipost Developer Experience Team](https://github.com/orgs/pepipost/teams/pepis/members) .
This pepipost library is maintained and funded by Pepipost Ltd. The names and logos for pepipost gem are trademarks of Pepipost Ltd.

<a name="license"></a>
## License
[MIT](https://choosealicense.com/licenses/mit/)

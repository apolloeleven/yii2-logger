Yii2 Custom Logger
==================
Sending Yii2 application logs to different targets asynchronously or synchronously.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist apollo11/yii2-logger "*"
```

or add

```
"apollo11/yii2-logger": "*"
```

to the require section of your `composer.json` file.

The package offers:

1. Abstract [Target](https://github.com/apolloeleven/yii2-logger/blob/master/Target.php) class with support of sending messages asynchronously. It also has possibility to hide sensitive information when sending $_POST or other $GLOBALS data to target.
2. Slack target: Sending messages to slack channel

Basic Usage
-----
The package supports three target classes: [EmailTarget](https://github.com/apolloeleven/yii2-logger/blob/master/EmailTarget.php), [SlackTarget](https://github.com/apolloeleven/yii2-logger/blob/master/SlackTarget.php), [DbTarget](https://github.com/apolloeleven/yii2-logger/blob/master/DbTarget.php).

All target classes have support for sending messages asynchronously and hide passwords provided by user. If you set `async`

### EmailTarget

Add the following code to your project configuration file under `components` -> `log` -> `targets`
```php
'class' => apollo11\logger\EmailTarget::class,
'except' => ['yii\web\HttpException:*', 'yii\web\HeadersAlreadySentException'],
'levels' => ['error', 'warning'],
// If async is set to true you have to provide consoleAppPath
'async' => true,
'consoleAppPath' => Yii::getAlias('@console/yii'),
// If you would like to use different php binary, when sending messages asynchronously you can set it from here
// 'phpExecPath' => 'php',
// Provide here keys which will be hidden before sending messages. It is case insensitive
'excludeKeys' => [
    '*PASSWORD*', // Will hide all keys from $GLOBALS objects which contains "password".
    '*PASSWORD', // Will hide all keys from $GLOBALS objects which ends with "password".
    'PASSWORD*', // Will hide all keys from $GLOBALS objects which starts with "password".
],
```

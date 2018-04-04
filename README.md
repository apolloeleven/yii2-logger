Yii2 Custom Logger
==================
Sending Yii2 application logs to different targets asynchronously or synchronously.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist apollo11/yii2-logger "~1.0"
```

or add

```
"apollo11/yii2-logger": "~1.0"
```

to the require section of your `composer.json` file.

The package offers:

1. Abstract [Target](https://github.com/apolloeleven/yii2-logger/blob/master/Target.php) class with support of sending messages asynchronously. It also has possibility to hide sensitive information when sending $_POST or other $GLOBALS data to target.
2. Slack target: Sending messages to slack channel

Basic Usage
-----
The package supports three target classes: [EmailTarget](https://github.com/apolloeleven/yii2-logger/blob/master/EmailTarget.php), [SlackTarget](https://github.com/apolloeleven/yii2-logger/blob/master/SlackTarget.php), [DbTarget](https://github.com/apolloeleven/yii2-logger/blob/master/DbTarget.php).

All target classes have support for sending messages asynchronously and hide passwords(or other sensitive data) provided by user. If you set `async` to `true` than you must provide the `consoleAppPath`.

EmailTarget and DbTarget work pretty much in the simillar way as it is described in [Yii Documentation](https://www.yiiframework.com/doc/guide/2.0/en/runtime-logging).

Add the following code to your project configuration file under `components` -> `log` -> `targets`
```php
'class' => <Target class>,
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

### SlackTarget
```php
'class' => apollo11\logger\SlackTarget::class,
'except' => ['yii\web\HttpException:*', 'yii\web\HeadersAlreadySentException'],
'webhookUrl' => <Slack channel webhook url>,
'icon_url' => '<Slack sender icon url>',
'icon_emoji' => '<Slack sender icon emoji>', // If both, icon_url and icon_emoji is provided system will use icon_emoji
'levels' => ['error', 'warning'],
'title_link' => '<Url which will be opened when clicking on title of the slack message>',
'async' => true,
'consoleAppPath' => Yii::getAlias('@console/yii'),
'username' => '<Username which will be used as sender on slack channer>',
'excludeKeys' => [],
```


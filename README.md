# PHP Slack Bot Cleverbot.IO Command

A cleverbot.io implementation on Slack using PHP Slack Bot

## Installation

Create a new composer.json file and add the following...
```
{
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/schnabear/php-cleverslack-bot.git"
        }
    ],
    "require": {
        "schnabear/php-cleverslack-bot": "dev-master"
    }
}
```

Then run...
```
composer install
```

## Usage

```php
require 'vendor/autoload.php';

define('CBIO_USER', 'CleverbotIOUser');
define('CBIO_KEY', 'CleverbotIOKey');
define('CBIO_NICK', 'CleverbotIONick');
define('SLACK_TOKEN', 'SlackToken');

$cleverbot = new \PhpCleverSlackBot\CleverbotCommand(CBIO_USER, CBIO_KEY);
$cleverbot->setNick(CBIO_NICK);
$bot = new \PhpSlackBot\Bot();
$bot->setToken(SLACK_TOKEN);
$bot->loadCatchAllCommand($cleverbot);
$bot->run();
```

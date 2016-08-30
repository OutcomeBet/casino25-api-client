# casino25-api-client
PHP API Client for Casino v2.5

All of the information you need you can find on [Wiki Home Page](https://github.com/OutcomeBet/casino25-api-client/wiki)


Example of usage:
```php
<?php
use outcomebet\casino25\api\client\Client;

require __DIR__.'/vendor/autoload.php';

$client = new Client(array(
        'url' => 'https://api.gamingsystem.org:8443/',
        'sslKeyPath' => __DIR__.'/ssl/apikey.pem',
));

var_export($client->listGames());
```

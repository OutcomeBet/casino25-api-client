# casino25-api-client
PHP API Client for Casino v2.5

Example of usage:
```php
<?php
use outcomebet\casino25\api\client\Client;

require __DIR__.'/vendor/autoload.php';

$client = new Client(array(
        'url' => 'https://api.c27.games/v1/',
        'sslKeyPath' => __DIR__.'/ssl/apikey.pem',
));

var_export($client->listGames());
```

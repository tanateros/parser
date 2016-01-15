<h1>Parser</h1>

<h3>Installation</h3>
<code>composer require tanateros/emailparser "dev-master"</code>

<h3>Example</h3>

```php
<?php
require './vendor/autoload.php';

$config = [
    'cacheDir' => __DIR__ . DIRECTORY_SEPARATOR . 'cache',
    'periodTime' => 0.01,
    'dataFormat' => 'txt',
];
$site = 'http://example.com';

$parser = new Parser\Email($config, $site);
$parser->parseSiteMail();
```

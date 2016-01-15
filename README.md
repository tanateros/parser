<h1>Parser</h1>

<h3>Installation</h3>
<code>composer require tanateros/parser "dev-master"</code>

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

// parse images
$emails = new TanaterosProject\Parser\Image($config, $site);
echo $emails->parse('img');

// parse e-mails
$emails = new TanaterosProject\Parser\Email($config, $site);
echo $emails->parse('mail');
```

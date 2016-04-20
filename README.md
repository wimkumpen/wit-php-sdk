# PHP SDK FOR WIT.AI API
This is a php sdk for the wit.ai API. It's a slim version of the Facebook PHP SDK. Still work in progress...
Only supports curl at this moment.

## Setup:
Add a ```composer.json``` file to your project:
```
{
  "require": {
      "wimkumpen/wit-php-sdk": "1.0.*"
  }
}
```

Then provided you have [composer](http://getcomposer.org/) installed, you can run the following command:
```
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your .php files in order to use the library
```
require_once __DIR__.'/vendor/autoload.php';
```

Then you need to ```use``` the relevant classes, for example:
```
use Wit\Wit;
```

## Basic usage:

```
use Wit\Wit;
$app = new Wit(array(
    'default_access_token' => 'your_access_token')
);

$response = $app->get('/intents');
var_dump($response->getDecodedBody());

$data = [
    "name" => "flight_request",
    "doc"  => "detect flight request",
    "expressions" => [
        ["body" => "fly from incheon to sfo"],
        ["body" => "I want to fly from london to sfo"],
        ["body" => "need a flight from paris to tokyo"],
    ]
];

$response = $app->post('/intents', $data);
var_dump($response->getDecodedBody());
```


## Todo:
- [ ] Clean some code out;
- [ ] Exceptions;
- [ ] Get intent via speech (https://wit.ai/docs/http/20160330#get-intent-via-speech-link)
- [ ] Implement other Http Clients;
- [ ] Implement structured response;
- [ ] Define requirements;
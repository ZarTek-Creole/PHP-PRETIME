<?PHP
require '../vendor/autoload.php';

$app = new \PhpPretime\App();

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);

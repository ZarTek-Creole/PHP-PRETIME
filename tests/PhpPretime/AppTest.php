<?PHP
namespace Tests\PhpPretime;

use GuzzleHttp\Psr7\ServerRequest;
use PhpPretime\App;
use PHPUnit\Framework\TestCase;


class AppTest extends TestCase {
    public function testRedirectTrailingSlash() {
        $app = new App();
        $request = new ServerRequest('GET','/testRedirectTrailingSlash/');
        $response = $app->run($request);
        $this->assertContains('/testRedirectTrailingSlash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }
}
<?php

declare(strict_types=1);

namespace System\Test\Integrate\Exceptions;

use PHPUnit\Framework\TestCase;
use System\Http\Request;
use System\Http\Response;
use System\Integrate\Application;
use System\Integrate\Exceptions\Handler;
use System\Integrate\Http\Exception\HttpException;
use System\Integrate\Http\Karnel;
use System\Text\Str;
use System\View\Templator;

final class HandlerTest extends TestCase
{
    private Application $app;
    private Karnel $karnel;
    private Handler $handler;
    /**
     * Mock Logger.
     *
     * @var string[]
     */
    public static array $logs = [];

    protected function setUp(): void
    {
        $this->app = new Application(__DIR__);

        $this->app->set(
            Karnel::class,
            fn () => new $this->karnel($this->app)
        );

        $this->app->set(
            Handler::class,
            fn () => $this->handler
        );

        $this->karnel = new class($this->app) extends Karnel {
            protected function dispatcher(Request $request): array
            {
                throw new HttpException(500, 'Test Exception');

                return [
                    'callable'   => fn () => new Response('ok', 200),
                    'parameters' => [],
                    'middleware' => [],
                ];
            }
        };

        $this->handler = new class($this->app) extends Handler {
            public function render(Request $request, \Throwable $th): Response
            {
                // try to bypass test for json format
                if ($request->isJson()) {
                    return $this->handleJsonResponse($th);
                }

                if ($th instanceof HttpException) {
                    return new Response($th->getMessage(), $th->getStatusCode(), $th->getHeaders());
                }

                return parent::render($request, $th);
            }

            public function report(\Throwable $th): void
            {
                HandlerTest::$logs[] = $th->getMessage();
            }
        };
    }

    protected function tearDown(): void
    {
        $this->app->flush();
        HandlerTest::$logs = [];
    }

    /** @test */
    public function itCanRenderException()
    {
        $karnel      = $this->app->make(Karnel::class);
        $response    = $karnel->handle(new Request('/test'));

        $this->assertEquals('Test Exception', $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function itCanReportException()
    {
        $karnel      = $this->app->make(Karnel::class);
        $karnel->handle(new Request('/test'));

        $this->assertEquals(['Test Exception'], HandlerTest::$logs);
    }

    /** @test */
    public function itCanRenderJson()
    {
        $this->app->set('environment', 'prod');
        $karnel      = $this->app->make(Karnel::class);
        $response    = $karnel->handle(new Request('/test', [], [], [], [], [], [
            'content-type' => 'application/json',
        ]));

        $this->assertEquals([
            'code'     => 500,
            'messages' => [
                'message'   => 'Internal Server Error',
            ],
        ], $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function itCanRenderJsonForDev()
    {
        $this->app->set('environment', 'dev');
        $karnel      = $this->app->make(Karnel::class);
        $response    = $karnel->handle(new Request('/test', [], [], [], [], [], [
            'content-type' => 'application/json',
        ]));

        $content = $response->getContent();
        $this->assertEquals('Test Exception', $content['messages']['message']);
        $this->assertEquals('System\Integrate\Http\Exception\HttpException', $content['messages']['exception']);
        // skip meggase.file issue test with diferent platform
        $this->assertEquals(46, $content['messages']['line']);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /** @test */
    public function itCanRenderHttpException()
    {
        $this->app->set('view.instance', fn () => new Templator(__DIR__ . '/assets/', __DIR__ . '/assets/'));
        $this->app->set(
            'view.response',
            fn () => fn (string $view, array $data = []): Response => (new Response())->setContent(
                $this->app->make('view.instance')->render("{$view}.template.php", $data)
            )
        );

        $handler = $this->app->make(Handler::class);

        $exception = new HttpException(500, 'Internal Error', null, []);
        $render    = (fn () => $this->{'handleHttpException'}($exception))->call($handler);

        $this->assertTrue(Str::contains($render->getContent(), '<h1>Success</h1>'));
    }
}

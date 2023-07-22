<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use System\Http\Request;
use System\Router\RouteDispatcher;
use System\Router\Router;

class RouteControllerTest extends TestCase
{
    protected $backup;

    public function __construct()
    {
        parent::__construct();
    }

    protected function tearDown(): void
    {
        Router::Reset();
    }

    public function dispatcher(string $url, string $method)
    {
        $request  = new Request($url, [], [], [], [], [], [], $method);
        $dispatch = new RouteDispatcher($request, Router::getRoutesRaw());

        $call = $dispatch->run(
            // found
            function ($callable, $param) {
                if (is_array($callable)) {
                    [$class, $method] = $callable;

                    return call_user_func_array([new $class(), $method], $param);
                }

                return call_user_func($callable, $param);
            },
            // not found
            function ($path) { echo 'not found'; },
            // method not allowed
            function ($path, $method) { echo 'not allowed'; },
        );

        ob_start();
        call_user_func_array($call['callable'], $call['params']);

        return ob_get_clean();
    }

    /** @test */
    public function itCanRouteUsingResourceController()
    {
        Router::resource('/', RouteClassController::class);

        $res = $this->dispatcher('/', 'get');
        $this->assertEquals('works', $res);

        $res = $this->dispatcher('/create', 'get');
        $this->assertEquals('works create', $res);

        $res = $this->dispatcher('/', 'post');
        $this->assertEquals('works store', $res);

        $res = $this->dispatcher('/12', 'get');
        $this->assertEquals('works show', $res);

        $res = $this->dispatcher('/12/edit', 'get');
        $this->assertEquals('works edit', $res);

        $res = $this->dispatcher('/12', 'put');
        $this->assertEquals('works update', $res);

        $res = $this->dispatcher('/12', 'delete');
        $this->assertEquals('works destroy', $res);
    }

    /** @test */
    public function itCanRouteUsingResourceControllerWithCostumeOnly()
    {
        Router::resource('/', RouteClassController::class, [
            'only' => ['index'],
        ]);

        $res = $this->dispatcher('/', 'get');
        $this->assertEquals('works', $res);

        $res = $this->dispatcher('/', 'post');
        $this->assertEquals('not allowed', $res);
    }

    /** @test */
    public function itCanRouteUsingResourceControllerWithCostumeExcept()
    {
        Router::resource('/', RouteClassController::class, [
            'except' => ['store'],
        ]);

        $res = $this->dispatcher('/', 'get');
        $this->assertEquals('works', $res);

        $res = $this->dispatcher('/', 'post');
        $this->assertEquals('not allowed', $res);

        $res = $this->dispatcher('/create', 'get');
        $this->assertEquals('works create', $res);

        $res = $this->dispatcher('/12', 'get');
        $this->assertEquals('works show', $res);

        $res = $this->dispatcher('/12/edit', 'get');
        $this->assertEquals('works edit', $res);

        $res = $this->dispatcher('/12', 'put');
        $this->assertEquals('works update', $res);

        $res = $this->dispatcher('/12', 'delete');
        $this->assertEquals('works destroy', $res);
    }
}

class RouteClassController
{
    public function index()
    {
        echo 'works';
    }

    public function create()
    {
        echo 'works create';
    }

    public function store()
    {
        echo 'works store';
    }

    public function show()
    {
        echo 'works show';
    }

    public function edit()
    {
        echo 'works edit';
    }

    public function update()
    {
        echo 'works update';
    }

    public function destroy()
    {
        echo 'works destroy';
    }
}

<?php

use PHPUnit\Framework\TestCase;
use System\View\Exceptions\ViewFileNotFound;
use System\View\TemplatorFinder;

class TemplatorFinderTest extends TestCase
{
    /**
     * @test
     */
    public function itCanFindTemplatorFileLocation(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php']);

        $this->assertEquals($loader . 'php.php', $view->find('php'));
    }

    /**
     * @test
     */
    public function itCanFindTemplatorFileLocationWillThrows(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php']);

        $this->expectException(ViewFileNotFound::class);
        $view->find('blade');
    }

    /**
     * @test
     */
    public function itCanCheckFIleExist(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php', '.component.php']);

        $this->assertTrue($view->exists('php'));
        $this->assertTrue($view->exists('repeat'));
        $this->assertFalse($view->exists('index.blade'));
    }

    /**
     * @test
     */
    public function itCanFindInPath(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php']);

        $this->assertEquals($loader . 'php.php', (fn () => $this->{'findInPath'}('php', [$loader]))->call($view));
    }

    /**
     * @test
     */
    public function itCanFindInPathWillThrowException(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php']);

        $this->expectException(ViewFileNotFound::class);
        (fn () => $this->{'findInPath'}('blade', [$loader]))->call($view);
    }

    /**
     * @test
     */
    public function itCanAddPath(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([], ['.php']);
        $view->addPath($loader);

        $this->assertEquals($loader . 'php.php', $view->find('php'));
    }

    /**
     * @test
     */
    public function itCanAddExtension(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader]);
        $view->addExctension('.php');

        $this->assertEquals($loader . 'php.php', $view->find('php'));
    }

    /**
     * @test
     */
    public function itCan(): void
    {
        $loader  = __DIR__ . DIRECTORY_SEPARATOR . 'sample' . DIRECTORY_SEPARATOR . 'Templators' . DIRECTORY_SEPARATOR;

        $view = new TemplatorFinder([$loader], ['.php']);

        $view->find('php');
        $views = (fn () => $this->{'views'})->call($view);
        $this->assertCount(1, $views);
        $view->flush();
        $views = (fn () => $this->{'views'})->call($view);
        $this->assertCount(0, $views);
    }
}

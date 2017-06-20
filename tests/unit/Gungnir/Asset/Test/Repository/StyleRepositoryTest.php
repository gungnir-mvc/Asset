<?php
namespace Gungnir\Asset\Test\Repository;


use Gungnir\Asset\Repository\StyleRepository;
use Gungnir\Core\FileInterface;
use org\bovigo\vfs\vfsStream;

class StyleRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function itCanBeInstantiated()
    {
        $repository = new StyleRepository('/');
        $this->assertInstanceOf(StyleRepository::class, $repository);
    }

    /**
     * @test
     */
    public function itCanLoadAStylesheetThatExists()
    {
        $root = vfsStream::setup('root');
        $css = vfsStream::newDirectory('css');
        $root->addChild($css);
        $cssBasePath = $root->url() . '/css/';
        $content = 'body {color:black;}';
        file_put_contents($cssBasePath . 'main.css', $content);
        $repository = new StyleRepository($cssBasePath);

        $file = $repository->getStylesheet('main.css');

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals($content, $file->read());
    }

    /**
     * @test
     */
    public function itCanCombineMultipleStylesheetsThatExists()
    {
        $root = vfsStream::setup('root');
        $css = vfsStream::newDirectory('css');
        $root->addChild($css);
        $cssBasePath = $root->url() . '/css/';
        $content1 = 'body {color:black;}';
        $content2 = 'html {padding:0; margin:0;}';

        file_put_contents($cssBasePath . 'main.css', $content1);
        file_put_contents($cssBasePath . 'secondary.css', $content2);

        $repository = new StyleRepository($cssBasePath);

        $file = $repository->getCombinedStylesheet(['main.css', 'secondary.css']);

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals($content1 . ' ' . $content2, $file->read());
        $this->assertTrue(file_exists($cssBasePath . 'combined.css'));
    }
}
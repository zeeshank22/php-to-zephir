<?php

use PhpToZephir\EngineFactory;
use PhpToZephir\Logger;
use Symfony\Component\Console\Output\NullOutput;
use PhpToZephir\FileWriter;

class ConvertTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertCode()
    {
        $this->convert(__DIR__ . '/Code/');
    }

    private function convert($dir)
    {
        $engine     = EngineFactory::getInstance(new Logger(new NullOutput(), false));
        $fileWriter = new FileWriter();

        foreach ($engine->convertDirectory($dir, true) as $filePath => $file) {
            $zephirFile = file_get_contents(str_replace('.php', '.zep', $filePath));

            $fileWriter->write($file);

            $this->assertEquals(
                $zephirFile,
                $file['zephir'],
                'test failed on file ' . $filePath
            );
        }
    }
}

<?php

declare(strict_types=1);

namespace Datashaman\Tongs\Plugins\Tests;

use Datashaman\Tongs\Tongs;
use Datashaman\Tongs\Plugins\MorePlugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class MorePluginTest extends TestCase
{
    public function testHandle()
    {
        $tongs = new Tongs($this->fixture('basic'));
        $tongs->use(new MorePlugin());
        $files = $tongs->build();

        $this->assertFiles($this->fixture('basic/files.json'), $files);
        $this->assertDirEquals($this->fixture('basic/expected'), $this->fixture('basic/build'));
    }

    protected function assertFiles(string $expected, array $actual)
    {
        $expected = json_decode(File::get($expected), true);
        $this->assertEquals($expected, $actual);
    }
}

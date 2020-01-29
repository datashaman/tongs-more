<?php

namespace Datashaman\Tongs\Plugins;

use Datashaman\Tongs\Plugins\Plugin;
use Datashaman\Tongs\Tongs;
use Illuminate\Support\Facades\File;

class MorePlugin extends Plugin
{
    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = $this->normalize($options);

        parent::__construct($options);
    }

    /**
     * Handle files passed down the pipeline, and call the next plugin in the pipeline.
     *
     * @param array $files
     * @param callable $next
     *
     * @return array
     */
    public function handle(array $files, callable $next): array
    {
        $ret = [];

        foreach ($files as $path => $file) {
            $extension = File::extension($path);

            if ($extension === $this->options['ext'] && $file['contents']) {
                $parts = preg_split($this->options['pattern'], $file['contents']);

                if (is_array($parts) && count($parts) > 1) {
                    $file[$this->options['key']] = $parts[0];
                }
            }

            $ret[$path] = $file;
        }

        return $next($ret);
    }

    /**
     * Normalize options to a consistent internal form, converting
     * strings to arrays or whatever else is needed.
     *
     * @param array $options
     *
     * @return array
     */
    protected function normalize(array $options): array
    {
        $defaults = [
            'ext' => 'html',
            'key' => 'excerpt',
            'pattern' => '/\s*<!--\s*more\s*-->/',
        ];

        return array_merge(
            $defaults,
            $options
        );
    }
}

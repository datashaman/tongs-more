<?php

namespace Datashaman\Tongs\Plugins;

use Datashaman\Tongs\Plugins\Plugin;
use Datashaman\Tongs\Tongs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class MorePlugin extends Plugin
{
    /**
     * @param Tongs $tongs
     * @param array $options
     */
    public function __construct(Tongs $tongs, array $options = [])
    {
        $options = $this->normalize($options);

        parent::__construct($tongs, $options);
    }

    /**
     * Handle files passed down the pipeline, and call the next plugin in the pipeline.
     *
     * @param Collection $files
     * @param callable $next
     *
     * @return Collection
     */
    public function handle(Collection $files, callable $next): Collection
    {
        $files = $files
            ->map(
                function ($file, $path) {
                    return $this->transform($file, $path);
                }
            );

        return $next($files);
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

    /**
     * Transform an individual file's metadata.
     *
     * @param array $file
     * @param string $path
     *
     * @return array
     */
    protected function transform(array $file, string $path): array
    {
        $extension = File::extension($path);

        if (
            $extension !== $this->options['ext']
            || !$file['contents']
        ) {
            return $file;
        }

        $parts = preg_split($this->options['pattern'], $file['contents']);

        if (is_array($parts) && count($parts) > 1) {
            $file[$this->options['key']] = $parts[0];
        }

        return $file;
    }
}

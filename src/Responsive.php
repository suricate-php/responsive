<?php

declare(strict_types=1);

namespace Suricate\Responsive;

use RuntimeException;
use Suricate\Service;

class Responsive extends Service
{
    protected $parametersList = [];

    protected $images = [];
    protected $sourceImg = '';

    const DEFAULT_MEDIA_QUERY = 'default';

    public function setSource(string $image): Responsive
    {
        $this->sourceImg = $image;

        return $this;
    }

    public function setAlt(string $alt): Responsive
    {
        return $this;
    }

    private function fetchSource()
    {
        $method =
            substr($this->sourceImg, 0, 4) === 'http'
                ? 'loadRemoteImage'
                : 'loadLocalImage';

        try {
            $this->$method();
        } catch (RuntimeException $e) {
            // FIXME: invalid image
        }
    }

    /**
     * Load image from local filesystem
     *
     * @return resource
     * @throws RuntimeException
     */
    private function loadLocalImage()
    {
        if (
            is_file($this->sourceImg) &&
            ($imgString = file_get_contents($this->sourceImg))
        ) {
            $imgResource = imagecreatefromstring($imgString);
            if ($imgString === false) {
                throw new RuntimeException(
                    "Source image " .
                        $this->sourceImg .
                        " is not a regular image"
                );
            }
            return $imgResource;
        }

        throw new RuntimeException(
            "Cannot load source image " . $this->sourceImg
        );
    }

    private function loadRemoteImage()
    {
        // FIXME: Load from curl
    }

    private function saveToCache()
    {
    }

    public function addSize(
        int $width,
        int $height,
        string $type = "jpeg",
        int $dpi = 1,
        string $mediaQuery = self::DEFAULT_MEDIA_QUERY
    ): Responsive {
        if (!isset($this->images[$type])) {
            $this->images[$type] = [];
        }
        if (!isset($this->images[$type][$mediaQuery])) {
            $this->images[$type][$mediaQuery] = [];
        }

        $this->images[$type][$mediaQuery][] = [
            'width' => $width,
            'height' => $height,
            'dpi' => $dpi
        ];

        return $this;
    }
    private function transformImages()
    {
        $result = [];
        foreach ($this->images as $type => $medias) {
            foreach ($medias as $mediaQuery => $images) {
                $src = '';
                foreach ($images as $image) {
                    $src .= $image['url'] . $image['dpi'];
                }
            }
        }
    }

    public function render(): string
    {
        // srcset="url1 1x, url2 2x" media="min-zzz max aaa" type="image/xxx"
        // srcset="url1 1x, url2 2x" media="min-zzz max aaa" type="image/yyy"
        $output = '<picture>';

        $output .= '</picture>';

        return $output;
    }
}

<?php
/**
 * TanaterosProject\Parser\Image
 */

namespace TanaterosProject\Parser;

use Symfony\Component\DomCrawler\Crawler;
use TanaterosProject\Core\AbstractParser as Core;

/**
 * Class Image
 * @package TanaterosProject\Parser
 */
class Image extends Core
{

    /**
     * @param string $path
     * @return string
     */
    function parse($path)
    {
        if (!file_exists($this->pathMails = $this->config['cacheDir'] . DIRECTORY_SEPARATOR . $this->siteHash . DIRECTORY_SEPARATOR . $path))
            mkdir($this->pathMails);
        foreach ($this->getLinks() as $file => $url) {
            $readStream = fopen($url, 'r');
            $writeStream = fopen($this->pathSiteHash . DIRECTORY_SEPARATOR . $file, 'w');
            stream_set_blocking($readStream, 0);
            stream_set_blocking($writeStream, 0);
            $read = new \React\Stream\Stream($readStream, $this->loop);
            $write = new \React\Stream\Stream($writeStream, $this->loop);
            $read->on('end', function () use ($file, &$files) {
                $path = $this->pathSiteHash . DIRECTORY_SEPARATOR . $file;
                $crawler = new Crawler();
                $crawler->add(file_get_contents($path));

                $arrLinks = $crawler
                    ->filter('img')
                    ->each(function (Crawler $nodeCrawler) {
                        return [
                            $nodeCrawler->filter('img')->attr('src'),
                        ];
                    });

                $validImage = [];
                foreach ($arrLinks as $k => $url) {
                    if (filter_var($url[0], FILTER_VALIDATE_URL)) {
                        $validImage[] = $url[0];
                    }
                }
                $images = [];
                foreach ($validImage as $m) {
                    array_push($images, $m);
                    file_put_contents($this->pathMails . DIRECTORY_SEPARATOR . basename($m), file_get_contents($m));
                }
                file_put_contents($this->pathMails . DIRECTORY_SEPARATOR . $file, implode(PHP_EOL, $images));

                unset($files[$file]);
            });
            $read->pipe($write);
        }

        // каждые $this->config['periodTime'] секунд выполнять какое-то действие
        $this->loop->addPeriodicTimer($this->config['periodTime'], function ($timer) use (&$files) {
            if (0 === count($files)) {
                $timer->cancel();
            }
            echo PHP_EOL . "Passed {$this->config['periodTime']} sec. " . PHP_EOL;
        });
        echo "This script will show the download status every {$this->config['periodTime']} seconds." . PHP_EOL;
        $this->loop->run();
        return 'Dir of result in: ' . $this->config['cacheDir'] . DIRECTORY_SEPARATOR . $this->siteHash . DIRECTORY_SEPARATOR . $path;
    }
}
<?php
/**
 * abstract TanaterosProject\Core\AbstractParser
 */

namespace TanaterosProject\Core;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AbstractParser
 * @package TanaterosProject\Core
 */
abstract class AbstractParser
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var \React\EventLoop\ExtEventLoop|\React\EventLoop\LibEventLoop|\React\EventLoop\LibEvLoop|\React\EventLoop\StreamSelectLoop
     */
    protected $loop;
    /**
     * @var string
     */
    protected $siteHash;
    /**
     * @var string
     */
    protected $pathSiteHash;
    /**
     * @var string
     */
    protected $site;

    /**
     * @param array $config
     * @param string $site
     */
    function __construct($config, $site)
    {
        $this->config = $config;
        $this->loop = \React\EventLoop\Factory::create();
        $this->site = $site;
        $this->siteHash = basename($this->site);

        if (!file_exists($this->config['cacheDir']))
            mkdir($this->config['cacheDir']);
        if (!file_exists($this->pathSiteHash = $this->config['cacheDir'] . DIRECTORY_SEPARATOR . $this->siteHash))
            mkdir($this->pathSiteHash);
    }

    /**
     * @return array
     */
    function getLinks()
    {
        $crawler = new Crawler();
        $crawler->add(file_get_contents($this->site));
        $arrLinks = $crawler
            ->filter('a')
            ->each(function (Crawler $nodeCrawler) {
                return [
                    $nodeCrawler->filter('a')->attr('href'),
                ];
            });
        $validLinks = [];
        $i = 0;
        foreach ($arrLinks as $k => $url) {
            $url[0] = str_replace('/redirect.php?url=', '', $url[0]);
            if (!filter_var($url[0], FILTER_VALIDATE_URL)) {
                if (@get_headers($url[0])[0] == 'HTTP/1.1 200 OK')
                    $validLinks[$i . '.' . $this->config['dataFormat']] = $url[0];
                else if (@get_headers($this->site . $url[0])[0] == 'HTTP/1.1 200 OK')
                    $validLinks[$i . '.' . $this->config['dataFormat']] = $this->site . $url[0];
                $i++;
            }
        }
        return $validLinks;
    }

    /**
     * @param $path
     * @return string
     */
    abstract function parse($path);
}
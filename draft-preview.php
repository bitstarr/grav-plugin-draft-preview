<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use Grav\Common\Page\Page;

/**
 * Class DraftPreviewPlugin
 * @package Grav\Plugin
 */
class DraftPreviewPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                // Uncomment following line when plugin requires Grav < 1.7
                // ['autoload', 100000],
                ['onPluginsInitialized', 0]
            ],
            'onTwigTemplatePaths' => [ 'onTwigTemplatePaths', 0 ],
            'onPagesInitialized'       => ['onPagesInitialized', 0],
        ];
    }

    /**
     * Composer autoload
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }
        /*
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminTwigTemplatePaths' => [
                    ['onAdminTwigTemplatePaths', 11]
                ],
                'onPagesInitialized' => ['onPagesInitialized', 0]
            ]);
        }
        */

        // Enable the main events we are interested in
        $this->enable([
            // Put your main events here
        ]);
    }

    //  Add twig paths to admin plugin templates.
    /*
    public function onTwigTemplatePaths($event) {
        $event['paths'] = array_merge($event['paths'], [__DIR__ . '/templates']);
        return $event;
    }
    */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * Programmatically add a custom page.
     *
     * @param $url
     * @param $filename
     * @param null $object
     * @throws \Exception
     */
    public function addPage($url, $filename)
    {
        /** @var Pages $pages */
        $pages = $this->grav['pages'];
        $page = $pages->dispatch($url);

        if (!$page) {
            $page = new Page;
            $page->init(new \SplFileInfo(__DIR__ . '/pages/' . $filename));
            $page->slug(basename($url));
            $page->id($page->modified() . md5($url));
            $page->folder(basename($url));
            $page->route($url);
            $page->rawRoute($url);
            $pages->addPage($page, $url);
        }
    }

    /**
     * [onPagesInitialized]
     *
     * @return void
     */
    public function onPagesInitialized(): void
    {
        /** @var Uri $uri */
        $uri = $this->grav['uri'];
        $route = Uri::getCurrentRoute()->getRoute();

        if ($route === '/preview') {
            $this->addPage($route, 'preview.md');
        }
    }

}

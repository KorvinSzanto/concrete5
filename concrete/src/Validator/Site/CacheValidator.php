<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Page;
use URL;

class CacheValidator extends SiteValidator implements DocumentedValidatorInterface, HelpInterface, EnvironmentSpecificInterface
{

    const E_DISABLED = 1;
    const E_SHORT_LIFETIME = 2;
    const E_BLOCKS = 3;
    const E_THEME = 4;
    const E_ASSETS = 5;
    const E_OVERRIDES = 6;

    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Is this mixed value valid.
     *
     * @param \Concrete\Core\Entity\Site\Site $site
     * @param \ArrayAccess|null $error
     *
     * @return bool
     *
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($site, \ArrayAccess $error = null)
    {
        $valid = true;

        if (!$this->config->get('concrete.cache.enabled')) {
            $this->addRecommended(self::E_DISABLED, 'Cache is not enabled', $error);
            $valid = false;
        }

        if ($this->config->get('concrete.cache.lifetime') < 21600) {
            $this->addRecommended(self::E_SHORT_LIFETIME, 'Cache lifetime is very short', $error);
            $valid = false;
        }

        if (!$this->config->get('concrete.cache.blocks')) {
            $this->addRecommended(self::E_BLOCKS, 'Block caching is disabled', $error);
            $valid = false;
        }

        if (!$this->config->get('concrete.cache.theme_css')) {
            $this->addRecommended(self::E_THEME, 'Theme CSS caching is disabled', $error);
            $valid = false;
        }

        if (!$this->config->get('concrete.cache.assets')) {
            $this->addRecommended(self::E_ASSETS, 'JS and CSS caching is disabled', $error);
            $valid = false;
        }

        if (!$this->config->get('concrete.cache.overrides')) {
            $this->addRecommended(self::E_OVERRIDES, 'Overrides caching is disabled', $error);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment()
    {
        return EnvironmentSpecificInterface::PRODUCTION;
    }

    /**
     * @inheritdoc
     */
    public function linkForError($code)
    {
        return 'https://documentation.concrete5.org/editors/dashboard/system-and-maintenance/jobs/cache-and-speed-settings';
    }

    /**
     * @inheritdoc
     */
    public function getHelpText($code)
    {
        $p = Page::getByPath('/dashboard/system/optimization/cache');
        $path = URL::to($p->getCollectionPath());
        $title = h($p->getCollectionName());
        $url = sprintf('<a href="%s">%s</a>', $path, $title);
        switch ($code) {
            case self::E_DISABLED || self::E_BLOCKS || self::E_THEME || self::E_ASSETS || self::E_OVERRIDES:
                $html = t('The cache is disabled which can cause performance issues on a site. To enable it, visit %s.',
                    $url);
                break;
            case self::E_SHORT_LIFETIME:
                $html = t('The lifetime for the cache is short which reduces the effectiveness of the cache. Visit %s to increase the cache lifetime.',
                    $url);
                break;
            default:
                $html = '';
        }

        return $html;
    }
}

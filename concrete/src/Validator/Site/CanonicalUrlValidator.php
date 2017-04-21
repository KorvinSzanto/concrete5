<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Page;
use URL;

class CanonicalUrlValidator extends SiteValidator implements DocumentedValidatorInterface
{

    const E_NOT_A_SITE = 1;
    const E_NOT_SET = 2;
    const E_NOT_ENFORCED = 3;

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
        if (!$site instanceof Site) {
            $this->addMustFix(
                self::E_NOT_A_SITE,
                'Invalid argument, mixed value must be a Site object.',
                $error
            );
            $valid = false;
        }

        if (!$url = $site->getSiteCanonicalURL()) {
            $this->addMustFix(self::E_NOT_SET, 'Canonical URL is not set', $error);
            $valid = false;
        }

        if (!$this->config->get('concrete.seo.redirect_to_canonical_url')) {
            $this->addRecommended(self::E_NOT_ENFORCED, 'Redirecting to Canonical URL is not enabled', $error);
            $valid = false;
        }

        return $valid;
    }

    /**
     * Takes an error code matching possible errors returned from this validator
     *
     * @param int $code
     * @return string|null The url link to the documentation for resolving issues with this validator
     */
    public function linkForError($code)
    {
        if ($code === self::E_NOT_SET) {
            return 'https://documentation.concrete5.org/editors/dashboard/system-and-maintenance/seo-and-statistics/pretty-urls#canonical-url-warning';
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getHelpText($code)
    {
        $p = Page::getByPath('/dashboard/system/seo/urls');
        $path = URL::to($p->getCollectionPath());
        $title = h($p->getCollectionName());
        switch ($code) {
            case self::E_NOT_SET:
                $html = t(/*i18n: %s is a link to a page*/'To set the Canonical URL visit %s.', sprintf('<a href="%s">%s</a>', $path, $title));
                break;
            case self::E_NOT_ENFORCED:
                $html = t(/*i18n: %s is a link to a page*/'To enable Canonical URL redirection visit %s.', sprintf('<a href="%s">%s</a>', $path, $title));
                break;
            default:
                $html = '';
        }

        return $html;
    }
}

<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;

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
            $this->addRecommended(self::E_NOT_ENFORCED, 'Canonical URL should be required', $error);
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
}

<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;

class SessionIpMismatchValidator extends SiteValidator implements DocumentedValidatorInterface
{
    const E_NOT_ENFORCED = 1;

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

        if (!$this->config->get('concrete.security.session.invalidate_on_ip_mismatch')) {
            $this->addRecommended(self::E_NOT_ENFORCED,
                'Invalidating sessions on IP mismatch should be enabled.', $error);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function getHelpText($code)
    {
        $html = t(/*i18n: %s is a configuration key and value*/'The default value for the configuration value %s has been changed to %s.',
            '<code>concrete.security.session.invalidate_on_ip_mismatch</code>', '<code>false</code>');
        $html .= ' ' . t('This can make it easier for attackers to hijack sessions');
        return $html;
    }

    public function linkForError($code)
    {
        return 'https://documentation.concrete5.org/developers/configuration-and-keyvalue-storage/manual-configuration';
    }
}

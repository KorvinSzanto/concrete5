<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;

class XFrameOptionsValidator extends SiteValidator implements HelpInterface, DocumentedValidatorInterface
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

        if ($this->config->get('concrete.security.misc.x_frame_options') != 'SAMEORIGIN') {
            $this->addRecommended(self::E_NOT_ENFORCED,
                'X Frame Options header is not set to the default value.', $error);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function getHelpText($code)
    {
        $html = t('The default value for the configuration value 
        <code>concrete.security.misc.x_frame_options</code> has been changed to <code>%s</code>. 
        This can make it easier for attackers to perform clickjacking attacks.',
            h($this->config->get('concrete.security.misc.x_frame_options')));
        return $html;
    }

    /**
     * @inheritdoc
     */
    public function linkForError($code)
    {
        return 'https://documentation.concrete5.org/developers/configuration-and-keyvalue-storage/manual-configuration';
    }
}

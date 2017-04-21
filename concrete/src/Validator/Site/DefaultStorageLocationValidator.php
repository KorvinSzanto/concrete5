<?php

namespace Concrete\Core\Validator\Site;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Page;
use URL;

class DefaultStorageLocationValidator extends SiteValidator implements DocumentedValidatorInterface
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
        $slr = \Core::make(StorageLocationFactory::class);
        $fsl_id = $slr->fetchDefault()->getID();

        if ($fsl_id == 1) { // The default storage location always has the ID of 1, but the name can change
            $this->addRecommended(self::E_NOT_ENFORCED,
                'Default storage location is a public directory', $error);
            $valid = false;
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function getHelpText($code)
    {
        $p = Page::getByPath('/dashboard/system/files/storage');
        $path = URL::to($p->getCollectionPath());
        $title = h($p->getCollectionName());
        $lines = [];
        $lines[] = t('The default storage location stores files in a publicly available location.');
        $lines[] = t('It is suggested that the storage location be not in a publicly accessible directory to prevent access to sensitive files.');
        $lines[] = t(/*i18n: %s is a link to a page*/'You can create a new storage location from the %s page.',
            sprintf('<a href="%s">%s</a>', $path, $title));
        return join(' ', $lines);
    }

    /**
     * @inheritdoc
     */
    public function linkForError($code)
    {
        return 'https://documentation.concrete5.org/editors/dashboard/system-and-maintenance/files/file-storage-locations';
    }
}

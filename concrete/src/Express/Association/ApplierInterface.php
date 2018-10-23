<?php

namespace Concrete\Core\Express\Association;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\Entry;

interface ApplierInterface
{

    /**
     * Associate an entry with the given input
     *
     * This method creates the associations and flushes them into the database
     *
     * @param \Concrete\Core\Entity\Express\Association $association
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @param Entry|Entry[] $input
     *
     * @return void
     */
    public function associate(Association $association, Entry $entry, $input);
}

<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Page;
use SinglePage;

class Version20170418000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $sp = Page::getByPath('/dashboard/reports/security');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/reports/security');
            $sp->update(['cName' => 'Security & Settings Audit']);
            $sp->setAttribute('meta_keywords', 'security, audit, secure');
        }
    }

    public function down(Schema $schema)
    {
    }
}

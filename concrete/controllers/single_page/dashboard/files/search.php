<?php

namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Controller\Element\Search\Files\Header;
use Concrete\Controller\Search\FileFolder;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\User\User;
use Concrete\Core\File\FileIterator;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;

class Search extends DashboardPageController
{

    protected function getFileIterator()
    {
        $files = $this->app->make(FileIterator::class)
            // Include the data we want
            ->includeAttributes()->includeSets()->includeTree()->includeUser()->includeVersion()
            ->sortByVersionColumn('fvSize', 'desc')
            ->slice(100)
            // Add a wrapper that (crudely) inflates from array to file entity
            ->addWrapper(function ($iterator) {
                return $this->inflate($iterator);
            })
            // Filter out files we can't view
            ->addWrapper(function ($iterator) {
                return $this->checkPermissions($iterator);
            })
            // Convert DTA to a tree node
            ->addWrapper(function ($iterator) {
                return $this->treeNodes($iterator);
            })
            // Only output 10 items
            ->limit(10);

        return $files;
    }

    private function checkPermissions(\Iterator $files)
    {
        // No need to check permissions for the super user.
        $user = $this->app->make(\Concrete\Core\User\User::class);
        if ($user->getUserID() == USER_SUPER_ID) {
            foreach ($files as $data) {
                yield $data;
            }
            return;
        }

        // Otherwise check permissions
        foreach ($files as $data) {
            $checker = new Checker($data[0]);
            if ($checker->canView()) {
                yield $data;
            }
        }
    }

    private function inflate(\Iterator $files)
    {
        foreach ($files as $file) {
            $user = new User();
            $user->setUserID($file['uID']);
            $user->setUserName($file['uName']);

            $fileEntity = new File();
            $fileEntity->author = $user;
            $fileEntity->fID = $file['fID'];
            $fileEntity->fDateAdded = new \DateTime($file['fDateAdded']);
            $fileEntity->fPassword = $file['fPassword'];
            $fileEntity->fOverrideSetPermissions = $file['fOverrideSetPermissions'];
            $fileEntity->ocID = $file['ocID'];
            $fileEntity->folderTreeNodeID = $file['folderTreeNodeID'];

            yield [$fileEntity, $file];
        }
    }

    private function treeNodes(\Iterator $iterator)
    {
        /** @var File $file */
        foreach ($iterator as $file) {
            if ($node = Node::getByID($file[1]['treeNodeID'])) {
                yield $node;
            }
        }
    }

    public function view()
    {
        // Add Assets
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/imageeditor');

        // Prepare
        $iterator = $this->getFileIterator();
        $this->set('files', $iterator);

        // Set items
        $header = new Header();
        $this->set('headerMenu', $header);

        // Add a footer item like we used to have but with new data
        $this->addFooterItem(
            "<script type=\"text/javascript\">$(function() { $('#ccm-dashboard-content').concreteFileManager({upload_token: '" . '[' . "', result: " . $this->getJSON($iterator) . "}); });</script>"
        );
    }

    /**
     * Get a json string that is similar to what we used to have
     * @param $results
     * @return string
     */
    private function getJSON($results)
    {
        $json = [];
        foreach ($results as $result) {
            $item = $result->getTreeNodeJSON();
            $item->columns = [];
            $json[] = $item;
        }

        $obj = new \stdClass();
        $obj->items = $json;
        $obj->paginationTemplate = '';
        $obj->bulkMenus = [];
        $obj->baseUrl = 'https://c5.dev/';
        $obj->breadcrumb = [];
        $obj->columns = [];

        return json_encode($obj);
    }
}

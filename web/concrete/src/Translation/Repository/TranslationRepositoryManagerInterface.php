<?php

namespace Concrete\Core\Translation\Repository;

interface TranslationRepositoryManagerInterface
{

    /**
     * Get a repository by its handle
     *
     * @param $handle
     * @return mixed
     */
    public function getRepository($handle);

    /**
     * Add a repository to the manager
     *
     * @param string               $handle     The repository's handle
     * @param RepositoryInterface  $repository The repository
     * @return void
     */
    public function addRepository($handle, RepositoryInterface $repository);

    /**
     * Get all repositories in no specific order keyed by their handles
     *
     * @return TranslationRepositoryInterface[] [$handle => $repository, ...]
     */
    public function getRepositories();

}

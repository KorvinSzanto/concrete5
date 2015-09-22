<?php
use Concrete\Core\Translation\Repository\TranslationRepositoryManagerInterface;
?>
(function(c5, global) {

    var repositories = {};
    var translations = {};

    <?php
    /** @type TranslationRepositoryManagerInterface $manager */
    foreach ($manager->getRepositories() as $handle => $repository) {
        $handle_string = json_encode($handle);
        ?>

        repositories[<?= $handle_string ?>] = [];
        <?php
        foreach ($repository->getStrings() as $array) {
            list($string, $context) = $array;
            ?>
            repositories[<?= $handle_string ?>][<?= json_encode($string) ?>] = <?= json_encode(t($string, $context)) ?>;
            <?php
        }
    }
    ?>

    c5.translation.repositories = repositories;
    c5.translation.strings = translations;

    c5.translate = function(string) {
        return c5.translation.strings[string];
    };

    // Legacy adapters
    global.SomeLegacyGlobalNamespace = c5.translation.repositories['some_new_repository'];
    // ...

}(Concrete, this));

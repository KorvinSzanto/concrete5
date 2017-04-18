<?php
/**
 * @var \Concrete\Core\Validator\Site\SiteError[][] $result
 */
use Concrete\Core\Validator\ErrorLevel;

if (!count($result)) {
    ?>
     <div class="col-sm-12">
        <div class="alert alert-success text-center">
            <?= t('No issues found!') ?>
        </div>
     </div>
    <?php
} else {

    foreach ($result as $validatorName => $resultSet) {
        if (!count($resultSet)) {
            continue;
        }
        $severities = array_map(function (\Concrete\Core\Validator\Site\SiteError $error) {
            return $error->getSeverity();
        }, $resultSet);

        $highestSeverity = array_reduce($severities, function ($carry, $severity) {
            if ($severity === ErrorLevel::MUSTFIX) {
                return $severity;
            }

            if ($severity === ErrorLevel::RECOMMENDATION && $carry !== ErrorLevel::MUSTFIX) {
                return $severity;
            }

            return $carry;
        }, null);

        $color = $success;
        if ($highestSeverity === ErrorLevel::RECOMMENDATION) {
            $color = 'warning';
        } elseif ($highestSeverity) {
            $color = 'danger';
        }
        ?>
        <div class="col-sm-6">
            <div class="panel panel-<?= $color ?>">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= h($validatorName) ?></h3>
                </div>
                <ul class="ccm-error-set list-group">
                    <?php
                    foreach ($resultSet as $error) {
                        /**
                         * @var $error \Concrete\Core\Validator\Site\SiteError
                         */
                        $severity = $error->getSeverity();
                        $validator = $error->getValidator();
                        $color = $severity === ErrorLevel::RECOMMENDATION ? 'warning' : 'danger';
                        ?>
                        <li class="list-group-item alert alert-<?= $color ?>">
                            <?php
                            echo '<p>';
                            echo $error->getMessage();
                            if ($validator instanceof \Concrete\Core\Validator\Site\DocumentedValidatorInterface) {
                                if ($link = $validator->linkForError($error->getCode())) {
                                    ?>
                                    <a href="<?= $link ?>" target="_blank">
                                        <i class="fa fa-external-link"></i>
                                    </a>
                                    <?php
                                }
                            }
                            echo '</p>';
                            if ($validator instanceof \Concrete\Core\Validator\Site\HelpInterface) {
                                echo $validator->getHelpText($error->getCode());
                            }
                            ?>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
}
?>
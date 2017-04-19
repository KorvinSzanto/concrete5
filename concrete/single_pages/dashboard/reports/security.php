<?php
/**
 * @var \Concrete\Core\Validator\Site\SiteError[][] $result
 */
use Concrete\Core\Validator\ErrorLevel;


foreach ($result as $validatorName => $resultSet) {

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
    $icon = 'times';
    if (!count($resultSet)) {
        $color = 'success';
        $icon = 'check';
    } elseif ($highestSeverity === ErrorLevel::RECOMMENDATION) {
        $color = 'warning';
    } elseif ($highestSeverity) {
        $color = 'danger';
    }
    ?>
    <div class="col-sm-6">
        <div class="panel panel-<?= $color ?>">
            <div class="panel-heading">
                <h3 class="panel-title"><span class="fa fa-<?=$icon?>" aria-hidden="true"></span> <?= h($validatorName) ?></h3>
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
                        if ($validator instanceof \Concrete\Core\Validator\Site\DocumentedValidatorInterface) {
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
?>
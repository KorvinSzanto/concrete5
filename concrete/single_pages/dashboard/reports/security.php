<?php
/**
 * @var \Concrete\Core\Validator\Site\SiteError[][] $result
 */
use Concrete\Core\Validator\ErrorLevel;

foreach ($result as $validatorName => $resultSet) {
    $severities = array_map(function(\Concrete\Core\Validator\Site\SiteError $error) {
        return $error->getSeverity();
    }, $resultSet);

    $highestSeverity = array_reduce($severities, function($carry, $severity) {
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
    <div class="col-sm-4">
        <div class="panel panel-<?= $color ?>">
            <div class="panel-heading">
                <h3 class="panel-title"><?= h($validatorName) ?></h3>
            </div>
            <ul class="ccm-error-set list-group">
                <?php
                foreach ($resultSet as $error) {
                    $severity = $error->getSeverity();
                    $validator = $error->getValidator();
                    $color = $severity === ErrorLevel::RECOMMENDATION ? 'warning' : 'danger';
                    ?>
                    <li class="list-group-item alert alert-<?= $color ?>">
                        <?php
                        echo $error->getMessage();
                        if ($validator instanceof \Concrete\Core\Validator\Site\DocumentedValidatorInterface) {
                            if ($link = $validator->linkForError($error->getCode())) {
                                ?>
                                <a href="<?= $link ?>">
                                    <i class="fa fa-external-link pull-right"></i>
                                </a>
                                <?php
                            }
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

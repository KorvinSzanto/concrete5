<?php
use Concrete\Core\Device\Device;
?>

<section id="ccm-panel-page-devices" class="ccm-ui">

    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <span class="fa fa-chevron-left"></span>
        </a>
        <a href="" data-panel-navigation="back"><?= t('Devices')?></a>
    </header>

    <?php
    $preview_url = \URL::to('/ccm/system/panels/page/devices/preview');
    $preview_url = $preview_url->setQuery(array('cID' => \Page::getCurrentPage()->getCollectionID()));

    $manager = \Core::make('device/manager');

    $organized = array();

    $devices = $manager->getList();

    foreach ($devices as $device) {
        $type = $device->getType();

        if ($type == Device::UNKNOWN) {
            $organized[Device::UNKNOWN][] = $device;
        } else if ($type & Device::MOBILE) {
            $organized[Device::MOBILE][] = $device;
        } else if ($type & Device::TABLET) {
            $organized[Device::TABLET][] = $device;
        } else if ($type & Device::DESKTOP) {
            $organized[Device::DESKTOP][] = $device;
        }
    }

    $categories = array(
        Device::UNKNOWN => t('General'),
        Device::MOBILE => t('Phone'),
        Device::TABLET => t('Tablet'),
        Device::DESKTOP => t('Desktop'));

    /**
     * @var int $type
     * @var Device[] $device_list
     */
    foreach ($organized as $type => $device_list) {
        ?>

        <section class="ccm-panel-devicelist" style="color:white">
            <h3><?= $categories[$type] ?></h3>
            <menu>
                <?php
                $page = \Page::getCurrentPage();
                foreach ($device_list as $device) {
                    $device_preview_url = $preview_url->setQuery(array(
                        'cID' => $page->getCollectionID(),
                        'cvID' => $page->getVersionID(),
                        'device' => $device->getHandle()));
                    ?>
                    <li class="ccm-panel-devicelist-device"
                        data-device-brand="<?= h($device->getBrand()) ?>"
                        data-device-name="<?= h($device->getName()) ?>"
                        data-device-handle="<?= h($device->getHandle()) ?>"
                        data-device-width="<?= h($device->getWidth()) ?>"
                        data-device-height="<?= h($device->getHeight()) ?>"
                        data-device-agent="<?= h($device->getUserAgent()) ?>"
                        data-device-ratio="<?= h($device->getPixelRatio()) ?>"
                        data-device-type="<?= $type ?>"
                        data-device-preview-url="<?= $device_preview_url ?>"
                        data-device-orientation="<?= h($device->getDefaultOrientation()) ?>">
                        <script type="text/html" class="viewport"><?= $device->getViewportHTML() ?></script>

                        <a href="#" data-launch-panel-detail="page-device-preview" data-panel-transition="fade">
                            <span class="ccm-panel-device-name">
                                <?= h($device->getName()) ?>
                            </span>
                            <span class="ccm-panel-device-resolution">
                                <?= h($device->getWidth()) ?> x <?= h($device->getHeight()) ?>
                            </span>
                        </a>
                    </li>
                <?php
                }
                ?>
            </menu>
        </section>
        <?php
    }
    ?>
</section>
<script>
    $(function() {
        Concrete.event.unbind('PanelOpenDetail.device_preview');
        Concrete.event.bind('PanelOpenDetail.device_preview', function (e, data) {
            if (data.panel && data.container && data.panel.target && data.panel.identifier) {
                var panel = data.panel,
                    detail_container = data.container,
                    container = $('<div/>').appendTo(detail_container),
                    device = panel.target.closest('.ccm-panel-devicelist-device'),
                    identifier = panel.identifier;

                if (identifier == "page-device-preview") {
                    var device_template = device.children('.viewport').html(),
                        url = device.data('device-preview-url'),
                        handle = device.data('device-handle'),
                        width = device.data('device-width'),
                        height = device.data('device-height'),
                        ratio = device.data('device-ratio'),
                        orientation = device.data('device-orientation'),
                        viewport = $(_(device_template).template({device: device}));

                    viewport.find('.ccm-display-frame').css({
                        width: (orientation == 'landscape' ? width : height) / ratio,
                        height: (orientation == 'landscape' ? height : width) / ratio
                    }).attr('src', url);

                    container.empty().addClass('ccm-device-preview').addClass('ccm-device-orientation-' + orientation).append(viewport);
                }

            }

        });
    });
</script>
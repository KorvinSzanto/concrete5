<?php
namespace Concrete\Package\TestMetadatadriverXml;

use Concrete\Core\Database\EntityManager\Provider\XmlProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Controller test addon - testing metadatadriver with legacy annotation driver
 *
 * @author markus.liechti
 */
class Controller extends Package implements ProviderInterface{

    protected $pkgHandle = 'test_metadatadriver_xml';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    public function getPackageDescription() {
        return t("Test addon registers entities via the xml driver");
    }

    public function getPackageName(){
        return t("Test addon - uses xml driver");
    }
    
    /**
     * Return customized metadata driver wrapped in a XMLProvider for doctrine orm
     * Path: {package}/config/xml
     * 
     * @return XmlProvider
     */
    public function getDrivers(){
        $relPath = $this->getRelativePathFromInstallFolder() . REL_DIR_METADATA_XML;
        return new XmlProvider($relPath);
    }

}
<?php

namespace Concrete\Package\VatcodeCodicefiscale;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The VatcodeCodicefiscale package controller.
 */
class Controller extends Package
{
    /**
     * The package handle.
     *
     * @var string
     */
    protected $pkgHandle = 'vatcode_codicefiscale';

    /**
     * The package version.
     *
     * @var string
     */
    protected $pkgVersion = '0.9.2';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$appVersionRequired
     */
    protected $appVersionRequired = '8.2.0';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$pkgAutoloaderRegistries
     */
    protected $pkgAutoloaderRegistries = [
        'src' => 'VatcodeCodicefiscale',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageName()
     */
    public function getPackageName()
    {
        return t('VAT code or Codice Fiscale attribute');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageDescription()
     */
    public function getPackageDescription()
    {
        return t('This package adds a new attribute type to represent Italian VAT Codes and Codice Fiscale.');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::install()
     */
    public function install()
    {
        parent::install();
        $this->installContentFile('config/install.xml');
    }

    public function on_start()
    {
        if (!$this->app->isRunThroughCommandLineInterface()) {
            $this->registerAssets();
        }
    }

    /**
     * Register the assets.
     */
    private function registerAssets()
    {
        $al = AssetList::getInstance();
        $al->registerMultiple([
            'vatcode_codicefiscale/jqplugin' => [
                ['javascript', 'js/vatcode_codicefiscale.jquery.js', ['minify' => true, 'combine' => true, 'version' => $this->pkgVersion], $this],
            ],
        ]);
        $al->registerGroupMultiple([
            'vatcode_codicefiscale/jqplugin' => [
                [
                    ['javascript', 'jquery'],
                    ['javascript', 'vatcode_codicefiscale/jqplugin'],
                ],
            ],
        ]);
    }
}

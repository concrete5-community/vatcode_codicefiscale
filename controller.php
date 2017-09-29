<?php
namespace Concrete\Package\VatcodeCodicefiscale;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Package\Package;

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
    protected $pkgVersion = '1.0.0';

    /**
     * The minimum concrete5 version.
     *
     * @var string
     */
    protected $appVersionRequired = '8.2.0';

    /**
     * {@inheritdoc}
     */
    public function getPackageName()
    {
        return t('VAT code or Codice Fiscale attribute');
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageDescription()
    {
        return t('This package adds a new attribute type to represent Italian VAT Codes and Codice Fiscale.');
    }

    /**
     * {@inheritdoc}
     */
    public function testForInstall($testForAlreadyInstalled = true)
    {
        $result = parent::testForInstall($testForAlreadyInstalled);
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $package = parent::install();
        $typeFactory = $this->app->make(TypeFactory::class);
        /* @var TypeFactory $typeFactory */
        $type = $typeFactory->getByHandle('vatcode_codicefiscale');
        if ($type === null) {
            $type = $typeFactory->add('vatcode_codicefiscale', tc('AttributeKeyName', 'VAT Code or Codice Fiscale'), $package);
            $categoryService = $this->app->make(CategoryService::class);
            /* @var CategoryService $categoryService */
            $category = $categoryService->getByHandle('user');
            if ($category !== null) {
                $category->getController()->associateAttributeKeyType($type);
            }
        }
    }
}

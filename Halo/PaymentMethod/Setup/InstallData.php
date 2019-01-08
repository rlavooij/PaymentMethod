<?php
/**
 * Copyright (c) 2019. 
 * Copyright Holder : Halo - Ruben Lavooij
 * Copyright : Unless granted permission from Halo you can not distrubute , reuse  , edit , resell or sell this.
 */
namespace Halo\PaymentMethod\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Framework\DB\Ddl\Table;


class InstallData implements InstallDataInterface
{
	/**
	 * Customer setup factory
	 *
	 * @var \Magento\Customer\Setup\CustomerSetupFactory
	 */
	private $customerSetupFactory;
	protected $quoteSetupFactory;
	/**
	 * Init
	 *
	 * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
	 */
	public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
	\Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
	)
	{
		$this->customerSetupFactory = $customerSetupFactory;
		$this->quoteSetupFactory = $quoteSetupFactory;
	}
	/**
	 * Installs DB schema for a module
	 *
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{

		$installer = $setup;
		$installer->startSetup();

		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
		$entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);
		$customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "hide_po");

		$customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "hide_po",  array(
			'type' => 'int',
			'label' => 'Can a Trade Customer order with Purchase order?',
			'input' => 'select',
			'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
			"visible"  => true,
			"required" => true,
			"default" => false,
			"frontend" => "",
			"unique"     => false,
			"note"       => ""

		));

		$mobile   = $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "hide_po");

		$mobile = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'hide_po');
		$used_in_forms[]="adminhtml_customer";
		// $used_in_forms[]="checkout_register";
		// $used_in_forms[]="customer_account_create";
		// $used_in_forms[]="customer_account_edit";
		// $used_in_forms[]="adminhtml_checkout";
		$mobile->setData("used_in_forms", $used_in_forms)
			// ->setData("is_used_for_customer_segment", true)
			->setData("is_system", 0)
			->setData("is_user_defined", 0)
			->setData("is_visible", 1)
			->setData("admin_only", true)
			->setData("sort_order", 0);

		$mobile->save();

		$installer->endSetup();
	}
}
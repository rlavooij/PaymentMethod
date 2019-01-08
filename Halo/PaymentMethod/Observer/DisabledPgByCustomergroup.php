<?php
/**
 * Copyright (c) 2019. 
 * Copyright Holder : Halo - Ruben Lavooij
 * Copyright : Unless granted permission from Halo you can not distrubute , reuse  , edit , resell or sell this.
 */
namespace Halo\PaymentMethod\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

class DisabledPgByCustomergroup implements ObserverInterface
{
   
    protected $_customerSession;

    public function __construct(
        
        \Psr\Log\LoggerInterface $logger,
        Session $customerSession
    )
    {
        $this->_logger = $logger;
        $this->_customerSession = $customerSession;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        $quote           = $observer->getEvent()->getQuote();
        $customer = $this->_customerSession->getCustomer();
        $hide_po = $customer->getHidePo();

        // <--
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        // -->

        $this->_logger->info($method_instance->getCode());
        if (null !== $quote && $quote->getCustomerGroupId() == 4) {
            if ($method_instance->getCode() == 'purchaseorder') {
                if ($hide_po == 1){
                    $result->setData('is_available', true);
                    $logger->info(print_r("enabled gateway",true));
                }else{
                    $result->setData('is_available', false);
                    $logger->info(print_r("disabled gateway, hide_po not 1",true));
                }
            }
        }
        else{
            if($method_instance->getCode() =='purchaseorder'){
                $result->setData('is_available', false);
                $logger->info(print_r("disabled gateway",true));
                
            }    
        }
    }
}


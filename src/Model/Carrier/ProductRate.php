<?php
/**
 * WebShopApps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * WebShopApps ProductRate
 *
 * @category WebShopApps
 * @package WebShopApps_ProductRate
 * @copyright Copyright (c) 2016 Zowta LLC (http://www.WebShopApps.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author WebShopApps Team sales@webshopapps.com
 *
 */
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WebShopApps\ProductRate\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Model\Product\Type as Type;

class ProductRate extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'productrate';

    /**
     * @var bool
     */
    protected $_isFixed = false;
    
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_resultMethodFactory;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $freeQty = $this->applyFreeShipping($request);
        
        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        
        $productShippingPrice = $this->calculateShippingPrice($request);
        
        // set back to old values
        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);
        
        if (!empty($productShippingPrice) && $productShippingPrice >= 0) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_resultMethodFactory->create();

            $method->setCarrier('productrate');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('productrate');

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($productShippingPrice);
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }

    /**
     * ProductRate Rates Collector
     *
     * @param RateRequest $request
     * @return String $price
     */
    private function calculateShippingPrice(RateRequest $request)
    {
        
        $price = 0;

        $items = $request->getAllItems();

        $sumOfTotals = $this->getConfigData('totalling_algorithm') == 'S' ? true : false;

        if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
            $price = 0;
        } else {
            $priceFound=false;
            $bundledIdUsed = $this->getConfigFlag('bundle_id');

            $configurableQty = 0;
            $ignoreQty=0;
            foreach ($items as $item) {
                if ($item->getFreeShipping() && !$item->getProduct()->isVirtual()) {
                    $priceFound=true;
                    continue;
                }

                $currentQty = $item->getQty();
                if ($item->getProductType() == ConfigurableType::TYPE_CODE) {
                    $configurableQty = $currentQty;
                    continue;
                } elseif ($configurableQty > 0) {
                    $currentQty = $configurableQty;
                    $configurableQty = 0;
                }

                if ($item->getParentItem()!=null && $bundledIdUsed) {
                    $product = $item->getParentItem()->getProduct();
                } else {
                    $product = $item->getProduct();
                }

                $shippingPrice = $product->getData('shipperhq_shipping_fee');
                $addOnPrice = $product->getData('shipperhq_addon_price');
                $isPercentageAddOn = $product->getData('shipperhq_inc_percent');

                $parentQty = 1;
                if ($item->getParentItem()!=null) {
                    if ($item->getParentItem()->getProductType() == Type::TYPE_BUNDLE) {
                        $parentQty = $item->getParentItem()->getQty();
                    }
                }

                if ($sumOfTotals) {
                    if ($shippingPrice) {
                        $priceFound=true;
                        $price += $shippingPrice;
                    }

                    $qty=($currentQty * $parentQty) -1;
                    if ($shippingPrice==0) {
                        $ignoreQty+=$qty+1;
                    }
                    $shippingAddOn = $addOnPrice;
                    $shippingPercent =  $isPercentageAddOn;
                    $shippingPrice =  $shippingPrice;

                    if ($qty!=0) {
                        if ($shippingPercent) {
                            if ($shippingAddOn!='') {
                                $price+=($shippingPrice*($shippingAddOn/100))*$qty;
                            } else {
                                $price+=$shippingPrice * $qty;
                            }
                        } else {
                            if ($shippingAddOn!='') {
                                $price+=$shippingAddOn * $qty;
                            } else {
                                $price+=$shippingPrice * $qty;
                            }
                        }
                    }
                } else {
                    if ($price < $shippingPrice) {
                        $price = $shippingPrice;
                    }
                }
            }

            $max_shipping_cost=$this->getConfigData('max_shipping_cost');
            if (!empty($max_shipping_cost) && $max_shipping_cost!='' && $max_shipping_cost>0) {
                if ($price>$max_shipping_cost) {
                    $price=$max_shipping_cost;
                }
            }

            if ($price==0 && !$priceFound) {
                if ($this->getConfigData('default_shipping_cost')!='') {
                    $price=$this->getConfigData('default_shipping_cost');
                } else {
                    // we can't get a shipping price
                    return null;
                }
            }
        }

        return $price;
    }

    /**
     * Apply the free shipping & virtual logic to get correct weights/values
     * @param RateRequest $request
     * @return int
     */
    protected function applyFreeShipping(RateRequest $request)
    {
        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
            return $freeQty;
        }
        return $freeQty;
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'totalling' => [
                'S' => __('Sum of Totals'),
                'H' => __('Highest Price'),
            ]
        ];

        if (!isset($codes[$type])) {
            throw new LocalizedException(__('Please correct ProductRate code type: %1.', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new LocalizedException(__('Please correct ProductRate code for type %1: %2.', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['productrate' => $this->getConfigData('name')];
    }
}

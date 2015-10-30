<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Html_Page extends Mage_Core_Helper_Abstract
{
    public function getSocialPageInfo($head)
    {
        $html  = '';

        if (Mage::helper('mageworx_seomarkup/config')->isWebsiteOpenGraphEnabled()){
            $type  = Mage::helper('mageworx_seomarkup')->isHomePage() ? 'website' : 'article';
        } else {
            $type = 'article';
        }
        
        $title = $head->getMetaTitle() ? htmlspecialchars($head->getMetaTitle()) : htmlspecialchars($head->getTitle());
        $description = htmlspecialchars($head->getDescription());
        $siteName = Mage::helper('mageworx_seomarkup/config')->getWebSiteName();

        list($urlRaw) = explode('?', Mage::helper('core/url')->getCurrentUrl());
        $url = rtrim($urlRaw, '/');

        if (Mage::helper('mageworx_seomarkup/config')->isPageOpenGraphEnabled()) {
            $html .= "\n<meta property=\"og:type\" content=\"" . $type . "\"/>\n";
            $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
            $html .= "<meta property=\"og:description\" content=\"" . $description . "\"/>\n";
            $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";
            if ($siteName) {
                $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
            }
        }

        if (Mage::helper('mageworx_seomarkup/config')->isPageTwitterEnabled()) {
            $twitterUsername = Mage::helper('mageworx_seomarkup/config')->getPageTwitterUsername();
            if ($twitterUsername) {
                $html = $html ? $html : "\n";
                $html .= "<meta property=\"twitter:card\" content=\"summary\"/>\n";
                $html .= "<meta property=\"twitter:site\" content=\"" . $twitterUsername . "\"/>\n";
                $html .= "<meta property=\"twitter:title\" content=\"" . $title . "\"/>\n";
                $html .= "<meta property=\"twitter:description\" content=\"" . $description . "\"/>\n";
            }
        }

        return $html;
    }
}
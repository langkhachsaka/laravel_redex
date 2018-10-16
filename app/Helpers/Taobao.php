<?php
namespace App\Helpers;

class Taobao
{
    CONST UTF8_ENCODING = 'utf-8';
    CONST GBK_ENCODING = "gb18030";
    CONST TAG_DIV_CONTAIN_SHOP_NAME_IN_TAOBAO = '<div class="tb-shop-name">';
    CONST TAG_DIV_CONTAIN_SHOP_NAME_IN_TMALL_01 = '<div id="shopExtra"';
    CONST TAG_DIV_CONTAIN_SHOP_NAME_IN_TMALL_02 ='<div class="hd-shop-name">';
    CONST MAX_LENGTH_TAG_DIV = 1000; // only surmise.
    CONST TAOBAO_ADD = 'taobao.com';
    CONST TMALL_ADD = 'tmall.com';

    public function getShopName($url){
        $shopName = null;

        //// Get shop name from taobao site
        if(strpos($url, self::TAOBAO_ADD)){
            $html = file_get_contents($url);
            $html= mb_convert_encoding($html, self::UTF8_ENCODING , self::GBK_ENCODING);
            //Get div contain shop name
            $shopInfo =  substr($html, strrpos($html, self::TAG_DIV_CONTAIN_SHOP_NAME_IN_TAOBAO)-1,self::MAX_LENGTH_TAG_DIV);
            $divShopInfoTag = substr($shopInfo , 0 , strrpos($shopInfo, '</div>')+6);
            $DOM = new DOMDocument;
            $DOM->loadHTML('<?xml encoding="utf-8" ?>'.$divShopInfoTag);
            $linkToShop = $DOM->getElementsByTagName('a');
            $shopName = trim($linkToShop[0]->textContent);
        }
        // Get shop name from tmall site
        if(strpos($url, self::TMALL_ADD)){
            $html = file_get_contents($url);
            $html= mb_convert_encoding($html, self::UTF8_ENCODING , self::GBK_ENCODING);

            //Get div contain shop name
            $shopInfo =  substr($html, strrpos($html, self::TAG_DIV_CONTAIN_SHOP_NAME_IN_TMALL_01)-1,self::MAX_LENGTH_TAG_DIV);
            $divShopInfoTag = substr($shopInfo , 0 , strrpos($shopInfo, '</div>')+6);
            $DOM = new DOMDocument;
            $DOM->loadHTML('<?xml encoding="utf-8" ?>'.$divShopInfoTag);
            $linkToShop = $DOM->getElementsByTagName('a');
            $shopName = trim($linkToShop[0]->textContent);
        }
        return $shopName;
    }
}
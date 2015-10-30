<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Common
 * @version    0.6.0
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Helper_Strings extends Mage_Core_Helper_Abstract
{
    protected $_defaultEncoding = "UTF-8";

    protected function _cutBadSuffix($content)
    {
        $contentPieces = explode(" ", $content);
        if (count($contentPieces) > 1){
            unset($contentPieces[count($contentPieces) - 1]);
        }
        $content = implode(" ", $contentPieces);
        return $content;
    }

    public function strtoupper($value)
    {
        return function_exists("mb_strtoupper") ? mb_strtoupper($value, $this->_defaultEncoding) : strtoupper($value);
    }

    public function strtolower($value)
    {
        return function_exists("mb_strtolower") ? mb_strtolower($value, $this->_defaultEncoding) : strtolower($value);
    }

    public function strlen($value)
    {
        return function_exists("mb_strlen") ? mb_strlen($value, $this->_defaultEncoding) : strlen($value);
    }

    public function substr($string, $start, $length = null)
    {
        return function_exists("mb_substr") ? mb_substr($string, $start, $length, $this->_defaultEncoding) : substr($string, $start, $length);
    }

    /**
     * Damerau–Levenshtein distance
     *
     * @param string $source
     * @param string $target
     * @return int
     */
    public function dlWordDistance($source, $target)
    {
        if (!$source){
            if (!$target){
                return 0;
            } else {
                return $this->strlen($target);
            }
        } elseif (!$target) {
            return $this->strlen($source);
        }

        $score = array();
        $INF = $this->strlen($source) + $this->strlen($target);
        $score[0][0] = $INF;

        for ($i = 0; $i <= $this->strlen($source); $i++){
            $score[$i+1][1] = $i;
            $score[$i+1][0] = $INF;
        }

        for ($j = 0; $j <= $this->strlen($target); $j++){
            $score[1][$j + 1] = $j;
            $score[0][$j + 1] = $INF;
        }

        /** @var $sd Magpleasure_Common_Model_Type_Dictionary */
        $sd = Mage::getModel('magpleasure/type_dictionary');

        $common = $source.$target;
        for ($char = 0; $char <= $this->strlen($common) - 1; $char++){
            $symbol = $this->substr($common, $char, 1, "UTF-8");
            if (!$sd->containsKey($symbol)){
                $sd->add($symbol, 0);
            }
        }

        for ($i = 1; $i <= $this->strlen($source); $i++){
            $DB = 0;
            for ($j = 1; $j <= $this->strlen($target); $j++){

                $i1 = $sd[$this->substr($target, $j - 1, 1)];
                $j1 = $DB;
                if ($this->substr($source, $i - 1, 1) == $this->substr($target, $j - 1, 1)){
                    $score[$i + 1][$j + 1] = $score[$i][$j];
                    $DB = $j;
                } else {
                    $score[$i + 1][$j + 1] = min($score[$i][$j], min($score[$i + 1][$j], $score[$i][$j + 1])) + 1;
                }
                $score[$i + 1][$j + 1] = min(@$score[$i + 1][$j + 1], @$score[$i1][$j1] + ($i - $i1 - 1) + 1 + ($j - $j1 - 1));
            }
            $sd[$this->substr($source, $i-1, 1)] = $i;
        }

        return $score[$this->strlen($source) + 1][$this->strlen($target) + 1];
    }

    /**
     * Extract keywords from text
     *
     * @param string $text
     * @param int $limit
     * @return array
     */
    public function getKeywords($text, $limit = 10)
    {
        $unallowedWords = array(
            # en_US
            'i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www',
            # ru_RU
            'и','мне','я','мы','они','в','на','по','под','к','от',
        );

        $text = preg_replace('/\s\s+/ixu', '', $text); // replace whitespace
        $text = trim($text); // trim the string
        $text = preg_replace('/[^а-яА-Яa-zA-Z0-9 -]/ixu', ' ', $text); // only take alphanumerical characters, but keep the spaces and dashes too…
        $text = $this->strtolower($text); // make it lowercase
        $matchedWords = explode(" ", $text);

        foreach ( $matchedWords as $key=>$word ) {
            if ( $word == '' || in_array($this->strtolower($word), $unallowedWords) || $this->strlen($word) <= 3 ) {
                unset($matchedWords[$key]);
            }
        }

        /** @var $words Magpleasure_Common_Model_Type_Dictionary_String */
        $words = Mage::getModel('magpleasure/type_dictionary_string');

        foreach ($matchedWords as $word){
            $similarKey = $words->getSimilarKey($word);
            if (!$similarKey && !$words->containsKey($word)){
                $words->add($word, 1);
            } elseif ($similarKey && !$words->containsKey($word)) {
                $words[$similarKey] = $words[$similarKey] + 1;
            } elseif (!$similarKey && $words->containsKey($word)) {
                $words[$word] = $words[$word] + 1;
            }
        }

        $words->rsort();
        $resultArray = $words->keys();

        return array_slice($resultArray, 0, (($limit > 0) ? $limit : 1));
    }


    /**
     * Cut long text
     *
     * @param string $content
     * @param integer $limit
     * @return string
     */
    public function strLimit($content, $limit)
    {
        if (function_exists('mb_strlen')){
            if (mb_strlen($content, 'UTF-8') > $limit){
                $content = $this->_cutBadSuffix(mb_substr($content, 0, $limit - 1, 'UTF-8'));
            }
        } else {
            if (strlen($content) > $limit){
                $content = $this->_cutBadSuffix(substr($content, 0, $limit - 1));
            }
        }
        return $content;
    }

    /**
     * HTML to text
     *
     * @param string $content
     * @return string
     */
    public function htmlToText($content)
    {
        return $this->stripTags($content);
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool $escape
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        $result = strip_tags($data, $allowableTags);
        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }

    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     * @return  mixed
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } else {
            // process single item
            if (strlen($data)) {
                if (is_array($allowedTags) and !empty($allowedTags)) {
                    $allowed = implode('|', $allowedTags);
                    $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                    $result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
                    $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
                } else {
                    $result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
                }
            } else {
                $result = $data;
            }
        }
        return $result;
    }


    /**
     * Cut Line into pieces
     *
     * @param $line
     * @param $limit
     * @return array
     */
    public function cutToPieces($line, $limit)
    {
        $lines = array();
        $strLen = $this->strlen($line);
        $count = 0;
        if ($strLen > $limit){
            for ($i = 0; ($count * $limit) < $strLen; $i += $limit){
                $lines[] = $this->substr($line, $i, $limit);
                $count++;
            }

            if ($count * $limit != $strLen){
                $i += $limit;
                $lines[] = $this->substr($line, $i, ($strLen - ($limit * $count)));
            }
        } else {
            $lines[] = $line;
        }
        return $lines;
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     * @return string
     */
    public function escapeUrl($data)
    {
        return htmlspecialchars($data);
    }
}
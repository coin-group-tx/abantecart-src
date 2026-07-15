<?php

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ADocument
 */
final class ADocument
{
    private $title;
    private $description;
    private $keywords;
    private $base;
    private $charset = 'utf-8';
    private $language = 'en-gb';
    private $direction = 'ltr';
    private $links = [];
    private $metas = [];
    private $styles = [];
    private $scripts = [];
    private $scripts_bottom = [];
    private $breadcrumbs = [];

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @void
     */
    public function resetLinks()
    {
        $this->links = [];
    }

    /**
     * method add new Link item
     *
     * @param array $link_item - array("href"=>"","rel"=>"")
     *                         Examples: href => 'www.google.com', 'rel'  => 'canonical'
     *
     * @void
     */
    public function addLink($link_item = [])
    {
        if ($link_item["href"]) {
            $this->links[] = $link_item;
        }
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param array $metaData
     * @param string $key
     *
     * @return void
     */
    public function addMeta(array $metaData = [], string $key = '')
    {
        if ($metaData["name"] && $metaData["content"]) {
            if($key) {
                $this->metas[$key] = $metaData;
            }else {
                $this->metas[] = $metaData;
            }
        }
    }

    /**
     * @return array
     */
    public function getMetas(): array
    {
        return array_merge(
            [
                'keywords'    => [
                    'name'    => 'keywords',
                    'content' => $this->getKeywords()
                ],
                'description' => [
                    'name'    => 'description',
                    'content' => $this->getDescription()
                ],
                'generator'   => [
                    'name'    => 'generator',
                    'content' => 'AbanteCart v' . VERSION . ' - Open Source eCommerce solution'
                ]
            ], 
            $this->metas
        );
    }

    /**
     * @void
     */
    public function resetStyles()
    {
        $this->styles = [];
    }

    /**
     * method to add a new Style item
     *
     * @param array $style_item - array("href"=>"","rel"=>"","media"=>)
     *                          Examples: href => 'www.google.com', $rel = 'stylesheet', $media = 'screen'
     *
     * @void
     */
    public function addStyle($style_item = [])
    {
        if ($style_item["href"]) {
            $this->styles[] = $style_item;
        }
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @void
     */
    public function resetScripts()
    {
        $this->scripts = [];
    }

    /**
     * method to add a new JavaScript file to the head
     *
     * @param string $URI - web path to the file
     *               Examples: /js/bootstrap.js or http//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js
     *
     * @return void
     */
    public function addScript($URI)
    {
        if ($URI) {
            $this->scripts[] = $URI;
        }
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        //Need to have only unique scripts to avoid duplicates
        return array_unique($this->scripts);
    }

    /**
     * method to add a new JavaScript file to the bottom before </body> tag
     *
     * @param string $URI - web path to the file
     *               Examples: /js/bootstrap.js or http//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js
     *
     * @void
     */
    public function addScriptBottom($URI)
    {
        $this->scripts_bottom[] = $URI;
    }

    /**
     * @return array
     */
    public function getScriptsBottom()
    {
        //Need to have only unique scripts to avoid duplicates
        return array_unique($this->scripts_bottom);
    }

    public function resetScriptsBottom()
    {
        $this->scripts_bottom = [];
    }

    /**
     * method to reset a breadcrumb array
     *
     * @void
     */
    public function resetBreadcrumbs()
    {
        $this->breadcrumbs = [];
    }

    /**
     * method to initialize Breadcrumbs aray and add root attribute
     *
     * @param array $breadcrumb_item ("href"=>"", "text"=>"", "separator"=>)
     *
     * @void
     */
    public function initBreadcrumb($breadcrumb_item = [])
    {
        $this->resetBreadcrumbs();

        $this->addBreadcrumb($breadcrumb_item);
    }

    /**
     * method add new Breadcrumb item
     *
     * @param array $breadcrumb_item ("href"=>"", "text"=>"", "separator"=>)
     *
     * @void
     */
    public function addBreadcrumb($breadcrumb_item = [])
    {
        if ($breadcrumb_item["href"]) {
            $this->breadcrumbs[] = $breadcrumb_item;
        }
    }

    /**
     * @return array
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * trims text with a set length and ellipses
     *
     * @param string $input text to trim
     * @param int $length in characters to trim to
     * @param bool $ellipses if ellipses (...) are to be added
     * @param bool $strip_html if html tags are to be stripped
     *
     * @return string
     */
    public function trimText($input, $length, $ellipses = true, $strip_html = false)
    {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }
}

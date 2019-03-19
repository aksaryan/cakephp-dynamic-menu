<?php
App::uses('AppHelper', 'View/Helper');

class MynodesHelper extends AppHelper
{

    public $helpers = array(
        'Html',
        'Session',
        'PhpThumb.PhpThumb'
    );

    public function getWebsiteMenus ()
    {

        App::uses('Contentpage', 'Model');
        $this->FrontPages = new Contentpage();
        
        $activeMenuGroup = $this->Session->read('WebFront.activeMenuGroup');
        
        $output = '';
        
        $frontContentPages = $this->FrontPages->find('all', array(
            'conditions' => array(
                'Contentpage.content_group' => 2,
                'Contentpage.parent_id' => NULL
            ),
            'fields' => array(
                'Contentpage.id',
                'Contentpage.page_title',
                'Contentpage.page_alias'
            ),
            'recursive' => - 1
        ));
        
        //debug($frontContentPages); exit;
        $parentCnt = 0;
        
        //If not empty Parent Menus
        if (! empty($frontContentPages)) {
            
            $output = '<ul>';
            
            // foreach Parent Menus
            foreach ($frontContentPages as $firstMenus) {
                
                $menuActive = null;
                
                // Check if it has child menus
                $childMenus = $this->FrontPages->children($firstMenus['Contentpage']['id'], true, array(
                    'id',
                    'page_title',
                    'page_alias'
                ), 'Contentpage.id ASC');
                
                if ($activeMenuGroup == $firstMenus['Contentpage']['page_alias']) {
                    $menuActive = 'class="current"';
                }
                
                if (! empty($childMenus)) {
                    $output .= '<li ' . $menuActive . '>';
                    $output .= '<a href="' . $this->Html->url("/index/page/") . $firstMenus['Contentpage']['page_alias'] .
                     '">';
                    $output .= $firstMenus['Contentpage']['page_title'];
                    $output .= '</a>';
                    
                    $menuHtmlOutput = $this->_getChildMenu($childMenus);
                    
                    if (! empty($menuHtmlOutput)) {
                        $output .= $menuHtmlOutput;
                    }
                    
                    $output .= '</li>';
                
                } else {
                    $output .= '<li ' . $menuActive . '>';
                    $output .= '<a href="' . $this->Html->url("/index/page/") . $firstMenus['Contentpage']['page_alias'] .
                     '">';
                    $output .= $firstMenus['Contentpage']['page_title'];
                    $output .= '</a>';
                    $output .= '</li>';
                }
                
                $parentCnt ++;
            } // end Foreach Parent Menus
            

            $output .= '<li><a href="' . $this->Html->url("/index/logout/") . '" style="background-color:#036647; color:#fff;">' . __("Logout") . '</a></li>';
            
            $output .= '</ul>';
        
        } // end If not empty Parent Menus
        

        return html_entity_decode($output);
    }

    private function _getChildMenu ($childMenus = null)
    {

        $menu = null;
        App::uses('Contentpage', 'Model');
        $this->FrontPages = new Contentpage();
        
        if (! empty($childMenus)) {
            $menu = '<ul>';
            foreach ($childMenus as $cMenu) {
                $menu .= '<li>';
                $menu .= '<a href="' . $this->Html->url("/index/page/") . $cMenu['Contentpage']['page_alias'] . '">';
                $menu .= $cMenu['Contentpage']['page_title'];
                $menu .= '</a>';
                
                $childChildMenus = $this->FrontPages->children($cMenu['Contentpage']['id'], true);
                if (! empty($childChildMenus)) {
                    $menu .= $this->_getChildMenu($childChildMenus);
                }
                $menu .= '</li>';
            }
            $menu .= '</ul>';
        }
        
        return $menu;
    }

    public function getCountries ()
    {

        $countriesHTML = null;
        
        App::uses('Country', 'Model');
        $this->Country = new Country();
        
        //Get all countries
        $countries = $this->Country->find('list', array(
            'conditions' => array(
                'Country.status' => 1
            ),
            'recursive' => - 1,
            'order' => 'country ASC'
        ));
        
        $activeCountryId = $this->Session->read('WebFront.nodeAsset.country_id');
        if (! empty($countries)) {
            $countriesHTML .= '<select class="extranet-select" name="extranet_country" id="extranet_country">';
            $countriesHTML .= '<option value=""> -- Select Country -- </option>';
            foreach ($countries as $key => $value) {
                if ($activeCountryId == $key) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = null;
                }
                $countriesHTML .= '<option value="' . $key . '" ' . $selected . '>';
                $countriesHTML .= $value;
                $countriesHTML .= '</option>';
            }
            $countriesHTML .= '</select>';
        }
        
        return $countriesHTML;
    }

    public function getLanguages ()
    {

        $languagesHTML = null;
        
        App::uses('Language', 'Model');
        $this->Language = new Language();
        
        //Get all Languages
        $languages = $this->Language->find('list', array(
            'conditions' => array(
                'Language.status' => 1
            ),
            'recursive' => - 1,
            'order' => 'title ASC'
        ));
        
        $activeCountryLanguageId = $this->Session->read('WebFront.nodeAsset.language_id');
        
        if (! empty($languages)) {
            $languagesHTML .= '<select class="extranet-select" name="extranet_language" id="id="extranet_language">';
            $languagesHTML .= '<option value=""> -- Select Language -- </option>';
            foreach ($languages as $key => $value) {
                if ($activeCountryLanguageId == $key) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = null;
                }
                $languagesHTML .= '<option value="' . $key . '" ' . $selected . '>';
                $languagesHTML .= $value;
                $languagesHTML .= '</option>';
            }
            $languagesHTML .= '</select>';
        }
        
        return $languagesHTML;
    }

    public function getAssetType ()
    {

        $assetTypeHTML = null;
        
        // Get the types of asset
        $assetType = Configure::read('assetTypeNode');
        $activeAssetType = $this->Session->read('WebFront.nodeAsset.asset_type');
        
        if (! empty($assetType)) {
            $assetTypeHTML .= '<select class="extranet-select" name="extranet_assetType" id="extranet_assetType">';
            $assetTypeHTML .= '<option value=""> -- Select Document Type -- </option>';
            foreach ($assetType as $key => $value) {
                if ($activeAssetType == $value) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = null;
                }
                $assetTypeHTML .= '<option value="' . $value . '" ' . $selected . '>';
                $assetTypeHTML .= $value;
                $assetTypeHTML .= '</option>';
            }
            $assetTypeHTML .= '</select>';
        }
        
        return $assetTypeHTML;
    }

    function paginateLibraryItems ($item_per_page, $current_page, $total_records, $total_pages, $page_url)
    {

        $pagination = '';
        if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) { //verify total pages and current page number
            $pagination .= '<ul class="pagination" style="text-align:center">';
            
            $right_links = $current_page + 5;
            $previous = $current_page - 5; //previous link
            $next = $current_page + 1; //next link
            $first_link = true; //boolean var to decide our first link
            

            if ($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a href="' . $page_url . '?page=1" title="First">&laquo;</a></li>'; //first link
                $pagination .= '<li><a href="' . $page_url . '?page=' . $previous_link . '" title="Previous">&lt;</a></li>'; //previous link
                for ($i = ($current_page - 2); $i < $current_page; $i ++) { //Create left-hand side links
                    if ($i > 0) {
                        $pagination .= '<li><a href="' . $page_url . '?page=' . $i . '">' . $i . '</a></li>';
                    }
                }
                $first_link = false; //set first link to false
            }
            
            if ($first_link) { //if current active page is first link
                $pagination .= '<li class="first active">' . $current_page . '</li>';
            } elseif ($current_page == $total_pages) { //if it's the last active link
                $pagination .= '<li class="last active">' . $current_page . '</li>';
            } else { //regular current link
                $pagination .= '<li class="active">' . $current_page . '</li>';
            }
            
            for ($i = $current_page + 1; $i < $right_links; $i ++) { //create right-hand side links
                if ($i <= $total_pages) {
                    $pagination .= '<li><a href="' . $page_url . '?page=' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($current_page < $total_pages) {
                $next_link = ($i > $total_pages) ? $total_pages : $i;
                $pagination .= '<li><a href="' . $page_url . '?page=' . $next_link . '" >&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="' . $page_url . '?page=' . $total_pages . '" title="Last">&raquo;</a></li>'; //last link
            }
            
            $pagination .= '</ul>';
        }
        
        //debug($pagination); exit;
        return $pagination; //return pagination links
    }
}
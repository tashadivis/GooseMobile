<?php
/*
 * Main.php
 * 
 * Copyright 2014 Ortus IT <contact@ortus-it.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

class OrtusStruct
{
    public $info_array = array();
    public $debug = false;
    
    /**
     * Constructor of class Main.
     *
     * @return void
     */
    public function __construct($debug = false, $total_info = array(), $level = 1, $merge = array())
    {
        $this->debug = is_bool($debug) ? $debug : false;
        
        $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
        
             
        
        $this->cms    = $total_info['current_CMS'];
        $this->module = $total_info['install_Modules'];
        $this->js     = $total_info['current_Js_file'];
        $this->level  = $level;
        
        $this->getInfo();
        $this->info_array = $this->info_array + $merge;
        
        
    }
    
    
    public function getInfo()
    {
        if ($this->debug) {
            
            $arr = array(
                '__construct',
                'viewInfo',
                'getInfo'
                
                
            );
            
            switch ($this->level) {
                
                case 1:
                    array_push($arr, 'getUserClass', 'getUserFunctions', 'getUserVars', 'getUserIncludeRequireFile', 'getUserModules', 'getUserJsFile');
                    break;
                
                case 2:
                    array_push($arr, 'getUserFunctions', 'getUserVars');
                    break;
                
                case 3:
                default:
                    break;
                    
            }
            
            
            foreach (get_class_methods($this) as $key) {
                if (!in_array($key, $arr)) {
                    $this->info_array[$key] = $this->{$key}();
                }
                
            }
            
            return $this->info_array;
            
        }
        
    }
    
    public function viewInfo()
    {
        if ($this->debug) {
            return $this->info_array;
        }
    }
    
    
    
    public function getPhpVersion()
    {
        return phpversion();
    }
    
    public function getUserFunctions()
    {
        
        $defined_functions = get_defined_functions();
        return $defined_functions['user'];
        
    }
    
    public function getUserClass()
    {
        
        return get_declared_classes();
        
    }
    
    public function getUserVars()
    {
        
        $ignore = array(
            'GLOBALS',
            '_FILES',
            '_COOKIE',
            '_POST',
            '_GET',
            '_SERVER',
            '_ENV',
            'ignore'
        );
        $vars   = array_diff_key(get_defined_vars() + array_flip($ignore), array_flip($ignore));
        return $vars;
        
    }
    
    public function getUserIncludeRequireFile()
    {
        
        return get_included_files();
        
    }
    
    public function getUserCms()
    {
        
        return $this->cms;
        
    }
    
    
    public function getUserSiteUrl()
    {
        return sprintf("%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
    }
    
    public function getIpServer()
    {
        return $_SERVER['SERVER_ADDR'];
    }
    
    public function getUserModules()
    {
        
        return $this->module;
        
    }
    
    public function getUserJsFile()
    {
        
        return $this->js;
        
    }
    
    public function getUserBrowser()
    {
        
        $browser = array(
            "Navigator" => "/Navigator(.*)/i",
            "Firefox" => "/Firefox(.*)/i",
            "Internet Explorer" => "/MSIE(.*)/i",
            "Google Chrome" => "/chrome(.*)/i",
            "MAXTHON" => "/MAXTHON(.*)/i",
            "Opera" => "/Opera(.*)/i"
        );
        foreach ($browser as $key => $value) {
            if (preg_match($value, $this->agent)) {
                
                $browser = strtolower($key);
                preg_match_all($value, $this->agent, $match);
                switch ($browser) {
                    case "firefox":
                        $version = str_replace("/", "", $match[1][0]);
                        break;
                    
                    case "internet explorer":
                        $version = substr($match[1][0], 0, 4);
                        break;
                    
                    case "opera":
                        $version = str_replace("/", "", substr($match[1][0], 0, 5));
                        break;
                    
                    case "navigator":
                        $version = substr($match[1][0], 1, 7);
                        break;
                    
                    case "maxthon":
                        $version = str_replace(")", "", $match[1][0]);
                        break;
                    
                    case "google chrome":
                        $version = substr($match[1][0], 1, 10);
                }
                
                return array(
                    "Browser" => $key,
                    "Version" => $version
                );
                break;
            }
        }
        
    }
    
    public function getUserOS()
    {
        $OS = array(
            "Windows" => "/Windows/i",
            "Linux" => "/Linux/i",
            "Unix" => "/Unix/i",
            "Mac" => "/Mac/i"
        );
        
        foreach ($OS as $key => $value) {
            if (preg_match($value, $this->agent)) {
                return array(
                    "Operating System" => $key
                );
                break;
            }
        }
    }
    
    
    
}

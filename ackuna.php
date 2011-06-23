﻿<?php
/*
Plugin Name: Ackuna Language Translation Plugin
Plugin URI: http://www.ackuna.com/wordpress
Description: Allows your users to translate your blog into many different languages. The button is added to the top of every post.
Version: 1.1
Author: Ackuna
Author URI: http://www.ackuna.com/
License: GPL2
*/
/*  Copyright 2011 Ackuna (email : alex.buran@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class AckunaWidget {
	//==========================================
	// If your page is not written in English, replace your language below.
	// You must use the language CODE of your website, not the full language name.
	// For a full list of usable languages and codes, please visit: 
	// http://code.google.com/apis/language/translate/v1/reference.html#LangNameArray
	// e.g. If your page is written in Spanish, then the line:
	// var $ackuna_src = 'en';
	// Should be changed to:
	// var $ackuna_src = 'es';
	
	var $ackuna_src = 'en';
	//==========================================
	
	// Constructor
	function AckunaWidget (){
		add_filter('the_content', array(&$this, 'codeToContent'));
	}
	
	function codeToContent ($content){  
		// Add nothing to RSS feed.
		if (is_feed()) {
			return $content;
		}
		
		// Add nothing to categories.
		if (is_category()) {
			return $content;
		}
		
		// Add the link to the content.
		$link  = urlencode(get_permalink());
		return $this->getAckunaCode($link).$content;
	}
	
	// Get the actual button code.
	function getAckunaCode ($link) {
		$ackuna_code	= '<div style="clear: both; display: block; height: 21px; margin: 5px 0; overflow: hidden;">';
		$ackuna_code	.= '<div style="clear: both; float: right; width: auto;">';
		$ackuna_code	.= '<div class="ackuna">';
		$ackuna_code	.= '<a href="http://www.ackuna.com/" title="Translator" class="ackuna_drop">';
		$ackuna_code	.= '<span class="translate_d_86_21_1">Translator</span>';
		$ackuna_code	.= '</a>';
		$ackuna_code	.= '</div>';
		$ackuna_code	.= '<script type="text/javascript">';
		$ackuna_code	.= sprintf('var ackuna_src = "%s";', $this->ackuna_src);
		$ackuna_code	.= '</script>';
		
		// The following line may need to be removed if you already have jQuery on your site.
		$ackuna_code	.= '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>';
		
		$ackuna_code	.= '<script src="http://s1.ackuna.com/_v_1/javascript/e3.js" type="text/javascript"></script>';
		$ackuna_code	.= '</div>';
		$ackuna_code	.= '</div>';

        return $ackuna_code;
	}
	
	function Ackuna_doposts ($content) {  
		for ($i = 0; $i < 10; $i++){
			$content .= $this->Ackuna_postit($i);
		}
		return $content;
	}
	
	function Ackuna_postit ($i) {
		add_filter('the_content', array(&$this, 'codeToContent'));
		$content = $this->codeToContent($i);
		
		$cut	= explode("|", $content);
		$post	= $cut[0];
		$link	= $cut[1]; 
		return content . "<br />" . $link;
	}
}

$ackuna &= new AckunaWidget();
?>
<?php
/*
Plugin Name: Ackuna Language Translation Plugin
Plugin URI: http://ackuna.com/pages/translate_this
Description: Allows your users to translate your blog into many different languages. The button is added to the top of every post.
Version: 2.0.0
Author: Ackuna
Author URI: http://ackuna.com/
License: GPL2
*/

/*
Copyright 2015 Ackuna (email : info@ackuna.com)

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
	private $ackuna_src				= 'en';
	private $ackuna_include_jquery	= false;
	private $ackuna_languages = array(
		'af' => 'Afrikaans', 
		'sq' => 'Albanian', 
		'ar' => 'Arabic', 
		'hy' => 'Armenian', 
		'az' => 'Azerbaijani', 
		'eu' => 'Basque', 
		'be' => 'Belarusian', 
		'bn' => 'Bengali', 
		'bs' => 'Bosnian', 
		'bg' => 'Bulgarian', 
		'ca' => 'Catalan', 
		'ceb' => 'Cebuano', 
		'ny' => 'Chichewa', 
		'zh-CN' => 'Chinese', 
		'hr' => 'Croatian', 
		'cs' => 'Czech', 
		'da' => 'Danish', 
		'nl' => 'Dutch', 
		'en' => 'English', 
		'eo' => 'Esperanto', 
		'et' => 'Estonian', 
		'tl' => 'Filipino', 
		'fi' => 'Finnish', 
		'fr' => 'French', 
		'gl' => 'Galician', 
		'ka' => 'Georgian', 
		'de' => 'German', 
		'el' => 'Greek', 
		'gu' => 'Gujarati', 
		'ht' => 'Haitian Creole', 
		'ha' => 'Hausa', 
		'iw' => 'Hebrew', 
		'hi' => 'Hindi', 
		'hmn' => 'Hmong', 
		'hu' => 'Hungarian', 
		'is' => 'Icelandic', 
		'ig' => 'Igbo', 
		'id' => 'Indonesian', 
		'ga' => 'Irish', 
		'it' => 'Italian', 
		'ja' => 'Japanese', 
		'jw' => 'Javanese', 
		'kn' => 'Kannada', 
		'kk' => 'Kazakh', 
		'km' => 'Khmer', 
		'ko' => 'Korean', 
		'lo' => 'Lao', 
		'la' => 'Latin', 
		'lv' => 'Latvian', 
		'lt' => 'Lithuanian', 
		'mk' => 'Macedonian', 
		'mg' => 'Malagasy', 
		'ms' => 'Malay', 
		'ml' => 'Malayalam', 
		'mt' => 'Maltese', 
		'mi' => 'Maori', 
		'mr' => 'Marathi', 
		'mn' => 'Mongolian', 
		'my' => 'Myanmar', 
		'ne' => 'Nepali', 
		'no' => 'Norwegian', 
		'fa' => 'Persian', 
		'pl' => 'Polish', 
		'pt' => 'Portuguese', 
		'pa' => 'Punjabi', 
		'ro' => 'Romanian', 
		'ru' => 'Russian', 
		'sr' => 'Serbian', 
		'st' => 'Sesotho', 
		'si' => 'Sinhala', 
		'sk' => 'Slovak', 
		'sl' => 'Slovenian', 
		'so' => 'Somali', 
		'es' => 'Spanish', 
		'su' => 'Sundanese', 
		'sw' => 'Swahili', 
		'sv' => 'Swedish', 
		'tg' => 'Tajik', 
		'ta' => 'Tamil', 
		'te' => 'Telugu', 
		'th' => 'Thai', 
		'tr' => 'Turkish', 
		'uk' => 'Ukrainian', 
		'ur' => 'Urdu', 
		'uz' => 'Uzbek', 
		'vi' => 'Vietnamese', 
		'cy' => 'Welsh', 
		'yi' => 'Yiddish', 
		'yo' => 'Yoruba', 
		'zu' => 'Zulu', 
	);
	
	// Constructor.
	function AckunaWidget() {
		// Add functions to the content and excerpt.
		add_filter('the_content', array(&$this, 'codeToContent'));
		add_filter('get_the_excerpt', array(&$this, 'ackunaExcerptTrim'));
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'pluginSettingsLink'));
		// Initialize the plugin.
		add_action('admin_menu', array(&$this, '_init'));
		// Get the plugin options.
		if (get_option('ackuna_src')) {
			$this->ackuna_src = get_option('ackuna_src');
		}
		if (get_option('ackuna_include_jquery')) {
			$this->ackuna_include_jquery = get_option('ackuna_include_jquery');
		}
		// Get our version of jQuery, as needed.
		if (!is_admin() && $this->ackuna_include_jquery) {
			wp_deregister_script('jquery'); 
			wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js');
		}
		// Load our scripts and enqueue jQuery.
		wp_register_script('ackuna', 'http://s1.ackuna.com/e3_1/javascript/e.js', 'jquery', '3.1', true);
		wp_enqueue_script('jquery');
		wp_enqueue_script('ackuna');
	}
	
	function _init() {
		// Add the options page.
		add_options_page('Ackuna Settings', 'Ackuna', 'manage_options', 'ackuna', array(&$this, 'pluginOptions'));
		// Register our plugin settings.
		register_setting('ackuna_options', 'ackuna_src', array(&$this, 'validateLanguage'));
		register_setting('ackuna_options', 'ackuna_include_jquery');
	}
	
	// Called whenever content is shown.
	function codeToContent($content) {
		// What we add depends on type.
		if (is_feed()) {
			// Add nothing to RSS feed.
			return $content;
		} elseif (is_category()) {
			// Add nothing to categories.
			return $content;
		} else if(is_singular()) {
			// For singular pages we add the button to the content normally.
			return $this->getAckunaCode() . $content;
		} else {
            // For everything else add nothing.
            return $content;
        }
	}
	
	// Get the actual button code.
	function getAckunaCode() {
		return <<<EOL
			<div class="ackuna">
				<a href="http://www.translation-services-usa.com" title="Translation" class="ackuna_drop ackuna_image">translation services</a>
			</div>
			<script type="text/javascript">var ackuna_src = "{$this->ackuna_src}";</script>
EOL;
	}
	
	// Admin page display.
	function pluginOptions() {
		if (!current_user_can('manage_options'))  {
			wp_die('You do not have sufficient permissions to access this page.');
		}
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php settings_fields('ackuna_options'); ?>
				<h2>Ackuna Settings</h2>
				<p>Update the language and other settings for the Ackuna Blog Translator plugin.</p>
				<table class="widefat">
					<tbody>
						<tr>
							<td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
								<p><label id="ackuna_src" for="ackuna_src">Your Site's Current Language</label></p>
								<p>
									<select name="ackuna_src">
										<?php
										$current_src = get_option('ackuna_src') ? get_option('ackuna_src') : $this->ackuna_src;
										asort($this->ackuna_languages);
										foreach ($this->ackuna_languages as $key => &$value) {
											$selected = $current_src == $key ? 'selected="selected"' : '';
											printf('<option %s value="%s">%s</option>', $selected, $key, $value);
										}
										unset($value);
										?>
									</select>
								<p>
								<p>Set this to whatever language your blog is written in. If your blog is in English, and you want visitors to be able to view it in Spanish, Russian, and Japanese, select &quot;English.&quot;</p>
							</td>
						</tr>
						<tr>
							<td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
								<input name="ackuna_include_jquery" type="hidden" value="0" />
								<p><label for="ackuna_include_jquery"><input id="ackuna_include_jquery" <?php echo $this->ackuna_include_jquery ? 'checked="checked"' : ''; ?> name="ackuna_include_jquery" type="checkbox" value="1" /> Use a Validated jQuery Version</p>
								<p>Use this only if you are having trouble getting the Ackuna button to work.</p>
								<p><b>Caution:</b> this will override the jQuery script in your theme and could cause conflicts with other plugins and scripts on your blog.</p>
							</td>
						</tr>
						<tr>
							<td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
								<b>Note:</b> if you are using any caching plugins, such as WP Super Cache, you will need to clear your cached pages after updating your Ackuna settings.
							</td>
						</tr>
						<tr>
							<th><input name="submit" type="submit" value="Save Settings" class="button-primary" /></th>
						</tr>
					</tbody>
				</table>
				<p><b>Ackuna Blog Translator</b> is a project by <a href="http://ackuna.com/" target="_blank">Ackuna</a>. Developers, translate your apps for free now on Ackuna!</p>
			</form>
		</div>
		<?php
	}
	
	// Add settings link on plugin page
	function pluginSettingsLink($links) { 
		$settings_link = '<a href="options-general.php?page=ackuna">Settings</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	}
	
	// Remove (what's left of) our button code from excerpts.
	function ackunaExcerptTrim($text) {
		$pattern		= '/Translationvar ackuna_src = "(.*?)";/i';
		$replacement	= '';
		return preg_replace($pattern, $replacement, $text);
	}
	
	// Sanitize plugin settings options.
	function validateLanguage($language = null) {
		$return = $this->ackuna_src;
		if (array_key_exists($language, $this->ackuna_languages)) {
			$return = $language;
		}
		return $return;
	}
}

$translate_this &= new AckunaWidget();
?>
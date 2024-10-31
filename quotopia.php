<?php
/***
 * Plugin Name: Quotopia
 * Plugin URI: https://wordpress.org/plugins/quotopia/
 * Description: Just a simple plugin to display quotes from incredible women in sports.
 * Version: 1.0.7
 * Requires at least: 5.2
 * Author: Doug "BearlyDoug" Hazard
 * Author URI: https://wordpress.org/support/users/bearlydoug/
 * Text Domain: quotopia
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify it under 
 * the terms of the [GNU General Public License](http://wordpress.org/about/gpl/)
 * as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, on an "AS IS", but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see [GNU General Public Licenses](http://www.gnu.org/licenses/), or write to the
 * Free Software Foundation, Inc., 51 Franklin Street, 5th Floor, Boston, MA 02110, USA.
 */

/***
 * Internationalization, coming soon!
 */
// echo __('It worked! Now look for a directory named "a".', 'quotopia');

/***
 *	Setting up security stuff and paths...
 */
defined('ABSPATH') or die('Sorry, Charlie. No access for you!');
require_once(ABSPATH.'wp-admin/includes/file.php' );
require_once(ABSPATH.'wp-admin/includes/plugin.php');

/***
 * Including the BearlyDoug functions file...
 */
require_once('functions-bd.php');

/***
 * DEFINE VERSION HERE
 */
define('quotopiaVersion', '1.0.7');
define('quotopia', 'quotopia');

/***
 * Quotopia Navigation link.
 */
function bearlydougplugins_add_quotopia_submenu(){
	add_submenu_page(
		'bearlydoug',					// Parent Slug
		'Quotopia - quotes, YOUR way!',	// Page Title
		'Quotopia',					// Menu Title
		'edit_posts',					// Capabilities
		'quotopia',					// Nav Menu Link
		'quotopia_main_admin_interface'	// Function name
	);
}
add_action('admin_menu', 'bearlydougplugins_add_quotopia_submenu', 15);

/***
 *	Loading the CSS and JavaScript files here. Will also check to see if the main
 * BearlyDoug CSS file is enqueued. If not, then enqueue it.
 */
add_action('admin_enqueue_scripts', 'quotopia_enqueue_admin_files', 15);
function quotopia_enqueue_admin_files(){
	wp_register_style('quotopia', plugins_url('/includes/_CSS-quotopia.css',__FILE__ ));
	wp_register_script('cyclejs_js', plugins_url('/includes/_JS-cycle-ui.js',__FILE__));
	wp_register_script('storejs_js', plugins_url('/includes/_JS-store.min.js',__FILE__));

	wp_enqueue_style('quotopia');
	wp_enqueue_script('cyclejs_js');
	wp_enqueue_script('storejs_js');

	/***
	 * This has to get loaded into the footer, only if on the "quotopia" page.
	 */
	if(isset($_GET['page']) && $_GET['page'] == 'quotopia') {
		wp_enqueue_script('quotopiascbuilder', plugins_url('/includes/_JS-quotopiaSCBuilder.js',__FILE__ ), array(), false, true);
		wp_enqueue_script('jquery-ui-core');
        }

	if(!wp_style_is('bearlydoug', $list = 'enqueued')) {
		wp_register_style('bearlydougCSS', plugins_url('/includes/_CSS-bearlydoug.css',__FILE__ ));
		wp_register_script('bearlydougJS', plugins_url('/includes/_JS-bearlydoug.js',__FILE__) );
		wp_enqueue_style('bearlydougCSS');
		wp_enqueue_script('bearlydougJS');
        }
}

/***
 * Loading only the Plugin CSS file here.
 */
add_action('wp_enqueue_scripts', 'quotopia_enqueue_shortcode_files', 15);
function quotopia_enqueue_shortcode_files(){
	wp_register_style('quotopia', plugins_url('/includes/_CSS-quotopia.css',__FILE__ ));
	wp_enqueue_style('quotopia');
	wp_register_script('cyclejs_js', plugins_url('/includes/_JS-cycle-ui.js',__FILE__));
	wp_register_script('storejs_js', plugins_url('/includes/_JS-store.min.js',__FILE__));
	wp_enqueue_script('storejs_js');
	wp_enqueue_script('cyclejs_js');
}

/***
 * Handling the Quotopia admin page and tags saving function...
 */
function quotopia_main_admin_interface(){
	/***
	 * Counting the number of quote packs available inside wp-content/uploads/quotes
	 */
	$uploadDir = wp_get_upload_dir();
	$quotesDir = $uploadDir['basedir'] . '/quotes/';
	$quotePacks = 0;
	$files2 = glob($quotesDir ."*.txt");
	if($files2) {$quotePacks = count($files2);}

	/***
	 * Error warning, set to 0. woobr is the pop up iframe window close.
	 */
	$errNo = 0;

	/***
	 * Handling values. "bearlydougplugins_recursive_sanitize_text_field() is an array-friendly recursive
	 * version of WP's recursive_sanitize_text_field(), which only sanitizes a string. This function is called
	 * up via the "functions-bd.php" file.
	 */
	if(isset($_REQUEST['fnc'])){$fnc=sanitize_text_field($_REQUEST['fnc']);} else {$fnc=null;}
	if(isset($_REQUEST['Name'])){$Name=sanitize_text_field($_REQUEST['Name']);} else {$Name=null;}
	if(isset($_REQUEST['Source'])){$Source=sanitize_text_field($_REQUEST['Source']);} else {$Source=null;}
	if(isset($_REQUEST['Notes'])){$Notes=sanitize_text_field($_REQUEST['Notes']);} else {$Notes=null;}
	if(isset($_REQUEST['multiAuthor'])){$multiAuthor=sanitize_text_field($_REQUEST['multiAuthor']);} else {$multiAuthor=null;}
	if(isset($_REQUEST['singleAuthor'])){$singleAuthor=sanitize_text_field($_REQUEST['singleAuthor']);} else {$singleAuthor=null;}
	if(isset($_REQUEST['theAuthors'])){$theAuthors = bearlydougplugins_recursive_sanitize_text_field($_REQUEST['theAuthors']);} else {$theAuthors=null;}
	if(isset($_REQUEST['theQuotes'])){$theQuotes = bearlydougplugins_recursive_sanitize_text_field($_REQUEST['theQuotes']);} else {$theQuotes=null;}
	if(isset($_REQUEST['quotePackName'])){$quotePackName=sanitize_text_field($_REQUEST['quotePackName']);} else {$quotePackName=null;}

	/***
	 * If we're creating a NEW QuotePack
	 */
	if($fnc == "doQuotes") {
		$newName = htmlentities($Name);
		$quotePackName = str_replace(" ", "-", strtolower(preg_replace('/[^a-zA-Z0-9\040\-]/', "", $newName)));
		$quotePackName = preg_replace("/&#?[a-z0-9]+;/i", "", $quotePackName);
		$theQuotePackFile = $quotesDir . $quotePackName . '.txt';

		if(!file_exists($theQuotePackFile)) {
		/***
		 * If the file doesn't exist, create it!
		 */
			$quotePackArray = array();
			$today=date("M. jS, Y", time());
			$fileDetails = array(
				'Name' => $newName,
				'Date Updated' => $today,
				'Source' => $Source,
				'Notes' => $Notes
			);
			$quotePackArray['fileDetails'] = $fileDetails;

			$Quotes = array();
			for ($i = 0; $i <= 49; $i++) {
				if($multiAuthor == "No") {
					$theAuthor = stripslashes($singleAuthor);
				} else {
					$theAuthor = stripslashes($theAuthors[$i]);
				}
				$theQuote = stripslashes($theQuotes[$i]);
				if($theAuthor != "" && $theQuote != "") {
					$Quotes[] = array(
						"Quote" => $theQuote,
						"Author" => $theAuthor
					);
				}
			}
			$quotePackArray['Quotes'] = $Quotes;
			$quotePackString = json_encode($quotePackArray, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
			$quotePackString = str_replace("&amp;", "&", $quotePackString);
			file_put_contents($theQuotePackFile, mb_convert_encoding($quotePackString, 'UTF-8'));
		} else {
			/***
			 * The file exists, throw an error and highlight it.
			 */
			$errNo = 1;
			$errMsg = 'The file name, <u>' . $quotePackName . '.txt</u> already exists as a Quotopia Quote Pack. Please change the name (below).';
		}
	}

/***
 * Assign a background color if $errNo is greater than 0.
 */
	$bgColor = ($errNo == 0) ? '' : ' class="error"';

/***
 * Let's show the WP Admin interface!
 */
	echo '
	<h1 class="bdCTR">Quotopia, v' . constant("quotopiaVersion") . '</h1>
	<div class="bdTabs">
<!-- bdTabs Navigation Tabs -->
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab1" checked >
		<label class="bdLabel" for="bdTab1"><i class="dashicons dashicons-shortcode"></i><span>Shortcode Builder</span></label>
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab2">
		<label class="bdLabel" for="bdTab2"><i class="dashicons dashicons-editor-quote"></i><span>Build a Quote Pack</span></label>
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab3">
		<label class="bdLabel" for="bdTab3"><i class="dashicons dashicons-info-outline"></i><span>About Quotopia</span></label>
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab4">
		<label class="bdLabel" for="bdTab4"><i class="dashicons dashicons-universal-access"></i><span>More BD Plugins</span></label>
		<input type="hidden" id="quotopiaTextQP" name="quotopiaTextQP" />

		<input type="hidden" id="quotopiaTextAuthor" name="quotopiaTextAuthor" />
		<input type="hidden" id="quotopiaTextCycle" name="quotopiaTextCycle" />
		<input type="hidden" id="quotopiaTextDisplay" name="quotopiaTextDisplay" />
		<input type="hidden" id="quotopiaCycleFormat" name="quotopiaCycleFormat" />

<!-- bdTabs Content Tabs -->
		<div id="bdTab-content1" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<h3 class="bdCTR">You have <a href="#startQuotePacks">' . $quotePacks . ' Quote Packs</a> present.</h3>
						<div>
							<fieldset>
								<legend>Basic options (be sure to select which Quote Pack to use!)</legend>
								<dl class="fancyList">
									<dt>Quote Pack to use</dt>
									<dd>
										<select id="quotopiaPack" name="quotopiaPack">
											<option>Select a Quote Pack...</option>
';

	/***
	 * Show the details for each Quote Pack available...
	 */
	foreach(glob($quotesDir . '*.txt') as $file) {
		list($fileInfo, $allQuotes) = quotopia_read_json_file($file);
		$theName = $fileInfo['Name'];
		$totalQuotes = count($allQuotes);
		$identifier = basename($file, ".txt");
		echo '
											<option value="' . $identifier . '">' . $theName . ' (' . $totalQuotes . ' quotes)</option>';
		unset($fileInfo, $allQuotes);
	}

	echo '
										</select>
									</dd>
									<dt>Show Author section?</dt><dd><select id="quotopiashowhideAuthor" name="quotopiashowhideAuthor"><option value="1">Yes</option><option value="0">No</option></select></dd>
								</dl>
							</fieldset>
						</div>
						<div><br />
							<fieldset>
								<legend>Quotopia Cycle options</legend>
								<dl class="fancyList">
									<dt>Fade In/Out speed</dt><dd><input id="quotopiacSpeed" name="quotopiacSpeed" value="500" size="5" /> (1000 = 1 second; 500 is default)</dd>
									<dt>Quote Display time</dt><dd><input id="quotopiadSpeed" name="quotopiadSpeed" value="6000" size="5" /> (1000 = 1 second; 6000 is default)</dd>
									<dt>Cycle Method</dt>
									<dd>
										<div class="bdCols3">
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="blind" /> Blinds</label></div>
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="bounce" /> Bounce</label></div>
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="drop" /> Drop</label></div>
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="explode" /> Explode</label></div>
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="fade" /> Fade in/out</label></div>
<!-- Future version.
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="fold" /> Fold</label></div>
-->
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="shake" /> Shake</label></div>
											<div><label><input class="quotopiaCycle" type="radio" name="quotopiaCycle" value="slide" /> Slide down/up</label></div>
										</div>
									</dd>
								</dl>
								<div>Fade in/out is the default quote cycle method.</div>
							</fieldset>
						</div>
						<br /><br id="startQuotePacks" />
						<h4 class="bdCTR">Available Quote Packs</h4>';

	/***
	 * Show the details for each Quote Pack available...
	 */
	$bdQtCNT = 0;
	foreach(glob($quotesDir . '*.txt') as $file) {
		list($fileInfo, $allQuotes) = quotopia_read_json_file($file);
		$totalQuotes = count($allQuotes);
		echo '
							<input id="bdCollapsible' . $bdQtCNT . '" class="bdTheToggle bdCEToggle" type="checkbox">
							<label for="bdCollapsible' . $bdQtCNT . '" class="bdCELabel" tabindex="' . $bdQtCNT . '">' . $fileInfo["Name"] . ' (' . $totalQuotes . ' Quotes)</label>
							<div class="bdCEContent">
								<div class="bdCEInner">
									<dl class="fancyList">
										<dt>Name</dt><dd>' . $fileInfo["Name"] . '</dd>
										<dt>Identifier</dt><dd>' . basename($file, ".txt") . '</dd>
										<dt>Date Updated</dt><dd>' . $fileInfo["Date Updated"] . '</dd>
										<dt>Source</dt><dd>' . $fileInfo["Source"] . '</dd>
										<dt>Notes</dt><dd>' . $fileInfo["Notes"] . '</dd>
										<dt># Quotes</dt><dd>' . $totalQuotes . '</dd>
									</dl>
								</div>
							</div>		';
		unset($fileInfo, $allQuotes);
		$bdQtCNT++;
	}

	echo '

					</div>
					<div class="bdColumn">
						<div id="bdSCcontainer">
							<div>
								<div><div class="bdExampleBorder">Progress, far from consisting in change, depends on retentiveness. When change is absolute there remains no being to improve and no direction is set for possible improvement: and when experience is not retained, as among savages, infancy is perpetual. Those who cannot remember the past are condemned to repeat it.</div><div class="quotopiaAuthor"><span class="theAuthor">George Santayana</span></div></div>
							</div>
							<textarea id="bdShortCode" class="bdCTR" name="bdShortCode" wrap="soft"></textarea>
							<div id="bdMsg" class="bdHide">Text copied into your clipboard. Paste where you need/want it.</div>
						</div>
						<div>
							<div>When all your options (to the left) are set, simply click on the short code above. It\'ll select and copy the entire shortcode automatically. You can paste that into a widget, inside a Gutenberg block, or in your Classic Editor interface.</div>
							<div><br />For experienced WordPress developers, you can do a &quot;do_shortcode()&quot; call into your theme file(s). HOPEFULLY you\'re using a child theme!</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bdTab-content2" class="bdTab-content">
			<div><strong>We now support both Multiple Authors and Single Authors for quote packs. Select the appropriate link below to pick whether your quotes have different authors or the same author.</strong></div>
			<br />
			<input id="bdCollapsible' . $bdQtCNT . '" class="bdTheToggle bdCEToggle" type="checkbox">
			<label for="bdCollapsible' . $bdQtCNT . '" class="bdCELabel" tabindex="' . $bdQtCNT . '">Quotes have Multiple Authors? Click here!</label>
			<div class="bdCEContent">
				<div class="bdCEInner">
					<form method="post">
						<input type="hidden" name="fnc" value="doQuotes" />
						<input type="hidden" name="multiAuthor" value="Yes" />

						<div class="bdCT">
							<div class="bdBox2">';
	if($errNo > 0) {
		echo '
								<div' . $bgColor . '>' . $errMsg . '</div>
								<br />';
	}

	echo '
								<dl class="fancyList">
									<dt' . $bgColor . '>Quote Pack Name</dt><dd' . $bgColor . '><input class="qInputText" type="text" name="Name" value="' . $Name . '" placeholder="Numbers, Letters and Spaces only" /> </dd>
									<dt>Source of Quotes</dt><dd><input class="qInputText" type="text" name="Source" value="' . $Source . '" placeholder="Wiki, Customers, website name, etc" /></dd>
									<dt>Notes</dt><dd><input class="qInputText" type="text" name="Notes" value="' . $Notes . '" placeholder="Anything important to share?" /></dd>
									<dt>Identifier</dt><dd>' . $quotePackName . '</dd>
								</dl>
							</div>
							<div class="bdBox2">
								<ol>
									<li>The three boxes to the left must be filled in. Typically Doug uses "Notes" for "Initial Release", but can be anything.</li>
									<li>A maximum of 50 items is allowed.</li>
									<li>Both boxes for each item must be filled in, or it will not be saved. Spaces for the "Author" is fine, but not recommended.</li>
									<li>If an existing quote pack by the same name exists, you will be alerted. Don\'t worry, you won\'t lose any of your submitted quotes. Just change the name of the quote pack.</li>
								</ol>
							</div>
						</div>

						<h3 class="bdCTR">The Quotes</h3>
						<div class="bdCT">';

	if(isset($theAuthors)) {
		$qCNT = count($theAuthors);
	} else {
		$qCNT = 0;
	}

	$n = 1;
	for ($i = 0; $i <= 49; $i++) {
		if($qCNT > 0) {
			$theAuthor = stripslashes($theAuthors[$i]);
			$theQuote = stripslashes($theQuotes[$i]);
		} else {
			$theAuthor = null;
			$theQuote = null;
		}

		echo '
							<div class="bdBox4">
								<div><strong>Quote #' . $n . '</strong></div>
								<input class="qInputText" type="text" name="theAuthors[]" value="' . $theAuthor . '" placeholder="Author or Customer name" /><br />
								<textarea class="quotopiaInputTextarea" name="theQuotes[]" placeholder="The quote or testimonial.">' . $theQuote . '</textarea>
							</div>';
		$n++;
	}
	echo '
						</div>
						<div class="bdCTR"><input type="submit" value="Create Quotopia Quote Pack" /></div>
					</form>
				</div>
			</div>';
	$bdQtCNT++;

	echo '
			<input id="bdCollapsible' . $bdQtCNT . '" class="bdTheToggle bdCEToggle" type="checkbox">
			<label for="bdCollapsible' . $bdQtCNT . '" class="bdCELabel" tabindex="' . $bdQtCNT . '">All quotes have the SAME Author? Click here, instead!</label>
			<div class="bdCEContent">
				<div class="bdCEInner">
					<form method="post">
						<input type="hidden" name="fnc" value="doQuotes" />
						<input type="hidden" name="multiAuthor" value="No" />

						<div class="bdCT">
							<div class="bdBox2">';

	if($errNo > 0) {
		echo '
					<div' . $bgColor . '>' . $errMsg . '</div>
					<br />';
	}

	if(!empty($theAuthors)) {
		$singleAuthor = stripslashes($singleAuthor);
		$qCNT = 1;
	} else {
		$singleAuthor = null;
		$qCNT = 0;
	}

	echo '
								<dl class="fancyList">
									<dt' . $bgColor . '>Quote Pack Name</dt><dd' . $bgColor . '><input class="qInputText" type="text" name="Name" value="' . $Name . '" placeholder="Numbers, Letters and Spaces only" /> </dd>
									<dt>Author</dt><dd><input class="qInputText" type="text" name="singleAuthor" value="' . $singleAuthor . '" placeholder="Author for ALL quotes" /></dd>
									<dt>Source of Quotes</dt><dd><input class="qInputText" type="text" name="Source" value="' . $Source . '" placeholder="Wiki, Customers, website name, etc" /></dd>
									<dt>Notes</dt><dd><input class="qInputText" type="text" name="Notes" value="' . $Notes . '" placeholder="Anything important to share?" /></dd>
									<dt>Identifier</dt><dd>' . $quotePackName . '</dd>
								</dl>
							</div>
							<div class="bdBox2">
								<ol>
									<li>The four boxes to the left must be filled in. Typically Doug uses "Notes" for "Initial Release", but can be anything.</li>
									<li>A maximum of 50 items is allowed.</li>
									<li>Any quote box left empty will not be saved in the quote file.</li>
									<li>If an existing quote pack by the same name exists, you will be alerted. Don\'t worry, you won\'t lose any of your submitted quotes. Just change the name of the quote pack.</li>
								</ol>
							</div>
						</div>

						<h3 class="bdCTR">The Quotes</h3>
						<div class="bdCT">';

	$n = 1;
	for ($i = 0; $i <= 49; $i++) {
		if($qCNT > 0) {
			$theQuote = stripslashes($theQuotes[$i]);
		} else {
			$theQuote = null;
		}

		echo '
							<div class="bdBox4">
								<div><strong>Quote #' . $n . '</strong></div>
								<textarea class="quotopiaInputTextarea" name="theQuotes[]" placeholder="The quote or testimonial.">' . $theQuote . '</textarea>
							</div>';
		$n++;
	}
	echo '
						</div>
						<div class="bdCTR"><input type="submit" value="Create Quotopia Quote Pack" /></div>
					</form>
				</div>
			</div>';
		$bdQtCNT++;

	echo '
		</div>
		<div id="bdTab-content3" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<div>
							<h3 class="bdCTR">About Quotopia</h3>
							<div>As any competent WordPress developer will tell you, it\'s never a good idea to modify a parent theme\'s files to achieve what you want. Yet, that\'s what I did for a little while on <a href="https://shecruits.com" target="_blank">SheCruits.com</a>: Modified a couple files that prevented me from being able to update the theme files as they came out.</div>
							<div><br />I hang my head in shame, because I know better. Then I sat down, thought about it and said &quot;There\'s got to be a better way to do this!&quot;. Welcome to the Quotopia plugin!</div>
							<div><br />While initially developed to cycle through various quotes (as seen on SheCruits.com), one of my long time friends (Hemmo in the shout outs below) asked if this would also work as a Testimonials plugin. Yep, it will! And, honestly, it\'ll handle anything you need cycled on your site.</div>
							<div><br />Since this is the initial release, there are some features that did not make it, but are being planned. Check through the list below (&quot;What\'s next&quot;) for planned additions.</div>
							<div><br />Be sure to check out the Quotopia section on <a href="https://bearlydoug.com/plugins/quotopia/" target="_blank">BearlyDoug.com</a> for shortcode examples and additional Quote Packs!</div>
							<div><br />For support, please visit the &quot;Support&quot; section for this plugin on WordPress.org (link coming on the next release!)</div>
						</div>
					</div>
					<div class="bdColumn">
						<h3 class="bdCTR">What\'s next for Quotopia?!</h3>
						<div>The following items are planned updates/enhancements, as this plugin moves forward. Not all of them will be implemented in the next release.&ensp;As I "tick off the checklist", I\'ll note the date/version that feature was added and move it to the bottom of each section.</div>
						<br />
						<ul class="bdList">
							<li>Overall Plugin</li>
							<li>Internationalization</li>
						</ul>
						<ul class="bdList">
							<li>Shortcode Builder</li>
							<li>More "cycle" options</li>
						</ul>
						<ul class="bdList">
							<li>Quote Packs (AKA &quot;QPs&quot;)</li>
							<li>Ability to Edit</li>
							<li>Ability to Upload (or bulk upload several) QPs through interface.</li>
							<li>Language rating (color scale)</li>
						</ul>
						<ul class="bdList">
							<li>NOW IMPLEMENTED</li>
							<li>Now supports Single or Multi-Author quotes. (v. 1.0.6)</li>
							<li>Multiple Quotopias running on a single page/section now supported. (v. 1.0.3)</li>
							<li>Change the ShortCode Builder layout to make it more user friendly. (v. 1.0.1)</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="bdTab-content4" class="bdTab-content">';

	/***
	 * Centralizing the latest news from BD Plugins...
	 */
	include plugin_dir_path( __FILE__ ) . "includes/BDPluginsNews.php";

	echo '
		</div>
	</div>';
}

/***
 * Checks to see if the Quotes folder exists upon activation. If it doesn't,
 * let's create it, or error out. We'll deal with creation errors in a future
 * version.
 */
function quotopia_install() {
	$quotesPluginFolder = plugin_dir_path( __FILE__) . 'quotes/';
	$uploadDir = wp_get_upload_dir();
	$quotesFolder = $uploadDir['basedir'] . '/quotes/';
	if(!file_exists($quotesFolder)) {
		if(wp_mkdir_p($quotesFolder)) {
			quotopia_move_files_to_upload($quotesPluginFolder, $quotesFolder);
		}
	}
 }
register_activation_hook(__FILE__, 'quotopia_install');

/***
 * Reading a Quote file to pull the JSON string and return both the file details AND the quotes...
 */
function quotopia_read_json_file($File){
	$BDdata = file_get_contents($File);
	$BDdataSets = json_decode($BDdata, true);
	$BDfI = $BDdataSets['fileDetails'];
	$BDaQ = $BDdataSets['Quotes'];
	return array($BDfI, $BDaQ);
}

/***
 * Function to copy files around.
 */
function quotopia_move_files_to_upload($source_dir, $destination_dir) {
	$dir = opendir($source_dir);

	while($file = readdir($dir)) {
		if(($file != '.') && ($file != '..')) {
			if(is_dir($source_dir.'/'.$file)) {
				recursive_files_copy($source_dir.'/'.$file, $destination_dir.'/'.$file);
			} else {
				copy($source_dir.'/'.$file, $destination_dir.'/'.$file);
			}
		}
	}
	closedir($dir);
}

/***
 *	The ShortCode function...
 */
function quotopia_shortcode($atts) {
	$uploadDir = wp_get_upload_dir();
	$quotesDir = $uploadDir['basedir'] . '/quotes/';

	/***
	 * Setting up the defaults for the attributes, and sanitizing 'em.
	 */
	$qAttribs = shortcode_atts(array(
		'quotepack'		=> 'quotopia',
		'hideauthor'		=> 'no',
		'cyclespeed'		=> 500,
		'displayspeed'	=> 6000,
		'cycle'			=> 'fade'
	), $atts, 'quotopia');
	$qPack		= filter_var($qAttribs['quotepack'], FILTER_SANITIZE_STRING);
	$qotwIdentifier	= preg_replace('/\PL/u', '', $qPack);
	$hideauthor		= filter_var($qAttribs['hideauthor'], FILTER_SANITIZE_STRING);
	$cyclespeed		= filter_var($qAttribs['cyclespeed'], FILTER_SANITIZE_STRING);
	$displayspeed	= filter_var($qAttribs['displayspeed'], FILTER_SANITIZE_STRING);
	$cycleFormat	= filter_var($qAttribs['cycle'], FILTER_SANITIZE_STRING);

	/***
	 * Getting the correct quotes file and setting up the main quotes array.
	 */
	$file = $quotesDir . $qPack . '.txt';

	/***
	 * Making sure the quotes file actually exists. If not, revert back to default quotopia.txt
	 */
	if(!file_exists($file)) {
		$file = $quotesDir . 'quotopia.txt';
	}

	/***
	 * Now, let's get the quotes (and strip the file info)...
	 */
	list($fileInfo, $allQuotes) = quotopia_read_json_file($file);
	unset($fileInfo);

	/*
	 *	Building the output...
	 */
	$quotopiaQuote = '
	<div class="quotopia">';

	/***
	 * Output all the quotes for this file...
	 */
	$i = 1;
	foreach($allQuotes as $row) {
		$theQuote = $row['Quote'];
		$theAuthor = $row['Author'];

		$quotopiaQuote .='
		<div id="' . $qotwIdentifier . '-' . $i . '" class="quotopia">
			<div class="bdCTR">' . $theQuote . '</div>';

		if($hideauthor != "yes") {
			$quotopiaQuote .='
			<div class="quotopiaAuthor">' . $theAuthor . '</div>';
		}

		$quotopiaQuote .='
		</div>';

		$i++;
	}

	/***
	 * Processing the various cycle options...
	 */
	if($cycleFormat == "blind") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).toggle("blind", {direction: "down"}, 500).delay(6000).toggle("blind", {direction: "right"}, 500, cycle)';;
	} elseif($cycleFormat == "bounce") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).toggle("bounce", {times: 3}, 500).delay(6000).toggle("bounce", {times: 3}, 500, cycle)';;
	} elseif($cycleFormat == "drop") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).toggle("drop", {direction: "right"}, 500).delay(6000).toggle("drop", {direction: "up"}, 500, cycle)';;
	} elseif($cycleFormat == "explode") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).toggle("explode", {pieces: 16}, 500).delay(6000).toggle("explode", {pieces: 32}, 500, cycle)';;
	} elseif($cycleFormat == "shake") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).toggle("shake", {times: 4}, 500).delay(6000).toggle("shake", {times: 2}, 500, cycle)';;
	} elseif($cycleFormat == "slide") {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).slideDown(500).delay(6000).slideUp(500, cycle)';;
	} else {
		$quotopiaCycler = $qotwIdentifier . 'divs.eq(' . $qotwIdentifier . 'i).fadeIn(500).delay(6000).fadeOut(500, cycle)';;
	}

	$quotopiaQuote .= '
	</div>
	<script>
		var ' . $qotwIdentifier . 'divs = jQuery(\'div[id^="' . $qotwIdentifier . '-"]\').hide(), ' . $qotwIdentifier . 'i = store.get(\'' . $qotwIdentifier . 'ID\');
		(function cycle() {
			' . $quotopiaCycler . '
			' . $qotwIdentifier . 'i = ++' . $qotwIdentifier . 'i % ' . $qotwIdentifier . 'divs.length;
			store.set(\'' . $qotwIdentifier . 'ID\', ' . $qotwIdentifier . 'i);
		})();
	</script>
';
	/*
	 *	Output it!
	 */
	return $quotopiaQuote;
}
add_shortcode('quotopia', 'quotopia_shortcode');
?>
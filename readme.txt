=== Quotopia ===
Contributors: BearlyDoug
Plugin URI: https://wordpress.org/plugins/quotopia/
Donate link: https://paypal.me/BearlyDoug
Tags: Quotes, Famous Quotes, Custom Quotes, Testimonial, Testimonials, Inspirational Quotes, Life Quotes, Sports Quotes, Quote shortcode, Quotes shortcode, Quote plugin, Quotes plugin
Requires at least: 5.2
Tested up to: 6.4.1
Stable tag: 1.0.7
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Yet another quotes plugin. Allows you to load custom quotes (or testimonials) for whatever needs your website has. Quotes are loaded via text files; no database additions needed. Can customize many aspects of the display, using the shortcode builder page.

== Description ==
Yet another quotes plugin. Allows you to load custom quotes for whatever needs your website has. Quotes are loaded via text files; no database additions needed. Can customize many aspects of the display, using the shortcode builder page.

What sets this apart from other quote plugins is the fact that our plugin actually remembers the last quote or testimonial that was displayed and will display the next quote if the page gets refreshed. This keeps the cycle continuously moving forward, as someone navigates through your site.

Comes with a Quotes Pack builder interface, which allows you to load your own favorite quotes or customer testimonials. All quote packs are saved inside your wp-content/uploads directory in a "quotes" folder, so you never lose them, even if you deactivate this plugin. Quote files are situated in a JSON format, with a limit of 50 quotes, per file.

This keeps your server overhead low and frees this plugin from any database requirements.

**Current Version 1.0.7**

= Features: = 
* Quotopia Shortcode builder allows you to customize most aspects of the quote.
* Works anywhere you can use shortcode.
* Responsive, width-wise. Height of div will adjust automatically (longer quotes may present issues if this is used in a header of a website).
* Don't want to use any of the existing quote packs? Not a problem, build your own via the Quote Pack builder!

This plugin is not compatible with WordPress versions less than 5.0. Requires PHP 5.6+.

= TROUBLESHOOTING: =
* Check the FAQs/Help located on WordPress' Plugin page, or the Support forum on WordPress.org's plugin area.
* Please be aware that Quotopia can only work correctly once per page. If you have this in your sidebar, header or footer, you cannot include it on a post or page. This should be addressed in the next version.
* Quotopia has been extensively tested with both jQuery version 1.12.4 and 3.5.1, without any issues. If you don't see the quotes cycling, ensure you are allowing javascript to run.

== Installation ==

= If you downloaded this plugin: =
1. Upload the 'quotopia' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "Quotopia".
4. Follow the directions on the Shortcode Builder tab, create a new quotes library under the Quotes Builder tab, etc.

= If you install this plugin through WordPress 2.8+ plugin search interface: =
1. Click "Install", once you find the "Quotopia" plugin.
2. Activate the plugin through the 'Plugins' menu.
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "Quotopia".
4. Follow the directions on the Shortcode Builder tab, create a new quotes library under the Quotes Builder tab, etc.

== Frequently Asked Questions ==
** As this is the first release of Quotopia, FAQs are a little minimal right now ** 

= Why is the Shortcode Builder not working? =
Check to make sure you've got JavaScript enabled on your browser. Also make sure jQuery is working on your site.

= Where's the widget for this? Gutenberg block?! =
Coming in a future version, I promise!

= Why is the Admin interface not in [LANGUAGE] language? =
Internationalization will be coming very soon.

= What's with the animated bear icon / Why "BearlyDoug"? =
You'll need to check out the plugin and click on "BD Plugins" after you activate this plugin. :)

= Why free? Do you have a commercial version, too? =
Because I want to give back to the community that has given so much to me, no. What you see is what you get.WordPress has allowed me to advance my career and put me into a position where I'm doing okay. That said, you can still support this plugin (and others, as I release them) by hittin' that "Donate" link over on the right.

== Screenshots ==
1. Shortcode Builder.
2. Quote Packs listing.
3. Create Quote Pack.
4. Quotopia, in action

== Changelog ==
= TODO =
* Additional cycle methods
* Editing Quote Packs
* Upload Quote Packs
* Quote Pack language rating system
* Quotopia Widget and Gutenberg block
* Proper Internationalization

= 1.0.7 =
* Bumped supported WordPress version to 6.4.1
* Centralized the content under the "More BD Plugins" tab, allowing me to edit just one file for ALL plugins.
* Minor changes to the main functions-bd.php page.

= 1.0.6 =
* Standardized the wp-admin side CSS file for all plugins.
* Moved "All Quote Packs" info to below the ShortCode builder tab, under the shortcode builder, to allow for the "More BD Plugins" tab that is now standard across all my plugins.
* Introduced a "More BD Plugins" tab, linking to current plugins, announcing future planned plugins and relocated the "Support me/this plugin!" request to that page.
* You can now attribute multiple quotes to a single author, in addition to multiple quotes from multiple authors.

= 1.0.5 =
* Fixed the recursive sanitization function in functions-bd.php. WP's sanitize_text_field() does not work on arrays. I had located a recursive_sanitize_text_field() function and adapted it. Problem was that I forgot to do a second function renaming within the initial function to allow it to process arrays that go deeper than just one level. Fixed on Oct. 20th, 2020.
* Corrected a minor versioning issue within readme.txt
* Changed Admin side demo so people can see the cycle options in real time. Also changed quote to one of my favorite quotes.

= 1.0.4 =
* "About BearlyDoug" was broken; now fixed. (reported by Kirk Gomes, Oct. 17th, 2020)
* Now supports multiple quotes on one page (Oct. 17th, 2020)

= 1.0.3 =
* Changed minimum WP version supported (3.5.0 to 5.2 - Oct. 16th, 2020)

= 1.0.2 =
* Plugin URL added. (October 16th, 2020)
* Error showing $_GET['page'] undefined corrected. (reported by Kirk Gomes)
* Plugin was still displaying version 1.0.0; corrected.

= 1.0.1 =
* Minor improvements to sanitization processes, limited testing. (October 15th, 2020)
* Improved Shortcode Builder page.

= 1.0.0 =
* Initial Plugin development and launch, not released. (October 10th, 2020)

== Upgrade Notice ==
* Coming soon!
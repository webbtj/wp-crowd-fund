=== Plugin Name ===
Contributors: webbtj
Tags: crowdfunding, ecommerce, paypal
Requires at least: 3.4.2
Tested up to: 3.4.2
Stable tag: trunk

== TODO: ==
= For Launch =
- 		>>Admin JS -- check
- 		>>Front End JS -- check
- 		>>Anonymous Donations -- check
- 		>>PayPal Integration (Flexible Funding only using Express Checkout) -- check
- 		>>Extra Backer Fields (configurable) -- check
- 		>>API Comments -- check
- 		>>Cron to delete old holds -- think it's done, cron registers correctly, sql query works, just need to confirm cron 		actually runs -- check
- 		>>Fix Issue wth DateTime object -- check

= CAN Wait Until Shortly After Beta Launch =
- Generate report of sold perks/backer info (can wait until after launch)
- Admin CSS
- Remove/Hide Fixed Funding Option (for now)
- Interface to provide API Credentials
- Interface to provide titles/descriptions for a "product"

= Required For Public Launch =
- Front End CSS
- Settings Page (if applicable based on existing functionality)
	- How long are holds?

= Roadmap =
- Adaptive Payments Integration (Pre-auth)
- Flex-funding options: Pre-Auth (Adaptive) or Instant Billing (Payments Pro)
- "Blind" Donation - for fixed funding campaigns, campaigner gets these donations regarless of goal
- Chained Recipients (Adaptive) - specify a paypal account for the campaigner where most of the money goes (with a cut going to an admin account[optional])

== Description ==

This plugin provides crowd funding functionality to your WordPress website. Create unlimited campaigns with unlimited "perks" or "give backs". Create multiple perks of the same value for the same campaign. Contributors can choose any available perk with a value equal to, or less than their donation. Choose a fixed or flexible funding campaigns (or some of each). Payments are collected one a campaign is complete.

Features:
- Create any number of campaigns
- Create both fixed and flexible funding campaigns
- Create any number of give backs for a campaign
- Create multiple give backs of the same dollar value for the same campaign
- Set maximum available quantities for limited items in give backs
- Global configurations for PayPal API integration
- Create special instructions for contributors on checkout (ask them to provide extra information in the checkout comments such as mailing address for physical give backs or clothing size if applicable)
- Collect contributor name, email address and extra comments on checkout
- Very easy to use API for integrating the plugin into other plugins or custom themes
- Pre-designed templates found in the templates/ directory of the plugin
- Templates can be overridden by creating a file of the extact same name in your current theme's directory

Administer Global Campaign Limitations:
- Maximum campaign length
- Maximum campaign value
- Maximum number of give backs
- Maximum give back value
- Available campaign types (flexible and fixed)

== Installation ==

1. Extract the zip and add the directory wp-crowd-fund/ to your wp-content/plugins/ directory.
2. Activate the plugin via the WordPress Plugins menu
3. Visit the Crowd Fund configuration screen under the settings menu to set limitations and PayPal API credentials.

== Screenshots ==

1. The funding column.

== Changelog ==

= 0.0.5 =
Numbers of backers was always returning all backers [wpcf_backers] *fixed*
Contribution dollar values were always returning all contributions [wpcf_contributed] *fixed*
Contribution percentage was not accounting for the commas, so a goal of $12,000 was treated as 12,
	so the percentages were extremely off.

= 0.0.4 =
Added Perk and Backer variables to the payment process response pages. Vars are:
- $backer
- $backer_custom
- $backer_title
- $backer_description
- $backer_email
- $backer_amount
- $perk
- $perk_custom
- $perk_cost
- $perk_limit
- $perk_sold
- $perk_hold
- $perk_title
- $perk_description
Any variables without a value will be a boolean false. There may have been other minor changes since
0.0.3 regarding payment processing and some template tags.

= 0.0.3 =
PayPal Express Checkout Integration (API Creds hard coded for now)
Wrap rendered templates in classed divs
Output buffer to catch content rendered by plugin and put it into the content (instead of echoing in place)
Fix DateTime issue, some configutations may not have the DateTime module, just do time stamp math for now

= 0.0.2 =
Update some requirements/todo and roadmap stuff
Added Anonymous field
Changes to extra fields template structure to allow specifying if a field is required or required
Add JS logic and frontend-processing logic to handle required additional fields

= 0.0.1 =
Fixed some stub/missing api/template functions
Commented/Documented api/template functions
Custom Fields (configurable with template)
Put items on hold once they are requested
Finished some missing logic Perk Buy/Cancel work-flows
Disable admin input fields for perks with at least one unit sold
Frontend JS nice-ness, see js/wp-crowd-fund.js for details.
Moved some stuff that wasn't really part of the templating API into a "core.php" file

= 0.0.0 =
Initial github push
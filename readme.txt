=== Google Calendar Weekly Timetable ===
Contributors: Aurimas Kubeldzis
Donate link: http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/donate
Tags:  google calendar, timetable, schedule, weekly, google, calendar, event, events, 
Requires at least: 2.9.2
Tested up to: 3.3
Stable tag: 0.3.3

Displays Google Calendar(s) as custom styled weekly timetable/schedule in your blog. Easy to manage complex timetables from google calendar.

== Description ==

Parses Google Calendar Feeds for a week and automatically displays them as weekly timetable in a post. Makes it easy to manage complex schedules in centralized control panel. It's easy to split into subtables (for different cities, groups) and make future changes to timetable. 

= Features =

* Automatically forms lines for times that have events on (conserves vertical space compared to usual Google Calendar embedded weekly view).
* Events from multiple Google Calendar feeds can be shown in single timetable and vice versa.
* Choose different color for each feed.
* Format the style of table easily by making changes to CSS
* Options for display of time in 12-hour (PM/AM) or 24-hour time formats. 
* Support for different time formats, time zones and week days. 
* Multiple language support

Please visit the plugin homepage for how to get started and other help:

* [Plugin Homepage](http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/)

== Installation ==

Please send me any suggestions (bugs/features). Use comments or contact form on plugin homepage.

Common plugin installation instructions:

Use the automatic installer from within the WordPress administration, or:

1. Download the `.zip` file by clicking on the Download button on the right
1. Unzip the file
1. Upload the `google-calendar-weekly-timetable` directory to your `plugins` directory
1. Go to the Plugins page from within the WordPress administration
1. Click Activate for Google Calendar Events

After activation a new Google Calendar Weekly Timetable options menu will appear under Settings. You can now start adding feeds. 

SHORT INSTRUCTIONS FOR EXPERTS:

Copy XML url of public google calendar. In your GCWT wordpress settings Add feed pasting this url in `Feed URL`. Use WP shortcode in post/page of format `[timetable id="<list_of_feeds_separated_by_comma"]`. Use `css/gcwt-style.css` file to modify the style, use `.gcwt-table{}` table id.

Put events on endless repeat in google calendar. If you plan no changes - leave it like that, whenever you have plans for changes - change it from that date. If you really plan no changes, you can put start date with shortcode option start="18 april 2010" then it will always pick the same week.

INSTRUCTIONS FOR INTERMEDIATE USERS:

1. Create Google Calendar (or use existing one). Put some events and make them repeat forever. Go to Calendar Settings -> Share this calendar (second tab). Click on Make this calendar public. Save.
2. Again, go to Calendar Settings -> Calendar Details (first tab). Scroll down to calendar address and click on the XML button. COPY the pop-up link.
3. Go back to your wordpress admin panel. Go to Settings -> Google Calendar Weekly Timetable. Click on Add Feed. Name the calendar in `Feed Title` field and PASTE the link from previous step to `Feed URL` field.
4. Change other options if you need to and choose feed color (default is yellow, by accident). Click `Add Feed`. Notice the `Feed ID`.
5. Now go to the post/page that you want to insert the table to. Add shortcode `[timetable id="1"]`. Here "1" means the `Feed ID` that you noticed in previous step is 1.
6. Repeat the steps to add more feeds. Then update the `id="1"` option in your shortcode to the list of IDs you want to add to this particular timetable, separated by commas. For example `[timetable id="1,2,3"]`.

Controlling additional options:

SHORTCODE OPTIONS

* lang="de" - this will:
a) change the names of the weekdays. By default English, Russian and Lithuanian are described in file google-calendar-weekly-timetable/weekdays.txt. You can edit it from Wordpress admin panel. Add language short code followed by colon, followed by name of Monday and all the other weekdays separated by spaces (follow the logic of what's already added).
b) Check the title for language matching. If the title is in format "Pirmas Žingsnis; en: First Step; de:Erster Stufe" then it will search for appropriate language and only that will be displayed. Notice that you should use semicolon before the language code and colon after (;en:). Note: if the language code is not found, it will try to return the default language (by looking for separator ;??:)

* start="previous Monday" - this specifies when is the start day to parse the 7 days (default is today, so it can be middle of week)
* append=' H:i' - appends a string to the end of the title and inserts any date/time that you can specify according to date() php function. The timestamp used is the one with the end time of event.
* cache=42000 - specify cache duration setting for all feeds for this table. This will override individual settings for each feed. Use this if you want the table to be updated more/less frequently.
* refresh_one=1 - update/parse one feed at every refresh. This is a simple option for those who have too many feeds and too lazy to set different refresh times for each feed just to reduce the wait time. Note: individual feed cache settings are still valid (if some other feeds will expire, they will be refreshed together)

Options for when something doesn't work

* rowspan=0 - will turn of the feature of events taking more cells down if start time matches that (is enabled by default)
* time_format='H:i' - To specify time format 'H:i' is military and 'h:i' is 12-hour (see php date fuction for more options). By default it will pick settings from wordpress settings
* start_sunday=1 - Sunday is the first day of the week. By default it will pick settings from wordpress settings. Only Sunday and Monday supported.
* show_sunday=1 - Always show sunday, no matter if there are events or not.


STYLE CONTROL:
The simpliest way to edit table style is to edit `google-calendar-weekly-timetable/css/gcwt-style.css` file. The table has `id="timetable"` which is inherited by `class="gcwt-table"` class assigned inside `<div>` tag... so you can edit existing options or add additional using `.gcwt-table #timetable{<options>}`. 

== Screenshots ==

1. Google Calendar Weekly Timetable
2. You can separate calendars to multiple timetables


== Changelog ==

= 0.3.2 =
* Fix: Any language support - week day names defaults to WP language (set by define ('WPLANG', '');)
* Fix: "Start time" string removed from upper-left cell
* Bug fix: some problems with events not being displayed if there are too many of them

= 0.3.1 =
* Fix: Now it cache_duration works together with refresh_one shortcode options.
* Bug fix: The language code wasn't working correctly for title parsing in different languages. Also added documentation
* Changes to readme.txt.

= 0.3 =
* New feature: Event titles in multiple languages
* New feature: Now can specify time format instead of just switch between 12 and 24 hour clocks. However, it is picked up from wordpress by default so use this only if it doesn't work.
* New feature: User can now set table caption.
* New feature: Can specify that one feed would be parsed at every refresh of table. Use refresh_one=1 in shortcode.
* Bug fix: Fix for php4, where the plugin won't install
* Change: automatically pick 7 days according to the day the week starts on (before it would start today so on the week days that are already passed this week table would show next weeks schedule, while showing this weeks shedule in future days. You can get that back by adding start="today")

= 0.2.1 =
* New feature: Timezone, time format and week starts on settings are set automatically from wordpress by default 
* New feature: Shortcode option to set cache duration for a table (overrides individual settings) 
* New feature: Comments and manual on how to edit CSS
* New feature: If no arguments are passed with id=”" then all the feeds (if any) are automatically included 
* New feature: New shortcode [timetable]

= 0.2 =
* New feature: Start day of week can be chosen (Monday or Sunday). Use start_sunday=1 in shortcode and make sure to specify Sunday as a start date with start="previous Sunday"
* New feature/fix: Events can now occupy more blocks down if the times match and it doesn't go over other event. Use rowspan=0 shortcode option to disable.
* New feature: Can now specify exactly what 7 days to pick according to strtotime() php function. Using start="previous Monday" (ex.) in shortcode will render current week from Monday to Sunday
* Added "start time" to table left top cell. Indicating that you have start times on the left column.
* Bug fix: Adaptation to some recent google changes concerning https and http

= 0.1.2 =
* Bug fix: php warnings in case of no events in the feed
* Bug fix: Incompatable with Google Calendar Events and other plugins that use SimplePie(GCalendar). This is major code change, most function names changed, also database names.
* Bug fix: Background color and some other CSS options don't show up
* Bug fix: Table goes over widgets (oversized)
* Bug fix: Gets more events than it's supposed to (futher than a week)

= 0.1.1 =
* Changes to readme.txt.

= 0.1 =
* Initial release.

== Upgrade Notice ==

= 0.3.1 =
Minor bug fixes. Make sure to have a copy of your custom stylesheet before upgrade.

== Frequently Asked Questions ==

Please visit the [plugin homepage](http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/) and leave a comment for help, or [contact me](http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/contact-me) directly.
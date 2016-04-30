# Mozilla Web Content Dashboard
[Webdashboard] used to track localization status of Mozilla web projects.

## Installation
System requirements:
* PHP 5.5 or later.
* HTTP server (Apache, nginx, internal PHP server).

Setup:
* Rename or copy the file `app/config/settings.inc.php.ini` as `app/config/settings.inc.php`. You will need to customize it only if you plan to use local instances of [Langchecker] and [Webstatus].
* Install [Composer] (PHP dependency manager), either locally or globally, then install the dependencies by running `php composer.phar install` from the project's root folder.
* Make sure that the `/cache` folder is writable by the user running PHP.

# License
This software is released under the terms of the [Mozilla Public License v2.0].

[Composer]: https://getcomposer.org/download/
[Langchecker]: https://github.com/mozilla-l10n/langchecker/
[Mozilla Public License v2.0]: http://www.mozilla.org/MPL/2.0/
[Webdashboard]: https://github.com/mozilla-l10n/webdashboard
[Webstatus]: https://github.com/mozilla-l10n/webstatus/

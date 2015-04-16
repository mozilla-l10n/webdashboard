<?php
/*
This is the json source used on the old Product dashboard view
in the 4th column for Web bugs:
https://l10n.mozilla.org/shipping/dashboard?locale=ta-LK

The url fetched by Pike's dashboard is:
https://l10n.mozilla-community.org/webdashboard/json/?tag=URGENT

This is set up only for compatibility reasons with the old product
dashboard. When the old view using that data source is deleted, we should
retire this code too.

We need to heavily cache this feed because the new Web dashboard makes per
locale queries on Bugzilla and no longer stores a manually maintained array.
Given that the json feed is not per locale but for all locales, that means
about a hundred queries that could stall the product dashboard view.
We should just use that for 6 hours and maybe regenerate the queries via
a cron job at night.

The Json data should have this format:
{
"types": {
  "Webbugs": {
    "pluralLabel": "Webbugs"
  }
},
"properties": {
  "total_webbugs": {
    "valueType": "number"
  },
  "missing_webbugs": {
    "valueType": "number"
  }
},

"items": [
  {
    "type": "Webbugs",
    "label": "ach",
    "missing_webbugs": 1,
    "total_webbugs": 2
  }
}

missing_webbugs and total_webbugs should just be the same number.
Historically, missing was bugs marked as 'URGENT' in the local array
and actually represented bugs needing to be resolved on the web to be
able to ship a release. We no longer make this distinction on the web
dashboard.

*/
namespace Webdashboard;

use Bugzilla\Bugzilla;
use Cache\Cache;

require_once __DIR__ . '/../config/init.php';

// Include all data about our locales
include __DIR__ . '/../data/locales.php';

$results = [];
$results['types']["Webbugs"] = ["pluralLabel" => "Webbugs"];
$results['properties']["total_webbugs"] = ["valueType" => "number"];
$results['properties']["missing_webbugs"] = ["valueType" => "number"];

// All opened bugs for a locale in the mozilla.org/l10n component
foreach ($locales as $locale) {
    $bugzilla_query = 'https://bugzilla.mozilla.org/buglist.cgi?'
                    . 'f1=cf_locale'
                    . '&o1=equals'
                    . '&query_format=advanced'
                    . '&v1=' . urlencode(Bugzilla::getBugzillaLocaleField($locale))
                    . '&o2=equals'
                    . '&f2=component'
                    . '&v2=L10N'
                    . '&bug_status=UNCONFIRMED'
                    . '&bug_status=NEW'
                    . '&bug_status=ASSIGNED'
                    . '&bug_status=REOPENED'
                    . '&classification=Other'
                    . '&product=www.mozilla.org';

    /* Check if there is a cached request for this element.
     * For this request I use a ttl of 6 hours instead of default (15 minutes),
     * since it requires a lot of time.
     */
    $cache_id = "bugs_mozillaorg_{$locale}";
    $bugs = Cache::getKey($cache_id, 6 * 60 * 60);
    if ($bugs === false) {
        $csv = file($bugzilla_query . '&ctype=csv');
        $bugs = Bugzilla::getBugsFromCSV($csv);
        Cache::setKey($cache_id, $bugs);
    }

    // Generate all the bugs
    $results['items'][] = [
        "type"            => "Webbugs",
        "label"           => $locale,
        "missing_webbugs" => count($bugs),
        "total_webbugs"   => count($bugs),
    ];
}

$json_data = new Json;
print $json_data->outputContent($results, false, true);

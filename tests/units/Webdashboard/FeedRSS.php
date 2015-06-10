<?php
namespace tests\units\Webdashboard;

use atoum;
use Webdashboard\FeedRSS as _FeedRSS;

require_once __DIR__ . '/../bootstrap.php';

class FeedRSS extends atoum\test
{
    public function testBuildRSS()
    {
        $item = [
            'Bug 123456789: Test bug',
            'https://bugzilla.mozilla.org/show_bug.cgi?id=123456789',
            'Test bug',
        ];

        $obj = new _FeedRSS(
                'Test feed',
                'http://localhost',
                '[ab] Test feed generator',
                [$item]
        );

        $xml_content = file_get_contents(TEST_FILES . 'testfeed.xml');
        $xml_content = str_replace('%DATE%', date(DATE_RSS), $xml_content);

        $guid = sha1($item[0] . $item[1] . $item[2] . date('W'));
        $xml_content = str_replace('%GUID%', $guid, $xml_content);

        $this
            ->string($obj->buildRSS())
                ->isEqualTo($xml_content);
    }
}

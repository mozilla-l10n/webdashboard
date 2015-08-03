<?php
namespace tests\units\Webdashboard;

use atoum;
use Webdashboard\Utils as _Utils;

require_once __DIR__ . '/../bootstrap.php';

class Utils extends atoum\test
{
    public function testGetQueryParam()
    {
        $obj = new _Utils();

        $_GET['test_string'] = 'test';
        $_GET['test_bool'] = true;

        // Missing string param
        $this
            ->string($obj->getQueryParam('foo'))
                ->isEqualTo('');

        // Missing string param with fallback
        $this
            ->string($obj->getQueryParam('foo', 'bar'))
                ->isEqualTo('bar');

        // Existing param
        $this
            ->string($obj->getQueryParam('test_string'))
                ->isEqualTo('test');

        // Existing param
        $this
            ->boolean($obj->getQueryParam('test_bool', false))
                ->isTrue();

        // Missing boolean param
        $this
            ->boolean($obj->getQueryParam('foo', false))
                ->isFalse();

        unset($_GET['test_string']);
        unset($_GET['test_bool']);
    }

    public function secureTextDP()
    {
        return [
            ['test%0D', false, 'test'],
            ['%0Atest', false, 'test'],
            ['%0Ate%0Dst', false, 'test'],
            ['%0Ate%0Dst', true, 'test'],
            ['&test', false, '&amp;test'],
            [['test%0D', '%0Atest'], false, 'test'],
        ];
    }

    /**
     * @dataProvider secureTextDP
     */
    public function testsecureText($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->secureText($a, $b))
                ->isEqualTo($c);
    }
}

<?php

namespace Tests;

use RequestConverter\Setter;

/**
 * It's a fucking joke
 */
class SetterTest extends \PHPUnit_Framework_TestCase
{
    public function testItSetsA()
    {
        $s = new SetterTestSubject();
        $setter = new Setter($s);

        $setter->set("a", "foo");

        $this->assertSame("foo", $s->getA());
        $this->assertNull($s->getB());
    }

    public function testItSetsB()
    {
        $s = new SetterTestSubject();
        $setter = new Setter($s);

        $setter->set("b", "bar");

        $this->assertSame("bar", $s->getB());
        $this->assertNull($s->getA());
    }
}

class SetterTestSubject
{
    private $a, $b;

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }
}

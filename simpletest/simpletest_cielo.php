<?php
require_once('../../simpletest/autorun.php');

require_once('../../Cielo.class.php');

class TestOfRankings extends WebTestCase {
    function testWeAreTopOfGoogle() {
        $this->get('http://google.com/');
        $this->setField('q', 'simpletest');
        $this->click("I'm Feeling Lucky");
        $this->assertTitle('SimpleTest - Unit Testing for PHP');
    }
}
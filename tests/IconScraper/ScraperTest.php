<?php

namespace Mpclarkson\IconScraper;

use Mpclarkson\IconScraper\Scraper;

class ScraperTest extends \PHPUnit_Framework_TestCase {

    private $DEFAULT_FAV_CHECK = 'favicon.ico';
    private $TEST_LOGO_NAME = 'default.ico';
    private $RESOURCE_FAV_ICO;

    public function setUp() {
        $this->RESOURCE_FAV_ICO = __DIR__ . '/default.ico';
    }

    /**bin/phpunit -c
     * @covers Scraper::__construct
    * @uses Scraper
    */
    public function testUrlIsDefinedByConstructor() {
        $url = 'http://foo.bar';
        $args = array('url' => $url);
        $fav = new Scraper($args);
        $this->assertEquals($url, $fav->getUrl());
    }

    /**
    * @covers Scraper::baseUrl
    * @uses Scraper
    */
    public function testBaseFalseUrl() {

        $fav = new Scraper();

    	$notAnUrl = 'fgkljkdf';
    	$notPrefixedUrl = 'domain.tld';
    	$noHostUrl = 'http://';
    	$invalidPrefixUrl = 'ftp://domain.tld';
    	$emptyUrl = '';

    	$this->assertFalse($fav->baseUrl($notAnUrl));
    	$this->assertFalse($fav->baseUrl($notPrefixedUrl));
    	$this->assertFalse($fav->baseUrl($noHostUrl));
    	$this->assertFalse($fav->baseUrl($invalidPrefixUrl));
    	$this->assertFalse($fav->baseUrl($emptyUrl));
    }

    /**
    * @covers Scraper::baseUrl
    * @uses Scraper
    */
    public function testBaseUrlValid() {

        $fav = new Scraper();

    	$simpleUrl = 'http://domain.tld';
    	$simpleHttpsUrl = 'https://domain.tld';
    	$simpleUrlWithTraillingSlash = 'http://domain.tld/';
    	$simpleWithPort = 'http://domain.tld:8080';
    	$userWithoutPasswordUrl = 'http://user@domain.tld';
    	$userPasswordUrl = 'http://user:password@domain.tld';
    	$urlWithUnusedInfo = 'http://domain.tld/index.php?foo=bar&bar=foo#foobar';
    	$urlWithPath = 'http://domain.tld/my/super/path';

    	$this->assertEquals(self::slash($simpleUrl), $fav->baseUrl($simpleUrl));
    	$this->assertEquals(self::slash($simpleHttpsUrl), $fav->baseUrl($simpleHttpsUrl));
    	$this->assertEquals(self::slash($simpleUrlWithTraillingSlash), $fav->baseUrl($simpleUrlWithTraillingSlash));
    	$this->assertEquals(self::slash($simpleWithPort), $fav->baseUrl($simpleWithPort));
    	$this->assertEquals(self::slash($userWithoutPasswordUrl), $fav->baseUrl($userWithoutPasswordUrl));
    	$this->assertEquals(self::slash($userPasswordUrl), $fav->baseUrl($userPasswordUrl));
    	$this->assertEquals(self::slash($simpleUrl), $fav->baseUrl($urlWithUnusedInfo));
    	$this->assertEquals(self::slash($simpleUrl), $fav->baseUrl($urlWithPath, false));
    	$this->assertEquals(self::slash($urlWithPath), $fav->baseUrl($urlWithPath, true));
    }

    /**
    * @covers Scraper::info
    * @uses Scraper
    */
    public function testBlankInfo() {
        $fav = new Scraper();
        $this->assertFalse($fav->info(''));
    }

    /**
    * @covers Scraper::info
    * @uses Scraper
    */
    public function testInfoOk() {
        $fav = new Scraper();
        $dataAccess = $this->getMock('Mpclarkson\IconScraper\DataAccess');
        $header = array(
            0 => 'HTTP/1.1 200 OK',
        );
        $dataAccess->expects($this->once())->method('retrieveHeader')->will($this->returnValue($header));
        $fav->setDataAccess($dataAccess);

        $url = 'http://domain.tld';

        $res = $fav->info($url);

        $this->assertEquals($url, $res['url']);
        $this->assertEquals('200', $res['status']);
    }

    /**
    * @covers Scraper::info
    * @uses Scraper
    */
    public function testInfoRedirect() {
        $dataAccess = $this->getMock('Mpclarkson\IconScraper\DataAccess');
        $fav = new Scraper();
        $fav->setDataAccess($dataAccess);

        // Data
        $urlRedirect = 'http://redirected.domain.tld';
        $url = 'http://domain.tld';
        $headerRedirect = array(
            0 => 'HTTP/1.0 302 Found',
            'Location' => $urlRedirect,
        );
        $headerOk = array(0 => 'HTTP/1.1 200 OK');

        // Simple redirect
        $dataAccess->expects($this->at(0))->method('retrieveHeader')->will($this->returnValue($headerRedirect));
        $dataAccess->expects($this->at(1))->method('retrieveHeader')->will($this->returnValue($headerOk));

        $res = $fav->info($url);
        $this->assertEquals($urlRedirect, $res['url']);
        $this->assertEquals('200', $res['status']);

        // Redirect loop
        $dataAccess->expects($this->exactly(5))->method('retrieveHeader')->will($this->returnValue($headerRedirect));
        $res = $fav->info($url);
        $this->assertEquals($urlRedirect, $res['url']);
        $this->assertEquals('302', $res['status']);
    }

//
    /**
    //    * @covers Scraper::get
    //    * @uses Scraper
    //    */
//    public function testGetDefaultFavicon() {
//        $url = 'http://domain.tld/';
//        $fav = new Scraper(array('url' => $url));
//
//        $dataAccess = $this->getMock('Mpclarkson\IconScraper\DataAccess', array('retrieveHeader', 'retrieveUrl'));
//        $fav->setDataAccess($dataAccess);
//
//        // Header MOCK
//        $dataAccess->expects($this->any())->method('retrieveHeader')->will($this->returnValue(array(0 => 'HTTP/1.1 200 KO')));
//        $dataAccess->expects($this->any())->method('retrieveUrl')->will($this->returnValue(file_get_contents($this->RESOURCE_FAV_ICO)));
//
//        $this->assertEquals(self::slash($url) . $this->DEFAULT_FAV_CHECK, $fav->get()[0]['href']);
//    }
    /**
    * @covers Scraper::get
    * @uses Scraper
    */
    public function testGetFaviconEmptyUrl() {
    	$fav = new Scraper();
    	$this->assertEmpty($fav->get());
    }

    /**
    * @covers Scraper::get
    * @uses Scraper
    */
    public function testGetNotFoundFavicon() {
    	$url = 'http://domain.tld';
        $fav = new Scraper(array('url' => $url));

        $dataAccess = $this->getMock('Mpclarkson\IconScraper\DataAccess');
        $fav->setDataAccess($dataAccess);
        $dataAccess->expects($this->any())->method('retrieveHeader')->will($this->returnValue(array(0 => 'HTTP/1.1 404 KO')));
        $dataAccess->expects($this->any())->method('retrieveUrl')->will($this->returnValue('<head><crap></crap></head>'));

        $this->assertEmpty($fav->get());
    }

    /**
    * @covers Scraper::get
    * @uses Scraper
    */
    public function testGetNoHtmlHeader() {
    	$url = 'http://domain.tld/original';
        $logo = 'default.ico';
        $fav = new Scraper(array('url' => $url));

        $dataAccess = $this->getMock('Mpclarkson\IconScraper\DataAccess', array('retrieveHeader', 'retrieveUrl'));
        $fav->setDataAccess($dataAccess);

        $dataAccess->expects($this->any())->method('retrieveHeader')->will($this->returnValue(array(0 => 'HTTP/1.1 404 KO')));
        $dataAccess->expects($this->any())->method('retrieveUrl')->will($this->returnValue('<crap></crap>'));

        $this->assertEmpty($fav->get());
    }


    /**
     * Callback function for retrieveHeader in testGetExistingRootFavicon
     * If it checks default fav (favicon.ico), return 404
     * Return 200 while checking existing favicon
     **/
    public function headerExistingFav() {
        $headerOk = array(0 => 'HTTP/1.1 200 OK');
        $headerKo = array(0 => 'HTTP/1.1 404 KO');
        $args = func_get_args();

        if( strpos($args[0], $this->DEFAULT_FAV_CHECK) !== false ) {
            return $headerKo;
        }
        return $headerOk;
    }

    /**
     * Callback function for contentExistingFav in testGetExistingRootFavicon
     * return valid header, or icon file content if url contain '.ico'.
     * Return 200 while checking existing favicon
     **/
    public function contentExistingFav() {
        $xml = '<head><link rel="icon" href="'. $this->TEST_LOGO_NAME .'" /></head>';
        $ico = file_get_contents($this->RESOURCE_FAV_ICO);
        $args = func_get_args();

        if( strpos($args[0], '.ico') !== false ) {
            return $ico;
        }
        return $xml;
    }

    /**
     * Callback function for retrieveHeader in testGetOriginalFavicon
     * If it checks default fav (favicon.ico), return 404
     * Also return 404 if not testing original webdir (original/)
     * Return 200 while checking existing favicon in web subdir
     **/
    public function headerOriginalFav() {
        $headerOk = array(0 => 'HTTP/1.1 200 OK');
        $headerKo = array(0 => 'HTTP/1.1 404 KO');
        $args = func_get_args();

        if( strpos($args[0], 'original') === false || strpos($args[0], $this->DEFAULT_FAV_CHECK) !== false ) {
            return $headerKo;
        }

        return $headerOk;
    }

    /**
     * Callback function for retrieveUrl in testGetOriginalFavicon
     * Return crap if it we're not in web sub directory
     * Return proper <head> otherwise
     * Return img for final check
     **/
    public function contentOriginalFav() {
        $logo = 'default.ico';
        $xmlOk = '<head><link rel="icon" href="'. $logo .'" /></head>';
        $xmlKo = '<head><crap></crap></head>';
        $ico = file_get_contents($this->RESOURCE_FAV_ICO);
        $args = func_get_args();

        if( strpos($args[0], '.ico') !== false ) {
            return $ico;
        }
        if( strpos($args[0], 'original') === false ) {
            return $xmlKo;
        }

        return $xmlOk;
    }

    public static function slash($url) {
    	return $url . ($url[strlen($url) - 1] == '/' ? '' : '/');
    }
}

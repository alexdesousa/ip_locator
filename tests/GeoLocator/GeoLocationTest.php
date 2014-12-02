<?php
/**
 * @author Alexander De Sousa <alexanderjosedesousa@gmail.com>
 *
 * @brief GeoLocation object tests.
 */
class GeoLocationTest extends PHPUnit_Framework_TestCase {
    /**
     * Tests idempotency of transformation between types.
     */
    public function testIdempotency() {
        $gl       = new GeoLocator\GeoLocation("84559520","84559527","ES","SPAIN");
        $gl_array = $gl->toArray();
        $new_gl   = GeoLocator\fromArray($gl->toArray());

        return $this->assertEquals($gl, $new_gl);
    }

    public function testArrayIdempotency() {
        $gls = array();
        $gls[0] = new GeoLocator\GeoLocation("192.168.1.0","192.168.255.255","ES","Spain");
        $gls[1] = new GeoLocator\GeoLocation("192.168.1.0","192.168.255.255","VE","Venezuela");
        $gls[2] = new GeoLocator\GeoLocation("192.168.1.0","192.168.255.255","PT","Portugal");

        $new_gls = GeoLocator\fromUniversalFormat(GeoLocator\toUniversalFormat($gls));

        return $this->assertEquals($gls, $new_gls);

    }
}
?>

<?php

use opensrs\backwardcompatibility\dataconversion\domains\lookup\NameSuggest;

/**
 * @group backwardcompatibility
 * @group dataconversion
 * @group lookup
 * @group BC_NameSuggest
 */
class BC_NameSuggestTest extends PHPUnit_Framework_TestCase
{
    protected $validSubmission = array(
        'data' => array(
            'domain' => '',
            ),
        );

    /**
     * Valid conversion should complete with no
     * exception thrown.
     *
     *
     * @group validconversion
     */
    public function testValidDataConversion()
    {
        $data = json_decode(json_encode($this->validSubmission));

        $data->data->domain = 'phptest'.time().'.com';
        $data->data->maximum = '10';
        $data->data->nsselected = '.com;.net;.org';
        $data->data->lkselected = '.com;.net;.org';

        $shouldMatchNewDataObject = new \stdClass();
        $shouldMatchNewDataObject->attributes = new \stdClass();
        $shouldMatchNewDataObject->attributes->service_override = new \stdClass();
        $shouldMatchNewDataObject->attributes->service_override->lookup = new \stdClass();
        $shouldMatchNewDataObject->attributes->service_override->suggestion = new \stdClass();

        $shouldMatchNewDataObject->attributes->searchstring = $data->data->domain;
        $shouldMatchNewDataObject->attributes->services = array('lookup', 'suggestion');
        $shouldMatchNewDataObject->attributes->service_override->lookup->maximum =
            $data->data->maximum;
        $shouldMatchNewDataObject->attributes->service_override->lookup->tlds = array(
            '.com', '.net', '.org',
            );
        $shouldMatchNewDataObject->attributes->service_override->suggestion->maximum =
            $data->data->maximum;
        $shouldMatchNewDataObject->attributes->service_override->suggestion->tlds = array(
            '.com', '.net', '.org',
            );

        $ns = new NameSuggest();

        $newDataObject = $ns->convertDataObject($data);

        $this->assertTrue($newDataObject == $shouldMatchNewDataObject);
    }
}

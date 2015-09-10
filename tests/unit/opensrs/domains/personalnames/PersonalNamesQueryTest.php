<?php

use OpenSRS\domains\personalnames\PersonalNamesQuery;
/**
 * @group personalnames
 * @group PersonalNamesQuery
 */
class PersonalNamesQueryTest extends PHPUnit_Framework_TestCase
{
    protected $func = 'persQuery';

    protected $validSubmission = array(
        "data" => array(
            /**
             * Required
             *
             * domain: perosnal names domain
             *   to be queried
             */
            "domain" => "",

            /**
             * Optional
             * query_dns: request information about
             *   DNS settings
             * query_email: request information about
             *   associated email account
             *   * for both query_dns and query_email:
             *   0 = do not return (default)
             *   1 = return information wtih response
             */
            "query_dns" => "",
            "query_email" => ""
            )
        );

    /**
     * Valid submission should complete with no
     * exception thrown
     *
     * @return void
     *
     * @group validsubmission
     */
    public function testValidSubmission() {
        $data = json_decode( json_encode($this->validSubmission) );

        $data->data->domain = "john.smith.net";

        $ns = new PersonalNamesQuery( 'array', $data );

        $this->assertTrue( $ns instanceof PersonalNamesQuery );
    }

    /**
     * Data Provider for Invalid Submission test
     */
    function submissionFields() {
        return array(
            'missing domain' => array('domain'),
            );
    }

    /**
     * Invalid submission should throw an exception
     *
     * @return void
     *
     * @dataProvider submissionFields
     * @group invalidsubmission
     */
    public function testInvalidSubmissionFieldsMissing( $field, $parent = 'data', $message = null ) {
        $data = json_decode( json_encode($this->validSubmission) );

        $data->data->domain = "john.smith.net";

        if(is_null($message)){
          $this->setExpectedExceptionRegExp(
              'OpenSRS\Exception',
              "/$field.*not defined/"
              );
        }
        else {
          $this->setExpectedExceptionRegExp(
              'OpenSRS\Exception',
              "/$message/"
              );
        }



        // clear field being tested
        if(is_null($parent)){
            unset( $data->$field );
        }
        else{
            unset( $data->$parent->$field );
        }

        $ns = new PersonalNamesQuery( 'array', $data );
    }
}
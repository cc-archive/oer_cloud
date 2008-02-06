<?php

/**
 * Class for accessing the XML-RPC interface of OER Commons.
 */

class OERCommonsSearch {

    public $search_string = NULL;
    public $results = array();
    public $result_count = NULL;
    public $result_error = NULL;
    public $remote_method = "simpleSearch";
    public $remote_url = "http://www.oercommons.org/service/";
    public $base_url = "http://www.oercommons.org";

    # Class constructor
    function OERCommonsSearch() {

        # Don't continue if the PHP xmlrpc libs are not available
        if ( ! function_exists("xmlrpc_encode_request") ) {
            trigger_error("The PHP xmlrpc must be installed to use this class");
            exit;
        }

    }


    ##------------------------------------------------------------------##

    function prepareRequest($search_string) {
        # Encode the method and input for the XML-RCP server
        $xmlrpc_req = xmlrpc_encode_request($this->remote_method, $search_string);

        # Setup some connection parameters, including the XML-RPC request itself
        $req_opts = array (
            'http' => array (
                'method' => "POST",
                'header' => "Content-Type: text/xml",
                'content' => $xmlrpc_req
            )
        );

        return stream_context_create($req_opts);
    }

    ##------------------------------------------------------------------##

    /**
     * Carry out the actual search and if there are results, then drop them 
     * into a class-level varible, if not then set an error
     */
    function searchOERC($search_string) {
        $this->search_string = trim($search_string);
        $response = file_get_contents($this->remote_url, false, $this->prepareRequest($this->search_string));
        $xmlrpc_data = xmlrpc_decode($response);

        if ( xmlrpc_is_fault($xmlrpc_data) ) {
            $this->result_error = "xmlrpc Error: {$xmlrpc_data['faultCode']}\n\n {$xmlrpc_data['faultString']}";
        } else {
            $this->results = $xmlrpc_data[0];
            $this->result_count = count($this->results);
        }
    }

    ##------------------------------------------------------------------##

    /**
     * Don't return the results in a raw form, but organize them in to a 
     * reasonable format so that each array element actually contains a <div> 
     * with a single search result
     */
    function prettifyResults() {

        $pretty_results = array();

        if ( $this->result_count ) {
            $rs = $this->results;
            for ( $i = 0; $i < $this->result_count; $i++ ) {
                # $rs['subject'] is an array, so here we concatenate all of the 
                # subjects into in a single variable
                $subjects = "";
                if ( count($rs[$i]['subject']) > 0 ) {
                    foreach ( $rs[$i]['subject'] as $subject ) {
                        $subjects .=  "      $subject, ";
                    }
                    $subjects = rtrim($subjects, ", ");
                }
                $pretty_results[$i] = <<<HTML
<div class='result'>
    <div><a href='$this->base_url/{$rs[$i]['path']}'>{$rs[$i]['title']}</a></div>
    <div><strong>Subject</strong>: $subjects</div>
    <div><strong>Summary</strong>: {$rs[$i]['description']}</div>
</div>

HTML;
            }
        }

        return $pretty_results;
    }

    ##------------------------------------------------------------------##

}

?>

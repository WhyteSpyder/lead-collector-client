<?php
 /**
 * Created by Roger Dickey, Jr
 * rdickey@whytespyder.com
 * 8/30/14 10:38 AM
 */

namespace WhyteSpyder;

use OpenCloud\Rackspace;

class LeadCollector {
    private $username;
    private $api_key;
    private $ttl = 300;
    private $queue_region = 'DFW';
    private $queue_name = 'ws-lead-collector';

    public function __construct($username, $api_key) {
        $this->username = $username;
        $this->api_key = $api_key;
    }

    /**
     * @param $source
     * @param array $data
     * @throws \Exception
     */
    public function save($source, $data = array()) {
        try {
            $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
                'username' => $this->username,
                'apiKey' => $this->api_key,
            ));

            $service = $client->queuesService(null, $this->queue_region);

            $service->setClientId();

            $queue = $service->getQueue($this->queue_name);

            $queue->createMessage(array(
                'body' => array(
                    'source' => $source,
                    'form' => $data,
                ),
                'ttl' => $this->ttl
            ));
        }
        catch (\Exception $e) {
            throw new Exception(sprintf("Lead Collector: (%d) %s", $e->getCode(), $e->getMessage()));
        }
    }
} 
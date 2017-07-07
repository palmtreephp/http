<?php

namespace Palmtree\Http;

class RemoteUser
{
    /** @var string */
    protected $ipAddress;
    /** @var string */
    protected $userAgent;

    /** @var array */
    protected $serverVars;
    /** @var array */
    protected $trustedIpHeaders;

    /**
     * RemoteUser constructor.
     *
     * @param array $serverVars            Array of server vars. Defaults to the $_SERVER superglobal.
     * @param array $trustedIpHeaders      Array of headers to look for the user's IP address. The first matching header
     *                                     will be returned. Common IP headers for load balancers/proxies etc are:
     *                                     'HTTP_CLIENT_IP',
     *                                     'HTTP_X_FORWARDED_FOR',
     *                                     'HTTP_X_FORWARDED',
     *                                     'HTTP_X_CLUSTER_CLIENT_IP',
     *                                     'HTTP_FORWARDED_FOR',
     *                                     'HTTP_FORWARDED',
     *                                     'HTTP_X_REAL_IP',
     *                                     'REMOTE_ADDR',
     *
     */
    public function __construct($serverVars = [], $trustedIpHeaders = ['REMOTE_ADDR'])
    {
        if (empty($serverVars)) {
            $serverVars = $_SERVER;
        }

        $this->serverVars       = $serverVars;
        $this->trustedIpHeaders = $trustedIpHeaders;

        $this->userAgent = (isset($this->serverVars['HTTP_USER_AGENT'])) ? $this->serverVars['HTTP_USER_AGENT'] : '';
    }

    /**
     * @return mixed|string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        if ($this->ipAddress === null) {
            foreach ($this->getTrustedIpHeaders() as $header) {
                if (isset($this->serverVars[$header])) {
                    $ips = explode(',', $this->serverVars[$header]);
                    $ip  = trim(end($ips));
                    $ip  = filter_var($ip, FILTER_VALIDATE_IP);

                    if ($ip !== false) {
                        $this->ipAddress = $ip;

                        return $this->ipAddress;
                    }
                }
            }
        }

        return $this->ipAddress;
    }

    /**
     * @return array
     */
    public function getTrustedIpHeaders()
    {
        return $this->trustedIpHeaders;
    }

    /**
     * @param array $trustedIpHeaders
     *
     * @return $this
     */
    public function setTrustedIpHeaders($trustedIpHeaders)
    {
        $this->trustedIpHeaders = $trustedIpHeaders;

        return $this;
    }
}

<?php


namespace UTM\Browser;

/**
 * OS Detection.
 */
class Os
{
    public const UNKNOWN = 'unknown';
    public const OSX = 'OS X';
    public const IOS = 'iOS';
    public const SYMBOS = 'SymbOS';
    public const WINDOWS = 'Windows';
    public const ANDROID = 'Android';
    public const LINUX = 'Linux';
    public const NOKIA = 'Nokia';
    public const BLACKBERRY = 'BlackBerry';
    public const FREEBSD = 'FreeBSD';
    public const OPENBSD = 'OpenBSD';
    public const NETBSD = 'NetBSD';
    public const OPENSOLARIS = 'OpenSolaris';
    public const SUNOS = 'SunOS';
    public const OS2 = 'OS2';
    public const BEOS = 'BeOS';
    public const WINDOWS_PHONE = 'Windows Phone';
    public const CHROME_OS = 'Chrome OS';

    public const VERSION_UNKNOWN = 'unknown';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var bool
     */
    private $isMobile = false;

    /**
     * @var UserAgent
     */
    private $userAgent;

    /**
     * @param string|UserAgent|null $userAgent
     *
     * @throws \Sinergi\BrowserDetector\InvalidArgumentException
     */
    public function __construct($userAgent = null)
    {
        if ($userAgent instanceof UserAgent) {
            $this->setUserAgent($userAgent);
        } elseif (null === $userAgent || \is_string($userAgent)) {
            $this->setUserAgent(new UserAgent($userAgent));
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Return the name of the OS.
     *
     * @return string
     */
    public function getName()
    {
        if (!isset($this->name)) {
            OsDetector::detect($this, $this->getUserAgent());
        }

        return $this->name;
    }

    /**
     * Set the name of the OS.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Return the version of the OS.
     *
     * @return string
     */
    public function getVersion()
    {
        if (isset($this->version)) {
            return (string) $this->version;
        } else {
            OsDetector::detect($this, $this->getUserAgent());

            return (string) $this->version;
        }
    }

    /**
     * Set the version of the OS.
     *
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = (string) $version;

        return $this;
    }

    /**
     * Is the browser from a mobile device?
     *
     * @return bool
     */
    public function getIsMobile()
    {
        if (!isset($this->name)) {
            OsDetector::detect($this, $this->getUserAgent());
        }

        return $this->isMobile;
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        return $this->getIsMobile();
    }

    /**
     * Set the Browser to be mobile.
     *
     * @param bool $isMobile
     */
    public function setIsMobile($isMobile = true)
    {
        $this->isMobile = (bool) $isMobile;
    }

    /**
     * @return $this
     */
    public function setUserAgent(UserAgent $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return UserAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
}

<?php

namespace UTM\Browser;


class Browser
{
    public const UNKNOWN = 'unknown';
    public const VIVALDI = 'Vivaldi';
    public const OPERA = 'Opera';
    public const OPERA_MINI = 'Opera Mini';
    public const WEBTV = 'WebTV';
    public const IE = 'Internet Explorer';
    public const POCKET_IE = 'Pocket Internet Explorer';
    public const KONQUEROR = 'Konqueror';
    public const ICAB = 'iCab';
    public const OMNIWEB = 'OmniWeb';
    public const FIREBIRD = 'Firebird';
    public const FIREFOX = 'Firefox';
    public const SEAMONKEY = 'SeaMonkey';
    public const ICEWEASEL = 'Iceweasel';
    public const SHIRETOKO = 'Shiretoko';
    public const MOZILLA = 'Mozilla';
    public const AMAYA = 'Amaya';
    public const LYNX = 'Lynx';
    public const WKHTMLTOPDF = 'wkhtmltopdf';
    public const SAFARI = 'Safari';
    public const SAMSUNG_BROWSER = 'SamsungBrowser';
    public const CHROME = 'Chrome';
    public const NAVIGATOR = 'Navigator';
    public const GOOGLEBOT = 'GoogleBot';
    public const SLURP = 'Yahoo! Slurp';
    public const W3CVALIDATOR = 'W3C Validator';
    public const BLACKBERRY = 'BlackBerry';
    public const ICECAT = 'IceCat';
    public const NOKIA_S60 = 'Nokia S60 OSS Browser';
    public const NOKIA = 'Nokia Browser';
    public const MSN = 'MSN Browser';
    public const MSNBOT = 'MSN Bot';
    public const NETSCAPE_NAVIGATOR = 'Netscape Navigator';
    public const GALEON = 'Galeon';
    public const NETPOSITIVE = 'NetPositive';
    public const PHOENIX = 'Phoenix';
    public const GSA = 'Google Search Appliance';
    public const YANDEX = 'Yandex';
    public const EDGE = 'Edge';
    public const DRAGON = 'Dragon';

    public const VERSION_UNKNOWN = 'unknown';

    /**
     * @var UserAgent
     */
    private $userAgent;

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
    private $isRobot = false;

    /**
     * @var bool
     */
    private $isChromeFrame = false;

    /**
     * @var bool
     */
    private $isFacebookWebView = false;

    /**
     * @var bool
     */
    private $isCompatibilityMode = false;

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
     * Return the name of the Browser.
     *
     * @return string
     */
    public function getName()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->name;
    }

    /**
     * Check to see if the specific browser is valid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isBrowser($name)
    {
        return 0 == strcasecmp($this->getName(), trim($name));
    }

    /**
     * Set the version of the browser.
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
     * The version of the browser.
     *
     * @return string
     */
    public function getVersion()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return (string) $this->version;
    }

    /**
     * Set the Browser to be a robot.
     *
     * @param bool $isRobot
     *
     * @return $this
     */
    public function setIsRobot($isRobot)
    {
        $this->isRobot = (bool) $isRobot;

        return $this;
    }

    /**
     * Is the browser from a robot (ex Slurp,GoogleBot)?
     *
     * @return bool
     */
    public function getIsRobot()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isRobot;
    }

    /**
     * @return bool
     */
    public function isRobot()
    {
        return $this->getIsRobot();
    }

    /**
     * @param bool $isChromeFrame
     *
     * @return $this
     */
    public function setIsChromeFrame($isChromeFrame)
    {
        $this->isChromeFrame = (bool) $isChromeFrame;

        return $this;
    }

    /**
     * Used to determine if the browser is actually "chromeframe".
     *
     * @return bool
     */
    public function getIsChromeFrame()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isChromeFrame;
    }

    /**
     * @return bool
     */
    public function isChromeFrame()
    {
        return $this->getIsChromeFrame();
    }

    /**
     * @param bool $isFacebookWebView
     *
     * @return $this
     */
    public function setIsFacebookWebView($isFacebookWebView)
    {
        $this->isFacebookWebView = (bool) $isFacebookWebView;

        return $this;
    }

    /**
     * Used to determine if the browser is actually "facebook".
     *
     * @return bool
     */
    public function getIsFacebookWebView()
    {
        if (!isset($this->name)) {
            BrowserDetector::detect($this, $this->getUserAgent());
        }

        return $this->isFacebookWebView;
    }

    /**
     * @return bool
     */
    public function isFacebookWebView()
    {
        return $this->getIsFacebookWebView();
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

    /**
     * @param bool
     *
     * @return $this
     */
    public function setIsCompatibilityMode($isCompatibilityMode)
    {
        $this->isCompatibilityMode = $isCompatibilityMode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompatibilityMode()
    {
        return $this->isCompatibilityMode;
    }

    /**
     * Render pages outside of IE's compatibility mode.
     */
    public function endCompatibilityMode()
    {
        header('X-UA-Compatible: IE=edge');
    }
}

<?php
/**
 * Bsodergren\utm Media tool for load flags
 */

namespace UTM\Browser;

class OsDetector implements DetectorInterface
{
    /**
     * Determine the user's operating system.
     *
     * @return bool
     */
    public static function detect(Os $os, UserAgent $userAgent)
    {
        $os->setName($os::UNKNOWN);
        $os->setVersion($os::VERSION_UNKNOWN);
        $os->setIsMobile(false);

        self::checkMobileBrowsers($os, $userAgent);

        return
            // Chrome OS before OS X
            self::checkChromeOs($os, $userAgent)
            // iOS before OS X
            || self::checkIOS($os, $userAgent)
            || self::checkOSX($os, $userAgent)
            || self::checkSymbOS($os, $userAgent)
            || self::checkWindows($os, $userAgent)
            || self::checkWindowsPhone($os, $userAgent)
            || self::checkFreeBSD($os, $userAgent)
            || self::checkOpenBSD($os, $userAgent)
            || self::checkNetBSD($os, $userAgent)
            || self::checkOpenSolaris($os, $userAgent)
            || self::checkSunOS($os, $userAgent)
            || self::checkOS2($os, $userAgent)
            || self::checkBeOS($os, $userAgent)
            // Android before Linux
            || self::checkAndroid($os, $userAgent)
            || self::checkLinux($os, $userAgent)
            || self::checkNokia($os, $userAgent)
            || self::checkBlackBerry($os, $userAgent)
        ;
    }

    /**
     * Determine if the user's browser is on a mobile device.
     *
     * @return bool
     */
    public static function checkMobileBrowsers(Os $os, UserAgent $userAgent)
    {
        // Check for Opera Mini
        if (false !== stripos($userAgent->getUserAgentString(), 'opera mini')) {
            $os->setIsMobile(true);
        } // Set is mobile for Pocket IE
        elseif (false !== stripos($userAgent->getUserAgentString(), 'mspie')
            || false !== stripos($userAgent->getUserAgentString(), 'pocket')) {
            $os->setIsMobile(true);
        }
    }

    /**
     * Determine if the user's operating system is iOS.
     *
     * @return bool
     */
    private static function checkIOS(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'CPU OS')
            || false !== stripos($userAgent->getUserAgentString(), 'iPhone OS')
            && stripos($userAgent->getUserAgentString(), 'OS X')) {
            $os->setName($os::IOS);
            if (preg_match('/CPU( iPhone)? OS ([\d_]*)/i', $userAgent->getUserAgentString(), $matches)) {
                $os->setVersion(str_replace('_', '.', $matches[2]));
            }
            $os->setIsMobile(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Chrome OS.
     *
     * @return bool
     */
    private static function checkChromeOs(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), ' CrOS')
            || false !== stripos($userAgent->getUserAgentString(), 'CrOS ')
        ) {
            $os->setName($os::CHROME_OS);
            if (preg_match('/Chrome\/([\d\.]*)/i', $userAgent->getUserAgentString(), $matches)) {
                $os->setVersion($matches[1]);
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is OS X.
     *
     * @return bool
     */
    private static function checkOSX(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'OS X')) {
            $os->setName($os::OSX);
            if (preg_match('/OS X ([\d\._]*)/i', $userAgent->getUserAgentString(), $matches)) {
                if (isset($matches[1])) {
                    $os->setVersion(str_replace('_', '.', $matches[1]));
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Windows.
     *
     * @return bool
     */
    private static function checkWindows(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'Windows NT')) {
            $os->setName($os::WINDOWS);
            // Windows version
            if (preg_match('/Windows NT ([\d\.]*)/i', $userAgent->getUserAgentString(), $matches)) {
                if (isset($matches[1])) {
                    switch (str_replace('_', '.', $matches[1])) {
                        case '6.3':
                            $os->setVersion('8.1');
                            break;
                        case '6.2':
                            $os->setVersion('8');
                            break;
                        case '6.1':
                            $os->setVersion('7');
                            break;
                        case '6.0':
                            $os->setVersion('Vista');
                            break;
                        case '5.2':
                        case '5.1':
                            $os->setVersion('XP');
                            break;
                        case '5.01':
                        case '5.0':
                            $os->setVersion('2000');
                            break;
                        case '4.0':
                            $os->setVersion('NT 4.0');
                            break;
                        default:
                            if ((float) $matches[1] >= 10.0) {
                                $os->setVersion($matches[1]);
                            }
                            break;
                    }
                }
            }

            return true;
        } // Windows Me, Windows 98, Windows 95, Windows CE
        elseif (preg_match(
            '/(Windows 98; Win 9x 4\.90|Windows 98|Windows 95|Windows CE)/i',
            $userAgent->getUserAgentString(),
            $matches
        )) {
            $os->setName($os::WINDOWS);
            switch (strtolower($matches[0])) {
                case 'windows 98; win 9x 4.90':
                    $os->setVersion('Me');
                    break;
                case 'windows 98':
                    $os->setVersion('98');
                    break;
                case 'windows 95':
                    $os->setVersion('95');
                    break;
                case 'windows ce':
                    $os->setVersion('CE');
                    break;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Windows Phone.
     *
     * @return bool
     */
    private static function checkWindowsPhone(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'Windows Phone')) {
            $os->setIsMobile(true);
            $os->setName($os::WINDOWS_PHONE);
            // Windows version
            if (preg_match('/Windows Phone ([\d\.]*)/i', $userAgent->getUserAgentString(), $matches)) {
                if (isset($matches[1])) {
                    $os->setVersion((float) $matches[1]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is SymbOS.
     *
     * @return bool
     */
    private static function checkSymbOS(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'SymbOS')) {
            $os->setName($os::SYMBOS);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Linux.
     *
     * @return bool
     */
    private static function checkLinux(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'Linux')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::LINUX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Nokia.
     *
     * @return bool
     */
    private static function checkNokia(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'Nokia')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::NOKIA);
            $os->setIsMobile(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is BlackBerry.
     *
     * @return bool
     */
    private static function checkBlackBerry(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'BlackBerry')) {
            if (false !== stripos($userAgent->getUserAgentString(), 'Version/')) {
                $aresult = explode('Version/', $userAgent->getUserAgentString());
                if (isset($aresult[1])) {
                    $aversion = explode(' ', $aresult[1]);
                    $os->setVersion($aversion[0]);
                }
            } else {
                $os->setVersion($os::VERSION_UNKNOWN);
            }
            $os->setName($os::BLACKBERRY);
            $os->setIsMobile(true);

            return true;
        } elseif (false !== stripos($userAgent->getUserAgentString(), 'BB10')) {
            $aresult = explode('Version/10.', $userAgent->getUserAgentString());
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $os->setVersion('10.'.$aversion[0]);
            } else {
                $os->setVersion('10');
            }
            $os->setName($os::BLACKBERRY);
            $os->setIsMobile(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is Android.
     *
     * @return bool
     */
    private static function checkAndroid(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'Android')) {
            if (preg_match('/Android ([\d\.]*)/i', $userAgent->getUserAgentString(), $matches)) {
                if (isset($matches[1])) {
                    $os->setVersion($matches[1]);
                }
            } else {
                $os->setVersion($os::VERSION_UNKNOWN);
            }
            $os->setName($os::ANDROID);
            $os->setIsMobile(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is FreeBSD.
     *
     * @return bool
     */
    private static function checkFreeBSD(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'FreeBSD')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::FREEBSD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is OpenBSD.
     *
     * @return bool
     */
    private static function checkOpenBSD(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'OpenBSD')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::OPENBSD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is SunOS.
     *
     * @return bool
     */
    private static function checkSunOS(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'SunOS')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::SUNOS);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is NetBSD.
     *
     * @return bool
     */
    private static function checkNetBSD(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'NetBSD')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::NETBSD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is OpenSolaris.
     *
     * @return bool
     */
    private static function checkOpenSolaris(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'OpenSolaris')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::OPENSOLARIS);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is OS2.
     *
     * @return bool
     */
    private static function checkOS2(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'OS\/2')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::OS2);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user's operating system is BeOS.
     *
     * @return bool
     */
    private static function checkBeOS(Os $os, UserAgent $userAgent)
    {
        if (false !== stripos($userAgent->getUserAgentString(), 'BeOS')) {
            $os->setVersion($os::VERSION_UNKNOWN);
            $os->setName($os::BEOS);

            return true;
        }

        return false;
    }
}

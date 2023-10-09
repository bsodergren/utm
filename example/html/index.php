<?php

use UTM\Utils\UtmDevice;
use UTM\Template\Template;



require '.configure.php';

UtmDevice::getHeader();

$t = new Template();
$t->render("main");
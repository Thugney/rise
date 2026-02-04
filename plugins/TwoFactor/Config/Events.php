<?php

namespace TwoFactor\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("twofactor_general");
});


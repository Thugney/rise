<?php

namespace TwoFactor\Models;

use App\Models\Crud_model;

class TwoFactor_user_settings_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'twofactor_user_settings';
        parent::__construct($this->table);
    }

}

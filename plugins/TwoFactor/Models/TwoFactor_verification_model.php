<?php

namespace TwoFactor\Models;

use App\Models\Crud_model;

class TwoFactor_verification_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'twofactor_verification';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $verification_table = $this->db->prefixTable("twofactor_verification");

        $where = "";

        $code = get_array_value($options, "code");
        if ($code) {
            $code = $this->db->escapeString($code);
            $where .= " AND $verification_table.code='$code'";
        }

        $email = get_array_value($options, "email");
        if ($email) {
            $email = $this->db->escapeLikeString($email);
            $where .= " AND $verification_table.params LIKE '%$email%' ESCAPE '!'";
        }

        $phone = get_array_value($options, "phone");
        if ($phone) {
            $phone = $this->db->escapeLikeString($phone);
            $where .= " AND $verification_table.params LIKE '%$phone%' ESCAPE '!'";
        }

        $sql = "SELECT $verification_table.*
        FROM $verification_table
        WHERE $verification_table.deleted=0 $where 
        ORDER BY $verification_table.id DESC";
        return $this->db->query($sql);
    }

}

<?php
class LanguageLoader {
    function initialize() {
        $ci =& get_instance();
        $site_lang = $ci->session->userdata('site_language');
        if ($site_lang) {
            $ci->lang->load('site', $site_lang);
        } else {
            $ci->lang->load('site', 'english');
        }
    }
}

?>
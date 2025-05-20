<?php
class LanguageSwitcher extends CI_Controller {

    public function switchLanguage($language = "english") {
        $this->session->set_userdata('site_language', $language);
        redirect($_SERVER['HTTP_REFERER']); // redirect to previous page
    }
}

?>
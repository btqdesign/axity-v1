<?php

class WPML_Get_LS_Languages_Status extends WPML_Singleton {

	private $in_get_ls_languages = false;

	public function is_getting_ls_languages() {
		return $this->in_get_ls_languages;
	}

	public function start() {
		$this->in_get_ls_languages = true;
	}

	public function end() {
		$this->in_get_ls_languages = false;
	}

}
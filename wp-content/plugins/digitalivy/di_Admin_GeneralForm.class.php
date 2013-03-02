<?php 
class DIAdminGeneralForm{

	public function Get(){
		include dirname(plugin_basename(__FILE__)) . '/templates/' . 'DI_Admin_GeneralForm.php';
	}

	public function Post(){
		include dirname(plugin_basename(__FILE__)) . '/templates/' . 'DI_Admin_GeneralForm.php';
	}

	public function Run(){
		if ( ! empty( $_POST ) ) {
			$this->Get();
		}

		$this->Post();
	}
}

?>
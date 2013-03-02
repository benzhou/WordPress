<?php 

class DIAdminGeneralForm{

	private $_diPlugin = null;
	private $template_args = array();
	
	public function __construct( &$diPlugin ) {
		$this->_diPlugin = $diPlugin;
	}

	public function Get(){
		//include dirname(plugin_basename(__FILE__)) . '/templates/DI_Admin_GeneralForm.php';
		include plugin_dir_url(__FILE__).'/templates/DI_Admin_GeneralForm.php';
	}

	public function Post(){
		include dirname(plugin_basename(__FILE__)) . '/templates/DI_Admin_GeneralForm.php';
	}

	public function Run(){
		if ( ! empty( $_POST ) ) {
			$this->Post();
		}

		$this->Get();
	}
}

?>
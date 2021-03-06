<?php
//define('CURR_PATH',str_replace("\\", "/", dirname(__FILE__))) ;
require CURR_PATH . "/conf/securing.php" ;

class SantinizationHandler {
	/**
	 * 处理symbol的净化信息
	 * $F_SECURES_ALL安全函数的集合
	 * @param Node $node
	 * @param DataFlow $dataFlow
	 */
	public function setSantiInfo($node,$dataFlow){
		global $F_SECURES_ALL ;
		$funcName = NodeUtils::getNodeFunctionName($node) ;

		//查看sqli的净化信息
		if(in_array($funcName, $F_SECURES_ALL)){
			//设置净化函数
			$dataFlow->getLocation()->addSanitization($funcName) ;
			
		}
		
		//清除反作用的函数
		SantinizationHandler::clearSantiInfo($funcName, $node, $dataFlow) ;
		
	}	
	
	/**
	 * 查看净化栈中是否有可以抵消的元素
	 *	[+]'html_entity_decode',
	 *	[+]'stripslashes',
	 * @param string $funcName
	 * @param Node $node
	 * @param DataFlow $dataFlow
	 */
	public function clearSantiInfo($funcName, $node,$dataFlow){
		global $F_INSECURING_STRING ;
		//echo "<br/>" . $funcName ."<br/>";
		//print_r($dataFlow) ;
		//判断$funcName相反的函数是否在净化Map中
		//比如调用stripslashes($funcName=stripslashes)
		if(in_array($funcName,$F_INSECURING_STRING)){
			switch ($funcName){
				case 'stripslashes':
					//去除净化Map中最近的addslashes净化
					$map = $dataFlow->getLocation()->getSanitization() ;
					$position = array_search('addslashes',$map) ;
					array_splice($map,$position,1) ;
					break ;
					
				case 'html_entity_decode':
					//去除htmlentities净化
					$map = $dataFlow->getLocation()->getSanitization() ;
					$position = array_search('htmlentities',$map) ;
					array_splice($map,$position,1) ;
					break ;
				
				case 'htmlspecialchars_decode':
					//去除htmlspecialchars净化
					$map = $dataFlow->getLocation()->getSanitization() ;
					$position = array_search('htmlspecialchars',$map) ;
					array_splice($map,$position,1) ;
					break ;

			}
		}
		
	}
	
}


?>
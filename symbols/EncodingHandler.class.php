<?php
class EncodingHandler {
	
	/**
	 * 处理summary的编码信息
	 * @param Node $node
	 * @param DataFlow $dataFlow
	 */
	public static function setEncodeInfo($node,$dataFlow){
		global $F_ENCODING_STRING ;
		$funcName = NodeUtils::getNodeFunctionName($node) ;
		//发现有编码操作的函数，将编码信息加入至map中
		if(in_array($funcName, $F_ENCODING_STRING)){
			$dataFlow->getLocation()->addEncoding($funcName) ;
		}
		
		//清除解码
		EncodingHandler::clearEncodeInfo($funcName, $node, $dataFlow) ;
	}
	
	/**
	 * 清除相应的编码效果
	 * 	[+]'rawurldecode', - rawurlencode
	 *	[+]'urldecode', - urlencode
	 *	[+]'base64_decode', - base64_encode
	 * @param string $funcName
	 * @param Node $node
	 * @param DataFlow $dataFlow
	 */
	public function clearEncodeInfo($funcName, $node,$dataFlow){
		global $F_DECODING_STRING ;
		if(in_array($funcName,$F_DECODING_STRING)){
			switch ($funcName){
				case 'rawurldecode' or 'urldecode':
					//去除净化Map中最近的addslashes净化
					$map = $dataFlow->getLocation()->getEncoding() ;
					$position = array_search('urlencode',$map) ;
					array_splice($map,$position,1) ;
					break ;
					
				case 'base64_decode':
					//去除Map中最近的base64编码操作
					$map = $dataFlow->getLocation()->getEncoding() ;
					$position = array_search('base64_encode',$map) ;
					array_splice($map,$position,1) ;
					break ; 
			}
		}
	}
	
}

?>
<?php
require_once(dirname(__FILE__).'/annotations.php');

/**
 * 该类是vsapi的工具类.
 * 该类将客户端请求结果转换成vsapi1.0 所识别的结果
 *
 * @author Brook
 * @since weike. Ver 2.0
 */
class VSAPI {

	/**
	 *
	 * 该函数将对象转换为vsxml.
	 *
	 * 对于对象:
	 * 将类依照vsapi协议进行转换,对所有属性(除了以@transient标注的属性)进行转换;
 	 *
 	 * TODO:
 	 * (以@vsapi注解的类自动转换)//默认转换需要注解吗?
 	 * (未实现)如果类没有以@vsapi标注,则对所有以@vsapi标注的属性进行转换;
 	 * 对象属性递归调用,直至元数据为止;
 	 * 如果存在父类,则递归调用父类;
 	 *
 	 * 对于数组:
 	 * 将以key value的方式返回
 	 *
 	 * 其余情况,将返回一个简单vsxml对象.
 	 *
	 * @param array|object $body
	 * @param string tag $body 对应的标签
	 */
	public static function encode($body,$tag=null,$header=null){
		try {
			$header = $header?$header:self::successHeader();
			return self::builder()->build(
					array($header,$body),$tag);
		} catch (Exception $e) {
			Yii::app()->handleException($e);
			return self::builder()->build(
					array(array('ret_code'=>505,
						'ret_error'=>'internal server error.'
						)));
		}

	}

	public static function message($msgArr){
		return self::encode(null,null,$msgArr);
	}
	public static function success($msgArr=null){
		return self::encode(
				$msgArr,null,self::successHeader());
	}
	/**
	 * 返回vsapi错误提示.
	 *
	 * @param int $ret_code 错误编号
	 * @param string $ret_error 提示信息
	 * @return string
	 *
	 * @author Brook
	 * @since weike. Ver 2.0
	 */
	public static function error($ret_code,$ret_error,$body=null){
		return self::encode($body,null,array(
				'ret_code'=>$ret_code,
				'ret_error'=>$ret_error
		));
	}
	protected static function successHeader(){
		return array(//SUCESS_HEADER
						'ret_code'=>0,
						'ret_error'=>'OK'
				);
	}
// 	public static function error($code,$message){
// 		return self::encode(self::SUCESS_HEADER+$msgArr);
// 	}
	/**
	 * @return XMLBuilder
	 */
	private static function builder(){
		return new VsapiBuilder();
	}






	/** Bad Request——错误的请求 */
	const BAD_ARGUMENTS_ERROR=400;
	/** 需要授权   */
	const Authorization_Required=401;
	/** Payment Required (not used yet)——需要付款（尚未使用）  */
	const Payment_Required=402;
	/** Forbidden——禁止  */
	const Forbidden=403;
	/** Not Found——未找到 */
	const Not_Found=404;
	/** Method_Not_Allowed 不允许的方法*/
	const Method_Not_Allowed=405;
	/** Not Acceptable (encoding)——不接受（编码） */
	const Not_Acceptable=406;
	/** Proxy_Authentication_Required——需要代理授权 */
	const Proxy_Authentication_Required=407;
	/** Request_Timed_Out——请求超时 */
	const Request_Timed_Out=408;

	// 	409 Conflicting Request——冲突的请求410 Gone——消失411 Content Length Required——内容所需长度412 Precondition Failed——前提条件失败413 Request Entity Too Long——请求实体过长414 Request URI Too Long——请求URI太长415 Unsupported Media Type——不支持的媒体类型
	// 	Server Errors——服务器错误
	// 	500 Internal Server Error——内部服务器错误501 Not Implemented——未实现502 Bad Gateway——错误网关503 Service Unavailable——服务不可用504 Gateway Timeout——网关超时505 HTTP Version Not Supported——HTTP版本不受支持

	const UNKNOW_ERROR=500;



	/// --------------FLAGs----------


}
class VsapiBuilder{
	/**
	 * 需要封装为属性的列表;
	 */
	public $attributes;//XXX use const instead .
	public $transients;

	/**
	 * @var XMLBuilder
	 */
	private $xmlBuilder ;
	public function __construct(){
		$this->attributes=array('id');
		$this->transients=array('transient');
		$this->xmlBuilder=new XMLBuilder();
	}

	public function build($object,$tag=null){
		$this->vsapiBegin();
		$this->encode($object,$tag);
		$this->vsapiEnd();
		return $this->xmlBuilder->toString();
	}

	public function encode($object,$tag=null){
		// 		$annotations = self::getClassAnnotations($clazz);
		// 		if(array_key_exists('vsapi', $annotations)){
		if(!isset($object)){
			return;
		}

		if(is_array($object)){
			$this->encodeArray($object,$tag);
		}else if(is_object($object)){
			$this->encodeObject($object,$tag);
		}else{
			$this->encodeScalarType($object,$tag);
		}
	}

	private function getClassTag($class){
		$tag = $class;
		if(StringUtils::startsWith($tag, 'vs',false)){
			$tag=substr($tag, 2);
		}
		return $tag;
	}
	private function encodeObject($object,$tag=null){
		$vars = get_object_vars($object);
		$clazz = get_class($object);

		if(is_subclass_of($object, 'VsModel')){
			$object->loadFlag();
		}

		// header
		$tag = is_string($tag)? $tag:$this->getClassTag($clazz);

		if(!function_exists('lcfirst')) {// php5.3 以下不兼容
			function lcfirst($str) {// 首字母小写
				$str[0] = strtolower($str[0]);
				return $str;
			}
		}
		$tag=lcfirst($tag);

		$this->xmlBuilder->startTag($tag);

		foreach ($this->attributes as $attr){
			if(array_key_exists($attr, $vars)){
				$this->xmlBuilder->addAttribute($attr, $vars[$attr]);
			}
		}

		$this->xmlBuilder->closeTag();

		// for properties
		foreach ($vars as $key =>$value){
			$annotations = self::getPropertyAnnotations($clazz,$key);
			foreach ($this->transients as $trans){
				if(!array_key_exists($trans, $annotations)
 						&& !in_array($key, $this->attributes)){
					$this->encode($object->$key,$key);
				}
			}


		}

		// end
		$this->xmlBuilder->endsTag($tag);
	}

	private function encodeArray($array,$tag=null){
		if(is_string($tag)){
			$this->xmlBuilder->beginTag($tag);
		}

		foreach ($array as $key=>$value){
			$this->encode($value,$key);
		}
		if(is_string($tag)){
			$this->xmlBuilder->endsTag($tag);
		}
	}

	private function encodeScalarType($object,$tag){
		$value=null;
		switch (gettype($object)) {
			case 'boolean':
				$value= $object ? 'true' : 'false';
				break;
			case 'NULL':
				$value= 'null';
				break;
			case 'integer':
				$value= (int) $object;
				break;
			case 'double':
			case 'float':
				$value= str_replace(',','.',(float)$object); // locale-independent representation
				break;
			case 'string':
				$value=iconv(strtoupper(Yii::app()->charset), 'UTF-8', $object);
				break;
		}
		$this->xmlBuilder->addTag($tag, $value);
	}

	private function getPropertyAnnotations($class,$name){
		$string= Addendum::getDocComment(new AnnotatedProperty($class,$name));
		return self::getAnnotations($string);
	}

	private function getClassAnnotations($class){
		$string= Addendum::getDocComment(new AnnotatedClass($class));
		return self::getAnnotations($string);
	}

	protected static function utf16beToUTF8(&$str){
		$uni = unpack('n*',$str);
		return self::unicodeToUTF8($uni);
	}

	protected static function unicodeToUTF8( &$str ){
		$utf8 = '';
		foreach( $str as $unicode )
		{
			if ( $unicode < 128 )
			{
				$utf8.= chr( $unicode );
			}
			elseif ( $unicode < 2048 )
			{
				$utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
				$utf8.= chr( 128 + ( $unicode % 64 ) );
			}
			else
			{
				$utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
				$utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
				$utf8.= chr( 128 + ( $unicode % 64 ) );
			}
		}
		return $utf8;
	}

	protected static function utf8ToUTF16BE(&$str, $bom = false){
		$out = $bom ? "\xFE\xFF" : '';
		if(function_exists('mb_convert_encoding'))
			return $out.mb_convert_encoding($str,'UTF-16BE','UTF-8');

		$uni = self::utf8ToUnicode($str);
		foreach($uni as $cp)
			$out .= pack('n',$cp);
		return $out;
	}

	private function getAnnotations($string){//Reflector
		$annotations =array();
		$matcher=new NormalizeAnnotationMatcher();
		while(true) {
			if(preg_match('/[\*\s](?=@)/', $string, $matches, PREG_OFFSET_CAPTURE)) {
				$offset = $matches[0][1] + 1;
				$string = substr($string, $offset);
			}  else {
				break; // no more annotations
			}
			$data=null;
			if(($length = $matcher->matches($string, $data)) !== false) {
				$string = substr($string, $length);
				list($name, $params) = $data;
				$annotations[$name][] = $params;
			}
		}
		return $annotations;
	}


	private function vsapiBegin(){
		header('Content-Type: text/xml');
		$this->xmlBuilder->startTag('vssource');
		$this->xmlBuilder->addAttribute('request', Yii::app()->request->url);
		$this->xmlBuilder->closeTag();
	}

	private function vsapiEnd(){
		$this->xmlBuilder->endsTag('vssource');
	}
}

class XMLBuilder{
	private $content ='<?xml version="1.0" encoding="utf-8"?>';

	public function addAttribute($name,$value){
		$this->content .= ' '.$name.'="'.$this->handleHTMLCharts($value).'"';
	}
	public function beginTag($name){
		$this->startTag($name);
		$this->closeTag();
	}
	public function startTag($name){
		$this->content .="\r\n<".$name;
	}
	public function closeTag(){
		$this->content.='>';
	}
	public function endsTag($name){
		$this->content.= "</$name>";
// 			StringUtils::endsWith($this->content, '>')
// 				?	"</$name>":'/>';
	}

	public function addTag($name,$value){
		$this->startTag($name);
		if(isset($value)){
			$this->closeTag();
			$this->content.=$this->handleHTMLCharts($value);
			$this->content.="</$name>";
		}else{
			$this->endsTag($name);
		}
	}

	public function toString(){
		return $this->content;
	}

	private $HTML_SPECIAL_CODE=array(
			'&'=>'&amp;',
			'>'=>'&gt;;',
			'<'=>'&lt;',
			'"'=>'&quot;',
			"'"=>'&apos;',
			'null'=>'',
	);

	private function handleHTMLCharts($string){//normalize
		foreach ($this->HTML_SPECIAL_CODE as $key=>$value){
			$string=str_ireplace($key, $value, $string);
		}
		return $string;
	}
}

class AnnotatedClass extends ReflectionClass{
	private $annotations;
	public function __construct($class) {
		parent::__construct($class);
	}
}
class AnnotatedProperty extends ReflectionProperty{
	public function __construct($class,$name){
		parent::__construct($class,$name);
	}
}
class NormalizeAnnotationMatcher extends SerialMatcher {//XXX
	protected function build() {
		$this->add(new RegexMatcher('@'));
		$this->add(new RegexMatcher('[a-zA-Z0-9_\\\\]*'));
		$this->add(new AnnotationParametersMatcher);
	}
	protected function process($results) {
		return array($results[1], $results[2]);
	}
}




?>
<?php
/**
 * jsonrpc功能在yii的实现
 * 
 * @author Yucheng
 *
 */
class CJsonRPCAction extends CAction {
    /**
     * json 解析错误
     * 
     * @var int
     */
    const PARSE_ERROR = -32700;
    /**
     * 无效的请求
     *
     * @var int
     */
    const INVALID_REQUEST = -32600;
    /**
     * 请求的方法不存在
     *
     * @var int
     */
    const METHOD_NOT_FOUND = -32601;
    /**
     * 请求方法的参数无效
     *
     * @var int
     */
    const INVALID_PARAMS = -32602;
    /**
     * 对jsonrpc请求公开的方法标识
     * 
     * @var string
     */
    const ANNOTATION = '@jsonrpc';
    /**
     * 加载需求的class
     * @var array
     */
	public $classMap;
	/**
	 * 请求的json格式的字符串
	 * @var string
	 */
	private $requestText;
	/**
	 * 解析json格式后的对象
	 * @var mix
	 */
	private $requestObject;
	/**
	 * 存放响应对象数组
	 * @var array
	 */
	private $responseBatchArray = array();
	public function run()
	{
	    if ($this->requestText===null)
	        $this->requestText = file_get_contents("php://input");	    
	    $this->getOptions();
	    $this->processingRequests();
	    Yii::app()->end();
	}
	/**
	 * 请求rpc处理
	 */
    public function processingRequests() {
		try {
			$this->parseRequestJson();
			$this->performCalls();
		} catch(Exception $e) {
			$responseBody = new RpcError($e->getMessage(),$e->getCode());
			$responseObject = new RpcResponse($responseBody);
			$this->doResponse($responseObject->getRpcResponseObject());
		}
	}
	/**
	 * 解析json
	 * @throws Exception
	 */
	private function parseRequestJson() {
		if(!is_null($requestObjects = json_decode($this->requestText))) {
			$this->requestObject = $requestObjects;
		} else {
			throw new Exception("Parse error",self::PARSE_ERROR);
		}
	}
	/**
	 * 根据传入json对象的数量进行不同的处理
	 */
	private function performCalls() {
	    if($this->isBatchRequestAndNotEmpty()) {
	        $this->performBatchCall();
	    } else {
	        $this->performSingleCall();
	    }
	}
	/**
	 * 检测传入json对象的个数，超过一个返回true
	 * @throws Exception
	 * @return boolean
	 */
	private function isBatchRequestAndNotEmpty() {
	    if(is_array($this->requestObject)) {
	        if(empty($this->requestObject)) {
	            throw new Exception("Invalid Request",self::INVALID_REQUEST);
	        }
	        return true;
	    } else {
	        return false;
	    }
	}
	/**
	 * 当传入json对象只有一个的情况
	 */
	private function performSingleCall() {
	    $responseObject = $this->getResponseObject($this->requestObject);
	    $obj = $responseObject->getRpcResponseObject();
	    if (!$this->isNotification($this->requestObject)) {
	        $this->doResponse($obj);
	    }
	}
	/**
	 * 当传入json对象有多个的情况
	 */
	private function performBatchCall() {
	    foreach ($this->requestObject as $request) {
	        $responseObject = $this->getResponseObject($request);
	        if (!$this->isNotification($request)) {
	            array_push($this->responseBatchArray, $responseObject->getRpcResponseObject());
	        }
	    }
	    $this->doResponse($this->responseBatchArray);
	}
	/**
	 * 检测该对象的id是否不存在
	 * @param object $requestObject
	 * @return boolean
	 */
	private function isNotification($requestObject) {
	    if(is_object($requestObject) && is_null($requestObject->id)) {
	        return true;
	    }
	}
	/**
	 * 获取格式化后的请求对象
	 * @param object $requestObject
	 * @return RpcResponse
	 */
	private function getResponseObject($requestObject) {
	    try {
	        $this->validateRequest($requestObject);
	        $methodOwnerService = $this->isMethodAvailable($requestObject);
	        $this->validateAndSortParameters($methodOwnerService, $requestObject);
	        $responseObject = $this->buildResponseObject($requestObject, $methodOwnerService);
	    } catch(Exception $exception) {
	        $responseObject = $this->buildResponseObject($exception);
	        $responseObject->setResponseObjectId($requestObject->id);
	    } catch(Exception $exception) {
	        $responseObject = $this->buildResponseObject($exception);
	    }
	    return $responseObject;
	}
	private function validateRequest($request) {
	    if(!$this->isValidRequestObject($request)) {
	        throw new Exception("Invalid Request",self::INVALID_REQUEST);
	    } else {
	        return true;
	    }
	}
	private function isValidRequestObject($requestObject) {
	    return ($requestObject->jsonrpc == RpcResponse::VERSION
	            && $this->isValidRequestObjectId($requestObject->id)
	            && $this->isValidRequestObjectMethod($requestObject->method));
	}
	private function isValidRequestObjectId($requestId) {
	    return (is_null($requestId)
	            || is_string($requestId)
	            // 2 and "2" is valid but 2.1 and "2.1" is not
	            || (ctype_digit($requestId) xor is_int($requestId)));
	}
	private function isValidRequestObjectMethod($requestMethod) {
	    return (!is_null($requestMethod)
	            && is_string($requestMethod));
	}
	/**
	 * 检测请求方法是否存在
	 * @param  $requestObject
	 * @throws Exception
	 * @return CController
	 */
	protected function isMethodAvailable($requestObject) {
	    if(array_key_exists($requestObject->method, $this->getCallableMethodNames($this->getController()))) {
	        return $this->getController();
	    }
        throw new  Exception("Method not found",self::METHOD_NOT_FOUND);
	}
	/**
	 * 获取映射类中存在@jsonrpc的方法
	 */
	public function getCallableMethodNames($methodOwnerService) {
	    $methodNames = array();
	    $reflection = new ReflectionClass($methodOwnerService);
	    foreach($reflection->getMethods() as $method) {
	        if($this->isJsonRpcMethod($method)) {
	            $methodNames[$method->name] = $method->getParameters();
	        }
	    }
	    return $methodNames;
	}
	/**
	 * 检测是否存在jsonrpc的注释的方法
	 * @param string $method
	 * @return boolean
	 */
	protected function isJsonRpcMethod($method) {
	    if(strstr($method->getDocComment(),self::ANNOTATION)) {
	        return true;
	    }
	    return false;
	}
	private function validateAndSortParameters($methodOwnerService, $requestObject) {
	    $validParameters = $this->getCallableMethodParameters($methodOwnerService, $requestObject->method);
	
	    if($this->isValidParamsNumber($validParameters, $requestObject)
	            && $this->isValidParamsName($validParameters, $requestObject)) {
	
	        $this->setMethodParamsSequence($validParameters, $requestObject);
	    } else {
	        throw new Exception("Invalid params",self::INVALID_PARAMS);
	    }
	}
	public function getCallableMethodParameters($service, $methodName) {
	    $reflection = new ReflectionClass($service);
	    foreach($reflection->getMethods() as $method) {
	        if($method->name == $methodName) {
	            return $method->getParameters();
	        }
	    }
	}
	private function setMethodParamsSequence($validParameters, $requestParameters) {
	    $sortedObject = new stdClass();
	    if(is_object($requestParameters->params)) {
	        foreach($validParameters as $parameter) {
	            $sortedObject->{$parameter->name} = $requestParameters->params->{$parameter->name};
	        }
	        $requestParameters->params = $sortedObject;
	    }
	}
	private function buildResponseObject($requestOrExceptionObject, $service = null) {
	    if(is_null($service)) {
	        $responseBody = new RpcError($requestOrExceptionObject->getMessage(),$requestOrExceptionObject->getCode());
	        $responseObject = new RpcResponse($responseBody);
	    } else {
	        $callbackResult = $this->call($service, $requestOrExceptionObject);
	        $responseObject = new RpcResponse($callbackResult, $requestOrExceptionObject->id);
	    }
	    return $responseObject;
	}
	private function isValidParamsNumber($validParameters, $requestObject) {
	    $validParameterCount = count($validParameters);
	    $requestParameterCount = $this->countRequestParams($requestObject->params);
	
	    if($validParameterCount != $requestParameterCount) {
	        return false;
	    }
	    return true;
	}
	private function countRequestParams($requestObjectParams) {
	    if(is_object($requestObjectParams)) {
	        return count(get_object_vars($requestObjectParams));
	    } else {
	        return count($requestObjectParams);
	    }
	}
	private function isValidParamsName($validParameters, $requestObject) {
	    if(is_object($requestObject->params)){
	        $requestParamNames = array_keys(get_object_vars($requestObject->params));
	        foreach($validParameters as $parameter) {
	            if(!in_array($parameter->name, $requestParamNames, true)) {
	                return false;
	            }
	        }
	        return true;
	    } else {
	        return true;
	    }
	}
	private function call($methodOwnerService, $requestObject) {
	    $callbackFunction = array($methodOwnerService,$requestObject->method);
	    if ($requestObject->params==null)
	        $requestObject->params = array();
	    return call_user_func_array($callbackFunction, $requestObject->params);
	}
	private function doResponse($responseObject) {
	    if(!empty($responseObject)) {
	        header('Content-Type: application/json');
	        echo json_encode($responseObject);
	    }
	}
	
	protected function getOptions()
	{
	    foreach($this->classMap as $type=>$className)
	    {
	        $className=Yii::import($className,true);
	    }
	}
}
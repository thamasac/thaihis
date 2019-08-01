<?php
 
namespace backend\modules\api\v1\classes;
 /**
  * https://github.com/lindelius/php-jwt
  * Advanced Usage
  * Algorithm Choices
    If you would like to limit the hashing algorithms that can be used for the JWTs, 
  * you can do so by extending the model and specifying these algorithms in the JWT::$allowedAlgorithms property. 
  * You can find all the supported hashing algorithms in the JWT::$supportedAlgorithms property.
    If you are not going to allow the "HS256" algorithm, or if you would just rather have a different default, 
  * then you should also override the JWT::$defaultAlgorithm property.
  */
class MyJWT extends \Lindelius\JWT\JWT{
    public function __construct($algorithm = null, array $header = array(), $signature = null) {
        parent::__construct($algorithm, $header, $signature);
    }
    /**
     * The allowed hashing algorithms. If empty, all supported algorithms are
     * considered allowed.
     *
     * @var string[]
     */
    protected static $allowedAlgorithms = ['HS512', 'RS256','HS256'];

    /**
     * The default hashing algorithm.
     *
     * @var string
     */
    protected static $defaultAlgorithm  = 'HS256';
    
    /**
     * Leeway time (in seconds) to account for clock skew.
     *
     * @var int
     */
    protected static $leeway = 90;
    public function setAlg($alg) {
        self::$allowedAlgorithms = $alg;
        //\appxq\sdii\utils\VarDumper::dump(self::$allowedAlgorithms);
    }
}

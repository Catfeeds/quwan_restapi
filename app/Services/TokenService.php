<?php
/**
 * json web token 相关
 * 生成/撤销/验证等操作
 */

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Webpatser\Uuid\Uuid;

class TokenService
{
    private $signKey;
    private $validTime;

    /* 当前文件错误码 */
    const ERR = 500;

    public function __construct()
    {
        $this->signKey = Config::get('jwt')['signKey']; //加密 key
        $this->validTime = 7*86400;                     //token 有效期(s)
    }

    /**
     * 创建 token
     *
     * @param $userId
     * @param $platform
     * @param string $deviceName
     * @return string
     * @throws \Exception
     */
    public function createToken($userId, $platform='', $deviceName = '')
    {
        $tokenId = Uuid::generate();
        $time = time();
        $signer = new Sha256();
        $token = (new Builder())->setId($tokenId)
            //->setAudience('http://example.org')
            //->setAudience('http://example.org')
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + $this->validTime)
            ->setSubject($userId)
            //->set('key', 'value')
            ->sign($signer, $this->signKey)
            ->getToken();

        //按需撤销存在的 token
        //@todo

        //Redis 保存记录
        Redis::setex(sprintf('vp_jwt:%s', $tokenId), $this->validTime, $userId);
        $jwtInfo = ['platform' => $platform, 'deviceName' => $deviceName, 'createTime' => $time];
        Redis::setex(sprintf('vp_jwt_user:%s:%s', $userId, $tokenId), $this->validTime, json_encode($jwtInfo));

        return (string) $token;
    }

    /**
     * 通过 tokenId 撤销 token
     *
     * @param $tokenId
     * @return bool
     */
    public function revokeToken($tokenId)
    {
        $userId = Redis::get(sprintf('vp_jwt:%s', $tokenId));
        if ($userId !== null) {
            //删除 vp_jwt
            Redis::del(sprintf('vp_jwt:%s', $tokenId));
            //删除 vp_jwt_user
            Redis::del(sprintf('vp_jwt_user:%s:%s', $userId, $tokenId));
        }
        return true;
    }

    /**
     * get jwt instance
     *
     * @param $bearerToken
     * @return bool|\Lcobucci\JWT\Token
     */
    private function getjwtInstance($bearerToken)
    {
        try {
            $token = (new Parser())->parse( (string) str_replace('Bearer ', '', $bearerToken) );
            $signer = new Sha256();
            if ($token->verify($signer, $this->signKey)) {
                $c = $token->getClaims();
                $jti = $c['jti']->getValue() ?? 'unknown_jti';
                $sub = $c['sub']->getValue() ?? 'unknown_sub';
                $exp = $c['exp']->getValue() ?? 0;
                $nbf = $c['nbf']->getValue() ?? 0;
                $time = time();

                if ($nbf <= $time && $exp >= $time && (string) Redis::get(sprintf('vp_jwt:%s', $jti)) === $sub) {
                    return $token;
                }
            }
        } catch (\Exception $e) {
            // do nothing
            //echo $e->getMessage();
        }

        return false;
    }

    /**
     * j检查 token 是否有效
     *
     * @param $bearerToken
     * @return bool
     */
    public function isValid($bearerToken)
    {
        return $this->getjwtInstance($bearerToken) ? true : false;
    }

    /**
     * 获取 token 的 Claims
     *
     * @param $bearerToken
     * @return array|bool
     */
    public function getJwtClaims($bearerToken)
    {
        $token = $this->getjwtInstance($bearerToken);
        if ($token) {
            $claims = [];

            $c = $token->getClaims();
            if (is_array($c)) {
                foreach ($c as $k => $v) {
                    $claims[$v->getName()] = $v->getvalue();
                }
            }

            return $claims;
        }

        return false;
    }

    /**
     * 获取用户 id
     *
     * @param $bearerToken
     * @return mixed|null
     */
    public function getUserId($bearerToken)
    {
        return $this->getJwtClaims($bearerToken)['sub'] ?? null;
    }

}
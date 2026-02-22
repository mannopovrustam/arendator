<?php
namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Socialite;


class EgovProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopeSeparator = ' ';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['e-gaz'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(env('EGOV_URL','https://sso2.egov.uz:8443/sso/oauth/Authorization.do'), $state);
    }

    protected function getTokenUrl() {
        return env('EGOV_URL','https://sso2.egov.uz:8443/sso/oauth/Authorization.do') . '?';
        //'https://sso2.egov.uz:8443/sso/oauth/Authorization.do?';
    }

    protected function getUserByToken($token) {
        //\Log::debug("_getUserByToken( $token )");
        $postKey = 'form_params'; // : 'body';
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            $postKey => [
                'grant_type' => 'one_access_token_identify',
                'client_id' => 'e-gaz', 'client_secret' => '5i8XBwh7ThLyGqTaKLYA7bzP',
                'access_token' => $token,'scope' => 'e-gaz',
                'redirect' => env('APP_URL').'/authcode'
//                'redirect_uri' => 'http://reg.emehmon.uz/authcode'
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    private function replaceKeys($oldKey, $newKey, array $input){
        $return = array();
        foreach ($input as $key => $value) {
            if ($key===$oldKey)
                $key = $newKey;

            if (is_array($value)) $value = $this->replaceKeys( $oldKey, $newKey, $value);
            $return[$key] = $value;
        }
        return $return;
    }

    private function correctDate($data) {
        $old_date = explode('-', $data);
        if (count($old_date) == 3)
            return $old_date[0].'-'.$old_date[2].'-'.$old_date[1];
        return null;
    }

    protected function mapUserToObject(array $user){
        //\Log::debug('user data: ' . json_encode($user));

        //dd($user);
        $mob_phone_no = isset($user['mob_phone_no']) && $user['mob_phone_no'] ? $user['mob_phone_no'] : '0';
        $user['mob_phone_no'] = trim(str_replace('+','', $mob_phone_no));
        $user['pport_expr_date'] = isset($user['pport_expr_date']) ? $this->correctDate($user['pport_expr_date']) : null;
        $user['pport_issue_date'] = isset($user['pport_issue_date']) ? $this->correctDate($user['pport_issue_date']) : null;
        $user = $this->replaceKeys('first_name','firstname',$user);
        $user = $this->replaceKeys('sur_name','surname',$user);
        $user = $this->replaceKeys('mid_name','lastname',$user);
        $user = $this->replaceKeys('per_adr','address',$user);
        $user = $this->replaceKeys('mob_phone_no','phone',$user);
        $user = $this->replaceKeys('pport_no','pspNumb',$user);

        unset($user['full_name']);
        if(isset($user['_pport_expr_date'])) unset($user['_pport_expr_date']);
        if(isset($user['_pport_issue_date'])) unset($user['_pport_issue_date']);
        return (new User)->setRaw($user)->map([
            'id'       => $user['pin'],
            'nickname' => $user['user_id'],
            'name'     => $user['firstname'],
            'email'     => (isset($user['email']) ? $user['email'] : 'noemail@' .time()),
            'phone'     => trim($user['phone']),
            'pin'       => $user['pin'],
            //'avatar'   => !empty($user['images']) ? $user['images'][0]['url'] : null,
        ]);
    }

    protected function getCodeFields($state = null) {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'one_code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        try {
            //\Log::debug("_getTokenFields( $code )");
            return parent::getTokenFields($code) + ['grant_type' => 'one_authorization_code', 'scope' => 'e-gaz'];
        }
        catch (\Exception $ex) {
            \Log::error($ex->getMessage());
        }
    }

}

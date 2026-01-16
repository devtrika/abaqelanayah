<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Auth\OTPService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UnifonicWhatsAppOtpTest extends TestCase
{
    public function test_sends_otp_via_unifonic_whatsapp_when_enabled()
    {
        config()->set('unifonic.base_url', 'https://apis.unifonic.com');
        config()->set('unifonic.public_id', 'pub');
        config()->set('unifonic.secret', 'sec');
        config()->set('unifonic.whatsapp.enabled', true);
        config()->set('unifonic.whatsapp.template', 'sandbox_account_update');
        config()->set('unifonic.whatsapp.language', 'en');

        $user = User::forceCreate([
            'name' => 'T',
            'country_code' => '+966',
            'phone' => '555555555',
            'email' => 't@example.com',
            'password' => 'secret',
        ]);

        Http::fake(function ($request) use ($user) {
            $this->assertEquals('https://apis.unifonic.com/v1/messages', (string) $request->url());
            $this->assertTrue($request->hasHeader('PublicId'), 'PublicId header missing');
            $this->assertEquals(['pub'], $request->header('PublicId'));
            $this->assertTrue($request->hasHeader('Secret'), 'Secret header missing');
            $this->assertEquals(['sec'], $request->header('Secret'));
            
            $data = $request->data();
            $this->assertEquals('whatsapp', $data['recipient']['channel'] ?? null);
            $this->assertEquals('+' . $user->full_phone, $data['recipient']['contact'] ?? null);
            
            $content = $data['content'] ?? [];
            $this->assertEquals('authentication', $content['type'] ?? null);
            $this->assertEquals('sandbox_account_update', $content['authentication']['templateName'] ?? null);
            $this->assertEquals('en', $content['authentication']['language'] ?? null);
            $this->assertEquals('12345', $content['authentication']['code'] ?? null);
            
            return Http::response(['id' => 'msg_1'], 200);
        });

        $svc = new OTPService();
        $res = $svc->generateAndSendOTP($user);
        $this->assertEquals('12345', $res['code']);
    }
}


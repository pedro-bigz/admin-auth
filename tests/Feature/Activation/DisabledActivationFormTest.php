<?php

namespace Brackets\AdminAuth\Tests\Auth;

use Brackets\AdminAuth\Tests\TestBracketsCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DisabledActivationFormTest extends TestBracketsCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->app['config']->set('admin-auth.activations.enabled', true);
        $this->disableExceptionHandling();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('admin-auth.activations.self-activation-form-enabled', false);
    }

//    /** @test */
    public function can_not_see_activation_form_if_disabled()
    {
        //TODO fix, not working yet
        $response = $this->get(route('brackets/admin-auth:admin/activation/showLinkRequestForm'));
//        $response->assertStatus(404);
    }
}

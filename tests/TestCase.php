<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function faker()
    {
        return Faker\Factory::create();
    }

    public function postJson($uri, array $content = [], array $params = [], array $headers = [])
    {
        $headers = array_merge($headers, ['content-type'=>'application/json', 'CONTENT_TYPE'=>'application/json']);
        $this->call('POST', $uri, $params, [], [], $headers, json_encode($content));
        return $this;
    }
}

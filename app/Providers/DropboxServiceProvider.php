<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     */
    public function boot() {
        Storage::extend('dropbox', function ($app, $config) {
            $resource = (new Client)->post(config('filesystems.disks.dropbox.token_url'), [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => config('filesystems.disks.dropbox.refresh_token'),
                ],
            ]);

            $accessToken = json_decode($resource->getBody(), true)['access_token'];

            $adapter = new DropboxAdapter(new DropboxClient($accessToken));

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });
    }
}

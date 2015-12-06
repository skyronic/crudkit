<?php 

namespace CrudKit\Laravel;

class CrudKitServiceProvider extends ServiceProvider {

	protected $defer = false;

	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../static/build/' => public_path('vendor/crudkit/'),
		], 'public');
	}

	public function register()
	{
	}
}

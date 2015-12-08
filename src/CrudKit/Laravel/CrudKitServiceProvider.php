<?php 

namespace CrudKit\Laravel;
use Illuminate\Support\ServiceProvider;

class CrudKitServiceProvider extends ServiceProvider {

	protected $defer = false;

	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../static/' => public_path('vendor/crudkit/'),
		], 'public');
	}

	public function register()
	{
	}
}

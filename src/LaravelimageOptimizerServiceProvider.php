<?php
namespace Laravel\ImageOptimizer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
class LaravelimageOptimizerServiceProvider extends ServiceProvider
{
	
	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		
		
	}

	public function register()
	{
		$loader = AliasLoader::getInstance();
		$loader->alias('ImageOptimizer', 'ImageOptimizer::class');
	}
}



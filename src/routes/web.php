<?php
use Mdmaruf\ImageOptimizer\ImageOptimizer;


Route::get('/test', function() {
	//return "ok";
   return ImageOptimizer::optimize('uploads/2.png',9,false);
});
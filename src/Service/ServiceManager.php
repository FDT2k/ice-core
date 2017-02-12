<?php
namespace FDT2k\Noctis\Core\Service;
/*
ServiceManager is loaded on Env init and runs services function at specific points
*/
class ServiceManager
{
  static $services;

  static function registerService($instance,$before=''){
    self::$services[]=$instance;
  }

  static function triggerAutoload($name){

    foreach(self::$services as $service){
      if(method_exists($service,'runOnAutoload')){
        $service->runOnAutoload($name);
      }
    }
  }

  static function triggerBoot(){
    foreach(self::$services as $service){
      if(method_exists($service,'runBeforeInit')){
        $service->runBeforeInit();
      }
    }
  }
  static function triggerAfterPreinit(){

    foreach(self::$services as $service){
      if(method_exists($service,'runAfterFrameworkPreInit')){
        $service->runAfterFrameworkPreInit();
      }
    }
  }
  static function triggerAfterInit(){
    foreach(self::$services as $service){
      if(method_exists($service,'runAfterFrameworkInit')){
        $service->runAfterFrameworkInit();
      }
    }
  }

  static function triggerShutdown(){
    foreach(self::$services as $service){
      if(method_exists($service,'runOnShutdown')){
        $service->runOnShutdown();
      }
    }
  }

}

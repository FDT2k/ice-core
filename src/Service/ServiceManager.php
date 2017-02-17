<?php
namespace FDT2k\Noctis\Core\Service;
/*
ServiceManager is loaded on Env init and runs services function at specific points
*/
class ServiceManager
{
  static $services;

  static function registerService($instance){
    self::$services[]=$instance;
  }

  static function triggerAutoload($name){
    if(is_array(self::$services)){
      foreach(self::$services as $service){
        if(method_exists($service,'runOnAutoload')){
          $service->runOnAutoload($name);
        }
      }
    }
  }

  static function triggerBoot(){
    if(is_array(self::$services)){
      foreach(self::$services as $service){
        if(method_exists($service,'runBeforeInit')){
          $service->runBeforeInit();
        }
      }
    }
  }
  static function triggerAfterPreinit(){
    if(is_array(self::services)){
      foreach(self::$services as $service){
        if(method_exists($service,'runAfterFrameworkPreInit')){
          $service->runAfterFrameworkPreInit();
        }
      }
    }
  }
  static function triggerAfterInit(){
    if(is_array(self::$services)){
      foreach(self::$services as $service){
        if(method_exists($service,'runAfterFrameworkInit')){
          $service->runAfterFrameworkInit();
        }
      }
    }
  }

  static function triggerShutdown(){
    if(is_array(self::$services)){
      foreach(self::$services as $service){
        if(method_exists($service,'runOnShutdown')){
          $service->runOnShutdown();
        }
      }
    }
  }

  static function triggerBeforeController($controller){
    if(is_array(self::$services)){
    foreach(self::$services as $service){
      if(method_exists($service,'runBeforeControllerExec')){
        $service->runBeforeControllerExec($controller);
      }
    }
    }
  }
  static function triggerAfterController($controller){
    if(is_array(self::$services)){
    foreach(self::$services as $service){
      if(method_exists($service,'runAfterControllerExec')){
        $service->runAfterControllerExec($controller);
      }
    }
    }
  }

}

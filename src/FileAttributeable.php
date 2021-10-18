<?php
/**
 * Created by PhpStorm.
 * User: taejong
 * Date: 1/9/20
 * Time: 11:14 PM
 */

namespace Blocksystems\FileAttributes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileAttributeable{
	public function initializeFileAttributeable(){
		if(isset($this->fileableSync) && $this->fileableSync){
			if(isset($this->fileable)){
				if(!isset($this->filablePrefix)){
					$this->filablePrefix = Str::studly(static::class);
				}
				$prefix = $this->filablePrefix;
				foreach($this->fileable as $attr){
					$this->${'set'.Str::studly($attr).'Attribute'} = function($value) use ($attr, $prefix){
						if(!empty($value)){
							if(gettype($value) === 'array'){
								return array_map(function($file) use ($attr, $prefix){
									$fileUuid = (string)Str::uuid();
									$fileRealName = $file->getClientOriginalName();
									$containSlash = strpos($prefix, '/');
									if($containSlash === false){
										$folder_path = '/'.$prefix.'/';
									}elseif($containSlash > 0){
										$folder_path = '/'.$prefix;
									}
									if(!str_ends_with($folder_path, '/')){
										$folder_path = $folder_path.'/';
									}
									if(!isset($this->filablePrefix)){
										$folder_path .= $attr.'/';
									}
									$path = $folder_path.Carbon::now()->format("Y-m-d");
									$fullPath = $path.$fileUuid.'-'.$fileRealName;
									$fileExtension = $file->getClientOriginalExtension();
									Storage::disk()->put($fullPath, file_get_contents($file), 'public');

									return Storage::disk()->url($fullPath);
								}, $value);
							}
							$fileUuid = (string)Str::uuid();
							$fileRealName = $value->getClientOriginalName();
							$containSlash = strpos($prefix, '/');
							if($containSlash === false){
								$folder_path = '/'.$prefix.'/';
							}elseif($containSlash > 0){
								$folder_path = '/'.$prefix;
							}
							if(!str_ends_with($folder_path, '/')){
								$folder_path = $folder_path.'/';
							}
							if(!isset($this->filablePrefix)){
								$folder_path .= $attr.'/';
							}
							$path = $folder_path.Carbon::now()->format("Y-m-d");
							$fullPath = $path.$fileUuid.'-'.$fileRealName;
							$fileExtension = $value->getClientOriginalExtension();
							Storage::disk()->put($fullPath, file_get_contents($value), 'public');

							return Storage::disk()->url($fullPath);
						}else{
							return null;
						}
					};
				}
			}
		}
	}

	public static function bootFileAttributeable(){
		static::creating(
			function($model){
				if(isset($model->fileable)){
					if(!isset($model->filablePrefix)){
						$prefix = Str::studly(static::class);
					}else{
						$prefix = $model->filablePrefix;
					}
					foreach($model->fileable as $attr){
						if(gettype($model->{$attr}) !== 'string'){
							if(!empty($model->{$attr})){
								if(gettype($model->{$attr}) === 'array'){
									$model->{$attr} = array_map(function($file) use ($attr, $prefix){
										$fileUuid = (string)Str::uuid();
										$fileRealName = $file->getClientOriginalName();
										$containSlash = strpos($prefix, '/');
										if($containSlash === false){
											$folder_path = '/'.$prefix.'/';
										}elseif($containSlash > 0){
											$folder_path = '/'.$prefix;
										}
										if(!str_ends_with($folder_path, '/')){
											$folder_path = $folder_path.'/';
										}
										if(!isset($this->filablePrefix)){
											$folder_path .= $attr.'/';
										}
										$path = $folder_path.Carbon::now()->format("Y-m-d");
										$fullPath = $path.$fileUuid.'-'.$fileRealName;
										$fileExtension = $file->getClientOriginalExtension();
										Storage::disk()->put($fullPath, file_get_contents($file), 'public');

										return Storage::disk()->url($fullPath);
									}, $model->{$attr});
								}
								$fileUuid = (string)Str::uuid();
								$fileRealName = $model->{$attr}->getClientOriginalName();
								$containSlash = strpos($prefix, '/');
								if($containSlash === false){
									$folder_path = '/'.$prefix.'/';
								}elseif($containSlash > 0){
									$folder_path = '/'.$prefix;
								}
								if(!str_ends_with($folder_path, '/')){
									$folder_path = $folder_path.'/';
								}
								if(!isset($this->filablePrefix)){
									$folder_path .= $attr.'/';
								}
								$path = $folder_path.Carbon::now()->format("Y-m-d");
								$fullPath = $path.$fileUuid.'-'.$fileRealName;
								$fileExtension = $model->{$attr}->getClientOriginalExtension();
								Storage::disk()->put($fullPath, file_get_contents($model->{$attr}), 'public');
								$model->{$attr} = Storage::disk()->url($fullPath);
							}else{
								$model->{$attr} = null;
							}
						}
					};
				}
			}
		);
		static::saving(
			function($model){
				if(isset($model->fileable) && (!isset($model->fileableSync) ||  !$model->fileableSync)){
					if(!isset($model->filablePrefix)){
						$prefix = Str::studly(static::class);
					}else{
						$prefix = $model->filablePrefix;
					}
					foreach($model->fileable as $attr){
						if(gettype($model->{$attr}) !== 'string'){
							if(!empty($model->{$attr})){
								if(gettype($model->{$attr}) === 'array'){
									$model->{$attr} = array_map(function($file) use ($attr, $prefix){
										$fileUuid = (string)Str::uuid();
										$fileRealName = $file->getClientOriginalName();
										$containSlash = strpos($prefix, '/');
										if($containSlash === false){
											$folder_path = '/'.$prefix.'/';
										}elseif($containSlash > 0){
											$folder_path = '/'.$prefix;
										}
										if(!str_ends_with($folder_path, '/')){
											$folder_path = $folder_path.'/';
										}
										if(!isset($this->filablePrefix)){
											$folder_path .= $attr.'/';
										}
										$path = $folder_path.Carbon::now()->format("Y-m-d");
										$fullPath = $path.$fileUuid.'-'.$fileRealName;
										$fileExtension = $file->getClientOriginalExtension();
										Storage::disk()->put($fullPath, file_get_contents($file), 'public');

										return Storage::disk()->url($fullPath);
									}, $model->{$attr});
								}
								$fileUuid = (string)Str::uuid();
								$fileRealName = $model->{$attr}->getClientOriginalName();
								$containSlash = strpos($prefix, '/');
								if($containSlash === false){
									$folder_path = '/'.$prefix.'/';
								}elseif($containSlash > 0){
									$folder_path = '/'.$prefix;
								}
								if(!str_ends_with($folder_path, '/')){
									$folder_path = $folder_path.'/';
								}
								if(!isset($this->filablePrefix)){
									$folder_path .= $attr.'/';
								}
								$path = $folder_path.Carbon::now()->format("Y-m-d");
								$fullPath = $path.$fileUuid.'-'.$fileRealName;
								$fileExtension = $model->{$attr}->getClientOriginalExtension();
								Storage::disk()->put($fullPath, file_get_contents($model->{$attr}), 'public');
								$model->{$attr} = Storage::disk()->url($fullPath);
							}else{
								$model->{$attr} = null;
							}
						}
					};
				}
			}
		);
		static::updating(
			function($model){
				if(isset($model->fileable)){
					if(!isset($model->filablePrefix)){
						$prefix = Str::studly(static::class);
					}else{
						$prefix = $model->filablePrefix;
					}
					$autoDeleting = $model->fileAutoDeleting ?? true;
					foreach($model->fileable as $attr){
						if(gettype($model->{$attr}) !== 'string'){
							if(!empty($model->{$attr})){
								if(gettype($model->{$attr}) === 'array'){
									$origin = $model->getOriginal($attr);
									$model->{$attr} = array_map(function($file) use ($attr, $prefix, $origin, $autoDeleting){
										if($autoDeleting && isset($origin)){
											Storage::disk()->delete($origin);
										}
										$fileUuid = (string)Str::uuid();
										$fileRealName = $file->getClientOriginalName();
										$containSlash = strpos($prefix, '/');
										if($containSlash === false){
											$folder_path = '/'.$prefix.'/';
										}elseif($containSlash > 0){
											$folder_path = '/'.$prefix;
										}
										if(!str_ends_with($folder_path, '/')){
											$folder_path = $folder_path.'/';
										}
										if(!isset($this->filablePrefix)){
											$folder_path .= $attr.'/';
										}
										$path = $folder_path.Carbon::now()->format("Y-m-d");
										$fullPath = $path.$fileUuid.'-'.$fileRealName;
										$fileExtension = $file->getClientOriginalExtension();
										Storage::disk()->put($fullPath, file_get_contents($file), 'public');

										return Storage::disk()->url($fullPath);
									}, $model->{$attr});
								}
								if($autoDeleting && $model->getOriginal($attr) !== null){
									Storage::disk()->delete($model->getOriginal($attr));
								}
								$fileUuid = (string)Str::uuid();
								$fileRealName = $model->{$attr}->getClientOriginalName();
								$containSlash = strpos($prefix, '/');
								if($containSlash === false){
									$folder_path = '/'.$prefix.'/';
								}elseif($containSlash > 0){
									$folder_path = '/'.$prefix;
								}
								if(!str_ends_with($folder_path, '/')){
									$folder_path = $folder_path.'/';
								}
								if(!isset($this->filablePrefix)){
									$folder_path .= $attr.'/';
								}
								$path = $folder_path.Carbon::now()->format("Y-m-d");
								$fullPath = $path.$fileUuid.'-'.$fileRealName;
								$fileExtension = $model->{$attr}->getClientOriginalExtension();
								Storage::disk()->put($fullPath, file_get_contents($model->{$attr}), 'public');
								$model->{$attr} = Storage::disk()->url($fullPath);
							}else{
								$model->{$attr} = null;
							}
						}
					};
				}
			}
		);
		static::deleting(function($model){
			if(isset($model->fileable)){
				$autoDeleting = $model->fileAutoDeleting ?? true;
				if($autoDeleting){
					foreach($model->fileable as $attr){
						if(isset($model->{$attr})){
							$attrValue = json_decode($model->{$attr});
							if(isset($attrValue)){
								// multiple
								Storage::delete($attrValue);
							}else{
								// single
								Storage::delete($model->{$attr});
							}
						}
					}
				}
			}
		});
	}
}

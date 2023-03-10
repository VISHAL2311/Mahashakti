<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;
	use App\Helpers\MyLibrary;
	use App\Http\Traits\slug;
class ProductsTableSeeder extends Seeder
{
	public function run()
	{
		$moduleCode = DB::table('module')->select('id')->where('varTableName','products')->first();
		 
			 
				 
				 
				 
				 
				 
				 

				 
					 
							 
							 
					 
					 
						 
											 

					 


											 
						 
												$fkIntImgId = DB::table('image')->select('id')->where('txtImageName','theme_01_section_12_img_01')->first();
						 
					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

						
			
														
							$recId = DB::table('products')->select('id')->where('intDisplayOrder','1')->orWhere('varTitle','Product One')->first();
				if(!isset($recId->id)){
			
							$insetId = DB::table('products')->insertGetId([
			
				'varTitle' =>  "Product One",
										'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Product One'),$moduleCode->id),
										'fkIntImgId' => $fkIntImgId->id,
											
											
			
			
							'intDisplayOrder'=> '1',
							'txtDescription' => "",
				'txtShortDescription' => "",
				'chrPublish' => 'Y',
				'chrDelete'=> 'N',
							'varMetaTitle' => "Product One",
				'varMetaKeyword' => "Product One",
				'varMetaDescription' => "",
							'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

									DB::table('image_module_rel')->insert([
							'intFkImgId' => $fkIntImgId->id,
							'intFkModuleCode'=>$moduleCode->id,
							'intRecordId'=>$insetId,
							'created_at'=>Carbon::now(),
							'updated_at'=>Carbon::now(),
						]);
		 
										}
					 
				 
				 
				 
				 
				 
				 

				 
					 
							 
							 
					 
					 
						 
											 

					 


											 
						 
												$fkIntImgId = DB::table('image')->select('id')->where('txtImageName','theme_01_section_12_img_02')->first();
						 
					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

						
			
														
							$recId = DB::table('products')->select('id')->where('intDisplayOrder','2')->orWhere('varTitle','Product Two')->first();
				if(!isset($recId->id)){
			
							$insetId = DB::table('products')->insertGetId([
			
				'varTitle' =>  "Product Two",
										'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Product Two'),$moduleCode->id),
										'fkIntImgId' => $fkIntImgId->id,
											
											
			
			
							'intDisplayOrder'=> '2',
							'txtDescription' => "",
				'txtShortDescription' => "",
				'chrPublish' => 'Y',
				'chrDelete'=> 'N',
							'varMetaTitle' => "Product Two",
				'varMetaKeyword' => "Product Two",
				'varMetaDescription' => "",
							'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

									DB::table('image_module_rel')->insert([
							'intFkImgId' => $fkIntImgId->id,
							'intFkModuleCode'=>$moduleCode->id,
							'intRecordId'=>$insetId,
							'created_at'=>Carbon::now(),
							'updated_at'=>Carbon::now(),
						]);
		 
										}
					 
				 
				 
				 
				 
				 
				 

				 
					 
							 
							 
					 
					 
						 
											 

					 


											 
						 
												$fkIntImgId = DB::table('image')->select('id')->where('txtImageName','theme_01_section_12_img_03')->first();
						 
					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

						
			
														
							$recId = DB::table('products')->select('id')->where('intDisplayOrder','3')->orWhere('varTitle','Product Three')->first();
				if(!isset($recId->id)){
			
							$insetId = DB::table('products')->insertGetId([
			
				'varTitle' =>  "Product Three",
										'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Product Three'),$moduleCode->id),
										'fkIntImgId' => $fkIntImgId->id,
											
											
			
			
							'intDisplayOrder'=> '3',
							'txtDescription' => "",
				'txtShortDescription' => "",
				'chrPublish' => 'Y',
				'chrDelete'=> 'N',
							'varMetaTitle' => "Product Three",
				'varMetaKeyword' => "Product Three",
				'varMetaDescription' => "",
							'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

									DB::table('image_module_rel')->insert([
							'intFkImgId' => $fkIntImgId->id,
							'intFkModuleCode'=>$moduleCode->id,
							'intRecordId'=>$insetId,
							'created_at'=>Carbon::now(),
							'updated_at'=>Carbon::now(),
						]);
		 
										}
					 
				 
				 
				 
				 
				 
				 

				 
					 
							 
							 
					 
					 
						 
											 

					 


											 
						 
												$fkIntImgId = DB::table('image')->select('id')->where('txtImageName','theme_01_section_12_img_04')->first();
						 
					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

						
			
														
							$recId = DB::table('products')->select('id')->where('intDisplayOrder','4')->orWhere('varTitle','Product Four')->first();
				if(!isset($recId->id)){
			
							$insetId = DB::table('products')->insertGetId([
			
				'varTitle' =>  "Product Four",
										'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Product Four'),$moduleCode->id),
										'fkIntImgId' => $fkIntImgId->id,
											
											
			
			
							'intDisplayOrder'=> '4',
							'txtDescription' => "",
				'txtShortDescription' => "",
				'chrPublish' => 'Y',
				'chrDelete'=> 'N',
							'varMetaTitle' => "Product Four",
				'varMetaKeyword' => "Product Four",
				'varMetaDescription' => "",
							'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

									DB::table('image_module_rel')->insert([
							'intFkImgId' => $fkIntImgId->id,
							'intFkModuleCode'=>$moduleCode->id,
							'intRecordId'=>$insetId,
							'created_at'=>Carbon::now(),
							'updated_at'=>Carbon::now(),
						]);
		 
										}
					 
				 
				 
				 
				 
				 
				 

				 
					 
							 
							 
					 
					 
						 
											 

					 


											 
						 
												$fkIntImgId = DB::table('image')->select('id')->where('txtImageName','theme_01_section_12_img_01')->first();
						 
					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

				 
					 
							 
							 
					 
					 
						 
											 

					 


					 
					
					 
					
					 

						
			
														
							$recId = DB::table('products')->select('id')->where('intDisplayOrder','5')->orWhere('varTitle','Product Five')->first();
				if(!isset($recId->id)){
			
							$insetId = DB::table('products')->insertGetId([
			
				'varTitle' =>  "Product Five",
										'intAliasId' => MyLibrary::insertAlias(slug::create_slug('Product Five'),$moduleCode->id),
										'fkIntImgId' => $fkIntImgId->id,
											
											
			
			
							'intDisplayOrder'=> '5',
							'txtDescription' => "",
				'txtShortDescription' => "",
				'chrPublish' => 'Y',
				'chrDelete'=> 'N',
							'varMetaTitle' => "Product Five",
				'varMetaKeyword' => "Product Five",
				'varMetaDescription' => "",
							'created_at'=> Carbon::now(),
				'updated_at'=> Carbon::now()
			]);

									DB::table('image_module_rel')->insert([
							'intFkImgId' => $fkIntImgId->id,
							'intFkModuleCode'=>$moduleCode->id,
							'intRecordId'=>$insetId,
							'created_at'=>Carbon::now(),
							'updated_at'=>Carbon::now(),
						]);
		 
										}
								#=
		#==
	}
}
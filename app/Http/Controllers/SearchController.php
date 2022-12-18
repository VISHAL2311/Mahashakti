<?php

namespace App\Http\Controllers;

use config;
use Request;
use Request as CustomRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Modules;
use DB;
use App\Helpers\MyLibrary;
use App\Helpers\DocumentHelper;
use App\Helpers\GlobalSearch_hits;
use App\GlobalSearch;
use Powerpanel\CmsPage\Models\CmsPage;
use App\Alias;
use App\Helpers\FileToText;
use App\Helpers\RemoteFileToText;
use Validator;

class SearchController extends FrontController {
		/*
		 * Create a new controller instance.
		 *
		 * @return void
		 */

		public static $ignoreCommonWords = array('the','and','are');

		public function __construct() {
				parent::__construct();
		}

		/**
		 * This method loads index of Search Page
		 * @return  View
		 * @since   2018-09-15
		 * @author  NetQuick
		 */
		public function index() {
				$postData = Request::get();
				$term = Request::get('frontSearch');
				if ($term == "") {
						return redirect('/');
				}
				return view('frontsearch');
		}

		public function autoComplete() {
				$response = '';
				$term = Request::post('term');
				$onlystring = trim(preg_replace('/[^A-Za-z0-9]/', ' ', $term));
				$term = preg_replace('!\s+!', ' ', $term);
				$dataResult = array();
				if(strlen($term) > 2 && trim($onlystring) !="" && !in_array(strtolower($term), self::$ignoreCommonWords)){
					$dataResult = GlobalSearch::autocomplete_termSeach($term);
				}
				if (!empty($dataResult)) {
						/*foreach ($dataResult as $record) {
								$liDispTerm = str_replace("\xE2\x80\x8B", "", $record->term);
                $response .= '<li>' . $liDispTerm . '</li>';
						}*/
						$response .= Self::generateAutoSuggestionLink($dataResult,$term);
				}
				return $response;
		}

		/**
		 * This method loads Search result list view
		 * @return  View
		 * @since   2018-09-15
		 * @author  NetQuick
		 */
		public function search(Request $request) {
				$data = Request::post();
				$currentPage = (null !== (Request::post('page'))) ? Request::post('page') : 1;
				$limit = 10;
				$rules = array('frontSearch' => 'required|handle_xss');
				$messsages = array(
						'frontSearch.required' => 'Search term is required',
						'frontSearch.handle_xss' => 'Please enter valid search'
				);
				$validator = Validator::make($data, $rules, $messsages);
				if (!$validator->passes()) {
						return Redirect::to($data['current_page'])->withErrors($validator)->withInput();
				}
				$term = $data['frontSearch'];
				$term = preg_replace('!\s+!', ' ', $term);
				$dataResult = array();
				$onlystring = trim(preg_replace('/[^A-Za-z0-9]/', ' ', $term));
				if(strlen($term) > 2 && $onlystring !="" && !in_array(strtolower($term), self::$ignoreCommonWords)){
					$dataResult = GlobalSearch::termSeach($term,$limit);
				}
				$similarWords = array();
				if (!empty($dataResult) && count($dataResult) > 0) {
						GlobalSearch_hits::insertSearchHits($term);
				} else {
						//code for get top 5 similar word from data
						if(strlen($term) > 2 && trim($onlystring) !="" && !in_array(strtolower($term), self::$ignoreCommonWords)){
							$getTop5Words = GlobalSearch::getTopSimilarWords(self::cleanString($term));
							if (!empty($getTop5Words)) {
									foreach ($getTop5Words as $key => $value) {
											$similarWords[] = $value->varTitle;
									}
							}
						}
				}

				foreach ($dataResult as $index => $result) {
						if ($result->pageAliasId != 'na') {
								$pageAlias = Alias::select('alias.varAlias')
												->where('alias.id', $result->pageAliasId)
												->first();
								if (isset($pageAlias->varAlias)) {
										$dataResult[$index]->pageAlias = $pageAlias->varAlias;
										if ($result->varModuleName == "publications-category") {
												$dataResult[$index]->searchView = "docdownload";
										}
								}
								continue;
						}
						if ($result->intFKCategory == 'na' || $result->slug === null) {

								#Page alias================================
								if ($result->moduleId != 4) {
										$pageAlias = CmsPage::select('alias.varAlias')
														->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
														->where('cms_page.intFKModuleCode', $result->moduleId)
														->where('cms_page.chrMain', 'Y')
														->where('cms_page.chrPublish', 'Y')
														->where('cms_page.chrDelete', 'N')
														->first();
										if (isset($pageAlias->varAlias)) {
												$dataResult[$index]->pageAlias = $pageAlias->varAlias;
										}
								}

								if($result->intFKCategory != 'na'){
									$MODEL = '\\App\\' . $result->varModelName;

									$categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($result->varModuleName,$result->intFKCategory);
									$dataResult[$index]->pageAlias = $dataResult[$index]->pageAlias."/".$categoryRecordAlias;
								}

								#./Page alias================================
						} else {

                	if($result->varModuleName=="news"){
                		$categoryModuleObj = Modules::where('varModuleName', 'news-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "news-category";
                	}else if($result->varModuleName=="events"){
                		$categoryModuleObj = Modules::where('varModuleName', 'event-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "event-category";
                	}else if($result->varModuleName=="publications"){
                		$categoryModuleObj = Modules::where('varModuleName', 'publications-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "publications-category";
                	}else if($result->varModuleName=="photo-album"){
                		$categoryModuleObj = Modules::where('varModuleName', 'photo-album-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "photo-album-category";
                	}else{
                		$pagemodulecode = $result->moduleId;
                	}

                	if(isset($catModuleName)){
                		$pageAlias = CmsPage::select('alias.varAlias')
                          ->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
                          ->where('cms_page.intFKModuleCode', $pagemodulecode)
                          ->where('cms_page.chrMain', 'Y')
                          ->where('cms_page.chrPublish', 'Y')
                          ->where('cms_page.chrDelete', 'N')
                          ->first();
                    if (isset($pageAlias->varAlias)) {
                        $dataResult[$index]->pageAlias = $pageAlias->varAlias;
                    }	
                	}
                	
									#Category page alias================================
									if(isset($catModuleName)){
										$categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($catModuleName,$result->intFKCategory);
										$dataResult[$index]->pageAlias = $dataResult[$index]->pageAlias."/".$categoryRecordAlias;
										if ($result->varModuleName == "publications") {
												$dataResult[$index]->searchView = "docdownload";
										}	
									}              
						}
				}
				$data = array();
				$data['similarWords'] = array_unique($similarWords);
				$data['searchResults'] = $dataResult;
				$data['searchFoundCounter'] = (!empty($dataResult)) ? GlobalSearch::termSeach($term,$limit,true) : 0;
				$data['searchTerm'] = $term;
				$data['ajaxModuleUrl'] = url('/search');
				$data['currentPage'] = $currentPage;
				$data['lastPage'] = ceil($data['searchFoundCounter'] / $limit);
				//$data['searchDocs'] = $this->docSearch($term);
				if(strlen($term) > 2 && $onlystring !="" && !in_array(strtolower($term), self::$ignoreCommonWords)){
					$data['searchDocs'] = $this->documentsTableSearch($term);
				}
				
				view()->share('META_TITLE', "Search Result - Central Bank Of Bahamas");
				view()->share('META_KEYWORD', "search the Central Bank Of Bahamas website, Central Bank Of Bahamas website search option, search on Central Bank Of Bahamas");
				view()->share('META_DESCRIPTION', "Can't find what you are looking for on the Central Bank Of Bahamas website? Enter your keywords in the search bar and get a list of all the matching pages quickly. Search now!");
				if (CustomRequest::ajax()) {
					$returnRepsonse = array();
					$returnHtml = view('search-found-ajax', $data)->render();
					$returnRepsonse = array('html' => $returnHtml, 'lastpage' => $data['lastPage'], 'currentpage' => $data['currentPage']);
					if ($data['lastPage'] > $data['currentPage']) {
							$returnRepsonse['loadmoreHtml'] = '<div class="col-sm-12 load-more">
											<a href="javascript:;" id="load-more" title="Load More" class="btn-load">
													Load More<i class="fa fa-plus" aria-hidden="true"></i>
											</a>
									</div>';
					}
					return $returnRepsonse;
				}else{
					return view('search-found', $data);	
				}
		}
		
		public function documentsTableSearch($term) {
				$response = [];
				$records = GlobalSearch::getDocumentsSearchByTerm($term);
				$AWSContants = MyLibrary::getAWSconstants();
				$_APP_URL = $AWSContants['CDN_PATH'];
				
				if(!empty($records)){
						foreach($records as $record){
								$link = "";
								if ($AWSContants['BUCKET_ENABLED']) {
										$link = $_APP_URL . $AWSContants['S3_MEDIA_BUCKET_DOCUMENT_PATH'] . '/' . $record->txtSrcDocumentName . '.' . $record->varDocumentExtension;
								} else {
										$link = url('/documents/' . $record->txtSrcDocumentName . '.' . $record->varDocumentExtension);
								}
								$response[$record->id]['id'] = $record->id;
								$response[$record->id]['link'] = $link;
								$response[$record->id]['ext'] = $record->varDocumentExtension;
								$response[$record->id]['filename'] = $record->txtDocumentName;
								$response[$record->id]['txtSrcDocumentName'] = $record->txtSrcDocumentName;
						}
				}
				
				return $response;
		}

		/**
		 * This method genrate link for autosuggestion Search result list view
		 * @return  View
		 * @since   2018-09-15
		 * @author  NetQuick
		 */
		public function generateAutoSuggestionLink($dataResult,$searchTerm) {
			$returnHtml = "";
			
			foreach ($dataResult as $index => $result) {
						if ($result->pageAliasId != 'na') {
								$pageAlias = Alias::select('alias.varAlias')
												->where('alias.id', $result->pageAliasId)
												->first();
								if (isset($pageAlias->varAlias)) {
										$dataResult[$index]->pageAlias = $pageAlias->varAlias;
										if ($result->varModuleName == "publications-category") {
												$dataResult[$index]->searchView = "docdownload";
										}
								}
								continue;
						}
						if ($result->intFKCategory == 'na' || $result->slug === null) {

								#Page alias================================
								if ($result->moduleId != 4) {
										$pageAlias = CmsPage::select('alias.varAlias')
														->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
														->where('cms_page.intFKModuleCode', $result->moduleId)
														->where('cms_page.chrMain', 'Y')
														->where('cms_page.chrPublish', 'Y')
														->where('cms_page.chrDelete', 'N')
														->first();
										if (isset($pageAlias->varAlias)) {
												$dataResult[$index]->pageAlias = $pageAlias->varAlias;
										}
								}

								if($result->intFKCategory != 'na'){
									$MODEL = '\\App\\' . $result->varModelName;

									$categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($result->varModuleName,$result->intFKCategory);
									$dataResult[$index]->pageAlias = $dataResult[$index]->pageAlias."/".$categoryRecordAlias;
								}

								#./Page alias================================
						} else {

                	if($result->varModuleName=="news"){
                		$categoryModuleObj = Modules::where('varModuleName', 'news-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "news-category";
                	}else if($result->varModuleName=="events"){
                		$categoryModuleObj = Modules::where('varModuleName', 'event-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "event-category";
                	}else if($result->varModuleName=="publications"){
                		$categoryModuleObj = Modules::where('varModuleName', 'publications-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "publications-category";
                	}else if($result->varModuleName=="photo-album"){
                		$categoryModuleObj = Modules::where('varModuleName', 'photo-album-category')->first();
                		$pagemodulecode = $categoryModuleObj->id;
                		$catModuleName = "photo-album-category";
                	}else{
                		$pagemodulecode = $result->moduleId;
                	}

                	if(isset($catModuleName)){
                		$pageAlias = CmsPage::select('alias.varAlias')
                          ->leftJoin('alias', 'alias.id', '=', 'cms_page.intAliasId')
                          ->where('cms_page.intFKModuleCode', $pagemodulecode)
                          ->where('cms_page.chrMain', 'Y')
                          ->where('cms_page.chrPublish', 'Y')
                          ->where('cms_page.chrDelete', 'N')
                          ->first();
                    if (isset($pageAlias->varAlias)) {
                        $dataResult[$index]->pageAlias = $pageAlias->varAlias;
                    }	
                	}
                	
									#Category page alias================================
									if(isset($catModuleName)){
										$categoryRecordAlias = Mylibrary::getRecordAliasByModuleNameRecordId($catModuleName,$result->intFKCategory);
										$dataResult[$index]->pageAlias = $dataResult[$index]->pageAlias."/".$categoryRecordAlias;
										if ($result->varModuleName == "publications") {
												$dataResult[$index]->searchView = "docdownload";
										}	
									}              
						}
				}

			//code for genrate link for result
			$searchResults = $dataResult;
			if(!empty($searchResults) && count($searchResults) > 0){
				foreach($searchResults as $result){
					if(is_array($result->pageAlias)){
						foreach($result->pageAlias as $alias){
							if($result->slug != 'null'){
								$url = url('/'.$alias.'/'.$result->slug);
							}else{
								$url = url('/'.$alias);
							}

							if($result->moduleTitle == 'Event Category'){
							  $mtitle = 'Events';
							}
						  elseif($result->moduleTitle == 'News Category'){
							  $mtitle = 'News';
						  }
							elseif($result->moduleTitle == 'Photo Album Category'){
							  $mtitle = 'Photo Album';
							}
							elseif($result->moduleTitle == 'Publications Category'){
							  $mtitle = 'Publications';
							}
							elseif($result->moduleTitle == 'FAQ Category'){
							  $mtitle = 'FAQs';
							}
							else{
							  $mtitle = $result->moduleTitle;
							}
							

							$returnHtml .='<li>'.'<a href="'.$url.'">'.$result->term.' - <span class="mtitle">'.ucfirst($mtitle).'</span>'.'</a>'.'</li>';

						}
					}else{
							if(isset($result->pageAlias)){
								if($result->slug != 'null'){
									$licenceignoreids = array('2','3','4');
									if($result->varModelName=="LicensedEntities"){
										if(in_array($result->intFKCategory,$licenceignoreids)){
											$url = url('/'.$result->pageAlias);
										}else{
											$url = url('/'.$result->pageAlias.'/'.$result->slug);
										}
									}else{
										$url = url('/'.$result->pageAlias.'/'.$result->slug);
									}
								}else{
								 $url = url('/'.$result->pageAlias); 
								 	if($result->moduleId == 47){
										$url = $url.'/?title='.$searchTerm;
									}
								}
							}else{
								$url = url('/'.$result->slug);
							}
							
							if($result->moduleTitle == 'Event Category'){
							  $mtitle = 'Events';
							}
						  elseif($result->moduleTitle == 'News Category'){
							  $mtitle = 'News';
						  }
							elseif($result->moduleTitle == 'Photo Album Category'){
							  $mtitle = 'Photo Album';
							}
							elseif($result->moduleTitle == 'Publications Category'){
							  $mtitle = 'Publications';
							}
							elseif($result->moduleTitle == 'FAQ Category'){
							  $mtitle = 'FAQs';
							}
							else{
							  $mtitle = $result->moduleTitle;
							}

							$returnHtml .='<li>'.'<a href="'.$url.'">'.$result->term.' - <span class="mtitle">'.ucfirst($mtitle).'</span>'.'</a>'.'</li>';
					}
				}
			}

			return $returnHtml;

		}

		/*public function docSearch($term) {
				$response = [];
				$records = GlobalSearch::getDescriptionRecords();
				ini_set('memory_limit', '-1');
				$AWSContants = MyLibrary::getAWSconstants();
				$_APP_URL = $AWSContants['CDN_PATH'];
				foreach ($records as $record) {
						$txt = $record->txtDescription;
						$validFileExtensions = array('doc', 'docx');
						if ($term != "") {
								preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $txt, $matches);
								if ($record->fkIntDocId != 'na') {
										$matches = [];
										$doc = DocumentHelper::getDocsByIds([$record->fkIntDocId]);
										if (isset($doc[0])) {
												if ($AWSContants['BUCKET_ENABLED']) {
														$docpath = $AWSContants['S3_MEDIA_BUCKET_DOCUMENT_PATH'] . '/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension;
														$fileExists = Mylibrary::filePathExist($docpath);
												} else {
														$docpath = public_path('/documents/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension);
														$fileExists = file_exists($docpath);
												}

												if ($fileExists) {
														if ($AWSContants['BUCKET_ENABLED']) {
																$matches[2][0] = $_APP_URL . $AWSContants['S3_MEDIA_BUCKET_DOCUMENT_PATH'] . '/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension;
														} else {
																$matches[2][0] = url('/documents/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension);
														}
														$matches[2][1] = $doc[0]->id;
														$matches[2][2] = $doc[0]->txtDocumentName;
														$matches[2][3] = $doc[0]->varDocumentExtension;
														$matches[2][4] = $doc[0]->filesize;
												}
										}
								}

								$matchFound = false;
								$fileSource = null;
								$link = '';
								$fileOrginalName = "";
								$fileExt = "";
								$fileId = 0;
								$filesize = 0;
								if (isset($matches[2][0])) {
										$fileSource = $matches[2][0];
										$fileSrcName = basename($fileSource);
										$fileId = (isset($matches[2][1])) ? $matches[2][1] : 0;
										$fileOrginalName = (isset($matches[2][2])) ? $matches[2][2] : '';
										$fileExt = (isset($matches[2][3])) ? $matches[2][3] : '';
										$filesize = (isset($matches[2][4])) ? $matches[2][4] : 0;
										$link = $fileSource;
										$fileData = $fileSource;
										$customPath = false;
										$validfile = false;
										if (count($fileData) > 0) {
												if ($filesize < 5242880) {
														$externalPathInfo = pathinfo($fileSource);
														if (isset($externalPathInfo['extension']) && in_array($externalPathInfo['extension'], $validFileExtensions)) {
																/* if ($fileId == 0) {
																	// code for if file added in ckEditor
																	if (isset($matches[3][0]) && $matches[3][0] != "") {
																	$hitfunction = $matches[3][0];
																	$extractFileIdCoutArray = explode('setDocumentHitCounter(', $hitfunction);
																	if (isset($extractFileIdCoutArray[1]) && $extractFileIdCoutArray[1] != "") {
																	$extractFileId = explode(',', $extractFileIdCoutArray[1])[0];
																	if (is_numeric($extractFileId) && $extractFileId > 0) {
																	$doc = DocumentHelper::getDocsByIds([$extractFileId]);
																	if (isset($doc[0])) {
																	if ($AWSContants['BUCKET_ENABLED']) {
																	$docpath = $AWSContants['S3_MEDIA_BUCKET_DOCUMENT_PATH'] . '/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension;
																	$fileExists = Mylibrary::filePathExist($docpath);
																	} else {
																	$docpath = public_path('/documents/' . $doc[0]->txtSrcDocumentName . '.' . $doc[0]->varDocumentExtension);
																	$fileExists = file_exists($docpath);
																	}

																	if ($fileExists) {
																	$fileId = $doc[0]->id;
																	$fileOrginalName = $doc[0]->txtDocumentName;
																	$fileExt = $doc[0]->varDocumentExtension;
																	$filesize = $doc[0]->filesize;
																	}
																	}
																	}
																	}
																	}
																	} */
																/*if ($fileId > 0) {
																		$newfile = public_path() . '/vendor/filetotext/' . time() . '.' . $externalPathInfo['extension'];
																		if (file_exists($newfile)) {
																				unlink($newfile);
																		} else {
																				if ($this->is_url_exist($fileSource)) {
																						$fileSource1 = file_get_contents($fileSource);
																						//file_put_contents($newfile, $fileSource1);
																				}
																		}
																		//$fileSource = $newfile;
																		$customPath = true;
																		$validfile = true;
																}
														}
												}
										} else {
												$fileSource = '';
										}
								}
								if (!empty($fileSource)) {
										if ($validfile) {
												if ($fileId == 1006) {
														$matchFound = $this->searchContentPdf($fileSource, $term);
														if (isset($newfile) && file_exists($newfile)) {
																unlink($newfile);
														}
														if (!empty($matchFound)) {
																if ($fileId > 0) {
																		$response[$fileId]['id'] = $fileId;
																		$response[$fileId]['link'] = $link;
																		$response[$fileId]['ext'] = $fileExt;
																		$response[$fileId]['filename'] = $fileOrginalName;
																}
														}
												}
										}
								}
						}
				}
				$response = $response;
				return $response;
		}*/

		public function searchContentPdf($fileSource = false, $searchString = false) {
				if ($fileSource) {
						//$docObj = new FileToText($fileSource);
						//$filecontent = $docObj->convertToText();
						/*new code */
						$docObj = new RemoteFileToText($fileSource);
						$filecontent = $docObj->convertToText();
						
						$filecontent = $this->get_string_between($filecontent, $searchString);
						$filecontent = (!empty($filecontent)) ? $filecontent . "..." : '';
						return $filecontent;
				}
		}

		public function get_string_between($string, $start, $end = false) {
				$string = ' ' . $string;
				$string = strtolower($string);
				$start = strtolower($start);
				$ini = strpos($string, $start);
				if ($ini == 0)
						return '';
				if ($end == false) {
						$len = $ini + 50;
				} else {
						$len = strpos($string, $end, $ini) - $ini;
				}
				return substr($string, $ini, $len);
		}

		function is_url_exist($url) {
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				curl_exec($ch);
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				if ($code == 200) {
						$status = true;
				} else {
						$status = false;
				}
				curl_close($ch);
				return $status;
		}

		public static function cleanString($string){
			return addslashes($string);
		}

}

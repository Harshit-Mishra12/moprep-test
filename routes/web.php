<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', "Auth\LoginController@showLoginForm");
Route::get('get-chapter', "HomeController@getChapter");
Route::get('get-topic', "HomeController@getTopic");
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::get('faq/{slug}', 'HomeController@faq');
Route::get('demo/{slug}', 'HomeController@demo');
Route::get('course-details/{slug}', 'HomeController@courseDetails');
Route::get('testimonial', 'HomeController@testimonial');
Route::get('contact-us', 'HomeController@contactUs');
Route::post('login', 'Auth\LoginController@login')->name('login');

Route::get('privacy-policy', 'HomeController@privacyPolicy');
Route::get('terms-condition', 'HomeController@termsCondition');


Route::match(['get', 'post'], 'update-payment', 'HomeController@updatePayment');

Route::match(['get', 'post'], '/change-password', 'Admin\AdminController@changePassword')->name('changepassword');

Route::group(['prefix' => 'admin', 'as' => 'admin', 'middleware' => ['auth', 'checkadmin'], 'as' => 'admin.'], function () {

	Route::match(['get', 'post'], '/logout', 'Auth\LoginController@logout')->name('logout');
	Route::match(['get', 'post'], '/dashboard', 'Admin\DashboardController@index');

	// User
	Route::group(['prefix' => 'user'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\UserController@add')->name('user.add');
		Route::get('list', 'Admin\UserController@userList');
		Route::get('userListData', 'Admin\UserController@userListData')->name('user.userListData');
		Route::post('change-status', 'Admin\UserController@changeStatus')->name('user.changestatus');
		Route::get('update/{id}', 'Admin\UserController@updateUser');
		Route::get('delete/{id}', 'Admin\UserController@deleteUser');
		Route::get('examDetail/{id}', 'Admin\UserController@examDetailUser');
		Route::get('detail/{id}', 'Admin\UserController@detailUser');
		Route::get('detailPre/{id}', 'Admin\UserController@detailUserPre');
		Route::get('detailMock/{id}', 'Admin\UserController@detailUserMock');
	});

	// Admin User
	Route::group(['prefix' => 'admin-user'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\AdminUserController@add')->name('admin-user.add');
		Route::get('list', 'Admin\AdminUserController@userList');
		Route::get('userListData', 'Admin\AdminUserController@userListData')->name('admin-user.userListData');
		Route::post('change-status', 'Admin\AdminUserController@changeStatus')->name('admin-user.changestatus');
		Route::get('update/{id}', 'Admin\AdminUserController@updateUser');
		Route::get('delete/{id}', 'Admin\AdminUserController@deleteUser');
		Route::get('examDetail/{id}', 'Admin\AdminUserController@examDetailUser');
		Route::get('detail/{id}', 'Admin\AdminUserController@detailUser');
		Route::get('detailPre/{id}', 'Admin\AdminUserController@detailUserPre');
		Route::get('detailMock/{id}', 'Admin\AdminUserController@detailUserMock');
	});

	// Course
	Route::group(['prefix' => 'course'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\CourseController@add')->name('course.add');
		Route::match(['get', 'post'], 'map', 'Admin\CourseController@map')->name('course.map');
		Route::get('list', 'Admin\CourseController@subjectList');
		Route::get('map-list', 'Admin\CourseController@mapList');
		Route::post('change-status', 'Admin\CourseController@changeStatus')->name('course.changestatus');
		Route::get('update/{id}', 'Admin\CourseController@updateSubject');
		Route::get('view/{id}', 'Admin\CourseController@viewSubject');
		Route::get('delete/{id}', 'Admin\CourseController@deleteSubject');
		Route::post('change-order', 'Admin\CourseController@changeOrder')->name('course.change-order');
		Route::post('change-live', 'Admin\CourseController@changeLive')->name('course.changelive');
	});


	// Course Master
	Route::group(['prefix' => 'coursemaster'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\CourseMasterController@add')->name('coursemaster.add');
		Route::get('list', 'Admin\CourseMasterController@courseList')->name('coursemaster.list');
		Route::post('change-status', 'Admin\CourseMasterController@changeStatus')->name('coursemaster.changestatus');
		Route::get('update/{id}', 'Admin\CourseMasterController@updateCourse');
		Route::get('view/{id}', 'Admin\CourseMasterController@viewCourse');
		Route::get('delete/{id}', 'Admin\CourseMasterController@deleteCourse');
		Route::post('change-order', 'Admin\CourseMasterController@changeOrder')->name('coursemaster.change-order');
		Route::post('change-live', 'Admin\CourseMasterController@changeLive')->name('coursemaster.changelive');
	});

	// Batch
	Route::group(['prefix' => 'batch'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\BatchController@add')->name('batch.add');
		Route::get('list', 'Admin\BatchController@batchList');
		Route::post('change-status', 'Admin\BatchController@changeStatus')->name('batch.changestatus');
		Route::get('update/{id}', 'Admin\BatchController@updateBatch');
		Route::get('delete/{id}', 'Admin\BatchController@deleteBatch');
		Route::post('change-order', 'Admin\BatchController@changeOrder')->name('batch.change-order');
	});

	//FAQ'S
	Route::group(['prefix' => 'faq'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\FaqController@add')->name('faq.add');
		Route::get('list', 'Admin\FaqController@faqList');
		Route::post('change-status', 'Admin\FaqController@changeStatus')->name('faq.changestatus');
		Route::get('update/{id}', 'Admin\FaqController@updateFaq');
		Route::get('delete/{id}', 'Admin\FaqController@deleteFaq');
	});

	//Demo_Videos
	Route::group(['prefix' => 'demo_video'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\DemoController@add')->name('demo_video.add');
		Route::get('list', 'Admin\DemoController@demoList');
		Route::post('change-status', 'Admin\DemoController@changeStatus')->name('demo_video.changestatus');
		Route::get('update/{id}', 'Admin\DemoController@updateDemo');
		Route::get('delete/{id}', 'Admin\DemoController@deleteDemo');
	});

	// TopicMaterials
	Route::group(['prefix' => 'topic-materials'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\TopicMaterialsController@add')->name('topic-materials.add');
		Route::get('list', 'Admin\TopicMaterialsController@topicMaterialList');
		Route::get('listData', 'Admin\TopicMaterialsController@topicMaterialListData')->name('topic-materials.topicMaterialListData');
		Route::post('change-status', 'Admin\TopicMaterialsController@changeStatus')->name('topic-materials.changestatus');
		Route::get('update/{id}', 'Admin\TopicMaterialsController@updateTopicMateria');
		Route::get('delete/{id}', 'Admin\TopicMaterialsController@deleteTopicMateria');
		Route::post('get-batch-name', 'Admin\TopicMaterialsController@getBatchName');
		Route::post('change-live', 'Admin\TopicMaterialsController@changeLive')->name('topic-materials.changelive');
		Route::post('change-lock', 'Admin\TopicMaterialsController@changeLock')->name('topic-materials.changelock');
	});

	// NoteMaterials
	Route::group(['prefix' => 'notes-materials'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\NoteMaterialsController@add')->name('notes-materials.add');
		Route::get('list', 'Admin\NoteMaterialsController@notesMaterialList');
		Route::post('change-status', 'Admin\NoteMaterialsController@changeStatus')->name('notes-materials.changestatus');
		Route::get('update/{id}', 'Admin\NoteMaterialsController@updateNotesMaterial');
		Route::get('delete/{id}', 'Admin\NoteMaterialsController@deleteNotesMaterial');
		Route::post('get-batch-name', 'Admin\NoteMaterialsController@getBatchName');
		Route::post('get-topic', 'Admin\NoteMaterialsController@getTopicName');
	});

	// ZoomClasses
	Route::group(['prefix' => 'zoom-classes'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\ZoomClassesController@add')->name('zoom-classes.add');
		Route::get('list', 'Admin\ZoomClassesController@zoomClassesList');
		Route::post('change-status', 'Admin\ZoomClassesController@changeStatus')->name('zoom-classes.changestatus');
		Route::get('update/{id}', 'Admin\ZoomClassesController@zoomClassesUpdate');
		Route::get('delete/{id}', 'Admin\ZoomClassesController@zoomClassesDelete');
		Route::post('get-batch-name', 'Admin\ZoomClassesController@zoomClassesGetBatchName');
		Route::post('get-topic', 'Admin\ZoomClassesController@zoomClassesGetTopicName');
	});

	//Question Bank

	Route::group(['prefix' => 'question_bank'], function () {

		Route::group(['prefix' => 'chapter'], function () {
			Route::match(['get', 'post'], 'add', 'Admin\QuestionBankController@add')->name('question_bank.chapter.add');
			Route::get('list', 'Admin\QuestionBankController@chapterList');
			Route::post('change-order', 'Admin\QuestionBankController@changeOrder')->name('chapter.change-order');
			Route::post('change-status', 'Admin\QuestionBankController@changeStatus')->name('question_bank.chapter.changestatus');
			Route::get('update/{id}', 'Admin\QuestionBankController@updateChapter');
			Route::get('view/{id}', 'Admin\QuestionBankController@viewTopics');
			Route::get('delete/{id}', 'Admin\QuestionBankController@deleteChapter');
			Route::post('change-livechapterlist', 'Admin\QuestionBankController@changeLiveChapterList')->name('question_bank.chapter.changelivechapterlist');
		});

		Route::group(['prefix' => 'question'], function () {
			Route::match(['get', 'post'], 'add', 'Admin\QuestionBankController@addQuestion')->name('question_bank.question.add_question');
			Route::get('list', 'Admin\QuestionBankController@questionList');
			Route::get('listData', 'Admin\QuestionBankController@questionListData')->name('question_bank.question.questionListData');
			Route::get('view/{course_id}/{type}/{course_master_id}', 'Admin\QuestionBankController@viewQuestion');
			Route::get('viewCourseQuestions', 'Admin\QuestionBankController@viewCourseQuestions')->name('question_bank.question.viewCourseQuestions');
			Route::get('view-mock-question/{course_id}/{type}', 'Admin\QuestionBankController@viewMockQuestion');
			Route::get('update/{id}', 'Admin\QuestionBankController@updateQuestion');
			Route::get('delete/{id}', 'Admin\QuestionBankController@deleteQuestion');
			Route::post('change-status', 'Admin\QuestionBankController@changeRecall')->name('question_bank.question.changeRecall');
			Route::match(['get', 'post'], 'addQuestionWord', 'Admin\QuestionBankController@addQuestionWord')->name('question_bank.question.add_question_word');
			Route::get('loadQuestion', 'Admin\QuestionBankController@selectedYearQuestion');

			Route::post('get-subject-name', 'Admin\QuestionBankController@getSubjectName');
			Route::post('change-live', 'Admin\QuestionBankController@changeLive')->name('question_bank.question.changelive');
			Route::post('bulkUpdateStatus', 'Admin\QuestionBankController@bulkUpdateStatus')->name('questionBank.bulkUpdateStatus');
			Route::post('bulkUpdatePYQQuestion', 'Admin\QuestionBankController@bulkUpdatePYQQuestion')->name('questionBank.bulkUpdatePYQQuestion');
			Route::post('bulkUpdateQuestion', 'Admin\QuestionBankController@bulkUpdateQuestion')->name('questionBank.bulkUpdateQuestion');
		});
	});


	Route::group(['prefix' => 'questionBank'], function () {
		Route::match(['get', 'post'], 'addQuestionWord', 'Admin\QuestionBankController@addQuestionWord')->name('questionBank.add_question_word');
		Route::get('yearList', 'Admin\QuestionBankController@questionYearList');
		Route::get('questionYearListData', 'Admin\QuestionBankController@questionYearListData')->name('questionBank.questionYearListData');

		// Route::post('get-subject-name','Admin\QuestionBankController@getSubjectName');

		Route::get('questionBulkList/{id}', 'Admin\QuestionBankController@questionBulkList');
		Route::get('updateBulkQuestion/{id}', 'Admin\QuestionBankController@updateBulkQuestion');
		Route::post('update_question', 'Admin\QuestionBankController@updateBulkQuestionSubmit')->name('questionBank.update_question');
		Route::get('deleteBulkQuestion/{id}', 'Admin\QuestionBankController@deleteBulkQuestion');
		Route::get('yearPartUpdate/{id}', 'Admin\QuestionBankController@yearPartUpdate')->name('questionBank.yearPartUpdate');
		Route::post('updateYearSubmit', 'Admin\QuestionBankController@updateYearSubmit')->name('questionBank.updateYearSubmit');
		Route::get('deleteYearQuestion/{id}', 'Admin\QuestionBankController@deleteYearQuestion');
		Route::match(['get', 'post'], 'addQuestionSingle', 'Admin\QuestionBankController@addQuestionSingle')->name('questionBank.addQuestionSingle');
		Route::post('change-live', 'Admin\QuestionBankController@changeLive')->name('questionBank.changelive');
		Route::post('change-liveprevyear', 'Admin\QuestionBankController@changeLivePrevYear')->name('questionBank.changeliveprevyear');
		Route::post('change-lockprevyear', 'Admin\QuestionBankController@changeLockPrevYear')->name('questionBank.changeLockPrevYear');
		Route::post('change-livepreyearlist', 'Admin\QuestionBankController@changeLivePreYearList')->name('questionBank.changelivepreyearlist');
	});

	// information list
	Route::group(['prefix' => 'information'], function () {
		Route::get('terms-condition', 'Admin\InformationController@termCondition');
		Route::get('privacy-policy', 'Admin\InformationController@privacyPolicy');
		Route::post('update', 'Admin\InformationController@UpdateDetail')->name('information.update');
	});

	//Banner

	Route::group(['prefix' => 'designation'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\DesignationsController@add')->name('designation.add');
		Route::get('list', 'Admin\DesignationsController@roleList');
		Route::post('change-status', 'Admin\DesignationsController@changeStatus')->name('designation.changestatus');
		Route::get('update/{id}', 'Admin\DesignationsController@updateDesignation');
		Route::get('delete/{id}', 'Admin\DesignationsController@deleteDesignation');
	});

	Route::group(['prefix' => 'banner'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\BannerController@add')->name('banner.add');
		Route::get('list', 'Admin\BannerController@bannerList');
		Route::post('change-status', 'Admin\BannerController@changeStatus')->name('banner.changestatus');
		Route::get('update/{id}', 'Admin\BannerController@updateBanner');
		Route::get('delete/{id}', 'Admin\BannerController@deleteBanner');
	});

	//Subscription

	Route::group(['prefix' => 'subscription'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\SubscriptionController@add')->name('subscription.add');
		Route::get('list', 'Admin\SubscriptionController@subscriptionList');
		Route::post('change-status', 'Admin\SubscriptionController@changeStatus')->name('subscription.changestatus');
		Route::get('update/{id}', 'Admin\SubscriptionController@updateSubscription');
		Route::get('delete/{id}', 'Admin\SubscriptionController@deleteSubscription');
	});


	//Notifications

	Route::group(['prefix' => 'notification'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\NotificationController@add')->name('notification.add');
		Route::get('list', 'Admin\NotificationController@notificationList');
		Route::get('listData', 'Admin\NotificationController@notificationListData')->name('notification.notificationListData');
		Route::post('change-status', 'Admin\NotificationController@changeStatus')->name('notification.changestatus');
		Route::get('update/{id}', 'Admin\NotificationController@updateNotification');
		Route::get('delete/{id}', 'Admin\NotificationController@deleteNotification');
	});


	//Mockup Test

	Route::group(['prefix' => 'mockup'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\MockupController@add')->name('mockup.add');
		Route::get('list', 'Admin\MockupController@mockupList');
		Route::get('listData', 'Admin\MockupController@mockupListData')->name('mockup.mockupListData');
		Route::post('change-st atus', 'Admin\MockupController@changeStatus')->name('mockup.changestatus');
		Route::post('change-lock', 'Admin\MockupController@changeLock')->name('mockup.changeLock');
		Route::get('update/{id}', 'Admin\MockupController@updateMockup');
		Route::get('delete/{id}', 'Admin\MockupController@deleteMockup');
		Route::get('detailMock/{id}', 'Admin\MockupController@detailUserMock');
	});

	//Mockup Questions

	Route::group(['prefix' => 'mockupquestion'], function () {
		Route::match(['get', 'post'], 'add', 'Admin\MockupQuestionController@add')->name('mockupquestion.add');
		Route::match(['get', 'post'], 'addSubjectQuestion', 'Admin\MockupQuestionController@addSubjectQuestion')->name('mockupquestion.addSubjectQuestion');
		Route::get('loadQuestion', 'Admin\MockupQuestionController@selectedYearQuestion');
		Route::get('loadQuestionSubject', 'Admin\MockupQuestionController@selectedSubjectQuestion');
		Route::get('list', 'Admin\MockupQuestionController@mockupList');
		Route::get('listData', 'Admin\MockupQuestionController@mockupListData')->name('mockupquestion.mockupListData');
		Route::get('view-mock-question/{mock_id}', 'Admin\MockupQuestionController@viewMockQuestion');
		Route::get('delete/{id}', 'Admin\MockupQuestionController@deleteMockup');
		Route::post('change-live', 'Admin\MockupQuestionController@changeLive')->name('mockupquestion.changelive');
	});

	Route::match(['get', 'post'], 'add-assign-batch', 'Admin\PurchaseController@addAssignBatch')->name('addAssignBatch');
	Route::post('getbatch', 'Admin\PurchaseController@getBatch');
	Route::get('delete-purchase-history/{id}', 'Admin\PurchaseController@deletePurchasedHistory');
	Route::get('accept-purchase-history/{id}', 'Admin\PurchaseController@acceptPurchasedHistory');
	Route::get('reject-purchase-history/{id}', 'Admin\PurchaseController@rejectPurchasedHistory');
	Route::get('purchase-history', 'Admin\PurchaseController@purchasedHistory');
	Route::get('purchase-history-data', 'Admin\PurchaseController@purchasedHistoryData')->name('purchasedHistoryData');
	Route::get('purchase-history-excel', 'Admin\PurchaseController@exportToExcel');
	Route::get('user-list-excel', 'Admin\PurchaseController@exportUserToExcel');
	Route::get('mock-exam-list-excel/{id}', 'Admin\PurchaseController@exportMockToExcel');
});


Route::match(['get', 'post'], '/logout', 'Auth\LoginController@logout')->name('logout');

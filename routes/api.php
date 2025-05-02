<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PreLoginController;
use App\Http\Controllers\API\PostLoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', "API\PreLoginController@register");
Route::post('login', "API\PreLoginController@login");
Route::post('verifyEmail', "API\PreLoginController@verifyEmail");
Route::post('resendOtp', "API\PreLoginController@resendOtp");
Route::post('verifyOtp', "API\PreLoginController@verifyOtp");
Route::get('stateList', "API\PreLoginController@stateList");
Route::get('mockTestSubmitAutoWithoutLogin', "API\PreLoginController@mockTestSubmitAutoWithoutLogin");
Route::post('createNewPassword', "API\PreLoginController@createNewPassword");
Route::get("privacyPolicy", "API\PreLoginController@privacyPolicy");
Route::match(['get', 'post'], 'webhookResponse', 'API\PreLoginController@webhookResponse')->name('webhookResponse');
Route::get("sendOtpSms", "API\PreLoginController@sendOtpSms");
Route::get("webhookResponseData", "API\PreLoginController@webhookResponseData");
Route::get("getWebhooks", "API\PreLoginController@getWebhooks");
Route::get("removeNotification", "API\PreLoginController@removeNotification");

// old api
// Route::post('check-exist-user', "API\PreLoginController@checkExistUser");
// Route::post('mobile-getotp', "API\PreLoginController@sendOtpOnMobile");
// Route::post('validate-otp', "API\PreLoginController@validateOtp");

Route::group([
	'middleware' => ['auth:api', 'Userauth']
], function () {

	// Home Screen
	Route::get("bannerList", "API\PostLoginController@bannerList");
	Route::get("uncompletedQuestionBank", "API\PostLoginController@uncompletedQuestionBank");
	Route::get("upcomingMockTests", "API\PostLoginController@upcomingMockTests");

	// Question Bank
	Route::get("questionBank", "API\PostLoginController@questionBank");
	Route::post("chapterQuestionBank", "API\PostLoginController@chapterQuestionBank");
	Route::post("questionBankSearch", "API\PostLoginController@questionBankSearch");
	Route::post("questionBankSearchSubject", "API\PostLoginController@questionBankSearchSubject");
	Route::post("chapterQuestions", "API\PostLoginController@chapterQuestions");
	Route::post("chapterQuestionsDelete", "API\PostLoginController@chapterQuestionsDelete");
	Route::post("questionBankQuestions", "API\PostLoginController@questionBankQuestions");
	Route::post("questionBankQuestionsResume", "API\PostLoginController@questionBankQuestionsResume");
	Route::post("questionBankAnswers", "API\PostLoginController@questionBankAnswers");
	Route::post("questionBankPause", "API\PostLoginController@questionBankPause");
	Route::post("questionBankQuit", "API\PostLoginController@questionBankQuit");
	Route::post("questionBankBookmark", "API\PostLoginController@questionBankBookmark");

	// Previous Year
	Route::get("previousYearTopics", "API\PostLoginController@previousYearTopics");
	Route::post("previousYearPartQuestions", "API\PostLoginController@previousYearPartQuestions");
	Route::post("previousYearPartQuestionsDelete", "API\PostLoginController@previousYearPartQuestionsDelete");
	Route::post("previousYearSearch", "API\PostLoginController@previousYearSearch");
	Route::post("previousYearQuestions", "API\PostLoginController@previousYearQuestions");
	Route::post("previousYearAnswers", "API\PostLoginController@previousYearAnswers");
	Route::post("previousYearPause", "API\PostLoginController@previousYearPause");
	Route::post("previousYearQuit", "API\PostLoginController@previousYearQuit");
	Route::post("previousYearBookmark", "API\PostLoginController@previousYearBookmark");

	// Mock Test
	Route::get("mockTestUpcomming", "API\PostLoginController@mockTestUpcomming");
	Route::get("mockTestPast", "API\PostLoginController@mockTestPast");
	Route::post("mockTestSearch", "API\PostLoginController@mockTestSearch");
	Route::post("mockTestInstructions", "API\PostLoginController@mockTestInstructions");
	Route::post("mockTestQuestions", "API\PostLoginController@mockTestQuestions");
	Route::post("mockTestAnswers", "API\PostLoginController@mockTestAnswers");
	Route::post("mockTestPause", "API\PostLoginController@mockTestPause");
	Route::post("mockTestEnd", "API\PostLoginController@mockTestEnd");
	Route::post("mockTestAnalysis", "API\PostLoginController@mockTestAnalysis");
	Route::post("mockTestBookmark", "API\PostLoginController@mockTestBookmark");
	Route::post("mockTestQuestionList", "API\PostLoginController@mockTestQuestionList");
	Route::post("mockTestQuestionListBookmarked", "API\PostLoginController@mockTestQuestionListBookmarked");
	Route::post("mockTestSolution", "API\PostLoginController@mockTestSolution");
	Route::post("mockTestSolutionBookmarked", "API\PostLoginController@mockTestSolutionBookmarked");
	Route::post("mockTestQuestionBookmark", "API\PostLoginController@mockTestQuestionBookmark");

	// User Profile
	Route::get("userProfile", "API\PostLoginController@userProfile");
	Route::post("updateUserProfile", "API\PostLoginController@updateUserProfile");
	Route::get("userProfileQbankCompleted", "API\PostLoginController@userProfileQbankCompleted");
	Route::get("userProfileMockCompleted", "API\PostLoginController@userProfileMockCompleted");

	// Subscription Plans
	Route::get("subscriptionPlansList", "API\PostLoginController@subscriptionPlansList");
	Route::post("subscriptionPlansDetail", "API\PostLoginController@subscriptionPlansDetail");

	// Notifications
	Route::get("notificaionList", "API\PostLoginController@notificaionList");
	Route::post("removeNotification", "API\PostLoginController@removeNotification");

	// Contact Us
	Route::get("contactUs", "API\PostLoginController@contactUs");

	// Bookmarks
	Route::get("bookmarkedMockupList", "API\PostLoginController@bookmarkedMockupList");
	Route::get("mockTestBookmarkedAnalysis", "API\PostLoginController@mockTestBookmarkedAnalysis");
	Route::get("mockTestResultAnalysis", "API\PostLoginController@mockTestResultAnalysis");

	Route::get("bookmarkedSubjectList", "API\PostLoginController@bookmarkedSubjectList");
	Route::post("bookmarkedSubjectQuestions", "API\PostLoginController@bookmarkedSubjectQuestions");
	Route::post("bookmarkedSubjectQuestionsDetail", "API\PostLoginController@bookmarkedSubjectQuestionsDetail");

	Route::get("bookmarkedYearList", "API\PostLoginController@bookmarkedYearList");
	Route::post("bookmarkedYearQuestions", "API\PostLoginController@bookmarkedYearQuestions");
	Route::post("bookmarkedYearQuestionsDetail", "API\PostLoginController@bookmarkedYearQuestionsDetail");

	Route::get("allBookmarkedQuestions", "API\PostLoginController@allBookmarkedQuestions");
	Route::get("allBookmarkedQuestionsDetails", "API\PostLoginController@allBookmarkedQuestionsDetails");

	Route::post("deleteBookmarkedQuestions", "API\PostLoginController@deleteBookmarkedQuestions");

	// Subscription
	Route::post("requestPackageSubscription", "API\PostLoginController@requestPackageSubscription");
	Route::post("requestPackageSubscriptionRazor", "API\PostLoginController@requestPackageSubscriptionRazor");
	Route::post("responsePackageSubscriptionRazor", "API\PostLoginController@responsePackageSubscriptionRazor");

	// Extra
	Route::post("deleteMockTesting", "API\PostLoginController@deleteMockTesting");
	Route::post("mockTestSubmitAuto", "API\PostLoginController@mockTestSubmitAuto");

	// Delete Account
	Route::post("deleteAccount", "API\PostLoginController@deleteAccount");

	Route::get("transactionHistory", "API\PostLoginController@transactionHistory");

	Route::get("checkUserSubscription", "API\PostLoginController@checkUserSubscription");

	Route::get("logout", "API\PostLoginController@logout");


	// old api
	// Route::get("user-profile", "API\PostLoginController@userProfile");
	// Route::get('course-list',"API\PostLoginController@courseList");
	// Route::post('batch-list','API\PostLoginController@batchList');
	// Route::get('purchased-batch-list','API\PostLoginController@purchasedBatchList');
	// Route::post('payment-initiated','API\PostLoginController@paymentInitiate');

	// Route::post('topic-batch-list',"API\PostLoginController@topicBatchList");
	// Route::post('notes-material-list',"API\PostLoginController@notesMaterialsList");
	// Route::post('chapters-list',"API\PostLoginController@chaptersList");
	// Route::post('create-exam',"API\PostLoginController@createExam");
	// Route::post('zoom-list',"API\PostLoginController@zoomList");
	// Route::post('submit-question',"API\PostLoginController@submitQuestion");
	// Route::post('submit-flag-questions',"API\PostLoginController@submitFlagUserQuestion");
	// Route::get('demo-video',"API\PostLoginController@demoVideo");
	// Route::post('exam-pause-continue',"API\PostLoginController@examPauseContinue");
	// Route::get('pause-continue-exam-list',"API\PostLoginController@pauseContinueExamList");

	Route::prefix('v2')->group(function () {

		Route::post("courseMasterList", "API\PostLoginControllerV2@courseMasterList");
		Route::post("questionBankAnswers", "API\PostLoginControllerV2@questionBankAnswers");
		Route::get("uncompletedQuestionBank", "API\PostLoginControllerV2@uncompletedQuestionBank");
		Route::get("upcomingMockTests", "API\PostLoginControllerV2@upcomingMockTests");
		Route::get("questionBank", "API\PostLoginControllerV2@questionBank");
		Route::post("questionBankSearch", "API\PostLoginControllerV2@questionBankSearch");
		Route::get("mockTestUpcomming", "API\PostLoginControllerV2@mockTestUpcomming");
		Route::get("subscriptionPlansList", "API\PostLoginControllerV2@subscriptionPlansList");
		Route::post("previousYearSearch", "API\PostLoginControllerV2@previousYearSearch");
		Route::get("previousYearTopics", "API\PostLoginControllerV2@previousYearTopics");
		Route::get("subjectList", "API\PostLoginControllerV2@subjectList");
		Route::post("requestPackageSubscriptionRazor", "API\PostLoginControllerV2@requestPackageSubscriptionRazor");
		Route::get("checkUserSubscription", "API\PostLoginControllerV2@checkUserSubscription");
		Route::get("mockTestResultData", "API\PostLoginControllerV2@mockTestResultData");
	});
});

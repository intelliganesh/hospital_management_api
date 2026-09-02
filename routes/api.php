<?php

use App\Http\Controllers\AmountForController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankInformationController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationCostController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\DietPlansController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ExternalAppointmentController;
use App\Http\Controllers\FistulaController;
use App\Http\Controllers\FoodAdviceController;
use App\Http\Controllers\GetListRolesController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceReportsController;
use App\Http\Controllers\IPDAnaesthesiaController;
use App\Http\Controllers\IPDAnaesthesiaDepartmentController;
use App\Http\Controllers\IPDAnaesthesiaRecoverObservationController;
use App\Http\Controllers\IPDBillingController;
use App\Http\Controllers\IpdController;
use App\Http\Controllers\IPDDischargeSummaryController;
use App\Http\Controllers\IPDDoctorNotesController;
use App\Http\Controllers\IpdEnrollmentController;
use App\Http\Controllers\IPDNurseNotesController;
use App\Http\Controllers\IPDPreliminaryNotesController;
use App\Http\Controllers\IPDPreOperativeAnaesthesiaEvaluationController;
use App\Http\Controllers\IPDPreOperativeChecklistController;
use App\Http\Controllers\IPDSurgeryController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\Master\AgniController;
use App\Http\Controllers\Master\AllergyController;
use App\Http\Controllers\Master\AvasthaController;
use App\Http\Controllers\Master\BillingServiceCategoryController;
use App\Http\Controllers\Master\ChiefComplaintController;
use App\Http\Controllers\Master\ComorbiditiesController;
use App\Http\Controllers\Master\DepartmentsController;
use App\Http\Controllers\Master\DREController;
use App\Http\Controllers\Master\ExpensesController;
use App\Http\Controllers\Master\FindingsController;
use App\Http\Controllers\Master\KoshtaController;
use App\Http\Controllers\Master\MedicineCategoriesController;
use App\Http\Controllers\Master\MedicinesController;
use App\Http\Controllers\Master\PrakritiController;
use App\Http\Controllers\Master\ProctoscopyController;
use App\Http\Controllers\Master\ReferralDoctorController;
use App\Http\Controllers\Master\RolesController;
use App\Http\Controllers\Master\RoomController;
use App\Http\Controllers\Master\SurgicalHistoryController;
use App\Http\Controllers\Master\TestsController;
use App\Http\Controllers\Master\VikrutiController;
use App\Http\Controllers\Master\YogaAsanaController;
use App\Http\Controllers\MedicineCategoryMappingController;
use App\Http\Controllers\OnExaminationsController;
use App\Http\Controllers\OPDController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientFistulaController;
use App\Http\Controllers\PatientTestsController;
use App\Http\Controllers\PostSurgeryFollowUpController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\Reports\ExpensesReportsController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\ServiceCostController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

// Public routes (non-authenticated)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::post('/registration', [AuthController::class, 'registration']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);
Route::post('/forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('/verify_password', [AuthController::class, 'verifyPassword']);

// External Appointments Routes (Public - No Authentication Required)
Route::post('/external-appointments', [ExternalAppointmentController::class, 'store']);
Route::get('/external-appointments', [ExternalAppointmentController::class, 'index']);
Route::get('/external-appointments/doctors', [ExternalAppointmentController::class, 'getDoctorList']);
Route::get('/external-appointments/doctors/{doctor_id}', [ExternalAppointmentController::class, 'getDoctorDetail']);
Route::get('/external-appointments/doctor/{doctor_id}', [ExternalAppointmentController::class, 'getByDoctor']);
Route::get('/external-appointments/upcoming', [ExternalAppointmentController::class, 'getUpcoming']);
Route::get('/external-appointments/statistics', [ExternalAppointmentController::class, 'getStatistics']);
Route::patch('/external-appointments/{id}/status', [ExternalAppointmentController::class, 'changeStatus']);
Route::get('/external-appointments/{id}', [ExternalAppointmentController::class, 'show']);

Route::delete('/external-appointments/{id}', [ExternalAppointmentController::class, 'destroy']);
Route::get("/bank_information_dropdown_list", [BankInformationController::class, 'bankInformationDropdownList']);
Route::post('/external-appointments/{id}/generate_link', [ExternalAppointmentController::class, 'generateLinkForConsultation']);
Route::post('/external-appointments/{id}/send_link', [ExternalAppointmentController::class, 'sendLinkForConsultation']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::put('/external-appointments/{id}', [ExternalAppointmentController::class, 'update']);

    //images
    Route::post('/images', [ImageController::class, 'image']);
    Route::post("/refresh_token", [AuthController::class, 'refreshToken']);
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'all']);

    //bank information
    Route::get('/bank_information_list', [BankInformationController::class, 'all']);
    Route::post('/bank_information_add', [BankInformationController::class, 'create']);
    Route::get('/bank_information_details/{id}', [BankInformationController::class, 'get']);
    Route::put('/bank_information_update/{id}', [BankInformationController::class, 'update']);
    Route::delete('/bank_information_delete/{id}', [BankInformationController::class, 'delete']);
    //countries,states,city
    Route::get('/countries/{id}', [CountryController::class, 'all']);
    Route::get('/states/{id}', [StateController::class, 'all']);
    Route::get('/cities/{id}', [CityController::class, 'all']);
    //users
    Route::get('/user_list', [UserController::class, 'all']);
    Route::post('/user_add', [UserController::class, 'create']);
    Route::put('/user_edit/{id}', [UserController::class, 'update']);
    Route::get('/user_details/{id}', [UserController::class, 'get']);
    Route::get('/get_roles_list', [UserController::class, 'getRolesList']);
    Route::delete('/user_delete/{id}', [UserController::class, 'destroy']);
    Route::get('/user_profile_details', [AuthController::class, 'userProfile']);
    Route::post('/old_password_check', [UserController::class, 'resetPasswordAfterLogin']);
    //patient
    Route::get('/patient_list', [PatientController::class, 'all']);
    Route::post('/patient_add', [PatientController::class, 'create']);
    Route::put('/patient_edit/{id}', [PatientController::class, 'update']);
    Route::get('/patient_details/{id}', [PatientController::class, 'get']);
    Route::delete('/patient_delete/{id}', [PatientController::class, 'destroy']);
    Route::get('/patients/{id}/download', [PatientController::class, 'download']);
    Route::get('/patients_list_for_dropdown', [PatientController::class, 'patientListForDropDown']);
    Route::get('/patients/{id}/anaesthesia_download', [PatientController::class, 'anaesthesiaForm']);
    Route::get('/patient_statistics', [PatientController::class, 'getStatistics']);
    Route::get('/patient_preoperative_checklist/{id}', [IpdController::class, 'download_patiient_preoperative_checklist']);

    //Patient Fistula data
    Route::get('/patient_fistula_list', [PatientFistulaController::class, 'all']);
    Route::post('/patient_fistula_add', [PatientFistulaController::class, 'create']);
    Route::put('/patient_fistula_update/{id}', [PatientFistulaController::class, 'update']);
    Route::get('/patient_fistula_details/{id}', [PatientFistulaController::class, 'get']);
    Route::delete('/patient_fistula_delete/{id}', [PatientFistulaController::class, 'destroy']);
    Route::get('/patient_fistula_by_patient/{id}', [PatientFistulaController::class, 'getByPatient']);
    //rooms
    Route::get('/room_list', [RoomController::class, 'all']);
    Route::post('/room_add', [RoomController::class, 'create']);
    Route::get('/room_details/{id}', [RoomController::class, 'get']);
    Route::put('/room_update/{id}', [RoomController::class, 'update']);
    Route::delete('/room_delete/{id}', [RoomController::class, 'delete']);
    Route::get('/rooms_list_for_dropdown/{ward_id}', [RoomController::class, 'roomsListForDropdown']);
    //wards
    Route::get('/ward_list', [WardController::class, 'all']);
    Route::post('/ward_add', [WardController::class, 'create']);
    Route::get('/ward_details/{id}', [WardController::class, 'get']);
    Route::put('/ward_update/{id}', [WardController::class, 'update']);
    Route::delete('/ward_delete/{id}', [WardController::class, 'delete']);
    Route::get('/wards_list_for_dropdown', [WardController::class, 'wardsListForDropdown']);
    //beds
    Route::get('/bed_list', [BedController::class, 'all']);
    Route::post('/bed_add', [BedController::class, 'create']);
    Route::get('/bed_details/{id}', [BedController::class, 'get']);
    Route::put('/bed_update/{id}', [BedController::class, 'update']);
    Route::delete('/bed_delete/{id}', [BedController::class, 'delete']);
    Route::get('/beds_list_for_dropdown/{room_id}', [BedController::class, 'bedsListForDropdown']);
    //system settings
    Route::get('/get_system_settings', [SystemSettingsController::class, 'all']);
    // Route::post('/add_system_settings', [SystemSettingsController::class, 'add']);
    // Route::put('/edit_system_settings', [SystemSettingsController::class, 'update']);
    Route::post('/add_or_edit_system_settings', [SystemSettingsController::class, 'addOrEdit']);
    //country,state,city
    Route::get('/countries', [CountryController::class, 'all']);
    Route::get('/cities/{stateId}', [CityController::class, 'all']);
    Route::get('/state/{countryId}', [StateController::class, 'all']);
    //appointments
    Route::get('/appointments_list', [AppointmentsController::class, 'all']);
    Route::post('/appointments_add', [AppointmentsController::class, 'create']);
    Route::get('/appointments_details/{id}', [AppointmentsController::class, 'get']);
    Route::put('/appointments_update/{id}', [AppointmentsController::class, 'update']);
    // Route::post('/appointment_fees', [AppointmentsController::class, 'appointmentFees']);
    Route::delete('/appointments_delete/{id}', [AppointmentsController::class, 'delete']);
    Route::get('/appointments_statistics', [AppointmentsController::class, 'getStatistics']);
    //allergies
    Route::get('/allergies_list', [AllergyController::class, 'all']);
    Route::post('/allergies_add', [AllergyController::class, 'create']);
    Route::get('/allergies_details/{id}', [AllergyController::class, 'get']);
    Route::put('/allergies_update/{id}', [AllergyController::class, 'update']);
    Route::delete('/allergies_delete/{id}', [AllergyController::class, 'delete']);
    //roles
    Route::get('/roles_list', [RolesController::class, 'all']);
    Route::post('/roles_add', [RolesController::class, 'create']);
    Route::get('/roles_details/{id}', [RolesController::class, 'get']);
    Route::put('/roles_update/{id}', [RolesController::class, 'update']);
    Route::delete('/roles_delete/{id}', [RolesController::class, 'delete']);
    //opd
    Route::get('/opd_list', [OPDController::class, 'all']);
    Route::post('/opd_add', [OPDController::class, 'create']);
    Route::get('/opd_details/{id}', [OPDController::class, 'get']);
    Route::get('/opd_pua_list', [OPDController::class, 'getList']);
    Route::get('/doctor_list_show', [OPDController::class, 'show']);
    Route::put('/opd_update/{id}', [OPDController::class, 'update']);
    Route::delete('/opd_delete/{id}', [OPDController::class, 'delete']);
    //medicines
    Route::get('/medicines_list', [MedicinesController::class, 'all']);
    Route::post('/medicines_add', [MedicinesController::class, 'create']);
    Route::get('/medicines_details/{id}', [MedicinesController::class, 'get']);
    Route::put('/medicines_update/{id}', [MedicinesController::class, 'update']);
    Route::delete('/medicines_delete/{id}', [MedicinesController::class, 'delete']);
    Route::post('/medicines_list_for_dropdown', [MedicinesController::class, 'medicinesList']);
    // Route::get('/generate-consultation-report/{id}', [ConsultationController::class, 'generateConsultationReport']);
    //test
    Route::get('/tests_list', [TestsController::class, 'all']);
    Route::post('/tests_add', [TestsController::class, 'create']);
    Route::get('/tests_details/{id}', [TestsController::class, 'get']);
    Route::put('/tests_update/{id}', [TestsController::class, 'update']);
    Route::delete('/tests_delete/{id}', [TestsController::class, 'delete']);
    Route::get('/tests_list_for_dropdown', [TestsController::class, 'testList']);
    //patient test
    Route::get('/patient_tests_list', [PatientTestsController::class, 'all']);
    Route::post('/patient_tests_create', [PatientTestsController::class, 'create']);
    Route::get('/patient_tests_details/{id}', [PatientTestsController::class, 'get']);
    Route::put('/patient_tests_update/{id}', [PatientTestsController::class, 'update']);
    Route::delete('/patient_tests_delete/{id}', [PatientTestsController::class, 'delete']);
    //medicine categories
    Route::get('/medicine_categories_list', [MedicineCategoriesController::class, 'all']);
    Route::post('/medicine_categories_add', [MedicineCategoriesController::class, 'create']);
    Route::get('/medicine_categories_details/{id}', [MedicineCategoriesController::class, 'get']);
    Route::put('/medicine_categories_update/{id}', [MedicineCategoriesController::class, 'update']);
    Route::delete('/medicine_categories_delete/{id}', [MedicineCategoriesController::class, 'delete']);
    //medicine category mapping
    Route::get('/medicine_category_mapping_list', [MedicineCategoryMappingController::class, 'all']);
    Route::post('/medicine_category_mapping_add', [MedicineCategoryMappingController::class, 'create']);
    Route::get('/medicine_category_mapping_details/{id}', [MedicineCategoryMappingController::class, 'get']);
    Route::put('/medicine_category_mapping_update/{id}', [MedicineCategoryMappingController::class, 'update']);
    Route::delete('/medicine_category_mapping_delete/{id}', [MedicineCategoryMappingController::class, 'delete']);
    Route::get('/medicine_category_mapping_all_list', [MedicineCategoryMappingController::class, 'getAllMedicineCategoryAndMedicineList']);
    //consultations
    Route::get('/consultations_list', [ConsultationController::class, 'all']);
    Route::post('/consultations_add', [ConsultationController::class, 'create']);
    Route::get('/consultations_details/{id}', [ConsultationController::class, 'get']);
    Route::put('/consultations_update/{id}', [ConsultationController::class, 'update']);
    Route::delete('/consultations_delete/{id}', [ConsultationController::class, 'delete']);
    Route::get('/consultations_list_for_dropdown', [ConsultationController::class, 'consultationList']);
    Route::get('/patient_consultation_list', [ConsultationController::class, 'patientConsultationList']);
    Route::get('/generate-consultation-report/{id}', [ConsultationController::class, 'generateConsultationReport']);
    Route::get('/consultations_statistics', [ConsultationController::class, 'getStatistics']);
    //prescription
    Route::get('/prescription_download/{id}', [ConsultationController::class, 'downloadPrescription']);
    Route::get('/consultation_prescription/{appointment_id}', [ConsultationController::class, 'getConsultationPrescriptionData']);
    Route::get('/consultation_dates/{patient_id}', [ConsultationController::class, 'getConsultationDatesForPatient']);

    //examination
    Route::get('/examinations_list', [ExaminationController::class, 'all']);
    Route::post('/examinations_add', [ExaminationController::class, 'create']);
    Route::get('/examinations_details/{id}', [ExaminationController::class, 'get']);
    Route::put('/examinations_update/{id}', [ExaminationController::class, 'update']);
    Route::delete('/examinations_delete/{id}', [ExaminationController::class, 'delete']);
    //prescriptions
    Route::get('/prescriptions_list', [PrescriptionController::class, 'all']);
    Route::post('/prescriptions_add', [PrescriptionController::class, 'create']);
    Route::get('/prescriptions_details/{id}', [PrescriptionController::class, 'get']);
    Route::put('/prescriptions_update/{id}', [PrescriptionController::class, 'update']);
    Route::delete('/prescriptions_delete/{id}', [PrescriptionController::class, 'delete']);
    //findings
    Route::get('/findings_list', [FindingsController::class, 'all']);
    Route::post('/findings_add', [FindingsController::class, 'create']);
    Route::get('/findings_details/{id}', [FindingsController::class, 'get']);
    Route::put('/findings_update/{id}', [FindingsController::class, 'update']);
    Route::delete('/findings_delete/{id}', [FindingsController::class, 'delete']);
    Route::get('/findings_list_for_dropdown', [FindingsController::class, 'findingsList']);
    //billing service category
    Route::get('/billing_service_category_list', [BillingServiceCategoryController::class, 'all']);
    Route::post('/billing_service_category_add', [BillingServiceCategoryController::class, 'create']);
    Route::get('/billing_service_category_details/{id}', [BillingServiceCategoryController::class, 'get']);
    Route::put('/billing_service_category_update/{id}', [BillingServiceCategoryController::class, 'update']);
    Route::delete('/billing_service_category_delete/{id}', [BillingServiceCategoryController::class, 'delete']);
    Route::get('/billing_service_category_dropdown_list', [BillingServiceCategoryController::class, 'billingServiceCategoryList']);
    //agni
    Route::get('/agni_list', [AgniController::class, 'all']);
    Route::post('/agni_add', [AgniController::class, 'create']);
    Route::get('/agni_options_list', [AgniController::class, 'list']);
    Route::get('/agni_details/{id}', [AgniController::class, 'get']);
    Route::put('/agni_update/{id}', [AgniController::class, 'update']);
    Route::delete('/agni_delete/{id}', [AgniController::class, 'delete']);
    //avastha
    Route::get('/avastha_list', [AvasthaController::class, 'all']);
    Route::post('/avastha_add', [AvasthaController::class, 'create']);
    Route::get('/avastha_details/{id}', [AvasthaController::class, 'get']);
    Route::get('/avastha_options_list', [AvasthaController::class, 'list']);
    Route::put('/avastha_update/{id}', [AvasthaController::class, 'update']);
    Route::delete('/avastha_delete/{id}', [AvasthaController::class, 'delete']);
    //koshta
    Route::get('/koshta_list', [KoshtaController::class, 'all']);
    Route::post('/koshta_add', [KoshtaController::class, 'create']);
    Route::get('/koshta_details/{id}', [KoshtaController::class, 'get']);
    Route::get('/koshta_options_list', [KoshtaController::class, 'list']);
    Route::put('/koshta_update/{id}', [KoshtaController::class, 'update']);
    Route::delete('/koshta_delete/{id}', [KoshtaController::class, 'delete']);
    //prakriti
    Route::get('/prakriti_list', [PrakritiController::class, 'all']);
    Route::post('/prakriti_add', [PrakritiController::class, 'create']);
    Route::get('/prakriti_details/{id}', [PrakritiController::class, 'get']);
    Route::get('/prakriti_options_list', [PrakritiController::class, 'list']);
    Route::put('/prakriti_update/{id}', [PrakritiController::class, 'update']);
    Route::delete('/prakriti_delete/{id}', [PrakritiController::class, 'delete']);
    //vikruti
    Route::get('/vikruti_list', [VikrutiController::class, 'all']);
    Route::post('/vikruti_add', [VikrutiController::class, 'create']);
    Route::get('/vikruti_details/{id}', [VikrutiController::class, 'get']);
    Route::get('/vikruti_options_list', [VikrutiController::class, 'list']);
    Route::put('/vikruti_update/{id}', [VikrutiController::class, 'update']);
    Route::delete('/vikruti_delete/{id}', [VikrutiController::class, 'delete']);
    //yogo asna
    Route::get('/yoga_asana_list', [YogaAsanaController::class, 'all']);
    Route::post('/yoga_asana_add', [YogaAsanaController::class, 'create']);
    Route::get('/yoga_asana_details/{id}', [YogaAsanaController::class, 'get']);
    Route::put('/yoga_asana_update/{id}', [YogaAsanaController::class, 'update']);
    Route::delete('/yoga_asana_delete/{id}', [YogaAsanaController::class, 'delete']);
    Route::get('/yoga_asana_options_list', [YogaAsanaController::class, 'optionsList']);
    //invoice
    Route::get('/invoice_list', [InvoiceController::class, 'all']);
    Route::get('/invoice_details/{id}', [InvoiceController::class, 'get']);
    Route::post('/invoice_payment', [InvoiceController::class, 'addPayment']);
    Route::get('/invoice_download/{id}', [InvoiceController::class, 'download']);
    Route::post('/invoice_add_or_update/{id}', [InvoiceController::class, 'addOrUpdate']);
    Route::post('/amount_include_in_invoice', [InvoiceController::class, 'amountIncludeInInvoice']);
    Route::get('/payment_details/{id}', [InvoiceController::class, 'paymentDetails']);
    // Route::put('/invoice_update/{id}', [InvoiceController::class, 'update']);
    // Route::delete('/invoice_delete/{id}', [InvoiceController::class, 'delete']);
    //departement
    Route::get('/departments_list', [DepartmentsController::class, 'all']);
    Route::post('/departments_add', [DepartmentsController::class, 'create']);
    Route::get('/departments_details/{id}', [DepartmentsController::class, 'get']);
    Route::put('/departments_update/{id}', [DepartmentsController::class, 'update']);
    Route::delete('/departments_delete/{id}', [DepartmentsController::class, 'delete']);
    Route::get('/departments_dropdown_list', [DepartmentsController::class, 'departmentList']);
    //referral doctors
    Route::get('/referral_doctors_list', [ReferralDoctorController::class, 'all']);
    Route::post('/referral_doctors_add', [ReferralDoctorController::class, 'create']);
    Route::get('/referral_doctors_details/{id}', [ReferralDoctorController::class, 'get']);
    Route::put('/referral_doctors_update/{id}', [ReferralDoctorController::class, 'update']);
    Route::delete('/referral_doctors_delete/{id}', [ReferralDoctorController::class, 'delete']);
    Route::get('/referral_doctors_dropdown_list', [ReferralDoctorController::class, 'referralDoctorList']);
    //consultation cost
    Route::get('/consultation_cost_list', [ConsultationCostController::class, 'all']);
    Route::post('/consultation_cost_add', [ConsultationCostController::class, 'create']);
    Route::get('/consultation_cost_details/{id}', [ConsultationCostController::class, 'get']);
    Route::put('/consultation_cost_update/{id}', [ConsultationCostController::class, 'update']);
    Route::delete('/consultation_cost_delete/{id}', [ConsultationCostController::class, 'delete']);
    Route::get('/consultation_cost_dropdown_list/{departmentValue}', [ConsultationCostController::class, 'consultationCostList']);
    //chief complaint
    Route::get('/chief_complaint_list', [ChiefComplaintController::class, 'all']);
    Route::post('/chief_complaint_add', [ChiefComplaintController::class, 'create']);
    Route::get('/chief_complaint_details/{id}', [ChiefComplaintController::class, 'get']);
    Route::put('/chief_complaint_update/{id}', [ChiefComplaintController::class, 'update']);
    Route::delete('/chief_complaint_delete/{id}', [ChiefComplaintController::class, 'delete']);
    Route::get('/chief_complaint_dropdown_list/{departmentValue}', [ChiefComplaintController::class, 'chiefComplaintList']);
    //surgical history
    Route::get('/surgical_history_list', [SurgicalHistoryController::class, 'all']);
    Route::post('/surgical_history_add', [SurgicalHistoryController::class, 'create']);
    Route::get('/surgical_history_details/{id}', [SurgicalHistoryController::class, 'get']);
    Route::put('/surgical_history_update/{id}', [SurgicalHistoryController::class, 'update']);
    Route::delete('/surgical_history_delete/{id}', [SurgicalHistoryController::class, 'delete']);
    Route::get('/surgical_history_dropdown_list/{departmentValue}', [SurgicalHistoryController::class, 'surgicalHistoryList']);
    //comorbidities
    Route::get('/comorbidities_list', [ComorbiditiesController::class, 'all']);
    Route::post('/comorbidities_add', [ComorbiditiesController::class, 'create']);
    Route::get('/comorbidities_details/{id}', [ComorbiditiesController::class, 'get']);
    Route::put('/comorbidities_update/{id}', [ComorbiditiesController::class, 'update']);
    Route::delete('/comorbidities_delete/{id}', [ComorbiditiesController::class, 'delete']);
    Route::get("/comorbidities_dropdown_list/{departmentValue}", [ComorbiditiesController::class, 'comorbiditiesList']);
    //on examination
    Route::get('/on_examination_list', [OnExaminationsController::class, 'all']);
    Route::post('/on_examination_add', [OnExaminationsController::class, 'create']);
    Route::get('/on_examination_details/{id}', [OnExaminationsController::class, 'get']);
    Route::put('/on_examination_update/{id}', [OnExaminationsController::class, 'update']);
    Route::delete('/on_examination_delete/{id}', [OnExaminationsController::class, 'delete']);
    Route::get("/on_examination_dropdown_list/{departmentValue}", [OnExaminationsController::class, 'onExaminationList']);
    //list of user based on roles
    Route::post('/roles_list_for_dropdown', [GetListRolesController::class, 'rolesListForDropdown']);
    //amount for
    Route::get('/amount_for_list', [AmountForController::class, 'all']);
    Route::post('/amount_for_add', [AmountForController::class, 'create']);
    Route::get('/amount_for_details/{id}', [AmountForController::class, 'get']);
    Route::put('/amount_for_update/{id}', [AmountForController::class, 'update']);
    Route::delete('/amount_for_delete/{id}', [AmountForController::class, 'delete']);
    Route::get("/amount_for_dropdown_list", [AmountForController::class, 'amountForList']);
    //service cost
    Route::get('/service_cost_list', [ServiceCostController::class, 'all']);
    Route::post('/service_cost_add', [ServiceCostController::class, 'create']);
    Route::get('/service_cost_details/{id}', [ServiceCostController::class, 'get']);
    Route::put('/service_cost_update/{id}', [ServiceCostController::class, 'update']);
    Route::delete('/service_cost_delete/{id}', [ServiceCostController::class, 'delete']);
    Route::get('/service_cost_dropdown_list/{departmentValue}', [ServiceCostController::class, 'serviceCostList']);
    //diet plan
    Route::get('/diet_plan_list', [DietPlansController::class, 'all']);
    Route::post('/diet_plan_add', [DietPlansController::class, 'create']);
    Route::get('/diet_plan_details/{id}', [DietPlansController::class, 'get']);
    Route::put('/diet_plan_update/{id}', [DietPlansController::class, 'update']);
    Route::delete('/diet_plan_delete/{id}', [DietPlansController::class, 'delete']);
    Route::get('/diet_plan_dropdown_list/{departmentValue}', [DietPlansController::class, 'dietPlanList']);
    //diagnosis
    Route::get('/diagnosis_list', [DiagnosisController::class, 'all']);
    Route::post('/diagnosis_add', [DiagnosisController::class, 'create']);
    Route::get('/diagnosis_details/{id}', [DiagnosisController::class, 'get']);
    Route::put('/diagnosis_update/{id}', [DiagnosisController::class, 'update']);
    Route::delete('/diagnosis_delete/{id}', [DiagnosisController::class, 'delete']);
    Route::get('/diagnosis_dropdown_list/{departmentValue}', [DiagnosisController::class, 'diagnosisList']);
    //food advice
    Route::get('/food_advice_list', [FoodAdviceController::class, 'all']);
    Route::post('/food_advice_add', [FoodAdviceController::class, 'create']);
    Route::get('/food_advice_details/{id}', [FoodAdviceController::class, 'get']);
    Route::put('/food_advice_update/{id}', [FoodAdviceController::class, 'update']);
    Route::delete('/food_advice_delete/{id}', [FoodAdviceController::class, 'delete']);
    Route::get('/food_advice_dropdown_list/{departmentValue}', [FoodAdviceController::class, 'foodAdviceList']);
    //expenses
    Route::get('/expenses_list', [ExpensesController::class, 'all']);
    Route::post('/expenses_add', [ExpensesController::class, 'create']);
    Route::get('/expenses_details/{id}', [ExpensesController::class, 'get']);
    Route::put('/expenses_update/{id}', [ExpensesController::class, 'update']);
    Route::delete('/expenses_delete/{id}', [ExpensesController::class, 'delete']);
    Route::get('/expenses_dropdown_list', [ExpensesController::class, 'expensesList']);
    Route::get('/expenses_voucher/{id}', [ExpensesController::class, 'downloadVoucher']);
    //dre
    Route::get('/dre_list', [DREController::class, 'all']);
    Route::post('/dre_add', [DREController::class, 'create']);
    Route::get('/dre_details/{id}', [DREController::class, 'get']);
    Route::put('/dre_update/{id}', [DREController::class, 'update']);
    Route::delete('/dre_delete/{id}', [DREController::class, 'delete']);
    Route::get('/dre_dropdown_list/{departmentValue}', [DREController::class, 'dreList']);
    //proctoscopy
    Route::get('/proctoscopy_list', [ProctoscopyController::class, 'all']);
    Route::post('/proctoscopy_add', [ProctoscopyController::class, 'create']);
    Route::get('/proctoscopy_details/{id}', [ProctoscopyController::class, 'get']);
    Route::put('/proctoscopy_update/{id}', [ProctoscopyController::class, 'update']);
    Route::delete('/proctoscopy_delete/{id}', [ProctoscopyController::class, 'delete']);
    Route::get('/proctoscopy_dropdown_list/{departmentValue}', [ProctoscopyController::class, 'proctoscopyList']);
    //REPORTS
    Route::get('/reports/expenses_list', [ExpensesReportsController::class, 'all']);
    Route::get('/reports/expenses_download', [ExpensesReportsController::class, 'downloadExcel']);
    Route::get('/reports/invoice_list', [InvoiceReportsController::class, 'all']);
    Route::get('/reports/invoice_download', [InvoiceReportsController::class, 'downloadExcel']);
    //fistula report
    Route::get('/reports/fistula_list', [ReportController::class, 'fistulaReport']);
    Route::post('/reports/fistula_download', [ReportController::class, 'fistulaReportDownload']);
    //consultation report
    Route::get('/reports/consultation_list', [ReportController::class, 'consultationReport']);
    Route::post('/reports/consultation_download', [ReportController::class, 'consultationReportDownload']);
    //management
    Route::get('/management_list', [ManagementController::class, 'all']);
    Route::post('/management_add', [ManagementController::class, 'create']);
    Route::get('/management_details/{id}', [ManagementController::class, 'get']);
    Route::put('/management_update/{id}', [ManagementController::class, 'update']);
    Route::delete('/management_delete/{id}', [ManagementController::class, 'delete']);
    Route::get('/management_dropdown_list/{departmentValue}', [ManagementController::class, 'managementList']);
    //fistula
    Route::get('/fistula_list', [FistulaController::class, 'all']);
    Route::post('/fistula_add', [FistulaController::class, 'create']);
    Route::get('/fistula_details/{id}', [FistulaController::class, 'get']);
    Route::put('/fistula_update/{id}', [FistulaController::class, 'update']);
    Route::delete('/fistula_delete/{id}', [FistulaController::class, 'delete']);
    Route::get('/fistula_dropdown_list/{departmentValue}', [FistulaController::class, 'fistulaList']);
    //post surgery follow up
    Route::get('/post_surgery_list', [PostSurgeryFollowUpController::class, 'all']);
    Route::post('/post_surgery_add', [PostSurgeryFollowUpController::class, 'create']);
    Route::get('/post_surgery_details/{id}', [PostSurgeryFollowUpController::class, 'get']);
    Route::put('/post_surgery_update/{id}', [PostSurgeryFollowUpController::class, 'update']);
    Route::delete('/post_surgery_delete/{id}', [PostSurgeryFollowUpController::class, 'delete']);
    Route::post('/post_surgery_details', [PostSurgeryFollowUpController::class, 'postSurgeryDetails']);
    Route::get('/post_surgery_details_list_for_dropdown', [PostSurgeryFollowUpController::class, 'postSurgeryDetailsList']);
    // Route::get('/post_surgery_dropdown_list/{departmentValue}', [PostSurgeryFollowUpController::class, 'postSurgeonList']);
    Route::get('/download-post-surgery-follow-up', [PostSurgeryFollowUpController::class, 'followUpDownload']);
    Route::get('/download-post-surgery-follow-up-pdf/{id}', [PostSurgeryFollowUpController::class, 'getPostSurgeryFollowUpDownload']);

    //ipd enrollment
    Route::get('/ipd_enrollment_list', [IpdEnrollmentController::class, 'all']);
    Route::get('/ipd_enrollment_details/{id}', [IpdEnrollmentController::class, 'get']);
    Route::delete('/ipd_enrollment_delete/{id}', [IpdEnrollmentController::class, 'delete']);

    //ipd patients
    Route::get('/ipd_list', [IpdController::class, 'all']);
    Route::post('/ipd_add', [IpdController::class, 'create']);
    Route::get('/ipd_details/{id}', [IpdController::class, 'get']);
    Route::put('/ipd_update/{id}', [IpdController::class, 'update']);
    Route::delete('/ipd_delete/{id}', [IpdController::class, 'delete']);
    Route::get('/ipd_download_pdf/{id}', [IpdController::class, 'downloadPdf']);
    Route::get('/ipd_generate_pdf/{id}', [IpdController::class, 'generatePdf']);
    Route::get('/ipd_download_empty_pdf/{id}', [IpdController::class, 'downloadEmptyPdf'])->name('ipd_download_empty_pdf');
    Route::get('/ipd_prefilled_uploaded_pdf/{id}', [IpdController::class, 'downloadPrefilledUploadPdf'])->name('ipd_prefilled_upload_pdf');
    //Preliminary Notes
    Route::post('/preliminary_notes/{ipd_id}', [IPDPreliminaryNotesController::class, 'create']);
    Route::get('/preliminary_notes', [IPDPreliminaryNotesController::class, 'all']);
    Route::get('/preliminary_notes/{id}', [IPDPreliminaryNotesController::class, 'get']);
    Route::put('/preliminary_notes/{id}', [IPDPreliminaryNotesController::class, 'update']);
    Route::delete('/preliminary_notes/{id}', [IPDPreliminaryNotesController::class, 'delete']);

    //nurse notes
    Route::get('/ipd_nurse_notes_list', [IPDNurseNotesController::class, 'all']);
    Route::post('/ipd_nurse_notes_add', [IPDNurseNotesController::class, 'create']);
    Route::get('/ipd_nurse_notes_details/{id}', [IPDNurseNotesController::class, 'get']);
    Route::put('/ipd_nurse_notes_update/{id}', [IPDNurseNotesController::class, 'update']);
    Route::delete('/ipd_nurse_notes_delete/{id}', [IPDNurseNotesController::class, 'delete']);
    
    //doctor notes
    Route::get('/ipd_doctor_notes_list', [IPDDoctorNotesController::class, 'all']);
    Route::post('/ipd_doctor_notes_add', [IPDDoctorNotesController::class, 'create']);
    Route::get('/ipd_doctor_notes_details/{id}', [IPDDoctorNotesController::class, 'get']);
    Route::put('/ipd_doctor_notes_update/{id}', [IPDDoctorNotesController::class, 'update']);
    Route::delete('/ipd_doctor_notes_delete/{id}', [IPDDoctorNotesController::class, 'delete']);

    //ipd discharge summary
    Route::get('/ipd_discharge_summary_list', [IPDDischargeSummaryController::class, 'all']);
    Route::post('/ipd_discharge_summary_add', [IPDDischargeSummaryController::class, 'create']);
    Route::get('/ipd_discharge_summary_details/{id}', [IPDDischargeSummaryController::class, 'get']);
    Route::put('/ipd_discharge_summary_update/{id}', [IPDDischargeSummaryController::class, 'update']);
    Route::delete('/ipd_discharge_summary_delete/{id}', [IPDDischargeSummaryController::class, 'delete']);

    //ipd surgery
    Route::get('/ipd_surgery_list', [IPDSurgeryController::class, 'all']);
    Route::post('/ipd_surgery_add', [IPDSurgeryController::class, 'create']);
    Route::get('/ipd_surgery_details/{id}', [IPDSurgeryController::class, 'get']);
    Route::put('/ipd_surgery_update/{id}', [IPDSurgeryController::class, 'update']);
    Route::delete('/ipd_surgery_delete/{id}', [IPDSurgeryController::class, 'delete']);
    Route::post('/ipd_surgery_consent_form_update/{id}', [IPDSurgeryController::class, 'updateConsentForm']);
    Route::get('/ipd_surgery_list_by_ipd/{ipd_id}', [IPDSurgeryController::class, 'getByIPDId']);

    //ipd pre-operative checklist
    Route::get('/ipd_pre_operative_checklist_list', [IPDPreOperativeChecklistController::class, 'all']);
    Route::post('/ipd_pre_operative_checklist_add', [IPDPreOperativeChecklistController::class, 'create']);
    Route::get('/ipd_pre_operative_checklist_details/{id}', [IPDPreOperativeChecklistController::class, 'get']);
    Route::put('/ipd_pre_operative_checklist_update/{id}', [IPDPreOperativeChecklistController::class, 'update']);
    Route::delete('/ipd_pre_operative_checklist_delete/{id}', [IPDPreOperativeChecklistController::class, 'delete']);

    //ipd pre-operative anaesthesia evaluation
    Route::get('/ipd_pre_operative_anaesthesia_evaluation_list', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'all']);
    Route::post('/ipd_pre_operative_anaesthesia_evaluation_add', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'create']);
    Route::get('/ipd_pre_operative_anaesthesia_evaluation_details/{id}', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'get']);
    Route::put('/ipd_pre_operative_anaesthesia_evaluation_update/{id}', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'update']);
    Route::delete('/ipd_pre_operative_anaesthesia_evaluation_delete/{id}', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'delete']);
    Route::get('/ipd_pre_operative_anaesthesia_evaluation_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}', [IPDPreOperativeAnaesthesiaEvaluationController::class, 'getByIPDAnaesthesiaId']);

    //ipd anaesthesia
    Route::get('/ipd_anaesthesia_list', [IPDAnaesthesiaController::class, 'all']);
    Route::post('/ipd_anaesthesia_add', [IPDAnaesthesiaController::class, 'create']);
    Route::get('/ipd_anaesthesia_details/{id}', [IPDAnaesthesiaController::class, 'get']);
    Route::put('/ipd_anaesthesia_update/{id}', [IPDAnaesthesiaController::class, 'update']);
    Route::delete('/ipd_anaesthesia_delete/{id}', [IPDAnaesthesiaController::class, 'delete']);

    //ipd anaesthesia department (intraoperative)
    Route::get('/ipd_anaesthesia_department_list', [IPDAnaesthesiaDepartmentController::class, 'all']);
    Route::post('/ipd_anaesthesia_department_add', [IPDAnaesthesiaDepartmentController::class, 'create']);
    Route::get('/ipd_anaesthesia_department_details/{id}', [IPDAnaesthesiaDepartmentController::class, 'get']);
    Route::put('/ipd_anaesthesia_department_update/{id}', [IPDAnaesthesiaDepartmentController::class, 'update']);
    Route::delete('/ipd_anaesthesia_department_delete/{id}', [IPDAnaesthesiaDepartmentController::class, 'delete']);
    Route::get('/ipd_anaesthesia_department_list_by_ipd/{ipd_id}', [IPDAnaesthesiaDepartmentController::class, 'getByIPDId']);
    Route::get('/ipd_anaesthesia_department_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}', [IPDAnaesthesiaDepartmentController::class, 'getByIPDAnaesthesiaId']);

    //ipd anaesthesia recovery observation
    Route::get('/ipd_anaesthesia_recover_observation_list', [IPDAnaesthesiaRecoverObservationController::class, 'all']);
    Route::post('/ipd_anaesthesia_recover_observation_add', [IPDAnaesthesiaRecoverObservationController::class, 'create']);
    Route::get('/ipd_anaesthesia_recover_observation_details/{id}', [IPDAnaesthesiaRecoverObservationController::class, 'get']);
    Route::put('/ipd_anaesthesia_recover_observation_update/{id}', [IPDAnaesthesiaRecoverObservationController::class, 'update']);
    Route::delete('/ipd_anaesthesia_recover_observation_delete/{id}', [IPDAnaesthesiaRecoverObservationController::class, 'delete']);
    Route::get('/ipd_anaesthesia_recover_observation_list_by_ipd/{ipd_id}', [IPDAnaesthesiaRecoverObservationController::class, 'getByIPDId']);
    Route::get('/ipd_anaesthesia_recover_observation_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}', [IPDAnaesthesiaRecoverObservationController::class, 'getByIPDAnaesthesiaId']);

    //ipd billing
    Route::get('/ipd_billing_list', [IPDBillingController::class, 'all']);
    Route::get('/ipd_billing_details/{ipd_id}', [IPDBillingController::class, 'get']);
    Route::post('/ipd_billing_update/{ipd_id}', [IPDBillingController::class, 'updateInvoice']);
    Route::post('/ipd_billing_add_payment/{ipd_id}', [IPDBillingController::class, 'addPayment']);
    Route::post('/ipd_billing_add_charges/{ipd_id}', [IPDBillingController::class, 'addCharges']);
    Route::get('/ipd_billing_get_payment_details/{ipd_id}', [IPDBillingController::class, 'paymentDetails']);
    Route::put('/ipd_billing_update_charges/{id}', [IPDBillingController::class, 'updateCharges']);
    Route::delete('/ipd_billing_delete_charges/{id}', [IPDBillingController::class, 'deleteCharges']);
});

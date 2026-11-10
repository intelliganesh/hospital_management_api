<?php

/*
|--------------------------------------------------------------------------
| RBAC configuration (server-side role/permission enforcement)
|--------------------------------------------------------------------------
| mode: enforce (default) | log (never blocks, logs would-be denials) | off
| Permission keys == frontend/src/rolesRoute.tsx PERMISSIONS strings.
| Rules: first match wins. Each rule: methods, pattern (Str::is on the route
| URI without "api/", with {param} left as written), and either
|   'open' => true            any authenticated user
|   'any'  => [perm, ...]     user needs ANY of these (Super Admin bypasses)
|   'roles'=> [role, ...]     user must hold one of these role names
| Options: 'self' => true (allowed when route {id} is the caller's own id),
|          'guard' => 'users' (Super Admin protection), 'when' => param rules.
*/

$rules = [];
$allow = function ($methods, $patterns, array $any, array $opts = []) use (&$rules) {
    foreach ((array) $patterns as $p) {
        $rules[] = array_merge(['methods' => (array) $methods, 'pattern' => $p, 'any' => $any], $opts);
    }
};
$open = function ($methods, $patterns) use (&$rules) {
    foreach ((array) $patterns as $p) {
        $rules[] = ['methods' => (array) $methods, 'pattern' => $p, 'open' => true];
    }
};
// read/write helper for a standard CRUD resource (X_list, X_add, X_details/*, X_update/*, X_delete/*)
$crud = function (string $b, array $read, array $create, array $update, array $delete, ?array $readDetails = null) use ($allow) {
    $allow('GET', "{$b}_list", $read);
    $allow('GET', "{$b}_details/*", $readDetails ?? $read);
    $allow('POST', "{$b}_add", $create);
    $allow('PUT', "{$b}_update/*", $update);
    $allow('DELETE', "{$b}_delete/*", $delete);
};

$ALL = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

// ---------------------------------------------------------------- OPEN
$open('POST', 'refresh_token');
$open('GET', 'user_profile_details');
$open('POST', 'old_password_check');
$open('GET', 'get_roles_list');
$open('GET', 'get_system_settings');
$open('GET', ['countries', 'countries/*', 'states/*', 'state/*', 'cities/*']);
$open('GET', ['doctor_list_show', 'opd_pua_list']);

// PHI-bearing dropdowns are NOT open
$allow('GET', 'consultations_list_for_dropdown', ['consultations_view']);
$allow('GET', 'patients_list_for_dropdown', ['patients_view', 'appointments_view', 'consultations_view', 'ipd_view']);
$allow('GET', 'post_surgery_details_list_for_dropdown', ['post_surgery_follow_up']);
$allow('POST', 'roles_list_for_dropdown', ['masters_view', 'users_view']);
// generic lookups used by many forms
$open($ALL, ['*dropdown*', '*_options_list']);

// ---------------------------------------------------------------- UPLOADS
$rules[] = ['methods' => ['POST'], 'pattern' => 'images', 'open' => true,
    'when' => ['param' => 'modal_type', 'values' => ['system_settings' => ['settings_edit', 'setting_update']]]];

// ---------------------------------------------------------------- DASHBOARD / SETTINGS
$allow('GET', 'dashboard', ['dashboard_view']);
$allow('POST', 'add_or_edit_system_settings', ['settings_edit', 'setting_update']);

// ---------------------------------------------------------------- USERS / ROLES / PERMISSIONS
$allow('POST', 'registration', ['user_create'], ['guard' => 'users']);
$allow('GET', 'user_list', ['users_view', 'users_list']);
$allow('POST', 'user_add', ['user_create'], ['guard' => 'users']);
$allow('PUT', 'user_edit/*', ['user_edit'], ['self' => true, 'guard' => 'users']);
$allow('GET', 'user_details/*', ['users_view', 'user_details'], ['self' => true]);
$allow('DELETE', 'user_delete/*', ['user_delete'], ['guard' => 'users']);
$allow('GET', ['roles_list', 'roles_details/*'], ['masters_view', 'users_view', 'roles_manage']);
$allow(['POST', 'PUT', 'DELETE'], ['roles_add', 'roles_update/*', 'roles_delete/*'], ['roles_manage']);
$allow(['GET', 'PUT'], ['permissions_list', 'role_permissions/*'], ['roles_manage']);
$allow('GET', 'documentation/logs', [], ['roles' => ['Super Admin', 'Admin']]);

// ---------------------------------------------------------------- PATIENTS
$allow('GET', 'patient_list', ['patients_view', 'patients_list']);
$allow('POST', 'patient_add', ['patient_create']);
$allow('PUT', 'patient_edit/*', ['patient_edit']);
$allow('GET', 'patient_details/*', ['patients_view', 'patient_details', 'appointments_view', 'consultations_view', 'ipd_view']);
$allow('DELETE', 'patient_delete/*', ['patient_delete']);
$allow('GET', 'patients/*/download', ['patient_files_download']);
$allow('GET', 'patients/*/anaesthesia_download', ['anesthesia_files_download']);
$allow('GET', 'patient_statistics', ['patients_view']);
$allow('GET', 'patient_preoperative_checklist/*', ['ipd_view', 'patient_files_download', 'anesthesia_files_download']);
$allow('GET', ['patient_fistula_list', 'patient_fistula_details/*', 'patient_fistula_by_patient/*'], ['patients_view', 'consultations_view']);
$allow('POST', 'patient_fistula_add', ['patient_create', 'consultation_create']);
$allow('PUT', 'patient_fistula_update/*', ['patient_edit', 'consultation_edit']);
$allow('DELETE', 'patient_fistula_delete/*', ['patient_delete', 'consultation_delete']);
$allow('GET', 'patient_consultation_list', ['consultations_view', 'patients_view', 'post_surgery_follow_up']);

// OPD (no menu link; legacy)
$allow('GET', ['opd_list', 'opd_details/*'], ['patients_view', 'appointments_view', 'consultations_view']);
$allow('POST', 'opd_add', ['patient_create', 'appointment_create']);
$allow('PUT', 'opd_update/*', ['patient_edit', 'appointment_edit']);
$allow('DELETE', 'opd_delete/*', ['patient_delete', 'appointment_delete']);

// ---------------------------------------------------------------- APPOINTMENTS / ONLINE
$allow('GET', 'appointments_list', ['appointments_view', 'appointments_list']);
$allow('GET', 'appointments_details/*', ['appointments_view', 'appointment_details']);
$allow('POST', 'appointments_add', ['appointment_create']);
$allow('PUT', 'appointments_update/*', ['appointment_edit']);
$allow('DELETE', 'appointments_delete/*', ['appointment_delete']);
$allow('GET', 'appointments_statistics', ['appointments_view']);
$allow('PUT', 'external-appointments/*', ['online_appointments_view']);

// ---------------------------------------------------------------- CONSULTATIONS
$allow('GET', 'consultations_list', ['consultations_view', 'consultations_list']);
$allow('GET', 'consultations_details/*', ['consultations_view', 'consultation_details']);
$allow('POST', 'consultations_add', ['consultation_create']);
$allow('PUT', 'consultations_update/*', ['consultation_edit']);
$allow('PUT', 'consultations_update_patient/*', ['consultation_edit', 'patient_edit']);
$allow('DELETE', 'consultations_delete/*', ['consultation_delete']);
$allow('GET', 'consultations_statistics', ['consultations_view']);
$allow('GET', 'consultation_dates/*', ['consultations_view', 'consultation_details', 'patients_view']);
$allow('GET', 'generate-consultation-report/*', ['consultation_download']);
$allow('GET', ['prescription_download/*', 'consultation_prescription/*'], ['prescription_download']);
foreach (['examinations', 'prescriptions', 'patient_tests'] as $b) {
    $suffixAdd = $b === 'patient_tests' ? 'create' : 'add';
    $allow('GET', ["{$b}_list", "{$b}_details/*"], ['consultations_view']);
    $allow('POST', "{$b}_{$suffixAdd}", ['consultation_create']);
    $allow('PUT', "{$b}_update/*", ['consultation_edit']);
    $allow('DELETE', "{$b}_delete/*", ['consultation_delete']);
}
$allow($ALL, ['post_surgery_*', 'download-post-surgery-follow-up*'], ['post_surgery_follow_up']);

// ---------------------------------------------------------------- BILLS / RECEIPTS
$allow('GET', 'invoice_list', ['bills_view', 'bills_list']);
$allow('GET', ['invoice_details/*', 'payment_details/*'], ['bills_view', 'bill_details']);
$allow('POST', ['invoice_payment', 'amount_include_in_invoice'], ['bill_edit', 'bill_create']);
$allow('POST', 'invoice_add_or_update/*', ['bill_edit', 'bill_create', 'consultation_create', 'consultation_edit']);
$allow('GET', ['invoice_download/*', 'receipt_download/*', 'receipt_download_by_invoice/*'], ['invoice_download']);
$allow('GET', 'receipt_download_by_external_appointment/*', ['invoice_download', 'online_appointments_view']);

// ---------------------------------------------------------------- EXPENSES
$allow('GET', 'expenses_list', ['expenses_view', 'expenses_list']);
$allow('GET', 'expenses_details/*', ['expenses_view', 'expense_details', 'expense_report_view']);
$allow('GET', 'expenses_voucher/*', ['expenses_view', 'expense_details', 'expense_report_view']);
$allow('POST', 'expenses_add', ['expense_create']);
$allow('PUT', 'expenses_update/*', ['expense_edit']);
$allow('DELETE', 'expenses_delete/*', ['expense_delete']);

// ---------------------------------------------------------------- REPORTS
$allow('GET', ['reports/expenses_list', 'reports/expenses_download'], ['expense_report_view']);
$allow('GET', ['reports/invoice_list', 'reports/invoice_download'], ['invoice_report_view']);
$allow(['GET', 'POST'], ['reports/fistula_list', 'reports/fistula_download'], ['fistula_report_view']);
$allow(['GET', 'POST'], ['reports/consultation_list', 'reports/consultation_download'], ['consultation_report_view']);
$allow(['GET', 'POST'], ['reports/ipd_list', 'reports/ipd_download'], ['reports_view']);

// ---------------------------------------------------------------- IPD
$allow('GET', ['ipd_enrollment_list', 'ipd_enrollment_details/*'], ['ipd_view', 'ipd_enrollments_list']);
$allow('DELETE', 'ipd_enrollment_delete/*', ['ipd_enrollment_edit']);
$allow('GET', 'ipd_list', ['ipd_view', 'ipd_patients_list']);
$allow('GET', 'ipd_details/*', ['ipd_view', 'ipd_patient_details']);
$allow('POST', 'ipd_add', ['ipd_enrollment_create']);
$allow('PUT', 'ipd_update/*', ['ipd_enrollment_edit']);
$allow('DELETE', 'ipd_delete/*', ['ipd_enrollment_edit']);
$allow('PUT', 'ipd_update_discharge_datetime/*', ['ipd_discharge_edit']);
$allow('POST', 'ipd_billing_update/*', ['ipd_patient_discharge']);
$allow($ALL, ['ipd_*', 'preliminary_notes*'], ['ipd_view']);

// ---------------------------------------------------------------- MASTERS
// consultation form quick-add modals create these masters
$quick = ['masters_view', 'consultation_create', 'consultation_edit'];
foreach (['chief_complaint', 'surgical_history', 'comorbidities', 'on_examination', 'diagnosis', 'tests', 'diet_plan', 'proctoscopy', 'management', 'service_cost', 'medicines'] as $b) {
    $allow('POST', "{$b}_add", $quick);
}
$allow(['GET', 'PUT'], ['dre_details/*', 'dre_update/*'], array_merge($quick, ['post_surgery_follow_up']));
$allow('POST', 'dre_add', array_merge($quick, ['post_surgery_follow_up']));
$allow($ALL, ['shloka_*'], ['shloka_view']);
$allow($ALL, ['medicine_category_mapping_*'], ['masters_view', 'settings_view']);
foreach ([
    'allergies', 'amount_for', 'agni', 'avastha', 'koshta', 'prakriti', 'vikruti', 'bank_information',
    'bed', 'billing_service_category', 'chief_complaint', 'comorbidities', 'consultation_cost', 'departments',
    'diagnosis', 'diet_plan', 'dre', 'findings', 'fistula', 'food_advice', 'management', 'medicine_categories',
    'medicines', 'on_examination', 'proctoscopy', 'referral_doctors', 'room', 'ward', 'service_cost',
    'surgical_history', 'tests', 'yoga_asana',
] as $b) {
    $crud($b, ['masters_view'], ['masters_view'], ['masters_view'], ['masters_view']);
}

return [
    'mode' => env('RBAC_MODE', 'enforce'),
    'guard' => 'api',
    'super_roles' => ['Super Admin'],
    'admin_roles' => ['Super Admin', 'Admin'],
    'protected_role' => 'Super Admin',
    'builtin_roles' => ['Super Admin', 'Admin', 'Doctor', 'Front Desk User', 'Nurse', 'Accountant'],
    'rules' => $rules,
    'catalog' => [
        'Dashboard' => [
            'dashboard_view' => 'Dashboard view',
        ],
        'Patients' => [
            'patients_view' => 'Patients view',
            'patients_list' => 'Patients list',
            'patient_create' => 'Patient create',
            'patient_edit' => 'Patient edit',
            'patient_delete' => 'Patient delete',
            'patient_details' => 'Patient details',
            'patient_files_download' => 'Download patient files',
            'anesthesia_files_download' => 'Download anaesthesia files',
            'post_surgery_follow_up' => 'Post-surgery follow-up',
        ],
        'Appointments' => [
            'appointments_view' => 'Appointments view',
            'appointments_list' => 'Appointments list',
            'appointment_create' => 'Appointment create',
            'appointment_edit' => 'Appointment edit',
            'appointment_delete' => 'Appointment delete',
            'appointment_details' => 'Appointment details',
        ],
        'Online Appointments' => [
            'online_appointments_view' => 'Online appointments view',
            'online_appointments_list' => 'Online appointments list',
        ],
        'Consultations' => [
            'consultations_view' => 'Consultations view',
            'consultations_list' => 'Consultations list',
            'consultation_create' => 'Consultation create',
            'consultation_edit' => 'Consultation edit',
            'consultation_delete' => 'Consultation delete',
            'consultation_details' => 'Consultation details',
            'prescription_download' => 'Prescription download',
            'consultation_download' => 'Consultation download',
        ],
        'IPD' => [
            'ipd_view' => 'Ipd view',
            'ipd_enrollments_list' => 'Ipd enrollments list',
            'ipd_patients_list' => 'Ipd patients list',
            'ipd_enrollment_create' => 'Ipd enrollment create',
            'ipd_enrollment_edit' => 'Ipd enrollment edit',
            'ipd_patient_details' => 'Ipd patient details',
            'ipd_patient_discharge' => 'Ipd patient discharge',
            'ipd_discharge_edit' => 'Edit IPD discharge date/time',
        ],
        'Bills' => [
            'bills_view' => 'Bills view',
            'bills_list' => 'Bills list',
            'bill_create' => 'Bill create',
            'bill_edit' => 'Bill edit',
            'bill_delete' => 'Bill delete',
            'bill_details' => 'Bill details',
            'invoice_download' => 'Invoice download',
        ],
        'Expenses' => [
            'expenses_view' => 'Expenses view',
            'expenses_list' => 'Expenses list',
            'expense_create' => 'Expense create',
            'expense_edit' => 'Expense edit',
            'expense_delete' => 'Expense delete',
            'expense_details' => 'Expense details',
        ],
        'Reports' => [
            'reports_view' => 'Reports view',
            'expense_report_view' => 'Expense report view',
            'invoice_report_view' => 'Invoice report view',
            'consultation_report_view' => 'Consultation report view',
            'fistula_report_view' => 'Fistula report view',
        ],
        'Masters' => [
            'masters_view' => 'Masters view',
            'shloka_view' => 'Shloka view',
        ],
        'Users and Roles' => [
            'users_view' => 'Users view',
            'users_list' => 'Users list',
            'user_create' => 'User create',
            'user_edit' => 'User edit',
            'user_delete' => 'User delete',
            'user_details' => 'User details',
            'roles_manage' => 'Manage roles and permissions',
        ],
        'Settings' => [
            'settings_view' => 'Settings view',
            'settings_edit' => 'Settings edit',
            'setting_update' => 'Setting update',
        ],
    ],
    // role => permissions, taken verbatim from frontend ROLE_PERMISSIONS (rolesRoute.tsx). Super Admin = whole catalog.
    'roles' => [
        'Admin' => [
            'dashboard_view',
            'patients_view',
            'appointments_view',
            'consultations_view',
            'bills_view',
            'expenses_view',
            'reports_view',
            'fistula_report_view',
            'invoice_report_view',
            'consultation_report_view',
            'expense_report_view',
            'masters_view',
            'shloka_view',
            'users_view',
            'settings_view',
            'online_appointments_view',
            'patient_create',
            'appointment_create',
            'consultation_create',
            'bill_create',
            'expense_create',
            'user_create',
            'patient_edit',
            'appointment_edit',
            'consultation_edit',
            'bill_edit',
            'expense_edit',
            'user_edit',
            'settings_edit',
            'patient_delete',
            'appointment_delete',
            'consultation_delete',
            'bill_delete',
            'expense_delete',
            'user_delete',
            'patients_list',
            'appointments_list',
            'consultations_list',
            'bills_list',
            'expenses_list',
            'users_list',
            'online_appointments_list',
            'patient_details',
            'appointment_details',
            'consultation_details',
            'bill_details',
            'expense_details',
            'user_details',
            'patient_files_download',
            'anesthesia_files_download',
            'post_surgery_follow_up',
            'prescription_download',
            'consultation_download',
            'invoice_download',
            'ipd_view',
            'roles_manage',
        ],
        'Doctor' => [
            'dashboard_view',
            'patients_view',
            'appointments_view',
            'consultations_view',
            'ipd_view',
            'expenses_view',
            'patients_list',
            'patient_create',
            'patient_edit',
            'patient_details',
            'patient_files_download',
            'anesthesia_files_download',
            'post_surgery_follow_up',
            'appointments_list',
            'appointment_create',
            'appointment_edit',
            'appointment_details',
            'consultations_list',
            'consultation_create',
            'consultation_edit',
            'consultation_details',
            'expenses_list',
            'expense_create',
            'expense_edit',
            'expense_details',
            'expense_delete',
            'online_appointments_view',
            'online_appointments_list',
        ],
        'Nurse' => [
            'dashboard_view',
            'patients_view',
            'appointments_view',
            'consultations_view',
            'ipd_view',
            'expenses_view',
            'expenses_list',
            'expense_create',
            'expense_edit',
            'expense_details',
            'expense_delete',
            'appointments_list',
            'appointment_create',
            'appointment_edit',
            'appointment_details',
            'consultations_list',
            'consultation_details',
            'consultation_download',
            'invoice_download',
            'prescription_download',
            'post_surgery_follow_up',
            'patients_list',
            'patient_create',
            'patient_details',
            'patient_files_download',
            'anesthesia_files_download',
            'online_appointments_view',
        ],
        'Front Desk User' => [
            'dashboard_view',
            'patients_view',
            'appointments_view',
            'bills_view',
            'expenses_view',
            'expenses_list',
            'expense_create',
            'expense_edit',
            'expense_details',
            'expense_delete',
            'consultation_download',
            'invoice_download',
            'prescription_download',
            'bills_list',
            'bill_edit',
            'bill_details',
            'patients_list',
            'patient_create',
            'patient_edit',
            'patient_details',
            'patient_files_download',
            'anesthesia_files_download',
            'post_surgery_follow_up',
            'reports_view',
            'invoice_report_view',
            'expense_report_view',
            'online_appointments_view',
            'online_appointments_list',
        ],
        'Accountant' => [
            'expense_report_view',
            'invoice_report_view',
            'fistula_report_view',
            'consultation_report_view',
            'reports_view',
        ],
    ],
    // granted to every role that holds the key on the left (IPD sub-permissions are implied by ipd_view)
    'implied' => [
        'ipd_view' => ['ipd_enrollments_list', 'ipd_patients_list', 'ipd_enrollment_create', 'ipd_enrollment_edit', 'ipd_patient_details', 'ipd_patient_discharge'],
    ],
    // additive operational grants beyond the frontend matrix (see docs/changes/rbac-backend.md "Matrix gaps")
    'additions' => [
        'Admin' => ['roles_manage', 'ipd_discharge_edit'],
        'Front Desk User' => ['ipd_discharge_edit', 'appointments_list', 'appointment_create', 'appointment_edit', 'appointment_details'],
        'Doctor' => ['prescription_download', 'consultation_download'],
        'Nurse' => ['online_appointments_list'],
    ],
];

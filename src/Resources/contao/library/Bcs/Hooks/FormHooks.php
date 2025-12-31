<?php

namespace Bcs\Hooks;
use DateTime;

use Bcs\Model\Assignment;
use Bcs\Model\District;
use Bcs\Model\Invoice;
use Bcs\Model\InvoiceDistrict;
use Bcs\Model\InvoiceRequest;
use Bcs\Model\PriceTier;
use Bcs\Model\Psychologist;
use Bcs\Model\Service;
use Bcs\Model\School;
use Bcs\Model\Student;
use Bcs\Model\Transaction;
use Bcs\Model\TransactionMisc;

use Contao\Controller;
use Contao\Environment;
use Contao\FrontendUser;
use Contao\Input;
use Contao\MemberModel;
use Contao\PageModel;


class FormHooks
{
    protected static $arrUserOptions = array();

    // When a form is submitted
    public function onFormSubmit($submittedData, $formData, $files, $labels, $form)
    {
        
        // BOOTSTRAP - For triggering one off scripts when the need arises
        if($formData['formID'] == 'bootstrap') {
            
            // Get all Transactions
             $transactions = Transaction::findAll();
             
             echo "Finding all Transactions <br>";
             
             foreach($transactions as $transaction) {
                 
                 echo "Transaction ID: ". $transaction->id ."<br>";
                 
                 $assignment = Assignment::findBy(['id = ?'], [$transaction->pid]);
                 
                 echo "Assignment ID: ". $assignment->id ."<br>";
                 echo "District ID: ". $assignment->district ."<br>";
                 echo "Transaction Saved <br><hr><br>";
                 
                 $transaction->district = $assignment->district;
                 $transaction->save();
                 
             }

        }
        
        
        
        
        // Assignment Selection Form
        else if($formData['formID'] == 'assignment_selection') {
            // Create transaction using submitted data
            $_SESSION['assignment_uuid'] = $submittedData['assignment_uuid'];
        }
        
        // Psych Work Form
        else if($formData['formID'] == 'psych_work_form') {
            // Create transaction using submitted data
            echo "clever bastard!";
            die();
        }
            
        // Assignment Generate Transaction Form
        else if($formData['formID'] == 'assignment_generate_transaction') {
            
            // First, try to find Transaction with these values
            $duplicate_check = Transaction::findOneBy(
            [
                'tl_transaction.pid=?',
                'tl_transaction.psychologist=?',
                'tl_transaction.service=?',
                'tl_transaction.price=?',
                'tl_transaction.meeting_date=?',
                'tl_transaction.meeting_start=?',
                'tl_transaction.meeting_end=?'
            ],[
                $submittedData['assignment_uuid'], // pid
                $submittedData['psychologist'], // psychologist
                $submittedData['service_provided'], // service
                $submittedData['hourly_rate'], // price
                strtotime($submittedData['meeting_date']), // meeting_date
                $submittedData['start_time'], // meeting_start
                $submittedData['end_time'], // meeting_end
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/assignments/assignments-duplicate-detected.html");
                die();
            } else {
                
                // Create a new Transaction
                $transaction = new Transaction();
                
                // Apply values
                $transaction->pid = $submittedData['assignment_uuid'];
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = $submittedData['psychologist'];
                $transaction->service = $submittedData['service_provided'];
                $transaction->price = $submittedData['hourly_rate'];
                $transaction->meeting_date = strtotime($submittedData['meeting_date']);
                $transaction->meeting_start = $submittedData['start_time'];
                $transaction->meeting_end = $submittedData['end_time'];
                $transaction->meeting_duration = $this->timeDifferenceInMinutes($submittedData['start_time'],$submittedData['end_time']);
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
    
                // Get LASID / SASID
                $assignment = Assignment::findBy('id', $transaction->pid);
                $student = Student::findBy('id', $assignment->student);
                $transaction->lasid = $student->lasid;
                $transaction->sasid = $student->sasid;
                $transaction->district = $assignment->district;
                
                // Save our new Transaction
                $transaction->save();
            }

        }
        
        // Assignment Add Meeting as a Covering Psychologist form
        else if($formData['formID'] == 'assignment_add_meeting') {
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.district=?',
                'tl_transaction_misc.school=?',
                'tl_transaction_misc.student_initials=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.meeting_date=?',
                'tl_transaction_misc.meeting_start=?',
                'tl_transaction_misc.meeting_end=?'
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $submittedData['district'], // district
                $submittedData['school'], // school
                $submittedData['student_initials'], // student_initials
                $submittedData['service'], // service
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                strtotime($submittedData['meeting_date']), // meeting_date
                $submittedData['start_time'], // meeting_start
                $submittedData['end_time'] // meeting_end
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/add-meetings/add-meetings-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                
                // Apply values
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->psychologist = FrontendUser::getInstance()->id;
                
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                
                $transaction->district = $submittedData['district'];
                $transaction->school = $submittedData['school'];
                $transaction->student_initials = $submittedData['student_initials'];
                $transaction->lasid = $submittedData['lasid'];
                $transaction->sasid = $submittedData['sasid'];
    
                
                $transaction->service = $submittedData['service'];
                $service = Service::findBy('id', $transaction->service);
                $transaction->service_label = "Covering - " . $service->name;
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->meeting_date = strtotime($submittedData['meeting_date']);
                $transaction->meeting_start = $submittedData['start_time'];
                $transaction->meeting_end = $submittedData['end_time'];
                $transaction->meeting_duration = $this->timeDifferenceInMinutes($submittedData['start_time'],$submittedData['end_time']);
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                
                // Save our new Transaction
                $transaction->save();
            }
            
        }

        // Assignment Misc. Billing Form
        else if($formData['formID'] == 'assignment_misc_billing') {
            
            $service = Service::findBy('name', 'Misc. Billing');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.date_submitted=?'
            ],[
                $submittedData['psychologist'], // psychologist
                $service->service_code, // service
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                $submittedData['date_submitted'] // date submitted
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/misc-billing/misc-billing-duplicate-detected.html");
                die();
            } else {
                 // Create a new Transaction
                $transaction = new TransactionMisc();
                
                // Apply values
                $transaction->pid = 0;
                $transaction->tstamp = time();
                
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
    
                $transaction->psychologist = $submittedData['psychologist'];
    
                $transaction->service = $service->service_code;
    
                $transaction->service_label = $submittedData['service_label'];
                
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                
                // Save our new Transaction
                $transaction->save();   
            }
            
        }
            
        // Assignment Misc. Travel Expenses Form
        else if($formData['formID'] == 'assignment_misc_travel_expenses') {
            
            $service = Service::findBy('name', 'Misc. Travel Expenses');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.date_submitted=?'
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $service->service_code, // service
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                $submittedData['date_submitted'] // date submitted
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/misc-travel-expenses/misc-travel-expenses-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                
                // Apply values
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->service_label = $submittedData['service_label'];
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }

        }

        // Assignment Parking form
        else if($formData['formID'] == 'assignment_parking') {
            
            $service = Service::findBy('name', 'Parking');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.district=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.date_submitted=?'
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $submittedData['district'], // district
                $service->service_code, // service
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                $submittedData['date_submitted'] // date submitted
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/parking/parking-duplicate-detected.html");
                die();
            } else {
                
                // Create a new Transaction
                $transaction = new TransactionMisc();
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->district = $submittedData['district'];
                $transaction->service_label = $submittedData['service_label'];
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }
            
        }

        // Assignment Manager form
        else if($formData['formID'] == 'assignment_manager') {
            
            $service = Service::findBy('name', 'Manager');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.date_submitted=?'
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $service->service_code, // service
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                $submittedData['date_submitted'] // date submitted
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/manager/manager-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->service_label = $submittedData['service_label'];
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }
            
            
        }

        // Assignment Editing Services form
        else if($formData['formID'] == 'assignment_editing_services') {
            
            $service = Service::findBy('name', 'Editing Services');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.meeting_duration=?',
                'tl_transaction_misc.price=?',
                'tl_transaction_misc.date_submitted=?'
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $service->service_code, // service
                $submittedData['meeting_duration'], // Meeting Duration
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'], // price
                $submittedData['date_submitted'] // date submitted
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/editing-services/editing-services-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->service_label = "Editing Services";
                $transaction->meeting_duration = $submittedData['meeting_duration'];
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }
            
        }

        // Assignment Test Late Cancel - First form
        else if($formData['formID'] == 'assignment_test_late_cancel_first') {
            
            $service = Service::findBy('service_code', '32');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.district=?',
                'tl_transaction_misc.school=?',
                'tl_transaction_misc.student_initials=?',
                'tl_transaction_misc.meeting_date=?',
                'tl_transaction_misc.price=?',
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $service->service_code, // service
                $submittedData['district'], // district
                $submittedData['school'], // school
                $submittedData['student_initials'], // student initials
                strtotime($submittedData['meeting_date']), // meeting date
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'] // price
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/test-late-cancel-first/test-late-cancel-first-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                
                // Apply values
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->service_label = $service->name;
                $transaction->district = $submittedData['district'];
                $transaction->school = $submittedData['school'];
                $transaction->student_initials = $submittedData['student_initials'];
                $transaction->lasid = $submittedData['lasid'];
                $transaction->sasid = $submittedData['sasid'];
                $transaction->meeting_date = strtotime($submittedData['meeting_date']);
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }

        }

        // Assignment Test Late Cancel - First form
        else if($formData['formID'] == 'assignment_test_late_cancel_additional') {
            
            $service = Service::findBy('service_code', '33');
            
            // First, try to find Transaction with these values
            $duplicate_check = TransactionMisc::findOneBy(
            [
                'tl_transaction_misc.psychologist=?',
                'tl_transaction_misc.service=?',
                'tl_transaction_misc.district=?',
                'tl_transaction_misc.school=?',
                'tl_transaction_misc.student_initials=?',
                'tl_transaction_misc.meeting_date=?',
                'tl_transaction_misc.price=?',
            ],[
                FrontendUser::getInstance()->id, // psychologist
                $service->service_code, // service
                $submittedData['district'], // district
                $submittedData['school'], // school
                $submittedData['student_initials'], // student initials
                strtotime($submittedData['meeting_date']), // meeting date
                $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'] // price
            ]);
            
            if($duplicate_check) {
                header("Location: https://ga.inc/payments/dashboard/test-late-cancel-additional/test-late-cancel-additional-duplicate-detected.html");
                die();
            } else {
                // Create a new Transaction
                $transaction = new TransactionMisc();
                $transaction->pid = 0;
                $transaction->tstamp = time();
                $transaction->date_submitted = strtotime($submittedData['date_submitted']);
                $transaction->psychologist = FrontendUser::getInstance()->id;
                $transaction->service = $service->service_code;
                $transaction->service_label = $service->name;
                $transaction->district = $submittedData['district'];
                $transaction->school = $submittedData['school'];
                $transaction->student_initials = $submittedData['student_initials'];
                $transaction->lasid = $submittedData['lasid'];
                $transaction->sasid = $submittedData['sasid'];
                $transaction->meeting_date = strtotime($submittedData['meeting_date']);
                $transaction->price = $submittedData['hourly_rate_dollars'] . '.' . $submittedData['hourly_rate_cents'];
                $transaction->notes = $submittedData['notes'];
                $transaction->status = 'created';
                $transaction->save();
            }
        }
       
        // Assignment Generate Transaction Form
        else if($formData['formID'] == 'dashboard_send_invoice_emails') {
            
            $first = true;
            
            // Loop through listed districts
            foreach($submittedData['districts'] as $district) {
               
                $i = InvoiceDistrict::findBy('id', $district);
                $ir = InvoiceRequest::findBy(['id = ?'], [$i->pid]);
                $d = District::findBy('id', $i->district);
                
                $addr = $d->contact_email;
                //$addr = 'mark@brightcloudstudio.com, ed@globalassessmentsinc.com';
                //$addr = 'mark@brightcloudstudio.com';
                
                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                // More headers
                $headers .= 'From: billing@globalassessmentsinc.com' . "\r\n";
                $headers .= 'Return-Path: billing@globalassessmentsinc.com' . "\r\n";
                $headers .= 'Cc: ed@globalassessmentsinc.com, ' . $d->contact_cc_email . "\r\n";
                
                $name = $d->district_name;
                
                // Format our "Date Start" field into a proper DateTime
                $ir_start_date = DateTime::createFromFormat('m/d/y', $ir->date_start);
                // Convert our proper date into the month name as a string
                $month = $ir_start_date->format('F');
                
                $sub = "$name, your $month invoice is ready for you";
                
                $message = "
                    <html>
                    <head>
                    <title>GAI - Invoice</title>
                    </head>
                    <body>
                        <p>Dear $name</p>
                        <p>Your $month invoice is ready for you. Please remit payment at your earliest convenience.</p>
                        <p>You can access the PDF file using the link below.</p>
                        <p>Link: <a href='$i->invoice_url'>Invoice PDF File</a></p>
                        <p>Thank you for your business!</p>
                        <p>Best,</p>
                        <p>Global Assessments, Inc</p>
                    </body>
                    </html>
                    ";

                
                if($first) {
                    //$first = false;
                    mail($addr, $sub, $message, $headers, "-fbilling@globalassessmentsinc.com");
                }
                
                //echo $message . "<br><br>";
                
            }
            
            //die();

            

            
        }
        
    }



    // When a form is loaded
    public function onPrepareForm($fields, $formId, $form)
    {
        // If we are loading our "Create Transactions" form
        if(str_contains(Environment::get('request'), "create-transaction")) {
            
            // If we don't have an assignment unique id in our session
            if(!$_SESSION['assignment_uuid']) {
                
                // Find the one "root" page
                $root_page = PageModel::findOneByType('root');
                // Get the url for our root page
                $root_url = $root_page->getFrontendUrl();
                // Set code to 302, which means temporary internal redirect
                $redirect_code = 302;
                // Temporary Redirect (302) the user to the root page
                Controller::redirect($root_url, ($redirect_code ? $redirect_code : NULL));
                
            }
            
        }
        
        ///////////////////////////////
        // ASSIGNMENT SELECTION FORM //
        //////////////////////////////
        if($form->formID == 'assignment_selection') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'assignment_uuid') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                        
                        // get all of the Assignments for this Member
                        $opt = [
                            'order' => 'date_created ASC'
                        ];

                        $member = FrontendUser::getInstance();
                         
                        // Get the Assignments using our specific criteria
                        $assignments = Assignment::findBy(['psychologist = ?', 'published = ?'], [$member->id, '1'], $opt);
        

                        // Loop through all the collected Assignments
                        foreach($assignments as $assignment) {
                            $label = '';
                            
                            // Get the formated 'Date Created'
                            $t = $assignment->date_created;
                            
                            // Get label for District
                            $district = District::findOneBy('id', $assignment->district);

                            // Get label for School
                            $school = School::findOneBy('id', $assignment->school);
                            
                            // date_created - district - school - student
                            $student = Student::findOneBy('id', $assignment->student);
                            
                            // Get total number of Transactions this Member has for this Assignment
                            $transactions_total = Transaction::countBy(['pid = ?'], [$assignment->id]);
                            
                            // Check if Administrator (id = 2) is in this member's Groups, if so add our Transaction Total number
                            if(in_array('2', $member->groups))
                                $label .= "<span id='transactions'>(" . $transactions_total . ")</span> ";
                            
                            $label .= "<span id='date'>" . $t . "</span> - ";
                            $label .= "<span id='district'>" . $district->district_name. "</span> - ";
                            $label .= "<span id='school'>" . $school->school_name . "</span> - ";
                            $label .= "<span id='student'>" . $student->name . "</span>";
                            
                            
                            // Format the assignment so it can be added to the form
                            $options[] = array (
                                'value' => $assignment->id,
                                'label' => $label,
                                'district' => $district->district_name,
                                'school' => $school->school_name,
                                'student' => $student->name
                            );
                        }
                        
                        
                        
                        
                        // Get the Assignments using our specific criteria
                        $assignments_shared = Assignment::findAllByShared($member->id, $opt);

                        // Loop through all the collected Assignments
                        foreach($assignments_shared as $assignment) {
                            
                            if(in_array($member->id, unserialize($assignment->psychologists_shared))) {
                            
                                $label = '';
                                
                                // Get the formated 'Date Created'
                                $t = $assignment->date_created;
                                
                                // Get label for District
                                $district = District::findOneBy('id', $assignment->district);
    
                                // Get label for School
                                $school = School::findOneBy('id', $assignment->school);
                                
                                // date_created - district - school - student
                                $student = Student::findOneBy('id', $assignment->student);
                                
                                // Get total number of Transactions this Member has for this Assignment
                                $transactions_total = Transaction::countBy(['pid = ?'], [$assignment->id]);
                                
                                // Check if Administrator (id = 2) is in this member's Groups, if so add our Transaction Total number
                                if(in_array('2', $member->groups))
                                    $label .= "<span id='transactions'>(" . $transactions_total . ")</span> ";
                                
                                $psy = MemberModel::findBy('id', $assignment->psychologist);
                                
                                $label .= "<span id='shared'>(Shared by ".$psy->firstname ." ". $psy->lastname.")</span> ";
                                $label .= "<span id='date'>" . $t . "</span> - ";
                                $label .= "<span id='district'>" . $district->district_name. "</span> - ";
                                $label .= "<span id='school'>" . $school->school_name . "</span> - ";
                                $label .= "<span id='student'>" . $student->name . "</span>";
                                
                                
                                // Format the assignment so it can be added to the form
                                $options[] = array (
                                    'value' => $assignment->id,
                                    'label' => $label,
                                    'district' => $district->district_name,
                                    'school' => $school->school_name,
                                    'student' => $student->name
                                );
                            }
                        }
                        
                        
                        
                        

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }


        //////////////////////////////////////////
        // ASSIGNMENT GENERATE TRANSACTION FORM //
        //////////////////////////////////////////
        else if($form->formID == 'assignment_generate_transaction') {
            
            // Get the Assignment
            $assignment = Assignment::findOneBy('id', $_SESSION['assignment_uuid']);

            
            // Customize certain fields, first loop through all form fields
            foreach($fields as $field) {
                
                // Hidden Fields
                $member = FrontendUser::getInstance();
                if($field->name == 'psychologist') { $field->value = $member->id; }
                if($field->name == 'report_submitted') { $field->value = $assignment->report_submitted; }
                if($field->name == 'service_assigned') { $field->value = $assignment->type_of_testing; }
                
                $service = Service::findOneBy('service_code', $assignment->type_of_testing);
                if($field->name == 'service_type') { $field->value = $service->service_type; }
                

                // Assignment Details
                $district = District::findOneBy('id', $assignment->district );
                if($field->name == 'district') { $field->value = $assignment->district; }
                if($field->name == 'district_label') { $field->value = $district->district_name; }
                $school = School::findOneBy('id', $assignment->school );
                if($field->name == 'school') { $field->value = $assignment->school; }
                if($field->name == 'school_label') { $field->value = $school->school_name; }
    

                
                //Get Student and fill in values
                $student = Student::findOneBy('id', $assignment->student );

                if($field->name == 'student') { $field->value = $assignment->student; }
                if($field->name == 'student_label') { $field->value = $student->name; }
                if($field->name == 'student_dob') { $field->value = $student->date_of_birth; }
                if($field->name == 'student_lasid') { $field->value = $student->lasid; }
                if($field->name == 'student_sasid') { $field->value = $student->sasid; }
                
                // Transaction Details
                if($field->name == 'service_provided') { $field->value = $assignment->type_of_testing; }
                if($field->name == 'service_provided_label') { $field->value = $service->name; }
                
            }

            
            // Return our modified fields
            return $fields;
        }
        
        //////////////////////////////////
        // ASSIGNMENT MISC BILLING FORM //
        /////////////////////////////////
        if($form->formID == 'assignment_misc_billing') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'psychologist') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);

                    // Set Configuration options for the query
                    $opt = [
                        'order' => 'firstname ASC'
                    ];
                    
                    // Hold the psys
                    $psychologists = MemberModel::findBy('disable', '0', $opt);
            
                    // loop through each service
                    foreach($psychologists as $psy) {
        
                        $options[] = array (
                            'value' => $psy->id,
                            'label' => $psy->firstname . ' ' . $psy->lastname
                        );
                        
                    }

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        
        //////////////////////////////////
        // ASSIGNMENT ADD MEETING FORM //
        /////////////////////////////////
        if($form->formID == 'assignment_add_meeting' || $form->formID == 'assignment_test_late_cancel_first' || $form->formID == 'assignment_test_late_cancel_additional') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'district') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                    
                    $opt = [
                        'order' => 'district_name ASC'
                    ];
                    // Hold the psys
                    $districts = District::findBy('published', '1', $opt);
            
                    // loop through each service
                    foreach($districts as $district) {
        
                        $options[] = array (
                            'value' => $district->id,
                            'label' => $district->district_name,
                            'mandatory' => false
                        );
                        
                    }

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
                
                // If this is our assignment uuid radio field
                else if($field->name == 'school') {
                    
                    // Convert to php array
                    $options = unserialize($field->options);
                    
                    $opt = [
                        'order' => 'school_name ASC'
                    ];
                    // Hold the psys
                    $schools = School::findBy('published', '1', $opt);
                    
                    $options[] = array (
                        'value' => '',
                        'label' => 'First, select a District',
                        'mandatory' => false
                    );
                    
                    // loop through each service
                    foreach($schools as $school) {
        
                        $options[] = array (
                            'value' => $school->id,
                            'label' => $school->school_name,
                            'mandatory' => false
                        );
                        
                    }

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
                
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        
        
        /////////////////////////
        // SEND INVOICE EMAILS //
        /////////////////////////
        
        if($form->formID == 'dashboard_send_invoice_emails') {
            
            $gen_id = $_GET['gen_req'];
            
            // Loop through the fields
            foreach($fields as $field) {

                if($field->name == 'districts') {
                    
                    // Convert to php array
                    //$options = unserialize($field->options);

                    // Set Configuration options for the query
                    $opt = [
                        'order' => 'firstname ASC'
                    ];
                    
                    // Hold the psys
                    $invoices = InvoiceDistrict::findBy(['published = ?', 'pid = ?'], ['', $gen_id]);
                    
                    $opt_dis = array();
                    // loop through each service
                    foreach($invoices as $invoice) {
                        $opt_dis[] = array (
                            'value' => $invoice->id,
                            'label' => $invoice->district_name
                        );
                    }

                    // Save back as a serialized array
                    $field->options = serialize($opt_dis);
                    
                    $invoice_request = InvoiceRequest::findBy(['id = ?'], [$gen_id]);
                    //$field->label .= ": " . $invoice_request->date_start . " - " . $invoice_request->date_end;
                    $field->label = "Districts for Invoice Request " . $invoice_request->id . ": " . $invoice_request->date_start . " - " . $invoice_request->date_end;
                    
                    
                }
                
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        
        /////////////////////////////////////
        // SEND INVOICE EMAILS - SELECTOR //
        ////////////////////////////////////
        
        if($form->formID == 'dashboard_send_invoice_emails_selector') {
            
            // Loop through the fields
            foreach($fields as $field) {
                
                // If this is our assignment uuid radio field
                if($field->name == 'selector') {
                    
                    // Convert to php array
                    //$options = unserialize($field->options);

                    // Set Configuration options for the query
                    $opt = [
                        'order' => 'id ASC'
                    ];
                    
                    // Hold the psys
                    $invoice_requests = InvoiceRequest::findAll();
                    Invoice::findBy(['psychologist = ?'], [$member->id]);
                    
                    $options[] = array (
                        'value' => '',
                        'label' => "First, select a Generation"
                    );
                    
                    // loop through each service
                    foreach($invoice_requests as $request) {
                        $options[] = array (
                            'value' => $request->id,
                            'label' => date('m/d/y', strtotime($request->date_start)) . " - " . date('m/d/y', strtotime($request->date_end))
                        );
                    }

                    // Save back as a serialized array
                    $field->options = serialize($options);
                }
                
            }

            // Prefill in our Work Assignment information
            return $fields;
        }
        
        
        
        
        

        // Return our modified fields
        return $fields;
        
    }

    

    // HELPER FUNCTIONS
    
    // Converts our H:i format into pure minutes
    public function hoursToMinutes($hours) 
    { 
        $minutes = 0; 
        if (strpos($hours, ':') !== false) 
        { 
            // Split hours and minutes. 
            list($hours, $minutes) = explode(':', $hours); 
        } 
        return $hours * 60 + $minutes; 
    } 
    
    public function timeDifferenceInMinutes($time1, $time2) {
        // Convert times to UNIX timestamps
        $timestamp1 = strtotime($time1);
        $timestamp2 = strtotime($time2);
    
        // Calculate the difference in seconds
        $differenceInSeconds = abs($timestamp2 - $timestamp1);
    
        // Convert seconds to minutes
        $differenceInMinutes = round($differenceInSeconds / 60);
    
        return $differenceInMinutes;
    }

}

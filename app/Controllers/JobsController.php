<?php
namespace App\Controllers;

use App\Models\Job;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\RedirectResponse;
use App\Services\JobService;

class JobsController extends BaseController {
//Litas todos lo jobs desde el panela adminitrativo
  private $jobService;

    public function __construct( Jobservice $jobService) {
        parent::__construct();
        $this->jobService = $jobService;
    }

  public function indexAction () {
    $jobs = Job::withTrashed()->get();
    return $this->renderHTML('jobs/index.twig', compact('jobs'));
 }

 public function deleteAction ( $request) {
    $params = $request->getQueryParams();
    $this->jobService->deleteJob($params['id']);

    return new RedirectResponse('/php/jobs');


    
 }

    public function getAddJobAction($request) {
        $responseMessage = null;

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $jobValidator = v::key('title', v::stringType()->notEmpty())
                  ->key('description', v::stringType()->notEmpty());

            try {
                $jobValidator->assert($postData);
                $postData = $request->getParsedBody();
                
                $files = $request->getUploadedFiles();
                $logo = $files['logo'];

                //guardamos la imagen logo
                $filePath = "";
                if($logo->getError() == UPLOAD_ERR_OK) {
                    $fileName = $logo->getClientFilename();
                    $filePath = "uploads/$fileName";
                    $logo->moveTo($filePath);
                }
                //guardamos title y description en la base de datos
                $job = new Job();
                $job->title = $postData['title'];
                $job->description = $postData['description'];
                $job->image = $filePath;
                $job->save();

                $responseMessage = 'Saved';
            } catch (\Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }

        return $this->renderHTML('addJob.twig', [
            'responseMessage' =>$responseMessage
        ]);
    }
}
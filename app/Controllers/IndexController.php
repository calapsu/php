<?php

namespace App\Controllers;

use App\Models\{Job, Project};

class IndexController extends BaseController {
    public function indexAction() {
        $jobs = Job::all();
        $project1 = new Project('Project 1', 'Description 1');
        $projects = [
            $project1
        ];

        //limites de meses en el proyecto si es menor al dado se elimina utilisamos use para icluir la duncion
        //closures
        //$limiMonths = 15;
        //$filterFunction = function (array $job)  use ($limiMonths) {
        //    return $job['months'] >= $limiMonths;
        //};
        //
        //$jobs = array_filter($jobs->toArray(), $filterFunction);

        

        $name = 'Hector Benitez';
    

        return $this->renderHTML('index.twig', [
            'name' => $name,
            'jobs' => $jobs
        ]);
    }
}
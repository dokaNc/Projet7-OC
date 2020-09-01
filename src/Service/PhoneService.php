<?php


namespace App\Service;


use App\Exception\ResourceValidationException;
use Knp\Component\Pager\Pagination\PaginationInterface;

class PhoneService extends Service
{
    private $repositoryName = 'phoneRepository';

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function getAllData($page)
    {
        return $this->getAll($this->repositoryName, $page);
    }

    /**
     * @param $data
     * @param $violations
     * @throws ResourceValidationException
     */
    public function addData($data, $violations)
    {
        return $this->add($data, $violations);
    }

    /**
     * @param $violations
     * @throws ResourceValidationException
     */
    public function updateData($violations)
    {
        return $this->update($violations);
    }
}
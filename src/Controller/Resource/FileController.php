<?php

namespace App\Controller\Resource;

use App\Entity\MetaKeyword;
use App\Entity\Resource;
use App\Repository\MetaKeywordRepository;
use App\Repository\ResourceRepository;
use App\Utils\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchByUserForm;
use App\Repository\SearchResourceRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/file/")
 */
class FileController extends AbstractController
{

    /**
     * @Route("{id}/file/{fileName}", methods={"GET"}, name="resource_download", requirements={"id"="\d+"})
     */
    public function download(Resource $resource, FileUploader $fileUploader)
    {

        $file = $resource->getFile();
        $filePath = sprintf('%s/%s/%s', $fileUploader->getTargetDirectory(), $file->getExtension(), $file->getUpload());

        return $this->file($filePath, $file->getFileName(), ResponseHeaderBag::DISPOSITION_INLINE);

    }

}

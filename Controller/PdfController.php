<?php
namespace Newageerp\SfPdf\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Newageerp\SfBaseEntity\Controller\OaBaseController;
use Newageerp\SfPdf\Event\SfPdfPreGenerateEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/app/nae-core/pdf")
 */
class PdfController extends OaBaseController
{
    protected EntityManagerInterface $entityManager;

    /**
     * @Route(path="/{schema}/{template}/{id}", methods={"GET"})
     */
    public function parsePdf(Request $request)
    {
        $orgSchema = $schema = $request->get('schema');
        $template = $request->get('template');
        $id = $request->get('id');
        $download = $request->get('download') === 'true' ? true : false;
        $showHtml = $request->get('showHtml') === 'true';

        $schema = implode('', array_map('ucfirst', explode("-", $schema)));
        $className = $this->convertSchemaToEntity($schema);

        /**
         * @var  $repository
         */
        $repository = $this->entityManager->getRepository($className);

        $data = $repository->find($id);

//        $fileName = 'Failas-' . date('Y-m-d') . '.pdf';
//        if (method_exists($data, 'getPdfFileName')) {
//            $fileName = $data->getPdfFileName();
//        }

        $templateName = 'pdf/' . $orgSchema . '/' . $template . '/index.html.twig';

        $pdfParams = [
            'data' => $data
        ];

        $event = new SfPdfPreGenerateEvent(
            $pdfParams,
        );
        $this->eventDispatcher->dispatch($event, SfPdfPreGenerateEvent::NAME);

        return $this->render($templateName, $pdfParams);
    }

}

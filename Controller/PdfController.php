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
    /**
     * @Route(path="/{schema}/{template}/{id}", methods={"GET"})
     */
    public function parsePdf(Request $request)
    {
        $orgSchema = $schema = $request->get('schema');
        $template = $request->get('template');
        $id = $request->get('id');
        $download = $request->get('download') === 'true';
        $showHtml = $request->get('showHtml') === 'true';

        $schema = implode('', array_map('ucfirst', explode("-", $schema)));
        $className = $this->convertSchemaToEntity($schema);

        /**
         * @var  $repository
         */
        $repository = $this->getEm()->getRepository($className);

        $data = $repository->find($id);

        $fileName = 'Failas-' . date('Y-m-d') . '.pdf';
        if (method_exists($data, 'getPdfFileName')) {
            $fileName = $data->getPdfFileName();
        }

        $templateName = 'pdf/' . $orgSchema . '/' . $template . '/index.html.twig';

        $pdfParams = [
            'data' => $data,
            'template' => $template,
        ];

        $event = new SfPdfPreGenerateEvent(
            $pdfParams,
            $fileName,
            $request,
        );
        $this->eventDispatcher->dispatch($event, SfPdfPreGenerateEvent::NAME);

        $pdfParams = $event->getData();
        $fileName = str_replace('/', '_', $event->getFileName());

        if ($showHtml) {
            return $this->render($templateName, $pdfParams);
        }

        $url = 'https://my.datasfs.com/api/r/utils/html2pdf?token=' . $_ENV['NAE_SFS_TOKEN'];

        $fields = json_encode([
            'fileName' => $fileName,
            'link' => $_ENV['NAE_SFS_FRONT_URL'] . '/app/nae-core/pdf/' . $orgSchema . '/' . $template . '/' . $id . '?showHtml=true&skipStamp=' . $request->get('skipStamp') . '&skipSign=' . $request->get('skipSign'),
            'download' => $download
        ]);
        $headers = [
            'Content-Type: application/json'
        ];

        $curlInstance = curl_init();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($curlInstance);
        curl_close($curlInstance);

        $result = json_decode($result, true);

        return $this->redirect($result['url']);
    }

    /**
     * @Route(path="/pdfInfo/{schema}/{template}/{id}", methods={"GET"})
     */
    public function pdfInfo(Request $request)
    {
        $orgSchema = $schema = $request->get('schema');
        $template = $request->get('template');
        $id = $request->get('id');

        $schema = implode('', array_map('ucfirst', explode("-", $schema)));
        $className = $this->convertSchemaToEntity($schema);

        /**
         * @var  $repository
         */
        $repository = $this->getEm()->getRepository($className);

        $data = $repository->find($id);

        $fileName = 'Failas-' . date('Y-m-d') . '.pdf';
        if (method_exists($data, 'getPdfFileName')) {
            $fileName = $data->getPdfFileName();
        }

        $pdfParams = [
            'data' => $data,
            'template' => $template,
        ];

        $event = new SfPdfPreGenerateEvent(
            $pdfParams,
            $fileName,
            $request,
        );
        $this->eventDispatcher->dispatch($event, SfPdfPreGenerateEvent::NAME);

        $fileName = str_replace('/', '_', $event->getFileName());

        $url = 'https://my.datasfs.com/api/r/utils/html2pdf?token=' . $_ENV['NAE_SFS_TOKEN'];

        $fields = json_encode([
            'fileName' => $fileName,
            'link' => $_ENV['NAE_SFS_FRONT_URL'] . '/app/nae-core/pdf/' . $orgSchema . '/' . $template . '/' . $id . '?showHtml=true&skipStamp=' . $request->get('skipStamp') . '&skipSign=' . $request->get('skipSign'),
        ]);
        $headers = [
            'Content-Type: application/json'
        ];

        $curlInstance = curl_init();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($curlInstance);
        curl_close($curlInstance);

        $result = json_decode($result, true);

        return $this->json($result);
    }
}

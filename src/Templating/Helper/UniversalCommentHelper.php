<?php
namespace MercuryKojo\Bundle\UniversalCommentBundle\Templating\Helper;

use Pimcore\Model\DataObject\UniversalComment;
use Pimcore\Templating\PhpEngine;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\Helper\Helper;
use Zend\Paginator\Paginator;

class UniversalCommentHelper extends Helper
{
    public function getName()
    {
        return 'Universal Comment Helper';
    }

    private $engine;
    private $request;

    public function __construct(PhpEngine $engine, RequestStack $requestStack)
    {
        $this->engine = $engine;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @param string $id
     * @param array $config
     * - template (string) [UniversalCommentBundle::container.html.php]
     * - itemCountPerPage (int) [6]
     * - pageParam (string) [page]
     * - page (int) [optional] overrides automatic page from request
     * - pageRange (int) [optional] overrides zend pagination default
     * - replyable (bool) [false]
     *
     * @throws \Exception
     * @return string
     */
    public function __invoke(string $id, array $config = [])
    {
        $page = $config['page'];
        if ($page == '') {
            $page = $this->request->get(isset($config['pageParam']) ? $config['pageParam'] : 'page', 1);
        }

        $comments = new UniversalComment\Listing();
        $comments->addConditionParam('sourceId = :id AND parentComment__id IS NULL', ['id' => $id]);

        $paginator = new Paginator($comments);
        $paginator->setItemCountPerPage(isset($config['itemCountPerPage']) ? $config['itemCountPerPage'] : 6);
        $paginator->setCurrentPageNumber($page);
        if (isset($config['pageRange'])) {
            $paginator->setPageRange($config['pageRange']);
        }

        $template = 'UniversalCommentBundle::container.html.php';
        if ($config['template'] != '') {
            $template = $config['template'];
        }

        return $this->engine->template($template, ['replyable' => ($config['replyable'] == true), 'comments' => $paginator, 'id' => $id]);
    }
}